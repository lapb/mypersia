<?php
	class CSecurityController {
		public function isUserLoggedIn() {
			return !(!isset($_SESSION['accountUser']) 
				|| !isset($_SESSION['idUser']) 
					|| (int)$_SESSION['idUser'] != $_SESSION['idUser'] 
						|| $_SESSION['idUser'] < 1 
							|| !isset($_SESSION['groupMemberUser']));
		}
		
		public function stripTags($text) {
			$tagsAllowed = '<h1><h2><h3><h4><h5><h6><p><a><br><i><em><li><ol><ul><del><img>';
			return strip_tags($text, $tagsAllowed);
		}
	}
?>
