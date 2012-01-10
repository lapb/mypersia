<?php
	if(!defined('__INDEX__'))
		die('Direct access not allowed.');
		
	require_once TP_GLOBAL_SOURCEPATH.'CDatabaseController.php';
	require_once TP_GLOBAL_SOURCEPATH.'CSecurityController.php';
	
	$db = CDatabaseController::getInstance();
	$security = new CSecurityController();
	
	if(!$security->isUserLoggedIn()) {
		$_SESSION['redirect'] = 'account-settings&amp;m=core';
		header('Location: ?p=login');
		exit;
	}
	
	$userId = $db->escapeString($_SESSION['idUser']);
	
	if(isset($_POST['password']) && $_POST['password'] != "" && isset($_POST['rpassword'])) {
		$password = hash('md5',$db->escapeString($_POST['password']));
		$rpassword = hash('md5',$db->escapeString($_POST['rpassword']));
		
		if($password != $password) {
			$_SESSION['errorMessage'] = 'Provided passwords do not match.';
			header('Location: ?m=core&p=account-settings');
			exit;
		}
		
		$spUpdateAccountPassword = DB_PREFIX.'PUpdateAccountPassword';
		
		$query = "CALL {$spUpdateAccountPassword}({$userId}, '$password');";
		
		$db->multiQuery($query);
		
		if($db->error != '') {
			$_SESSION['errorMessage'] = "Could not update password.";
		}
		
	} else if(isset($_POST['email'])) {
		if((strlen($_POST['email']) > 0 && !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) || strlen($_POST['email']) > 100) {
			$_SESSION['errorMessage'] = 'Provided email is invalid.';
			header('Location: ?m=core&p=account-settings');
			exit;
		}
		
		$email = $db->escapeString($_POST['email']);
		
		$spUpdateAccountEmail = DB_PREFIX.'PUpdateAccountEmail';
		
		$query = "CALL {$spUpdateAccountEmail}({$userId}, '{$email}');";
		
		$db->multiQuery($query);
		
		if($db->error != '') {
			$_SESSION['errorMessage'] = "Could not update email.".$db->error;
		}
		
	} else if(isset($_POST['gravatarEmail'])) {
		if((strlen($_POST['gravatarEmail']) > 0 && !filter_var($_POST['gravatarEmail'], FILTER_VALIDATE_EMAIL)) || strlen($_POST['gravatarEmail']) > 100) {
			$_SESSION['errorMessage'] = 'Provided email is invalid.';
			header('Location: ?m=core&p=account-settings');
			exit;
		}
		
		$email = $db->escapeString($_POST['gravatarEmail']);
		
		$spUpdateAccountGravatarEmail = DB_PREFIX.'PUpdateAccountGravatarEmail';
		
		if(strlen($email) < 1)
			$query = "CALL {$spUpdateAccountGravatarEmail}({$userId}, NULL);";
		else
			$query = "CALL {$spUpdateAccountGravatarEmail}({$userId}, '{$email}');";
			
		$db->multiQuery($query); 
		
		if($db->error != '') {
			$_SESSION['errorMessage'] = "Could not update gravatar email.";
		}
	} else {
		$_SESSION['errorMessage'] = 'No valid option provided.';
	}
	
	header('Location: ?m=core&p=account-settings');
?>