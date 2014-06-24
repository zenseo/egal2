<?php
error_reporting(E_ALL | E_STRICT);
define('MODX_MANAGER_PATH', '../../../../../manager/');
include_once MODX_MANAGER_PATH . "includes/config.inc.php";
require_once(MODX_MANAGER_PATH . "includes/protect.inc.php");
define('MODX_API_MODE', true);
include_once(MODX_MANAGER_PATH . "includes/document.parser.class.inc.php");
startCMSSession();
$modx = new DocumentParser;
//$modx->getSettings();
$modx->db->connect();

$table = $table_prefix . 'portfolio_galleries';
$table1 = $table_prefix . 'portfolio_settings';


//update sorting
if (isset($_REQUEST['sort'])) {
    $data = $_REQUEST['sort'];
    $res = '';
    foreach ($data as $key) {
        $res .= "('" . implode("', '", $key) . "')";
    }
    $res = str_replace(')(', '),(', $res);

    echo $modx->db->query("INSERT INTO $table (id, sortorder) VALUES $res ON DUPLICATE KEY UPDATE  sortorder  = VALUES(sortorder)");
};

//update details
if (isset($_REQUEST['id'])) {
    $fields = array(
        'title' => $_REQUEST['title'],
        'description' => $_REQUEST['description'],
        'keywords' => $_REQUEST['keywords'],
        'sortorder' => $_REQUEST['sortorder'],
    );
    $filename = $_REQUEST['filename'];
    $content_id = $_SESSION['dir'];
    echo $modx->db->update($fields, $table, "filename='" . $filename . "' AND content_id='" . $_SESSION['dir'] . "'");
}

////update settings
if (isset($_REQUEST['content_id']) && !isset($_REQUEST['pId'])) {
    update_settings();
}


if (isset($_POST['pId'])) {
    @$sql = $modx->db->select('content_id, iwidth, iheight, iquality, twidth, theight, tquality', $table1, "content_id='" . $_POST['pId'] . "'");
    $row = $modx->db->getRow($sql);

    if (!$row) {
        $_POST['content_id']=$_POST['pId'];
        unset($_POST['pId']);
        update_settings();
        @$sql = $modx->db->select('content_id, iwidth, iheight, iquality, twidth, theight, tquality', $table1, "content_id='" . $_POST['content_id'] . "'");
        $row = $modx->db->getRow($sql);
        echo json_encode($row);
    }
    else
        echo json_encode($row);
}

function update_settings() {
    global $modx, $table1;
    $res = "( '" . implode("', '", $_POST) . "' )";
    $tt = $modx->db->query("INSERT INTO $table1 (content_id, iwidth, iheight, iquality, twidth, theight, tquality) VALUES $res ON DUPLICATE KEY UPDATE
    iheight = VALUES(iheight),
    iwidth = VALUES(iwidth),
    iquality = VALUES(iquality),
    theight = VALUES(theight),
    twidth = VALUES(twidth),
    tquality = VALUES(tquality)");
    return $tt;
}