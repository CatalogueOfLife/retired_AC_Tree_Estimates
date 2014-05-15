<?php
    // Sets form variables, session start and several includes
    require_once 'includes/init.php';
?>
<!DOCTYPE html>
<head>
<?php
    // Sets jQuery and stylesheet paths
    require_once 'includes/head.php';
?>
</head>
<body>
<!--
<img src="images/i4life_logo_sm.jpg" width="150" height="62"
    style="right: 0; float: right; padding: 0 10px;" alt="i4Life">
-->
<h3>Catalogue of Life: Species estimates</h3>
<?php
    echo '<p class="version">Version ' . Tree::getVersion() . "</p>\n";
    // Test database handler first
    if (isset($_POST['copy'])) {
        echo '<p style="color:red; font-weight: bold;" id="alert">Estimates copied successfully</p>';
    }
    include 'includes/form.php';
?>
    <h4>Step 1. Find taxon in the CoL tree</h4
    ><div id="tree"></div>
</body>
</html>