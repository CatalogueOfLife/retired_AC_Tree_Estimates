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
<img src="images/logo.png" width="322" height="68"
    style="right: 0; float: right; padding: 0 10px;" alt="Catalogue of Life">
<h3>Catalogue of Life: Species estimates</h3>
<?php
    echo '<p class="version">Version ' . Tree::getVersion() . "</p>\n";
    // Test database handler first
    if (isset($_POST['copy'])) {
        echo '<p style="color:red; font-weight: bold;" id="alert">Estimates copied successfully</p>';
    }
    include 'includes/form.php';
?>
    <h4 class="border-top border-right padding-top">Step 1. Find taxon in the CoL tree</h4>
    <div id="tree"></div>

<p class="border-top clear" style="padding-top: 25px; width: 800px;">After adding your estimates, as a final step you can <a href="#" id="copy_to_col_link">submit the estimates to the CoL database</a>. Your estimate figure(s) will appear in the tree of this tool and will be copied to the test version of the Catalogue of Life. Your data will automatically appear in the next monthly edition of the Catalogue of Life.</p>
</body>
</html>