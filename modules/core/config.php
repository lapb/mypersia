<?php
	/*Website settings*/
	define('WS_STYLESHEET','modules/core/css/stylesheet.css');
	
	/*Menu links*/
	$wsMenu = array(
		array(
			'title' => 'Home',
			'link' => '.'
		),
		array(
			'title' => 'Install db',
			'link' => '?p=install'
		),
		array(
			'title' => 'Forum Romanum',
			'link' => '?m=forum&amp;p=index'
		),
		array(
			'title' => 'Show Source',
			'link' => '?p=source'
		)
	);
	
	define('WS_MENU',serialize($wsMenu));
	define('WS_SITELINK','');
	
	/*Template settings*/
	define('TP_SOURCEPATH',dirname(__FILE__).'/src/');
	define('TP_PAGESPATH',dirname(__FILE__).'/pages/');
	define('TP_MODULEPATH','modules/core/');
?>
