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

//update settings
if (isset($_REQUEST['update'])) {
    update_settings();
}


if (isset($_REQUEST['select'])) {
    @$sql = $modx->db->select('content_id, image_versions', $table1, "content_id='" . $_REQUEST['content_id'] . "'");
    @$row = $modx->db->getRow($sql);

    if ($row) echo ($row[image_versions]);
    else echo (update_settings());

}

function update_settings() {
    global $modx, $table1;

    //$res= array_shift($_POST);
    $res= $_POST['content_id'];
    $image_versions = json_encode($_POST);
    //$res = "( '" . implode("', '", $_POST) . "' )";
    $tt = $modx->db->query("INSERT INTO $table1 (content_id, image_versions) VALUES ($res,'$image_versions') ON DUPLICATE KEY UPDATE image_versions = VALUES(image_versions)");
    return $image_versions;
}