<?php
	class CCaptcha {
		private $errorMessage;
		
		public function __construct() {
			$this->errorMessage = "";
		}
		
		public function __destruct() {
		}
		
		public function getHTML() {
			$this->errorMessage = "";
			$publicKey = reCAPTCHA_PUBLIC_KEY;
			
			require_once TP_GLOBAL_SOURCEPATH.'/recaptcha-php/recaptchalib.php';
			
			return recaptcha_get_html($publicKey); 
		}
		
		public function checkAnswer() {
			$this->errorMessage = "";
			$privateKey = reCAPTCHA_PRIVATE_KEY;
			
			require_once TP_GLOBAL_SOURCEPATH.'/recaptcha-php/recaptchalib.php';
		
			$response = recaptcha_check_answer($privateKey, 
				$_SERVER['REMOTE_ADDR'], $_POST['recaptcha_challenge_field'],
					$_POST['recaptcha_response_field']);
			
			if(!$response->is_valid) {
				$this->errorMessage = "The reCAPTCHA was entered incorrectly. Please try again.";
				
				return false;
			}
			
			return true;
		}
		
		public function getErrorMessage() {
			return $this->errorMessage;
		}
	}
?>