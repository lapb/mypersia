<?php
	if(!defined('__INDEX__'))
		die('Direct access not allowed.');
		
	require_once TP_GLOBAL_SOURCEPATH.'CDatabaseController.php';
	require_once TP_GLOBAL_SOURCEPATH.'CSecurityController.php';
	
	$db = CDatabaseController::getInstance();
	$security = new CSecurityController();
	
	if(isset($_GET['tid']) && (int)$_GET['tid'] == $_GET['tid'] && $_GET['tid'] > 0) {
		$topicId = $db->escapeString($_GET['tid']);
	} else {
		$topicId = 0;
	}
	
	if(isset($_GET['id']) && (int)$_GET['id'] == $_GET['id'] && $_GET['id'] > 0) {
		$postId = $db->escapeString($_GET['id']);
	} else {
		$postId = 0;
	}
	
	$topicPart = ($topicId > 0 ? '&amp;tid='.$topicId : '');
	$postPart = ($postId > 0 ? '&amp;id='.$postId : '');
	
	if(!$security->isUserLoggedIn()) {
		$_SESSION['redirect'] = 'edit-post&amp;m=forum'.$topicPart.$postPart;
		header('Location: ?p=login');
		exit;
	}
	
	$tableArticle = DB_PREFIX.'Article';
	$tableTopicPost = DB_PREFIX.'TopicPost';
	$tableTopic = DB_PREFIX.'Topic';
	$fCheckUserIsOwnerOrAdmin = DB_PREFIX.'FCheckUserIsOwnerOrAdmin';
	$spGetPostDetails = DB_PREFIX.'PGetPostDetails';
	$isPublished = 0;

	if($topicId != 0 && $postId != 0) {
		$userId = $db->escapeString($_SESSION['idUser']);
		
			$query = "CALL {$spGetPostDetails}({$postId}, {$userId});";
			
			$db->multiQuery($query) or die($db->error);
			$results = $db->retrieveAndStoreResultsFromMultiQuery($statements);
			$row = $results[0]->fetch_assoc();
			
			if($results[0]->num_rows < 1) {
				$_SESSION['errorMessage'] = 'Invalid post id given.';
				header('Location: ?m=forum&p=topics');
				exit;
			}
			
			$title = '';
			$content = (empty($row['draftContent']) ? $row['content'] : $row['draftContent']);
			$isPublished = ($row['isPublished'] ? '1' : '0');
			
			if($row['firstPostId'] == $postId) {
				$titleValue = (empty($row['draftTitle']) ? $row['title'] : $row['draftTitle']);
				$title = <<<EOD
					<tr>
						<td>
							Title:<br />
							<input class="create-a" type="text" name="title" value="{$titleValue}" />
						</td>
					</tr>
EOD;
			}
		
		} else {
			if($topicId == 0) {
				$title = <<<EOD
					<tr>
						<td>
							Title:<br />
							<input class="create-a" type="text" name="title" value="" />
						</td>
					</tr>
EOD;
			} else {
				$title = '';
			}
			
			$content = '';
		}
		
		$html = <<<EOD
			<div>
				<script type="text/javascript">
					$(document).ready(
						function() {
							jQuery.jGrowl("Hello world. This is Growl. Page was now loaded, or re-loaded, I'm not sure on which...");
							
							/*
								Remove css styling from the text area so that we don't mess up the 
								editors styling.
							*/
							$('#content').removeClass('create-a').markItUp(mySettings);
							
							/*
								Setup autosave.
							*/
							autosave = $.autosave('#edit','#save', '#draft', 5000);
							
							/*
								Setup url hint showing that we are doing an ajax call.
							*/
							$('#edit').attr('action',$('#edit').attr('action')+'&ajax=1');
							
							// Upgrade form to make Ajax submit
							$('#edit').ajaxForm({ 
								dataType: 'json',
								
								beforeSubmit: function(data, status) {									
									autosave.beforeSave();
									$.jGrowl('Saving...');
								},
								
								success: function(data, status) {
									$.jGrowl('Saved: ' + status + ' at ' + data.timestamp);
									$.jGrowl('Topic: ' + data.topicId + ', post:' + data.postId);
									
									if(data.published == 1) {
										$('#topic_id').val(data.topicId);
										$('#post_id').val(data.postId);
										$('#is_published').val(1);
									} else {
										if(typeof data.error != 'undefined') {
											$.jGrowl('Error: ' + data.error);
										}
									}
								}
							});

							/*
								Setup handler for form button/url clicks.
							*/
							$('fieldset#editor').click(function(event){								
								if($(event.target).is('#publish')) {
									$('#draft').val(0);
									//$('#publish').click();
								} else if($(event.target).is('#save')) {
									$('#redirect').attr('name','redirect');
									$('#draft').val(1);
									//$('#save').click();
								} else if($(event.target).is('#cancel')) {
									history.back();
								} else if($(event.target).is('#post_link')) {
									if($('#is_published').val() == 1) {
										$('#post_link').attr('href','?m=forum&p=topic&id=' + $('#topic_id').val() + '#post-' + $('#post_id').val());
									} else {
										$.jGrowl('The post does not yet exist. Press "Publish" or "Save" to create it.');
										event.preventDefault();
									}
								}
								
								//event.preventDefault();
							});
						}
					);
				</script>
				
				<fieldset id='editor'>
					<form id="edit" method="post" action="?m=forum&amp;p=edit-postp">
						<table>
							<tr>
								<td>
									<input type="hidden" name="draft" id="draft" value="0"/>
								</td>
							</tr>
							<tr>
								<td>
									<input type="hidden" name="isPublished" id="is_published" value="{$isPublished}"/>
								</td>
							</tr>
							<tr>
								<td>
									<input type="hidden" name="redirect_" value="?m=forum&amp;p=edit-post{$topicPart}{$postPart}" id="redirect"/>
								</td>
							</tr>
							<tr>
								<td>
									<input type="hidden" id="topic_id" name="topicid" value="{$topicId}"/>
								</td>
							</tr>
							<tr>
								<td>
									<input type="hidden" id="post_id" name="postid" value="{$postId}"/>
								</td>
							</tr>
							{$title}
							<tr>
								<td>
									Content:<br />
									<textarea class="create-a" id="content" name="content" >{$content}</textarea>
								</td>
							</tr>
							<tr>
								<td>
									<input type="submit" id="publish" name="publish" value="Publish"/>
									<input type="submit" id="save" name="save" value="Save"/>
									<input type="button" id="cancel" name="cancel" value="Cancel"/>
								</td>
							</tr>
							<tr>
								<td>
									<a id="post_link" href="?m=forum&amp;p=topic&amp;id={$topicId}#post-{$postId}">View post</a>
								</td>
							</tr>
						</table>
					</form>
				</fieldset>
			</div>
EOD;

		$extraHeaderElements = array(
			'<script type="text/javascript" src="'.TP_MODULEPATH.'js/jquery-1.7.js"></script>',
			'<script type="text/javascript" src="'.TP_MODULEPATH.'js/markitup/jquery.markitup.js"></script>',
			'<script type="text/javascript" src="'.TP_MODULEPATH.'js/markitup/sets/html/set.js"></script>',
			'<script type="text/javascript" src="'.TP_MODULEPATH.'js/jGrowl-1.2.4/jquery.jgrowl_minimized.js"></script>',
			'<script type="text/javascript" src="'.TP_MODULEPATH.'js/jquery.form/jquery.form.js"></script>',
			'<script type="text/javascript" src="'.TP_MODULEPATH.'js/jquery.autosave/jquery.autosave.js"></script>',
			'<link rel="stylesheet" type="text/css" href="'.TP_MODULEPATH.'css/markitup/skins/markitup/style.css" />',
			'<link rel="stylesheet" type="text/css" href="'.TP_MODULEPATH.'css/markitup/sets/html/style.css" />',
			'<link rel="stylesheet" type="text/css" href="'.TP_MODULEPATH.'css/jGrowl/jquery.jgrowl.css" />'
		);

		require_once TP_GLOBAL_SOURCEPATH.'CHTMLPage.php';
	
		$chtml = new CHtmlPage();
		
		$chtml->printPage('Edit post',$html,$extraHeaderElements);
?>
