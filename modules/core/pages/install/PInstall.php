<?php
	if(!defined('__INDEX__'))
		die('Direct access not allowed.');
		
	$database = DB_DATABASE;
	$prefix = DB_PREFIX;
	
	$html = <<<EOD
		<h2>Installation</h2>
		<h3>Create tables</h3>
		
		<p>
			Press the link below in order to erase all exisitng content from the database and create new tables.
			You have choosen the database '{$database}' and tables will be created with prefix '{$prefix}'.
			Please edit config.php if this is incorrect.
		</p>
		<p>
			<a href="?p=installp" style="text-decoration: underline;">Empty the database and create new tables</a>
		</p>
EOD;

	require_once TP_GLOBAL_SOURCEPATH.'CHTMLPage.php';
	
	$chtml = new CHTMLPage();

	$chtml->printPage('Install page',$html);
?>