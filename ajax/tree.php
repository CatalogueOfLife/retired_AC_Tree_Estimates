<?php
    $id = isset($_GET['id']) && !empty($_GET['id']) ? (int)$_GET['id'] : 0;
    require_once '../Tree.php';
    $tree = new Tree();
    echo $tree->getChildren($id);
?>
