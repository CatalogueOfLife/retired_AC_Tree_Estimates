<?php

function alwaysFlush ()
{
    @ini_set('zlib.output_compression', 0);
    @ini_set('implicit_flush', 1);
    for ($i = 0; $i < ob_get_level(); $i++) {
        ob_end_flush();
    }
    ob_implicit_flush(1);
    set_time_limit(0);
}

function createDbInstance ($name)
{
    $ini = parse_ini_file('config/settings.ini', true);
    $config = $ini['db'];
    $dbOptions = array();
    if (isset($config["options"])) {
        $options = explode(",", $config["options"]);
        foreach ($options as $option) {
            $pts = explode("=", trim($option));
            $dbOptions[$pts[0]] = $pts[1];
        }
        return DbHandler::createInstance($name, $config, $dbOptions);
    }
}



?>