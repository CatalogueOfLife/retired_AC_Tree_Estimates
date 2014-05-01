<?php
class DCABootstrap
{
    private $_errors = array();

    public function __construct ($bs_dbh, $te_dbh)
    {
        foreach (array($bs_dbh, $te_dbh) as $dbh) {
            if (!($dbh instanceof PDO)) {
                die('Could not create database instance(s); check settings in settings.ini!');
            }
        }
    }

    public function getErrors ()
    {
        return $this->_errors;
    }
}
