<?php

$options = array(
    //'image_library' => 1,
    //'gallery_dir' => isset($_REQUEST['pId'])?($_REQUEST['pId']):"1",
    'delete_type' => 'POST',
    //'download_via_php' => true,
    'user_dirs' => true,
    'upload_dir' => $_GET['upload_dir'] ,
    'upload_url' => $_GET['upload_url'] ,
    'image_versions' => array(
        'image' => array(
            'max_width' => 200,
            'max_height' => 200,
            'jpeg_quality' => 50,
            'crop' =>  false
        )
    )
);

error_reporting(E_ALL | E_STRICT);
require('UploadHandler.php');


class CustomUploadHandler extends UploadHandler {

    // protected function get_user_id() {
    //     @session_start();
    //     if (isset($_REQUEST['content_id'])) $_SESSION['dir']=$_REQUEST['content_id'];
    //     return '/'.$_SESSION['dir'];
    // }

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