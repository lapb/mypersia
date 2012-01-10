<?php
	if(!defined('__INDEX__'))
		die('Direct access not allowed.');
	
	require_once TP_GLOBAL_SOURCEPATH.'CCaptcha.php';
	
	$captcha = new CCaptcha();
	
	$html = <<<EOD
		<h2>Create Account</h2>
		<form method="post" action="?m=core&amp;p=create-accountp">
			<fieldset class="create-account">
				<legend>Logga in</legend>
				<table>
					<tr>
						<td>
							<label for="username">Username:</label>
						</td>
						<td style="text-align: right;">
							<input id="username" name="username" type="text" value="" maxlength="20"/>
						</td>
					</tr>
					<tr>
						<td>
							<label for="password">Password:</label>
						</td>
						<td style="text-align: right;">
							<input id="password" name="password" value="" class="password" type="password" />
						</td>
					</tr>
					<tr>
						<td>
							<label for="passwordRepeat">Repeat Password:</label>
						</td>
						<td style="text-align: right;">
							<input id="passwordRepeat" name="passwordRepeat" value="" class="password" type="password" />
						</td>
					</tr>
					<tr>
						<td colspan='2'>
							<div style='float: right;'>
								{$captcha->getHTML()}
							</div>
						</td>
					</tr>
					<tr>
						<td colspan='2' style="text-align: right;">
							<input type="submit" name="submit" value="Create Account"/>
						</td>
					</tr>
				</table>
			</fieldset>
		</form>
EOD;

	require_once TP_GLOBAL_SOURCEPATH.'CHTMLPage.php';
	
	$chtml = new CHTMLPage();
	
	$chtml->printPage('Create account',$html);
?>