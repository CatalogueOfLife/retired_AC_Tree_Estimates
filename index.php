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
<img src="images/i4life_logo_sm.jpg" width="150" height="62"
    style="right: 0; float: right; padding: 0 10px;" alt="i4Life">
<h3>i4Life WP4: Species estimates</h3>
<?php
    echo '<p class="version">Version ' . Tree::getVersion() . "</p>\n";
    // Test database handler first
    include 'includes/form.php';
    if (formSubmitted()) {
        include 'includes/results.php';
    }
?>
</body>
</html>