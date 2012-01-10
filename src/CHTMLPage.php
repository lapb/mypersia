<?php	
	class CHTMLPage {
		
		protected $menu;
		
		protected $css;
		
		public function __construct($css = WS_STYLESHEET) {
			$this->css = $css;
			$this->menu = unserialize(WS_MENU);
		}
		
		public function printPage($title, $htmlContent, $headerElements = array()) {
			$html = $this->getHTMLHeader($title, $headerElements);
			$html .= $this->getPageHeader();
			$html .= $this->getPageBody($htmlContent);
			$html .= $this->getPageFooter();
			
			print $html;
		}
		
		public function getHTMLHeader($aTitle, $headerElements) {
			$extraElements = '';
			
			foreach($headerElements as $element) {
				$extraElements .= $element."\n";
			}
			
			$html = <<<EOD
				<!doctype html>
				<html lang="sv">
					<head>
						<meta charset="utf-8">
						<title>{$aTitle}</title>
						<link 
							rel='stylesheet' 
							href='{$this->css}' 
							type='text/css' 
							media='screen'
						/>
						<!--[if lt IE 9]>
							<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
						<![endif]-->
						{$extraElements}
					</head>
EOD;

			return $html;
		}
		
		public function getPageHeader($aTitle = WS_TITLE) {
			
			$htmlLoginMenu = $this->getLoginLogoutMenu();
			
			$html = <<<EOD
				<body>
					{$htmlLoginMenu}
					<header id="main-header">
						<h1>{$aTitle}</h1>
EOD;

			$navigation = '';
			
			foreach($this->menu as $entry) {
				if($navigation != '')
					$navigation .= ' | ';
				$navigation .= '<a href="'.$entry['link'].'">'.$entry['title'].'</a>';
			}
			
			$html .= <<<EOD
				<nav>
					{$navigation}
				</nav>
EOD;
			$html .= '</header>';
		
			return $html;
		}
		
		public function getPageBody($aBody) {
		
			$htmlErrorMessage = $this->getErrorMessage();
		
			$html = <<<EOD
				<div class="content">
					{$htmlErrorMessage}
					<div class="content-main">
						{$aBody}
					</div>
EOD;

			return $html;
		}
		
		public function getPageFooter($aFooter = WS_FOOTER) {
			$html = <<<EOD
						<footer id="body-footer">
							{$aFooter}
						</footer>
					</div>
				</body>
			</html>
EOD;

			return $html;
		}
		
		public function getLoginLogoutMenu() {
			$htmlMenu = '';
			
			if(isset($_SESSION['idUser']) && is_numeric($_SESSION['idUser']) && $_SESSION['idUser'] > 0 && isset($_SESSION['accountUser']) && $_SESSION['accountUser'] != '') {
				
				$admin = '';
				
				if(isset($_SESSION['groupMemberUser']) && $_SESSION['groupMemberUser'] == 'adm') {
					$admin = '<a href="?p=admin" style="text-decoration: underline;">Admin</a>';
				}
				
				$htmlMenu = <<<EOD
					<a href="?p=account-settings" style="text-decoration: underline;;">{$_SESSION['accountUser']}</a>&nbsp;{$admin}&nbsp;<a href="?p=logoutp" style="text-decoration: underline;">Logout</a>
EOD;
			} else {
				$htmlMenu = <<<EOD
					<a href="?p=login" style="text-decoration: underline;">Login</a>
EOD;
			}

			$html = <<<EOD
				<div class="login">
					{$htmlMenu}
				</div>
EOD;

			return $html;
		}
		
		public function getErrorMessage() {
			$html = '';
			
			if(isset($_SESSION['errorMessage']) && $_SESSION['errorMessage'] != '') {
				$html = <<<EOD
					<div class="errorMessage">
						{$_SESSION['errorMessage']}
					</div>
EOD;
					$_SESSION['errorMessage'] = '';
					unset($_SESSION['erorMessage']);
			}
			
			return $html;
		}
		
		public function __desctruct() {
		
		}
	}
?>
