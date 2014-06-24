<?php

if (IN_MANAGER_MODE != 'true' && !$modx->hasPermission('exec_module')) die('<b>INCLUDE_ORDERING_ERROR</b><br /><br />Please use the MODX Content Manager instead of accessing this file directly.');

global $modx;
$sql = "CREATE TABLE IF NOT EXISTS " . $modx->getFullTableName("portfolio_galleries") . " (" .
    "`id` int(11) NOT NULL auto_increment PRIMARY KEY, " .
    "`content_id` int(11) NOT NULL, " .
    "`filename` varchar(255) NOT NULL, " .
    "`title` varchar(255) NOT NULL, " .
    "`description` TEXT NOT NULL, " .
    "`keywords` TEXT NOT NULL, " .
    "`sortorder` smallint(7) NOT NULL default '0'" .
    ")";
$modx->db->query($sql);

$sql = "CREATE TABLE IF NOT EXISTS " . $modx->getFullTableName("portfolio_settings") . " (" .
    "`content_id` int(11) NOT NULL PRIMARY KEY," .
    "`iwidth` smallint(11) unsigned NOT NULL," .
    "`iheight` smallint(11) unsigned NOT NULL," .
    "`iquality` tinyint(3) unsigned NOT NULL," .
    "`twidth` smallint(11) unsigned NOT NULL," .
    "`theight` smallint(11) unsigned NOT NULL," .
    "`tquality` tinyint(3) unsigned NOT NULL" .
    ")";
$modx->db->query($sql);
//test
if (isset ($_REQUEST['content_id']))
    echo str_replace('(content_id)', $_REQUEST['content_id'],file_get_contents(dirname(__FILE__) . '/widget_mm.html'));
else echo file_get_contents(dirname(__FILE__) . '/widget.html');
