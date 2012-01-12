<?php
	if(!defined('__INDEX__'))
		die('Direct access not allowed.');
	
	require_once TP_GLOBAL_SOURCEPATH.'CDatabaseController.php';
	
	if((!isset($_POST['nameUser']) || !isset($_POST['passwordUser'])) 
		&& (!isset($_SESSION['silentLoginUsername']) 
			|| !isset($_SESSION['silentLoginPassword']))) {
		
		header('Location: '.WS_SITELINK.'?p=login');
		exit;
	}
	
	$db = CDatabaseController::getInstance();
	
	if(isset($_POST['redirect']))
		$redirect = $_POST['redirect'];
	else
		$redirect = 'index';
	
	if(!isset($_SESSION['silentLoginUsername']) || !isset($_SESSION['silentLoginPassword'])) {
		$user = $db->escapeString($_POST['nameUser']);
		$password = $db->escapeString($_POST['passwordUser']);
	} else {
		$user = $db->escapeString($_SESSION['silentLoginUsername']);
		$password = $db->escapeString($_SESSION['silentLoginPassword']);
		
		unset($_SESSION['silentLoginUsername']);
		unset($_SESSION['silentLoginPassword']);
	}
	
	$spAuthenticateAccount = DB_PREFIX.'PAuthenticateAccount';
	
	$query = <<<EOD
		CALL {$spAuthenticateAccount}('{$user}', '{$password}', @status, @userId, @groupId);
		SELECT @status AS status, @userId AS userId, @groupId AS groupId; 
EOD;
	
	$db->multiQuery($query);
		
	$results = $db->retrieveAndStoreResultsFromMultiQuery($statements);
	$row = $results[1]->fetch_assoc();
	
	require_once TP_GLOBAL_SOURCEPATH.'FDestroySession.php';
	
	session_start();
	session_regenerate_id();

	if($row['status'] == 0) {
		$_SESSION['idUser'] = $row['userId'];
		$_SESSION['accountUser'] = $user;
		$_SESSION['groupMemberUser'] = $row['groupId'];
	} else {
		$_SESSION['errorMessage']  = "Inloggningen misslyckades";
		$_SESSION['redirect'] = $redirect;
		$redirect = 'login';
	}
	
	header('Location: '.WS_SITELINK.'?p='.$redirect);
?>
