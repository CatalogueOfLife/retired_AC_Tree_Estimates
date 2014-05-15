<?php
session_start();

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

require_once 'Tree.php';
$tree = new Tree();
alwaysFlush();

if (isset($_POST['copy'])) {
    $tree->copyEstimates();
}
?>