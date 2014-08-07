<?php
//redactor! for MODx evo Plugin v1.1.6.1

function get_redactor_init($params, $mode='')
{
	$redactor_init = file_get_contents($params['redactor_path'] . 'widget.tpl');
	
	$textarea = array();
	if ($mode=='id')
	{
		foreach($params['elements'] as $value)
		{
			$textarea[] = '#' . $value;
		}
		$ph['textarea'] = join(',', $textarea);
	}
	else
	{
		$ph['textarea'] = 'textarea';
	}
	$ph['redactor_url'] = $params['redactor_url'];
	$ph['modx_browser_url'] = MODX_MANAGER_URL.'/media/browser/mcpuk/';
	$ph['id'] = $_GET['id'];
    $ph['upload_url'] = $GLOBALS['site_url'].$params['savePath'];
    $ph['upload_dir'] = $GLOBALS['base_path'].$params['savePath'];
	foreach($ph as $name => $value)
	{
		$name = '[+' . $name . '+]';
		$redactor_init = str_replace($name, $value, $redactor_init);
	}
	return $redactor_init;
}
