<?php
    require_once '../Tree.php';
    $tree = new Tree();
    echo $tree->saveEstimate($_POST);
?>