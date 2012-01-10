<?php
	if(!defined('__MODULE_INDEX__'))
		die;
		
	define('__INDEX__',true);
	
	require_once 'config.php';
	
	$page = isset($_GET['p']) ? $_GET['p'] : 'index';
	
	switch($page) {
		case 'index':
			require_once TP_PAGESPATH.'PIndex.php';
		break;
		
		case 'topics':
			require_once TP_PAGESPATH.'PTopicList.php';
		break;
		
		case 'topic':
			require_once TP_PAGESPATH.'PTopic.php';
		break;
		
		case 'create-post':
			require_once TP_PAGESPATH.'PCreatePost.php';
		break;
		
		case 'create-postp':
			require_once TP_PAGESPATH.'PCreatePostProcess.php';
		break;
		
		case 'create-topic':
			require_once TP_PAGESPATH.'PCreateTopic.php';
		break;
		
		case 'create-topicp':
			require_once TP_PAGESPATH.'PCreateTopicProcess.php';
		break;
		
		case 'edit-post':
			require_once TP_PAGESPATH.'PEditPost.php';
		break;
		
		case 'edit-postp':
			require_once TP_PAGESPATH.'PEditPostProcess.php';
		break;
		
		default:
			require_once TP_PAGESPATH.'PIndex.php';
		break;
	}
?>
