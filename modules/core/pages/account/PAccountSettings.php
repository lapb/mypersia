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
	
	$spGetAccountDetails = DB_PREFIX.'PGetAccountDetails';
	
	$userId = $db->escapeString($_SESSION['idUser']);
	
	$query = "CALL {$spGetAccountDetails}({$userId});";
	
	$db->multiQuery($query) or die("Could not fetch required information.".$db->error);
	
	$results = $db->retrieveAndStoreResultsFromMultiQuery($statements);
	$row = $results[0]->fetch_assoc();
	
	
	$html = <<<EOD
		<div class="account-settings">
			<h1>Manage account</h1>
			<h2>Account</h2>
			<fieldset>
				<table style="width:99%;">
					<tr>
						<td>
							<label for="id">Id: </label>
						</td>
						<td style="text-align: right;">
							<input type="text" name="id" id="id" readonly value="{$row['userId']}" />
						</td>
					</tr>
					<tr>
						<td>
							<label for="username">Username: </label>
						</td>
						<td style="text-align: right;">
							<input type="text" id="username" readonly value="{$row['username']}" name="username" />
						</td>
					</tr>
					<tr>
						<td>
							<label for="groupId">Group id: </label>
						</td>
						<td style="text-align: right;">
							<input type="text" name="groupId" id="groupId" readonly value="{$row['groupId']}"/>
						</td>
					</tr>
					<tr>
						<td>
							<label for="groupDescription">Group description: </label>
						</td>
						<td style="text-align: right;">
							<input type="text" name="groupDescription" id="groupDescription" value="{$row['groupDescription']}" readonly/>
						</td>
					</tr>
				</table>
			</fieldset>
			<h2>Password</h2>
			<form method="post" action="?m=core&amp;p=account-settingsp">
				<fieldset>
					<table style="width:99%;">
						<tr>
							<td>
								<label for="password">Password: </label>
							</td>
							<td style="text-align: right;">	
								<input type="password" name="password" id="password" value="" />
							</td>
						</tr>
						<tr>
							<td>
								<label for="rpassword">Repeat password: </label>
							</td>
							<td style="text-align: right;">	
								<input type="password" name="rpassword" id="rpassword" value="" />
							</td>
						</tr>
						<tr>
							<td colspan="2" style="text-align: right;">
								<input type="submit" name="submit" value="Change password" />
							</td>
						</tr>
					</table>
				</fieldset>
			</form>		
			<h2>Email</h2>
			<form method="post" action="?m=core&amp;p=account-settingsp">
				<fieldset>
					<table style="width:99%;">
						<tr>
							<td>
								<label for="email">Email: </label>
							</td>
							<td style="text-align: right;">	
								<input type="text" name="email" id="email" value="{$row['userEmail']}" />
							</td>
						</tr>
						<tr>
							<td colspan="2" style="text-align: right;">
								<input type="submit" name="submit" value="Change email" />
							</td>
						</tr>
					</table>
				</fieldset>
			</form>
			<h2>Gravatar</h2>
			<form method="post" action="?m=core&amp;p=account-settingsp">
				<fieldset>
					<table style="width:99%;">
						<tr>
							<td colspan="2">
								<p>
									Use your Gravatar from <a href="gravatar.com">gravatar.com</a>.
								</p>
							</td>
						</tr>
						<tr>
							<td>
								<label for="gravatarEmail">Gravatar id (email): </label>
							</td>
							<td style="text-align: right;">	
								<input type="text" name="gravatarEmail" id="gravatarEmail" value="{$row['gravatarEmail']}" />
							</td>
						</tr>
						<tr>
							<td>
								<img src="{$row['gravatar']}" alt="Gravatar" />
							</td>
							<td style="text-align: right;">
								<input type="submit" name="submit" value="Update gravatar" />
							</td>
						</tr>
					</table>
				</fieldset>
			</form>
		</div>
EOD;

	require_once TP_GLOBAL_SOURCEPATH.'CHTMLPage.php';
	
	$chtml = new CHTMLPage();
	
	$chtml->printPage('Account settings',$html);
?>