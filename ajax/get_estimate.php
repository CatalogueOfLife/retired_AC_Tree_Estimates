<?php
    $p = array($_GET['kingdom'], $_GET['rank'], $_GET['name']);
    require_once '../Tree.php';
    $tree = new Tree();
    echo $tree->getEstimate($p);
?>