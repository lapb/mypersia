<?php
	if(!defined('__INDEX__'))
		die('Direct access not allowed.');
	
	require_once TP_GLOBAL_SOURCEPATH.'CDatabaseController.php';
	
	if(!isset($_POST['nameUser']) || !isset($_POST['passwordUser'])) {
		header('Location: '.WS_SITELINK.'?p=login');
		exit;
	}
	
	$db = CDatabaseController::getInstance();
	
	if(isset($_POST['redirect']))
		$redirect = $_POST['redirect'];
	else
		$redirect = 'index';
	
	$user = $db->escapeString($_POST['nameUser']);
	$password = $db->escapeString($_POST['passwordUser']);
	
	$userTable = DB_PREFIX.'User';
	$groupMemberTable = DB_PREFIX.'GroupMember';
	
	$query = "SELECT idUser, accountUser, GroupMember_idGroup AS idGroup FROM {$userTable} AS U JOIN {$groupMemberTable} AS GM ON U.idUser = GM.GroupMember_idUser WHERE accountUser='{$user}' AND passwordUser='".md5($password)."'";
	
	$result = $db->query($query) or die('Could not fetch required information'.$db->error);

	$row = $result->fetch_assoc();
	
	require_once TP_GLOBAL_SOURCEPATH.'FDestroySession.php';
	
	session_start();
	session_regenerate_id();

	if($result->num_rows == 1) {
		$_SESSION['idUser'] = $row['idUser'];
		$_SESSION['accountUser'] = $row['accountUser'];
		$_SESSION['groupMemberUser'] = $row['idGroup'];
	} else {
		$_SESSION['errorMessage']  = "Inloggningen misslyckades";
		$_SESSION['redirect'] = $redirect;
		$redirect = 'login';
	}
	
	$result->close();
	
	header('Location: '.WS_SITELINK.'?p='.$redirect);
?>
