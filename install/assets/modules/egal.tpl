// <?php
/**
 * eGal
 * 
 * Gallery Management Module
 * 
 * @category        module
 * @version         0.1 Beta 1
 * @internal        @properties        &docId=Root Document ID;integer;0 &phpthumbImage=PHPThumb config for images in JSON;textarea;{'w': 940, 'h': 940, 'q': 95} &phpthumbThumb=PHPThumb config for thumbs in JSON;textarea;{'w': 175, 'h': 175, 'q': 75} &savePath=Save path;string;assets/galleries &keepOriginal=Keep original images;list;Yes,No;Yes &randomFilenames=Random filenames;list;Yes,No;No 
 * @internal        @shareparams 1
 * @internal        @dependencies requires files located at /assets/modules/evogallery/
 * @internal        @modx_category Manager and Admin
 * @internal    @installset base, sample
 */

$_SESSION[params]=$params;
include_once('../assets/modules/egal/egal.class.php');