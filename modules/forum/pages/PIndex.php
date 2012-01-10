<?php
	$html = <<<EOD
		<div>
			<div style="background: url('modules/forum/images/800px-Forum_Romanum_Rom.jpg') no-repeat center ; height: 100px;"></div>
			<p class="small">
				Picture taken from: http://en.wikipedia.org/wiki/File:Forum_Romanum_Rom.jpg
			<p>
			
			<h1>Forum Romanum</h1>
			<h2>A MyPersia Forum</h2>
			<p>
			According to Wikipedia, a forum is:
			</p>
			<p class="indentation">
			"The forum served as a city square and central hub where the people of Rome gathered for justice, and faith. The forum was also the economic hub of the city and considered to be the center of the Republic and Empire."	
			</p>
			Forum Romanum is the name of a MyPersia Forum. A forum built upon MyPersia.
		</div>
EOD;

	require_once TP_GLOBAL_SOURCEPATH.'CHTMLPage.php';
	
	$chtml = new CHTMLPage();

	$chtml->printPage('Index',$html);
?>
