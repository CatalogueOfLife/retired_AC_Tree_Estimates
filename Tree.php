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