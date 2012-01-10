<?php
	/*Databasse settings*/
	define('DB_USER','<database username>');
	define('DB_PASSWORD','<database password>');
	define('DB_DATABASE','<database name>');
	define('DB_HOST','<database host>');
	define('DB_PREFIX','<database prefix>');
	
	/*Template settings*/
	define('TP_MODULESPATH',dirname(__FILE__).'/modules/');
	define('TP_GLOBAL_SOURCEPATH',dirname(__FILE__).'/src/');
	
	/*Website settings*/
	define('WS_TITLE','MyPersia');
	
	$footer = <<<EOD
		<a href="http://validator.w3.org/check/referer">html</a>
		<a href="http://jigsaw.w3.org/css-validator/check/referer?profile=css3">css</a>
		<a href="http://validator.w3.org/checklink">links</a>
		<a href="?p=source">source</a>
EOD;

	define('WS_FOOTER',$footer);
	
	/*ReCaptcha keys*/
	define('reCAPTCHA_PUBLIC_KEY','<reCAPTCHA public key>');
	define('reCAPTCHA_PRIVATE_KEY','<reCAPTCHA private key>');
?>
