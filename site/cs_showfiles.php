<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Cs_showfiles
 * @author     Ted Lowe <lists@creativespirits.org>
 * @copyright  reative Spirits (c) 2018
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

/* notes:
 * Hide first feature is not implemented yet (todo)
 * since membership is shrinking, don't need to 'limit' the public from seeing our latest newsletter as a 'member benefit' anymore
 * 
 * assumption:
 * 
 *  Files Directory = docs/newsletters
 *  needs to resolve to https://www.example.com/newsletters
 *  page header will be Newsletters
 *  
 */
defined('_JEXEC') or die;

// Include dependancies
jimport('joomla.application.component.controller');

JLoader::registerPrefix('Cs_showfiles', JPATH_COMPONENT);
JLoader::register('Cs_showfilesController', JPATH_COMPONENT . '/controller.php');

showfiles();

// Execute the task.
$controller = JControllerLegacy::getInstance('Cs_showfiles');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();

function showfiles()
{	
	$files_dir = JComponentHelper::getParams('com_cs_showfiles')->get('files_directory');
	
	$base = basename($files_dir);	// docs/newsletters will be newsletters
	
	// put out page title (todo: why not in joomla settings?)

	echo "<h2>";
	echo ucwords($base);
	echo "</h2>";

	$header_msg = JComponentHelper::getParams('com_cs_showfiles')->get('header_msg');
	if ( !empty($header_msg))
		echo "$header_msg";
	
	$num_skip = 0; // todo: NOT implemented
	
	// todo: file_types pdf only
	
	$sd = date('Y');	// current year if not specified
	
	$guri = $base; // website/$guri?sd= will be the form of links to other directories (years)
	
	if ( isset( $_GET["sd"] ) && ! empty( $_GET["sd"]) )
		$sd = $_GET["sd"];

	$ul_style = ""; // todo:
	
	////////////////////////////////////
	echo "<p><table><tr><td style='vertical-align: top; padding-right: 20px;'>";
	
	$dirs = getdirdirs( $files_dir );
	rsort($dirs);
	
	foreach( $dirs as $dir )
	{
		echo "<a href='$guri?sd=$dir'>$dir</a><br>";
	}
	
	echo "</td><td style='vertical-align: top;'>";
	echo "<ul $ul_style>";
	
	$files = getdirfiles( "$files_dir/$sd");
	
	rsort($files);
	$n = count($files);
	//echo "got $n files";
	// don't show latest newsletter unless the user is logged in
	$num_skip = 0;//( $sd == $year && $hide_last && ! isJoomlaUserLoggedIn() ) ? 1 : 0; // todo
	
	// if looking at current year and there are newsletters this year and we are supposed to skip one,
	// determine we are after the monthly meeting or not; if so, show this month's newsletter (don't skip)
	if ( $num_skip && $n > 0 )
	{
		if ( showFile( $files[0] ) )
			$num_skip = 0;
	}
	// hide_last  logged_in	num_skip
	// 0		0	0
	// 0		1	0
	// 1		0	1
	// 1		1	0

	for ( $i = $num_skip ; $i < $n ; $i++ )
	{
		if ( $i == $num_skip )
			echo "<p><b>$sd</b></p>";
			
		$f = $files[$i];
		echo "<li $li_style><a target=\"_blank\" href=\"/$files_dir/$sd/$f\">$f</a></li>\n";
	}
	//print_r( $files );
	echo "</ul></td></tr></table>";
	
	////////////////////////////////////
	$footer_msg = JComponentHelper::getParams('com_cs_showfiles')->get('footer_msg');
	if ( !empty($footer_msg))
		echo "</br><p>$footer_msg</p>";
}
function showFile( $f )
{
	return true;
}
function oshowFile( $f )
{
	// format of newsletter: ORG-newsletter-2008-05.pdf
	// the file should be skipped if it is this month's newsletter and the meeting has not occurred yet

	$fileparts = explode( "-", $f );	// todo: megakludge; should have a sprintf format parameter to split out the date from site specific newsletter naming convention
	//print_r( $fileparts );
	$fy = $fileparts[2];
	$fmparts = explode( ".", $fileparts[3] );
	$fm = $fmparts[0];

	$today = new Date();
	$day = ($today->format('%d'));
	$month = ($today->format('%m'));
	$year = ($today->format('%Y'));

	if ( $fy != $year )
		return false; // wrong year - something has gone wrong - don't show

	// example: (result should be true, ie, show file)
	// newsletter is 2008-05
	// third friday was 2008-05-16
	// today is 2008-05-19

	//echo "fm=$fm, month=$month<br>";
	if ( $fm < $month )
		return true; // it's next month or later, show newsletter
	if ( $fm > $month )
		return false; // it's too soon to show this newsletter
	// else looking at same month

	// 3 = 3rd, 5 = friday
	$date3 = Date_Calc::NWeekdayOfMonth(3,5,$month,$year,"%d");
	//echo "day=$day, date3=$date3<br>";
	// has the 3rd friday passed this month?
	if ( $day > $date3 )
		return true;

	return false;
}
function getdirfiles( $d ) //{{{1
{
	$ret = array();

	if ( ! ( $dd = @opendir( $d ) ) )
		return $ret;

	// ignore index.html and index.php files

	while ($direntry = readdir($dd))
	{
		if ( is_dir( "$d/$direntry" ) )
			continue;
		if ( $direntry == "index.html" )
			continue;

		array_push($ret, $direntry );
	}

	closedir($dd);

	return $ret;
}
function getdirdirs( $d ) //{{{1
{
	$ret = array();

	if ( ! ( $dd = @opendir( $d ) ) )
		return $ret;

	// ignore index.html and index.php files

	while ($direntry = readdir($dd))
	{
		if ( ! is_dir( "$d/$direntry" ) )
			continue;
		if ( $direntry == "." || $direntry == ".." || $direntry == "source" )
			continue;

		array_push($ret, $direntry );
	}

	closedir($dd);

	return $ret;
}