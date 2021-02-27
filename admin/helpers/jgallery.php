<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_jgallery
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
JLoader::import('components.com_jgallery.helpers.jthumbs', JPATH_ADMINISTRATOR);
use Joomla\CMS\Date\Date;
/**
 * JGallery component helper.
 *
 * @param   string  $submenu  The name of the active view.
 *
 * @return  void
 *
 * @since   1.6
 */
abstract class JGalleryHelper
{
	
	static function json_answer($data) {
		$Jsession = JFactory::getSession();
		if ($Jsession != NULL)
		{
			ob_start();
			echo json_encode($data);
			$result = ob_get_contents();
			ob_end_clean();
		}
		ob_end_clean();
		header('Content-Type: application/json');
		header('Cache-Control: max-age=120, private, must-revalidate');
		header('Content-Disposition: attachment; filename="jgallery.json"');
		ob_end_clean();
		echo $result;
		JFactory::getApplication()->close();
	}
	/**
	 * Configure the Linkbar.
	 */
	public static function addSubmenu($submenu) 
	{
		// set some global property
		$document = JFactory::getDocument();
		$document->addStyleDeclaration('.icon-48-jgallery ' .
		                               '{background-image: url(../media/com_jgallery/images/tux-48x48.png);}');
		if ($submenu == 'categories') 
		{
			$document->setTitle(JText::_('COM_HELLOWORLD_ADMINISTRATION_CATEGORIES'));
		}
	}

	/**
	 * Get the actions
	 */
	public static function getActions($messageId = 0)
	{	
		$result	= new JObject;

		if (empty($messageId)) {
			$assetName = 'com_jgallery';
		}
		else {
			$assetName = 'com_jgallery.message.'.(int) $messageId;
		}

		//$actions = JAccess::getActions('com_jgallery', 'component');
		$actions = JAccess::getActionsFromFile(JPATH_COMPONENT_ADMINISTRATOR . '/access.xml');

		foreach ($actions as $action) {
			$result->set($action->name, JFactory::getUser()->authorise($action->name, $assetName));
		}

		return $result;
	}
	
	public static function join_paths(...$paths) {
		return preg_replace('~[/\\\\]+~', DIRECTORY_SEPARATOR, implode(DIRECTORY_SEPARATOR, $paths));
	}
	
	public static function guessDate($filename, &$date) {
		if(preg_match('/IMG[-_](\d{4})(\d{2})(\d{2})-(\d{2})(\d{2})(\d{2}).*/', $filename, $re))
		{
			$date = strtotime($re[1] . "-" . $re[2] . "-" . $re[3] . " " . $re[4] . ":" . $re[5] . ":" . $re[6] . " UCT");
		}
	}
	
	
	public static function getRoute($parentdir, $directory, $parent) {
		return JURI::root(true) . "/index.php?option=com_jgallery&view=jgallery&directory64=". base64_encode("${parentdir}/${directory}") ."&parent=" .$parent . "&Itemid=0";
	}
	
	public static function getFiles($rootdir, $directory,  $icon=true) {
		$listfiles = array();
		foreach (array("jpg", "JPG") as $ext) {
			$dir = JGalleryHelper::join_paths(JPATH_SITE, $rootdir,  $directory);
			foreach (glob($dir . "/*.$ext") as $filename) {
				$file = array();	
				array_push($file, basename($filename));
				array_push($file, JThumbsHelper::getthumbURL($rootdir, $directory, 'small', $filename));
				if ($icon) {
					array_push($listfiles, $file);
				} else {
					array_push($listfiles, $file[0]);
				}
			}
		}
		return $listfiles;
	}

	public static function getDirectories($rootdir, $directory,  $parent=0) {
		$listdirs = array();
		$dir = JGalleryHelper::join_paths(JPATH_SITE, $rootdir,  $directory);
		if ($parent > 0 ) {
			array_push($listdirs, "..");
		}
		foreach (glob(self::join_paths($dir , "*")) as $dirname) {
			if (is_dir($dirname) && basename($dirname) != "thumbs" && basename($dirname) != "jw_sig") {	
				array_push($listdirs, basename($dirname));
			}
		}
		return $listdirs;
	}		
	/**
	* Function to insert JGallery introduction
	*
	* Method is called by the onContentPrepare or onPrepareContent
	*
	* @param string The text string to find and replace
	*/       
	public static function display( $_params)
	{
		$content = "";
		$startdate = $enddate = -1;
		if (is_array( $_params )== false)
		{
			return  "errorf:" . print_r($_params, true);
		}
		if (! array_key_exists('dir', $_params))
		{
			return  "errorf: missing dir param" . print_r($_params, true);
		}
		if ( array_key_exists('start', $_params))
		{
			$start = new Date($_params['start']);
			$startdate = $start->toUnix();
		}
		if ( array_key_exists('end', $_params))
		{
			$end = new Date($_params['end']);
			$enddate = $end->toUnix();
		}
		if ( array_key_exists('rootdir', $_params))
		{
			$rootdir = $_params['rootdir'];		
		} else {
			$rootdir = ".";
		}
		if ( array_key_exists('parent', $_params))
		{
			$parent = $_params['parent'];
		} else {
			$parent = 0;
		}
		$directory = $_params['dir'];
		$document = JFactory::getDocument();
		$document->addScript('https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.js');
		$document->addStyleSheet('https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.css');
		$document->addStyleSheet(JURI::root(true) . '/plugins/content/jgallery/jgallery.css');
		$scriptDeclarations = array("
			(function($) {
				$(document).ready(function() {
					$.fancybox.defaults.buttons = [
						'slideShow',
						'fullScreen',
						'thumbs',
						'share',
						//'download',
						'zoom',
						'close'
			];})})(jQuery);");
		foreach ($scriptDeclarations as $scriptDeclaration) {
			 $document->addScriptDeclaration($scriptDeclaration);
		}
		//sub directories
		$listdirs = self::getDirectories($rootdir, $directory, $parent);
		$content .= "<h2>$directory</h2>";
		$i = 0;
		foreach ($listdirs  as $dirname) {
			$urlshortfilename = "/media/com_phocagallery/images/icon-folder-medium.png";
			$urlfilename = self::getRoute($directory, $dirname, $parent);
			$content .= "<a   href=\"$urlfilename\">
						<img src=\"$urlshortfilename\">
						<input style=\"border: 0; text-overflow:ellipsis;\" size=\"10\" type=\"text\"  name=\"$dirname\" value=\"$dirname\" readonly>
						</a>";
			if ($i == 0) {
				$parent = $parent + 1;
			}
		}
		$listfiles = self::getFiles($rootdir, $directory, false);
		foreach ($listfiles  as $filename) {
			$moddate = filemtime($filename);
			$exif = exif_read_data($filename);
			if (($exif !== false)&& array_key_exists('DateTimeOriginal', $exif)) {
				$moddate = strtotime($exif['DateTimeOriginal'] . " UCT");
			} else {
				JGalleryHelper::guessDate(basename($filename), $moddate);
			}
			if (($startdate == -1) || (($moddate - $startdate) >= 0))	{
				if (($enddate == -1 ) ||  (($enddate - $moddate) >= 0)) {
					$urlfilename = JThumbsHelper::getthumbURL($rootdir, $directory,"large", $filename );
					$urlshortfilename = JThumbsHelper::getthumbURL($rootdir, $directory,"small", $filename );
					$content .= "<a data-fancybox=\"gallery\"  href=\"$urlfilename\">
									<img src=\"$urlshortfilename\">
								</a>";
							
				}
			} else {
				//$content .= $filename . ":" . date("Ymd", $moddate) . "<br/>"; 
			}
		}
		return $content;
	}
	
	static function gallery($id, &$content){
		$content .= '<div  id="jgallery'. $id .'"></div>';

	}
	

}