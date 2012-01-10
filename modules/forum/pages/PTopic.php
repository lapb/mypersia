<?php
	if(!defined('__INDEX__'))
		die('Direct access not allowed.');
		
	require_once TP_GLOBAL_SOURCEPATH.'CDatabaseController.php';
	require_once TP_GLOBAL_SOURCEPATH.'CSecurityController.php';
	
	$db = CDatabaseController::getInstance();
	$security = new CSecurityController();
	
	$spGetTopicDetails 	 = DB_PREFIX.'PGetTopicDetails';
	$spGetTopicPosts 	 = DB_PREFIX.'PGetTopicPosts';

	if(!isset($_GET['id']) || (int)$_GET['id'] != $_GET['id'] || $_GET['id'] < 1) {
		header('Location: ?m=forum&p=topics');
		exit;
	}
	
	$id = $db->escapeString($_GET['id']);
	
	if(!$security->isUserLoggedIn()) {
		$_SESSION['redirect'] = 'topic&amp;m=forum&amp;id='.$id;
		header('Location: ?p=login');
		exit;
	}
	
	$query = "CALL {$spGetTopicDetails}($id);";
	
	$db->multiQuery($query) or die($db->error);
	$results = $db->retrieveAndStoreResultsFromMultiQuery($statements);
	
	if($results[0]->num_rows < 1) {
		header('Location: ?m=forum&p=topics');
		exit;
	}
	
	$row = $results[0]->fetch_assoc();
	
	$html = <<<EOD
		<div class="main">
		<h1>{$row['topicTitle']}</h1>
EOD;

	$sidebar = <<<EOD
		</div>
		<div class="sidebar">
			<h3 class="sidebar-header">About this topic</h3>
			<p>
				Created by {$row['topicCreator']} @ {$row['topicCreationDate']}.
				<br /><br />
				{$row['postCounter']} posts.
				<br /><br />
				Last reply by {$row['lastPostUsername']} @ {$row['lastTopicPostDate']}. 
			</p>
		</div>
		<div class="clear">
		</div>
EOD;

	$query = "CALL {$spGetTopicPosts}($id);";
	
	$db->multiQuery($query) or die($db->error);
	$results = $db->retrieveAndStoreResultsFromMultiQuery($statements);

	
	while($row = $results[0]->fetch_assoc()) {
		
		if($row['userId'] == $_SESSION['idUser'] || $_SESSION['groupMemberUser']  == 'adm') {
			$editLink = '<a href="?m=forum&amp;p=edit-post&amp;tid='.$id.'&amp;id='.$row['id'].'">E</a>';
		} else {
			$editLink = '';
		}
		
		$html .= <<<EOD
			<div class="topic-post">
				<div class="topic-post-sidebar">
					<img src="{$row['gravatar']}" alt="Gravatar" /><br /><br />
					{$row['username']}<br />
					{$row['created']}
				</div>
				<div class="topic-post-content">	
					<p class="post-content">
						<a id="post-{$row['id']}"></a>
						{$row['content']}
					</p>
					<div class="post-link-container">
						{$editLink}&nbsp;<a href="#post-{$row['id']}">#{$row['id']}</a>
					</div>
				</div>
				<div class="clear">
				</div>
			</div>
EOD;
	}
	
	$html .= <<<EOD
		<a href="?m=forum&amp;p=edit-post&amp;tid={$id}">Add reply</a>
EOD;
	
	$html .= $sidebar;
	require_once TP_GLOBAL_SOURCEPATH.'CHTMLPage.php';
	
	$chtml = new CHTMLPage();

	$chtml->printPage('Topic',$html);
?>
