<?php
error_reporting(E_ALL | E_STRICT);
define('MODX_MANAGER_PATH', '../../../../../manager/');
include_once MODX_MANAGER_PATH."includes/config.inc.php";
require_once(MODX_MANAGER_PATH."includes/protect.inc.php");
define('MODX_API_MODE', true);
include_once(MODX_MANAGER_PATH."includes/document.parser.class.inc.php");
startCMSSession();
$modx = new DocumentParser;
//$modx->getSettings();
$modx->db->connect();
//$result = $modx->db->select( '*', $table_prefix.'portfolio_galleries' );
//$cols = $modx->db->getRow( $result );
//echo $modx->getLoginUserID();

@session_start();
//if (isset($_REQUEST['iwidth']))  $_SESSION['iW']=$_REQUEST['iwidth'];
//if (isset($_REQUEST['iheight']))  $_SESSION['iH']=$_REQUEST['iheight'];
//if (isset($_REQUEST['iquality']))  $_SESSION['iQ']=$_REQUEST['iquality'];
//
//if (isset($_REQUEST['twidth']))  $_SESSION['tW']=$_REQUEST['twidth'];
//if (isset($_REQUEST['theight']))  $_SESSION['tH']=$_REQUEST['theight'];
//if (isset($_REQUEST['tquality']))  $_SESSION['tQ']=$_REQUEST['tquality'];

$options = array(
    'image_library' => 1,
    //'gallery_dir' => isset($_REQUEST['pId'])?($_REQUEST['pId']):"1",
    'delete_type' => 'POST',
    //'download_via_php' => true,
    'user_dirs' => true,
    'upload_dir' => $base_path . $_SESSION['params']['savePath'],
    'upload_url' => $site_url . $_SESSION['params']['savePath'],
    'db_host' => $database_server,
    'db_user' => $database_user,
    'db_pass' => $database_password,
    'db_name' => $dbase,
    'db_table' => $table_prefix . 'portfolio_galleries',
    'image_versions' => array(
//        '' => array(
//            'max_width' => 1920,
//            'max_height' => 1200,
//            'jpeg_quality' => 100
//        ),
        'images' => array(
            'max_width' => $_REQUEST['iwidth'],
            'max_height' => $_REQUEST['iheight'],
            'jpeg_quality' => $_REQUEST['iquality'],
            'crop' =>  $_REQUEST['icrop']
        ),
        'thumbs' => array(
            'max_width' => $_REQUEST['twidth'],
            'max_height' => $_REQUEST['theight'],
            'jpeg_quality' => $_REQUEST['tquality'],
            'crop' =>  $_REQUEST['tcrop']
        )
    )
);

error_reporting(E_ALL | E_STRICT);
require('UploadHandler.php');


class CustomUploadHandler extends UploadHandler {

    protected function get_user_id() {
        @session_start();
        if (isset($_REQUEST['content_id'])) $_SESSION['dir']=$_REQUEST['content_id'];
        return '/'.$_SESSION['dir'];
    }

//    protected function initialize() {
//global $modx;
//        echo "234";
////        $this->db = new mysqli(
////            $this->options['db_host'],
////            $this->options['db_user'],
////            $this->options['db_pass'],
////            $this->options['db_name']
////        );
//        parent::initialize();
////        $this->db->close();
//    }

    protected function handle_form_data($file, $index) {
        $file->title = @$_REQUEST['title'][$index];
        $file->description = @$_REQUEST['description'][$index];
        $file->sortorder = @$_REQUEST['sortorder'][$index];
    }

    protected function handle_file_upload($uploaded_file, $name, $size, $type, $error,
                                          $index = null, $content_range = null) {
        global $modx;
        $file = parent::handle_file_upload(
            $uploaded_file, $name, $size, $type, $error, $index, $content_range
        );

        if (empty($file->error)) {
            @$fields = array('content_id' => $_SESSION['dir'], 'filename' => $file->name, 'title' =>  $file->title, 'description' => $file->description, 'sortorder' => $file->sortorder);
            @$file->id = $modx->db->insert( $fields, $this->options['db_table']);
        }
        return $file;
    }

    protected function set_additional_file_properties($file) {
        global $modx;
        parent::set_additional_file_properties($file);

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {

            @$sql=$modx->db->select('*', $this->options['db_table'],"filename='".$file->name."'","sortorder");

            while ($row = $modx->db->getRow( $sql )) {
                $file->id = $row['id'];
                //$file->type = $type;
                $file->title = $row['title'];
                $file->description = $row['description'];
                $file->sortorder = $row['sortorder'];
                $file->keywords = $row['keywords'];
            }
        }
    }

    public function get($print_response = true) {
        if ($print_response && isset($_GET['download'])) {
            return $this->download();
        }
        $file_name = $this->get_file_name_param();
        if ($file_name) {
            $response = array(
                $this->get_singular_param_name() => $this->get_file_object($file_name)
            );
        } else {
            $response = array(
                $this->options['param_name'] => $this->get_file_objects()
            );
        }

        if (isset ($_REQUEST['regenerate'])){
        foreach ($response['files'] as $index => $value) {
            $this->handle_image_file('', $response['files'][$index]);
        }
        }

        return $this->generate_response($response, $print_response);
    }

    public function delete($print_response = true) {
        global $modx;
        $response = parent::delete(false);

        foreach ($response as $name => $deleted) {
            if ($deleted) {
                $modx->db->delete( $this->options['db_table'], "filename='".$name."' AND content_id='".$_SESSION['dir']."'" );
            }
        }
        return $this->generate_response($response, $print_response);
    }
    protected function get_file_objects($iteration_method = 'get_file_object') {
        global $modx;
        $upload_dir = $this->get_upload_path();
        if (!is_dir($upload_dir)) {
            return array();
        }
        $cId=1;
        if (isset($_REQUEST['content_id'])) $cId=$_REQUEST['content_id'];
        @$sql=$modx->db->select("filename", $this->options['db_table'],"content_id=$cId","sortorder");

        while ($row = $modx->db->getRow( $sql )) {
            $tmp[] = $row['filename'];
        }


        $arr = array_values(array_filter(array_map(
            array($this, $iteration_method), $tmp
        )));

        function sortByOrdering($obj1, $obj2) {
            return $obj1->sortorder - $obj2->sortorder;
        }
        usort ($arr, 'sortByOrdering');

        return $arr;
    }

protected function niceFilename($filename) {
        $changes = array(
            "Є"=>"EH", "І"=>"I", "і"=>"i", "№"=>"#", "є"=>"eh",
            "А"=>"A", "Б"=>"B", "В"=>"V", "Г"=>"G", "Д"=>"D",
            "Е"=>"E", "Ё"=>"E", "Ж"=>"ZH", "З"=>"Z", "И"=>"I",
            "Й"=>"J", "К"=>"K", "Л"=>"L", "М"=>"M", "Н"=>"N",
            "О"=>"O", "П"=>"P", "Р"=>"R", "С"=>"S", "Т"=>"T",
            "У"=>"U", "Ф"=>"F", "Х"=>"H", "Ц"=>"C", "Ч"=>"CH",
            "Ш"=>"SH", "Щ"=>"SCH", "Ъ"=>"", "Ы"=>"Y", "Ь"=>"",
            "Э"=>"E", "Ю"=>"YU", "Я"=>"YA", "Ē"=>"E", "Ū"=>"U",
            "Ī"=>"I", "Ā"=>"A", "Š"=>"S", "Ģ"=>"G", "Ķ"=>"K",
            "Ļ"=>"L", "Ž"=>"Z", "Č"=>"C", "Ņ"=>"N", "ē"=>"e",
            "ū"=>"u", "ī"=>"i", "ā"=>"a", "š"=>"s", "ģ"=>"g",
            "ķ"=>"k", "ļ"=>"l", "ž"=>"z", "č"=>"c", "ņ"=>"n",
            "а"=>"a", "б"=>"b", "в"=>"v", "г"=>"g", "д"=>"d",
            "е"=>"e", "ё"=>"e", "ж"=>"zh", "з"=>"z", "и"=>"i",
            "й"=>"j", "к"=>"k", "л"=>"l", "м"=>"m", "н"=>"n",
            "о"=>"o", "п"=>"p", "р"=>"r", "с"=>"s", "т"=>"t",
            "у"=>"u", "ф"=>"f", "х"=>"h", "ц"=>"c", "ч"=>"ch",
            "ш"=>"sh", "щ"=>"sch", "ъ"=>"", "ы"=>"y", "ь"=>"",
            "э"=>"e", "ю"=>"yu", "я"=>"ya", "Ą"=>"A", "Ę"=>"E",
            "Ė"=>"E", "Į"=>"I", "Ų"=>"U", "ą"=>"a", "ę"=>"e",
            "ė"=>"e", "į"=>"i", "ų"=>"u", "ö"=>"o", "Ö"=>"O",
            "ü"=>"u", "Ü"=>"U", "ä"=>"a", "Ä"=>"A", "õ"=>"o",
            "Õ"=>"O");
        $alias=strtr($filename, $changes);
        $alias = strtolower( $alias );
        $alias = preg_replace('/&.+?;/', '', $alias); // kill entities
//        $alias = str_replace( '_', '-', $alias );
        $alias = str_replace( ' ', '_', $alias );
//        $alias = preg_replace('/[^a-z0-9\s-.]/', '', $alias);
        $alias = preg_replace('/\s+/', '-', $alias);
        $alias = preg_replace('|-+|', '-', $alias);
//        $alias = trim($alias, '-');
        return $alias;
    }
//    protected function get_file_name($file_path, $name, $size, $type, $error,
//                                     $index, $content_range) {
//        return $this->niceFilename($this->get_unique_filename(
//            $file_path,
//            $this->trim_file_name($file_path, $name, $size, $type, $error,
//                $index, $content_range),
//            $size,
//            $type,
//            $error,
//            $index,
//            $content_range
//        ));
//    }
    protected function get_unique_filename($file_path, $name, $size, $type, $error,
                                           $index, $content_range) {
        while(is_dir($this->get_upload_path($name))) {
            $name = $this->upcount_name($name);
        }
        // Keep an existing filename if this is part of a chunked upload:
        $uploaded_bytes = $this->fix_integer_overflow(intval($content_range[1]));
        while(is_file($this->get_upload_path($name))) {
            if ($uploaded_bytes === $this->get_file_size(
                    $this->get_upload_path($name))) {
                break;
            }
            $name = $this->upcount_name($name);
        }
        return $this->niceFilename($name);
    }

}


$upl = new CustomUploadHandler($options);
