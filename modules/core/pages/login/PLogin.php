<?php
	if(!defined('__INDEX__'))
		die('Direct access not allowed.');
	
	if(isset($_SESSION['redirect']) && $_SESSION['redirect'] != '') {
		$redirect = $_SESSION['redirect'];
		$_SESSION['redirect'] = '';
	} else {
		$redirect = 'home';
	}
		
	$html = <<<EOD
		<h2>Login</h2>
		<p>
			Login using your username and password. <em>(admin - admin (admin), doe - doe (user))</em>
		</p>
		<fieldset>
			<legend>Logga in</legend>
			<form method="post" action="?p=loginp">
				<input type="hidden" name="redirect" value="{$redirect}"/>
				<table>
					<tr>
						<td>
							<label for="nameUser">User:</label>
						</td>
						<td>
							<input id="nameUser" name="nameUser" type="text"/>
						</td>
					</tr>
					<tr>
						<td>
							<label for="passwordUser">Password:</label>
						</td>
						<td>
							<input id="passwordUser" name="passwordUser" class="password" type="password"/>
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<input type="submit" name="submit" value="Login"/>
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<a href="?m=core&amp;p=create-account">Create an account</a>
						</td>
					</tr>
				</table>
			</form>
		</fieldset>
EOD;

	require_once TP_GLOBAL_SOURCEPATH.'CHTMLPage.php';
	
	$chtml = new CHTMLPage();
	
	$chtml->printPage('Login',$html);
?>