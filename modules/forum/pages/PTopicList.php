<?php
	if(!defined('__INDEX__'))
		die('Direct access not allowed.');
		
	require_once TP_GLOBAL_SOURCEPATH.'CDatabaseController.php';
	require_once TP_GLOBAL_SOURCEPATH.'CSecurityController.php';
	
	$db = CDatabaseController::getInstance();
	$security = new CSecurityController();
	
	if(!$security->isUserLoggedIn()) {
		$_SESSION['redirect'] = 'topics&amp;m=forum';
		header('Location: ?p=login');
		exit;
	}

	$spListTopics = DB_PREFIX.'PListTopics';
	$query = "CALL {$spListTopics}();";
	
	$db->multiQuery($query) or die($db->error);
	$results = $db->retrieveAndStoreResultsFromMultiQuery($statements);
	
	$html = <<<EOD
		<div class="main">
		<h1>Latest discussions</h1>
		<table style="width:99%" id="topics-list">
			<tr>
				<th style="width:60%;">
					Topic
				</th>
				<th>
					Posts
				</th>
				<th colspan="2">
					Most recent
				</th>
			</tr>
EOD;
	
	while($row = $results[0]->fetch_assoc()) {
		
		$html .= <<<EOD
			<tr>
				<td>
					<a href="?m=forum&amp;p=topic&amp;id={$row['topicId']}">{$row['title']}</a>
				</td>
				<td style="text-align: center;">
					{$row['postCounter']}
				</td>
				<td>
					{$row['username']}
				</td>
				<td>
					{$row['lastTopicPostDate']}
				</td>
			</tr>
EOD;
	}
	
	$html .= <<<EOD
			</table>
		</div>
		<div class="clear">
		</div>
EOD;
	require_once TP_GLOBAL_SOURCEPATH.'CHTMLPage.php';
	
	$chtml = new CHTMLPage();

	$chtml->printPage('Topic list',$html);
?>
