<?php
require_once 'DbHandler.php';
require_once 'Bootstrap.php';

class Tree
{
    // Basescheme database handler
    private $_bs_dbh;
    // Tree estimates database handler
    private $_te_dbh;
    // Results
    private $_res;
    // List of higher taxa
    private $_higherTaxa = array('class', 'family', 'kingdom', 'not assigned', 'order',
        'phylum', 'superfamily');

    // Collects bootstrap errors
    public $startUpErrors;

    public function __construct ()
    {
        $this->_createDbInstance('basescheme');
        $this->_bs_dbh = DbHandler::getInstance('basescheme');
        $this->_createDbInstance('tree_estimates');
        $this->_te_dbh = DbHandler::getInstance('tree_estimates');
        $ini = parse_ini_file('config/settings.ini', true);

        $bootstrap = new DCABootstrap($this->_bs_dbh, $this->_te_dbh);
        $this->startUpErrors = $bootstrap->getErrors();
        unset($bootstrap);
    }

    public function __destruct ()
    {
        //
    }

    public function getChildren ($id)
    {
        $q = 'SELECT t1.`taxon_id` AS `id`, t1.`name`, t1.`rank`, t1.`total_species_estimation`,
            t1.`total_species`,  t1.`estimate_source`,  t2.`taxon_id` AS `child_id`,
            t2.`name` AS `child_name`, t2.`rank` AS `child_rank`, t2.`total_species_estimation` AS
            `child_total_species_estimation`, t2.`total_species` AS `child_total_species`,
            t2.`estimate_source` AS `child_estimate_source`
            FROM `_taxon_tree` AS t1
            LEFT JOIN `_taxon_tree` AS t2 ON t1.`taxon_id` = t2.`parent_id`
            WHERE t1.`parent_id` = ? ORDER BY t1.`name`, t2.`name`';
        $stmt = $this->_bs_dbh->prepare($q);
        $stmt->execute(array($id));
        $d = array();
        // Use id as node lookup in tree array
        while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if (!isset($d[$r['id']])) {
                $t = array(
                    'title' => $this->_setTitle($r),
                    'key' => $r['id'],
                    'rank' => $r['rank'],
                    'name' => $r['name']
                );
                if (in_array($r['rank'], $this->_higherTaxa)) {
                    $t['lazy'] = true;
                }
                $d[$r['id']] = $t;
            }
            if (!isset($d[$r['id']]['children'][$r['child_id']])) {
                $d[$r['id']]['children'][$r['child_id']] = array(
                    'title' => $this->_setTitle($r, 'child_'),
                    'key' => $r['child_id'],
                    'rank' => $r['child_rank'],
                    'name' => $r['child_name'],
                    'lazy' => true
                );
            }
        }
        // Strip keys before returning as json
        return json_encode($this->_fixKeys($d));
    }

    public function getEstimate ($p)
    {
        $q = 'SELECT `id`, `kingdom`, `rank`, `name`, `estimate`, `source` FROM `estimates`
            WHERE `kingdom` = ? AND `rank` = ? AND `name` = ?';
        $stmt = $this->_te_dbh->prepare($q);
        $stmt->execute($p);
        $r = $stmt->fetch(PDO::FETCH_ASSOC);
        return !empty($r) ? json_encode($r) : json_encode(
            array(
                'kingdom' => $p[0],
                'rank' => ucfirst($p[1]),
                'name' => $p[2],
                'estimate' => null,
                'source' => null,
                'id' => null
            )
        );
    }

    public function saveEstimate ($p)
    {
        // Make sure estimate is a number
        $p['estimate'] = (int)$p['estimate'];
        if (!empty($p['id'])) {
            $q = 'UPDATE `estimates` SET `kingdom` = :kingdom, `rank` = :rank, `name` = :name, ' .
                '`estimate` = :estimate, `source` = :source, `updated` = :updated WHERE `id` = :id';
            $p['updated'] = date('Y-m-d H:i:s');
        } else {
            $q = 'INSERT INTO `estimates` (`kingdom`, `rank`, `name`, `estimate`, `source`) ' .
                'VALUES (:kingdom, :rank, :name, :estimate, :source)';
            unset($p['id']);
        }
        $stmt = $this->_te_dbh->prepare($q);
        foreach ($p as $k => &$v) {
            $stmt->bindParam($k, $v);
        }
        return $stmt->execute() ?
            'Estimate saved for ' . $p['rank'] . ' ' . $p['name'] :
            'Error: could not save estimate for ' . $p['rank'] . ' ' . $p['name'];
    }

    public function copyEstimates () {
        $q = 'SELECT * FROM `estimates`';
        $stmt = $this->_te_dbh->query($q);
        while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $id = $this->_getTaxonTreeId(array($r['name'], $r['rank'], $r['kingdom']));
            if ($id) {
                $this->_updateTaxonTree(array($r['estimate'], $r['source'], $id));
            }
        }
    }

    private function _getTaxonTreeId ($p)
    {
        $q = 'SELECT t1.`taxon_id` AS `id` FROM `_taxon_tree` AS t1
            LEFT JOIN `_search_scientific` AS t2 ON t1.`taxon_id` = t2.`id`
            WHERE t1.`name` = ? AND t1.`rank` = ? AND t2.`kingdom` = ?';
        $stmt = $this->_bs_dbh->prepare($q);
        $stmt->execute($p);
        $r = $stmt->fetch(PDO::FETCH_ASSOC);
        return !empty($r) ? $r['id'] : false;
    }

    private function _updateTaxonTree ($p)
    {
        $q = 'UPDATE `_taxon_tree` SET `total_species_estimation` = ?,
            `estimate_source` = ? WHERE `taxon_id` = ?';
        $stmt = $this->_bs_dbh->prepare($q);
        $stmt->execute($p);
    }

    private function _clearEstimates ()
    {
        $q = 'UPDATE `_taxon_tree` SET `total_species_estimation` = 0, `estimate_source` = ""';
        $this->_bs_dbh->query($q);
    }

    private function _setTitle ($r, $prefix = '')
    {
        $id = $r[$prefix . 'id'];
        $rank = $r[$prefix . 'rank'];
        $name = $r[$prefix . 'name'];
        $count = $r[$prefix . 'total_species'];
        $estimation = $r[$prefix . 'total_species_estimation'];
        if ($rank == 'genus') {
            $name = "<em>$name</em>";
        }
        if ($rank !== 'genus' && !in_array($rank, $this->_higherTaxa)) {
            $rank = $count = '';
        }
        $title =  (!empty($rank) ? ucfirst($rank) . ' ' : '') . $name;
        if ($count !== '') {
           $title .= ' (estimate: ' . (!empty($estimation) ? $estimation : '-') .
            ', CoL: ' . (!empty($count) ? $count : '-') . ')';
        }
        return $title;
    }

    private function _getKingdom ($r)
    {
        $q = 'SELECT `kingdom` FROM `_search_scientific` WHERE `id` = ?';
        $stmt = $this->_bs_dbh->prepare($q);
        $stmt->execute(array($r['id']));
        $r = $stmt->fetch(PDO::FETCH_ASSOC);
        return !empty($r) ? $r['kingdom'] : null;
    }

    private function _fixKeys ($array)
    {
        $numberCheck = false;
        foreach ($array as $k => $val) {
            if (is_array($val)) {
                $array[$k] = $this->_fixKeys($val);
            }
            if (is_numeric($k)) {
                $numberCheck = true;
            }
        }
        return $numberCheck === true ? array_values($array) : $array;
    }

    public static function getVersion ()
    {
        $ini = parse_ini_file('config/settings.ini', true);
        return $ini['settings']['version'] . ' [r' . $ini['settings']['revision'] . ']';
    }

    public static function getEdition ()
    {
        $ini = parse_ini_file('config/settings.ini', true);
        return $ini['credits']['string'] . ' (' . $ini['credits']['release_date'] . ')';
    }


    private function _createDbInstance ($name)
    {
        $ini = parse_ini_file('config/settings.ini', true);
        $config = $ini[$name];
        $dbOptions = array();
        if (isset($config["options"])) {
            $options = explode(",", $config["options"]);
            foreach ($options as $option) {
                $pts = explode("=", trim($option));
                $dbOptions[$pts[0]] = $pts[1];
            }
            DbHandler::createInstance($name, $config, $dbOptions);
        }
    }

    private function _setTreeEstimate ($i, $row)
    {
        $q = 'SELECT `id`, `estimate`, `source` FROM `estimates`
            WHERE `kingdom` = ? AND `rank` = ? AND `name` = ?';
        $stmt = $this->_te_dbh->prepare($q);
        $stmt->execute(array($row['kingdom'], $row['rank'], $row['name']));
        $r = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->_res[$i]['estimate_id'] = isset($r['id']) ? $r['id'] : null;
        $this->_res[$i]['estimate'] = isset($r['estimate']) ? $r['estimate'] : null;
        $this->_res[$i]['source'] = isset($r['source']) ? $r['source'] : null;
    }

    private function _appendTreeEstimates ()
    {
        foreach ($this->_res as $i => $row) {
            $this->_setTreeEstimate($i, $row);
        }
        return $this->_res;
    }

    private function _createPath ($row)
    {
        $path = '';
        foreach (array('kingdom', 'phylum', 'class', 'order', 'family') as $t) {
            if (!empty($row[$t])) {
                $path .= $row[$t] . ' &gt; ';
            }
        }
        return substr($path, 0, -5);
    }

    public function formatResults ()
    {
        $output = "<div id='results'>\n";
        foreach ($this->_res as $row) {
            $output .= "<div class='result'>\n" .
                "<div class='name'>" . ucfirst($row['rank']) . " " .  $row['name'] . "</div>\n" .
                "<div class='path'><b>Path</b>: " . $this->_createPath($row) . "</div>\n" .
                "<div class='path'><b>Estimate</b>: " .
                    (!empty($row['estimate']) ? $row['estimate'] : '-') . "</div>\n" .
                "<div class='path'><b>Source</b>: " .
                    (!empty($row['source']) ? $row['source'] : '-') . "</div\n>" .
                "<div class='edit'>Edit</div>\n</div>\n";
       }
       echo $output . "</div>\n";
    }

    public function find ($taxon)
    {
        $q = 'SELECT t1.`taxon_id`, t1.`name`, t1.`rank`, t2.`kingdom`,
                t2.`phylum`, t2.`class`, t2.`order`, t2.`family`
            FROM `_taxon_tree` t1
            LEFT JOIN `_search_scientific` AS t2 ON t1.`taxon_id` = t2.`id`
            WHERE t1.`name` LIKE ?
            AND `rank` NOT IN ("species", "infraspecies", "not assigned", "subspecies",
                "variety", "form")
            ORDER BY `name`';
        $stmt = $this->_bs_dbh->prepare($q);
        $stmt->execute(array($taxon . '%'));
        $this->_res = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $this->_res ? $this->_appendTreeEstimates($this->_res) : false;
    }

    public function getStartUpErrors ()
    {
        return $this->startUpErrors;
    }

    public function getTotalNumberOfTaxa ()
    {
        $query = $this->_buildQuery('tt');
        $stmt = $this->_dbh->prepare($query);
        foreach ($this->_sc as $field => $value) {
            $stmt->bindValue(':' . $field, $value);
        }
        $stmt->execute();
        $res = $stmt->fetch(PDO::FETCH_NUM);
        return $res ? $res[0] : false;
    }

    public function useIndicator ()
    {
        $this->_indicator = new Indicator();
    }

    public function setIndicatorBreakLine ($v)
    {
        $this->_indicator->setBreakLine($v);
    }

    public function setIndicatorMarkersPerLine ($v)
    {
        $this->_indicatorMarkersPerLine = $v;
    }

    public function setIndicatorIterationsPerMarker ($v)
    {
        $this->_indicatorIterationsPerMarker = $v;
    }



}