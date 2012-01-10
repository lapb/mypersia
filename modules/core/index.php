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
		
		case 'install':
			require_once TP_PAGESPATH.'install/PInstall.php';
		break;
		
		case 'installp':
			require_once TP_PAGESPATH.'install/PInstallProcess.php';
		break;
		
		case 'login':
			require_once TP_PAGESPATH.'login/PLogin.php';
		break;
		
		case 'loginp':
			require_once TP_PAGESPATH.'login/PLoginProcess.php';
		break;
		
		case 'logoutp':
			require_once TP_PAGESPATH.'login/PLogoutProcess.php';
		break;
		
		case 'profile':
			require_once TP_PAGESPATH.'user/PProfileShow.php';
		break;
		
		case 'admin':
			require_once TP_PAGESPATH.'admin/PUsersList.php';
		break;
		
		case 'source':
			require_once TP_PAGESPATH.'source/PSource.php';
		break;
		
		case 'create-account':
			require_once TP_PAGESPATH.'account/PAccountCreate.php';
		break;
		
		case 'create-accountp':
			require_once TP_PAGESPATH.'account/PAccountCreateProcess.php';
		break;
		
		case 'account-settings':
			require_once TP_PAGESPATH.'account/PAccountSettings.php';
		break;
		
		case 'account-settingsp':
			require_once TP_PAGESPATH.'account/PAccountSettingsProcess.php';
		break;
		
		default:
			require_once TP_PAGESPATH.'PIndex.php';
		break;
	}
?>
