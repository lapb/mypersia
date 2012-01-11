<?php
	if(!defined('__INDEX__'))
		die('Direct access not allowed.');
		
	require_once TP_GLOBAL_SOURCEPATH.'CDatabaseController.php';
	require_once TP_GLOBAL_SOURCEPATH.'CCaptcha.php';
	
	$db = CDatabaseController::getInstance();
	$captcha = new CCaptcha();
	
	$spCreateAccount = DB_PREFIX.'PCreateAccount';
		
	if(isset($_POST['submit'])) {
		if(isset($_POST['username'])) {
			$username = $db->escapeString($_POST['username']);
		} else {
			$username = '';
		}
		
		if($username == '' || strlen($username) > 20) {
			$_SESSION['errorMessage'] = 'Invalid username provided.';
			header('Location: ?m=core&p=create-account');
			exit;
		}
		
		if(isset($_POST['password']) || $_POST['password'] == '') {
			$password = hash('md5',$db->escapeString($_POST['password']));
			$rawPassword = $db->escapeString($_POST['password']);
		} else {
			$_SESSION['errorMessage'] = 'Invalid password provided.';
			header('Location: ?m=core&p=create-account');
			exit;
		}
		
		if(isset($_POST['passwordRepeat'])) {
			$passwordRepeat = hash('md5',$db->escapeString($_POST['passwordRepeat']));
		} else {
			$passwordRepeat = '';
		}
		
		if($password != $passwordRepeat) {
			$_SESSION['errorMessage'] = 'Provided passwords do not match.';
			header('Location: ?m=core&p=create-account');
			exit;
		}
		
		$captcha->checkAnswer();
		
		if($captcha->getErrorMessage() != '') {
			$_SESSION['errorMessage'] = $captcha->getErrorMessage();
			header('Location: ?m=core&p=create-account');
			exit;
		}
		
		$query = <<<EOD
			CALL {$spCreateAccount}('{$username}', '{$password}', @created);
			SELECT @created AS created;
EOD;
		$db->multiQuery($query);
		
		$results = $db->retrieveAndStoreResultsFromMultiQuery($statements);
		$row = $results[1]->fetch_assoc();
		
		if($row['created']) {
			$_SESSION['silentLoginUsername'] = $username;
			$_SESSION['silentLoginPassword'] = $rawPassword;
			header('Location: ?m=core&p=loginp');
		} else {
			$_SESSION['errorMessage'] = 'Username already taken.';
			header('Location: ?m=core&p=create-account');
		}
	} else {
		header('Location: ?m=core&p=create-account');
	}
?>