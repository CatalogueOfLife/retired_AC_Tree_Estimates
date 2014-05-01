<?php
session_start();
require_once 'Tree.php';
require_once 'includes/library.php';
alwaysFlush();

// Fetch these vars only
$vars = array(
    'taxon'
);

// Fetch variables from POST if at least one has been submitted
if (!empty($_POST)) {
    foreach ($vars as $var) {
        if (isset($_POST[$var]) && !empty($_POST[$var])) {
            $$var = $_POST[$var];
        }
        else {
            $$var = '';
        }
        $_SESSION[$var] = $$var;
    }
}
// ... otherwise try SESSION
else {
    foreach ($vars as $var) {
        if (isset($_SESSION[$var]) && !empty($_SESSION[$var])) {
            $$var = $_SESSION[$var];
        }
        else {
            $$var = '';
        }
    }
}
?>