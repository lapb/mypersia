<?php
	define('__MODULE_INDEX__',true);
	
	session_start();
	require_once 'global.config.php';
	
	$module = isset($_GET['m']) ? $_GET['m'] : 'core';
	
	switch($module) {
		case 'core':
			require_once TP_MODULESPATH.'core/index.php';
		break;
		
		case 'forum':
			require_once TP_MODULESPATH.'forum/index.php';
		break;
		
		default:
			require_once TP_MODULESPATH.'core/index.php';
		break;
	}
?>
