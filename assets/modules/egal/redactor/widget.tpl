<link rel="stylesheet" href="[+redactor_url+]assets/redactor.css" />
<script src="[+redactor_url+]assets/redactor.min.js"></script>

<script type="text/javascript">
<!--
var lastType,lastImageCtrl,lastFileCtrl;
function OpenBrowser(type) {
	var width = screen.width * 0.7;
	var height = screen.height * 0.7;
				
	var iLeft = (screen.width  - width) / 2 ;
	var iTop  = (screen.height - height) / 2 ;

	var sOptions = 'toolbar=no,status=no,resizable=yes,dependent=yes' ;
	sOptions += ',width=' + width ;
	sOptions += ',height=' + height ;
	sOptions += ',left=' + iLeft ;
	sOptions += ',top=' + iTop ;

	var oWindow = window.open( '[+modx_browser_url+]browser.html?Type=' + type, 'FCKBrowseWindow', sOptions ) ;
	lastType = type;
}

var $j = jQuery.noConflict();

$j(document).ready(function() {
	$j('[+textarea+]').redactor({
        imageGetJson: '[+redactor_url+]uploader/server/php/index.php?content_id=[+id+]&upload_url=[+upload_url+]&upload_dir=[+upload_dir+]',
        imageUploadParam: 'files[]',
        imageUpload: '[+redactor_url+]uploader/server/php/index.php?content_id=[+id+]&upload_url=[+upload_url+]&upload_dir=[+upload_dir+]'
        });
});

function SetUrl(url, width, height, alt) {
	if(lastFileCtrl) {
		var c = document.mutate[lastFileCtrl];
		if(c) c.value = url;
		lastFileCtrl = '';
	} else if(lastImageCtrl) {
		var c = document.mutate[lastImageCtrl];
		if(c) c.value = url;
		lastImageCtrl = '';
	} else {
		if (lastType=='images') { $j.redactor({ replaceWith:'<img src="'+url+'" alt="" />' }); }
		if (lastType=='files') { $j.redactor({ openWith:'<a href="'+url+'">', closeWith:'</a>', placeHolder:url.substr(url.lastIndexOf("/") + 1) }); }
	}
	lastType = '';
}
-->
</script>
