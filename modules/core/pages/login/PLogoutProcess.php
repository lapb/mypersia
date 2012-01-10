<?php
	if(!defined('__INDEX__'))
		die('Direct access not allowed.');
		
	$redirect = isset($_SESSION['redirect']) ? $_SESSION['redirect'] : 'index';

	require_once TP_GLOBAL_SOURCEPATH.'FDestroySession.php';
	
	header('Location: '.WS_SITELINK.'?p='.$redirect);
?>