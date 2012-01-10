<?php
	if(!defined('__INDEX__'))
		die('Direct access not allowed.');
		
	require_once TP_GLOBAL_SOURCEPATH.'CDatabaseController.php';
	require_once TP_GLOBAL_SOURCEPATH.'CSecurityController.php';
	
	$db = CDatabaseController::getInstance();
	$security = new CSecurityController();

	$tableArticle = DB_PREFIX.'Article';
	$tableTopicPost = DB_PREFIX.'TopicPost';
	$spCreateOrUpdatePost = DB_PREFIX.'PCreateOrUpdatePost';
	$spGetPostDetails = DB_PREFIX . 'PGetPostDetails';

	if(isset($_POST['topicid']) && (int)$_POST['topicid'] == $_POST['topicid'] && $_POST['topicid'] > 0) {
		$topicId = $db->escapeString($_POST['topicid']);
	} else {
		$topicId = 0;
	}
	
	if(isset($_POST['postid']) && (int)$_POST['postid'] == $_POST['postid'] && $_POST['postid'] > 0) {
		$postId = $db->escapeString($_POST['postid']);
	} else {
		$postId = 0;
	}
	
	if(isset($_POST['draft']) && $_POST['draft'] == 1) {
		$action = 'draft';
	} else {
		$action = 'publish';
	}
	
	$timestamp = date('Y-m-d H:i:s',time());
	$published = 0;
	$topicPart = ($topicId > 0 ? '&amp;tid='.$topicId : '');
	$postPart = ($postId > 0 ? '&amp;id='.$postId : '');
	
	if(!$security->isUserLoggedIn()) {
		$_SESSION['redirect'] = 'edit-post&amp;m=forum'.$topicPart.$postPart;
		header('Location: ?p=login');
		exit;
	}
	
	if(isset($_POST['redirect'])) {
		$redirect = $_POST['redirect'];
	} else {
		$redirect = '';
	}
			
	if(!isset($_POST['content']) || $_POST['content'] === '') {
		if(isset($_GET['ajax']) && $_GET['ajax'] == 1) {
			echo <<<EOD
			{
				"error": "No content given.",
				"timestamp": "{$timestamp}",
				"postId": {$postId},
				"topicId": {$topicId},
				"published": {$published}
			}	
EOD;
		} else {
			$_SESSION['errorMessage'] = 'No content given.';
			header('Location: ?m=forum&p=edit-post'.$topicPart.$postPart);
			exit;
		}
	}
	
	$userId = $db->escapeString($_SESSION['idUser']);
	$content = $security->stripTags($db->escapeString($_POST['content']));
	
	if(isset($_POST['title']) && $_POST['title'] != '') {
		$title = $security->stripTags($db->escapeString($_POST['title']));
	} else {
		if($topicId != 0 && $postId != 0) {
			
			$query = "CALL $spGetPostDetails({$postId}, {$userId})";
		
			$db->multiQuery($query) or die($db->error);
			
			$results = $db->retrieveAndStoreResultsFromMultiQuery($statements);
			$row = $results[0]->fetch_assoc();
			
			if($results[0]->num_rows < 1) {
				header('Location: ?m=forum&p=topics');
				exit;
			}
		
			$title = $row['title'];
		} else {
			$title = '';
		}
	}
	
	$query = <<<EOD
		SET @aPostId = {$postId};
		SET @aTopicId = {$topicId};
		CALL {$spCreateOrUpdatePost}(@aPostId, @aTopicId,'{$userId}','{$title}','{$content}', '{$action}', @isPublished);
		SELECT @aTopicId AS aTopicId, @aPostId AS aPostId, @isPublished AS isPublished, NOW() AS timestamp;
EOD;
	
	$db->multiQuery($query) or die($db->error);
	
	$results = $db->retrieveAndStoreResultsFromMultiQuery($statements);
	
	if(count($results) > 3 && $results[3]->num_rows == 1) {
		$row = $results[3]->fetch_assoc();
		
		 $postId = $row['aPostId'];
		 $topicId = $row['aTopicId'];
		 $timestamp = $row['timestamp'];
		 $published = (($row['isPublished'] == 0) ? 0 : 1);
		 $location = '?m=forum&p=topic&id='.$topicId.'#post-'.$postId;
	} else {
		$location = '?m=forum&p=topics';
	}
	
	if(isset($_GET['ajax']) && $_GET['ajax'] == 1) {
		echo <<<EOD
		{
			"timestamp": "{$timestamp}",
			"postId": {$postId},
			"topicId": {$topicId},
			"published": {$published}
		}	
EOD;
	} else {
		header('Location: '.($redirect != '' ? $redirect : $location));
	}
?>