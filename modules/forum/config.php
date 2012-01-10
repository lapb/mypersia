<?php
	/*Website settings*/
	define('WS_STYLESHEET','modules/forum/css/stylesheet.css');
	
	/*Menu links*/
	$wsMenu = array(
		array(
			'title' => 'Home',
			'link' => '?m=forum'
		),
		array(
			'title' => 'Latest',
			'link' => '?m=forum&amp;p=topics'
		),
		array(
			'title' => 'Create new topic',
			'link' => '?m=forum&amp;p=edit-post'
		),
		array(
			'title' => 'Core',
			'link' => '?m=core&amp;p=index'
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
	define('TP_MODULEPATH','modules/forum/');
?>
