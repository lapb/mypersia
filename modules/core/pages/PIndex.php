<?php
	$html = <<<EOD
		<div>
			<h1>Welcome</h1>
			This is the index page, which is located at modules/core/pages/PIndex.php. To get started, please
			edit this file.
		</div>
EOD;

	require_once TP_GLOBAL_SOURCEPATH.'CHTMLPage.php';
	
	$chtml = new CHTMLPage();

	$chtml->printPage('Index',$html);
?>
