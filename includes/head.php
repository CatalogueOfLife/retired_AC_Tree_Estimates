<meta http-equiv="content-type" content="text/html; charset=utf-8">
<title>Catalogue of Life: Species estimates</title>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js" type="text/javascript"></script>
<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1/jquery-ui.min.js" type="text/javascript"></script>
<script src="scripts/jquery.fancytree.min.js" type="text/javascript"></script>

<!-- Set source for tree -->
<script type="text/javascript">
    var treeSource = <?php echo $tree->getChildren(0); ?>
</script>

<!-- Initialize tree -->
<script src="scripts/tree.js" type="text/javascript"></script>

<link href="style/fancytree/skin-xp/ui.fancytree.min.css" rel="stylesheet" type="text/css">
<link href="style/style.css" rel="stylesheet" type="text/css" media="all">
