<?php
	if(!defined('__INDEX__'))
		die('Direct access not allowed.');
	
	require_once TP_GLOBAL_SOURCEPATH.'CDatabaseController.php';
	
	$db = CDatabaseController::getInstance();
	
	if(!isset($_SESSION['accountUser']) || $_SESSION['accountUser'] == '') {
		$_SESSION['errorMessage'] = 'You must be logged in to access the administration page';
		$_SESSION['redirect'] = 'admin';
		header('Location: ?p=login');
		exit;
	}
	
	if(!isset($_SESSION['groupMemberUser']) || $_SESSION['groupMemberUser'] != 'adm') {
		$_SESSION['errorMessage'] = 'You do not have access to the administration page';
		header('Location: ?p=login');
		exit;
	}
	
	if(isset($_GET['order']))
		$ascOrDesc = ($_GET['order'] == 'asc' ? 'desc' : 'asc');
	else
		$ascOrDesc = 'asc';
	
	$html = '';
	
	$orderBy = '';
		
	$sortOrder = '';
		
	$order = '';
	
	if(isset($_GET['col'])) {
		switch(strtolower($_GET['col'])) {
			case 'iduser':
				$orderBy = 'idUser';
			break;
			
			case 'accountuser':
				$orderBy = 'accountUser';
			break;
			
			case 'emailuser':
				$orderBy = 'emailUser';
			break;
			
			case 'idgroup':
				$orderBy = 'idGroup';
			break;
			
			case 'namegroup':
				$orderBy = 'nameGroup';
			break;
			
			default:
				$orderBy = '';
			break;
		}
		
		if(isset($_GET['order']) && $orderBy != '') {
			switch(strtolower($_GET['order'])) {
				case 'asc':
					$sortOrder = 'asc';
				break;
				
				case 'desc':
					$sortOrder = 'desc';
				break;
				
				default:
					$sortOrder = '';
					$orderBy = '';
				break;
			}
		}
	
		$order = " ORDER BY {$orderBy} {$sortOrder}";
	}
	
	$userTable = DB_PREFIX.'User';
	$groupTable = DB_PREFIX.'Group';
	$groupMemberTable = DB_PREFIX.'GroupMember';
	
	$query = "SELECT idUser, accountUser, emailUser, idGroup, nameGroup FROM {$userTable} AS U JOIN {$groupMemberTable} AS GM ON U.idUser = GM.GroupMember_idUser JOIN {$groupTable} AS G ON G.idGroup = GM.GroupMember_idGroup".$order;
	
	$result = $db->query($query) or die('Could not fetch required data'.$db->getError());
	
	$html = <<<EOD
		<h2>Admin: Show user accounts</h2>
		<table>
			<tr>
				<th><a href='?p=admin&amp;col=idUser&amp;order={$ascOrDesc}'>Id</a></th>
				<th><a href='?p=admin&amp;col=accountUser&amp;order={$ascOrDesc}'>Account</a></th>
				<th><a href='?p=admin&amp;col=emailUser&amp;order={$ascOrDesc}'>Email</a></th>
				<th><a href='?p=admin&amp;col=idGroup&amp;order={$ascOrDesc}'>Grupp</a></th>
				<th><a href='?p=admin&amp;col=nameGroup&amp;order={$ascOrDesc}'>Grupp description</a></th>
			</tr>
EOD;

			while($row = $result->fetch_assoc()) {
			$html .= <<<EOD
				<tr>
					<td>{$row['idUser']}</td>
					<td>{$row['accountUser']}</td>
					<td>{$row['emailUser']}</td>
					<td>{$row['idGroup']}</td>
					<td>{$row['nameGroup']}</td>
				</tr>
EOD;
			}
			
	$html .= <<<EOD
		</table>
EOD;

	$result->close();

	require_once TP_GLOBAL_SOURCEPATH.'CHTMLPage.php';
	
	$chtml = new CHTMLPage();

	$chtml->printPage('Show all user accounts',$html);
?>