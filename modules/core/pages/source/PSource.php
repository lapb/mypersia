<?php
// ===========================================================================================
//
// Origin: http://github.com/mosbth/Utility
//
// Filename: source.php
//
// Description: Shows a directory listning and view content of files.
//
// Author: Mikael Roos, mos@bth.se
//
// Change history:
// 
// 2011-01-26: 
// Added $sourceBasedir which makes it possible to set which basedir to use. This makes it
// possible to store source.php in another place. It does not need to be in the same directory 
// it displays. Use it like this (before including source.php):
// $sourceBasedir=dirname(__FILE__);
//
// 2011-01-20: 
// Can be included and integrated in an existing website where you already have a header 
// and footer. Do like this in another file:
// $sourceNoEcho=true;
// include("source.php");
// echo "<html><head><style type='text/css'>$sourceStyle</style></header>";
// echo "<body>$sourceBody</body></html>";
//
// 2010-09-14: 
// Thanks to Rocky. Corrected NOTICE when files had no extension.
//
// 2010-09-09: 
// Changed error_reporting to from E_ALL to -1.
// Display images of certain types, configurable option $IMAGES.
// Enabled display option of SVG-graphics.
//
// 2010-09-07: 
// Added replacement of \t with spaces as configurable option ($SPACES).
// Removed .htaccess-files. Do not show them.
//
// 2010-04-27: 
// Hide password even in config.php~.
// Added rownumbers and enabled linking to specific row-number.
//

// -------------------------------------------------------------------------------------------
//
if(!defined('__INDEX__'))
	die('Direct access not allowed.');
		
$sourceBody="";  // resulting html

// Separator between directories and files, change between Unix/Windows
$SEPARATOR = DIRECTORY_SEPARATOR;   // Using built-in PHP-constant for separator. //$SEPARATOR = '/';   // Unix, Linux, MacOS, Solaris
//$SEPARATOR = '\\';   // Windows 

// Show the content of files named config.php, except the rows containing DB_USER, DB_PASSWORD
$HIDE_DB_USER_PASSWORD = TRUE; // TRUE or FALSE

// Which directory to use as basedir for file listning, end with separator.
// Default is current directory
$BASEDIR = '.' . $SEPARATOR;
if(isset($sourceBasedir)) {
  $BASEDIR = $sourceBasedir . $SEPARATOR;
}

// Show syntax of the code, currently only supporting PHP or DEFAULT.
// PHP uses PHP built-in function highlight_string.
// DEFAULT performs <pre> and htmlspecialchars.
// HTML to be done.
// CSS to be done.
$SYNTAX = 'PHP';   // DEFAULT or PHP
$SPACES = '  ';

// The link to this page. You may want to change it from relative link to absolute link.
$HREF = '?p=source&amp;';


// -------------------------------------------------------------------------------------------
//
// Page specific code
//
$html = <<<EOD
<header>
<h1>Show sourcecode</h1>
<p>
The following files exists in this folder. Click to view.
</p>
</header>
EOD;


// -------------------------------------------------------------------------------------------
//
// Verify the input variable _GET, no tampering with it
//
$currentdir  = isset($_GET['dir']) ? $_GET['dir'] : '';

$fullpath1   = realpath($BASEDIR);
$fullpath2   = realpath($BASEDIR . $currentdir);
$start    = basename($fullpath1);

$len = strlen($fullpath1);
if(  strncmp($fullpath1, $fullpath2, $len) !== 0 ||
  strcmp($currentdir, substr($fullpath2, $len+1)) !== 0 ) {
  die('Tampering with directory?');
}
$fullpath = $fullpath2;
$currpath = substr($fullpath2, $len+1);

$dirs = explode($SEPARATOR,$currpath);
$dirlinks = array();
$prevdirs = '';

foreach($dirs as $dir) {
	$dirlinks[] = "<a href='{$HREF}dir={$prevdirs}{$dir}'>{$dir}</a>";
	
	$prevdirs .= $dir.$SEPARATOR;
}

$currpathlinks = implode($SEPARATOR,$dirlinks);


// -------------------------------------------------------------------------------------------
//
// Show the name of the current directory
//
$html .= <<<EOD
<p>
<a href='{$HREF}dir='>{$start}</a>{$SEPARATOR}{$currpathlinks}
</p>

EOD;


// -------------------------------------------------------------------------------------------
//
// Open and read a directory, show its content
//
$dir   = $fullpath;
$curdir1 = empty($currpath) ? "" : "{$currpath}{$SEPARATOR}";
$curdir2 = empty($currpath) ? "" : "{$currpath}";

$list = Array();
if(is_dir($dir)) {
    if ($dh = opendir($dir)) {
        while (($file = readdir($dh)) !== false) {
          if($file != '.' && $file != '..' && $file != '.svn' && $file != '.git' && $file != '.htaccess') {
            $curfile = $fullpath . $SEPARATOR . $file;
            if(is_dir($curfile)) {
                  $list[$file] = "<a href='{$HREF}dir={$curdir1}{$file}'>{$file}{$SEPARATOR}</a>";
                } else if(is_file($curfile)) {
                  $list[$file] = "<a href='{$HREF}dir={$curdir2}&amp;file={$file}'>{$file}</a>";
                }
             }
        }
        closedir($dh);
    }
}

ksort($list);

$html .= '<p>';
foreach($list as $val => $key) {
  $html .= "{$key}<br />\n";
}
$html .= '</p>';


// -------------------------------------------------------------------------------------------
//
// Show the content of a file, if a file is set
//
$dir   = $fullpath;
$file  = "";

if(isset($_GET['file'])) {
  $file = basename($_GET['file']);

  // Get the content of the file
  $content = file_get_contents($dir . $SEPARATOR . $file);

  // Remove password and user from config.php, if enabled
  if($HIDE_DB_USER_PASSWORD == TRUE && 
     ($file == 'config.php' || $file == 'config.php~' || $file == 'global.config.php')) {

    $pattern[0]   = '/(DB_PASSWORD|DB_USER)(.+)/';
    $replace[0]   = '/* <em>\1,  is removed and hidden for security reasons </em> */ );';
    $pattern[1]	  = '/(reCAPTCHA_PRIVATE_KEY|reCAPTCHA_PUBLIC_KEY)(.+)/';
    $replace[1]   = '/* <em>\1,  is removed and hidden for security reasons </em> */ );';
    
    $content = preg_replace($pattern, $replace, $content);
  }

  //
  // Show syntax if defined
  //
  if($SYNTAX == 'PHP') {
    $content = highlight_string($content, TRUE);
  } else {
    $content = htmlspecialchars($content);
    $content = "<pre>{$content}</pre>";
  }
    $html .= <<< EOD
<h3>{$file}</h3>
<div class='sourcecode'>
	<pre>
{$content}
	</pre>
</div>

EOD;
} 



// -------------------------------------------------------------------------------------------
//
// Create and print out the html-page
//
$pageTitle = "Show sourcecode";
$sourceBody=$html;

require_once TP_GLOBAL_SOURCEPATH.'CHTMLPage.php';
$chtml = new CHTMLPage();
$chtml->printPage($pageTitle,$sourceBody);
