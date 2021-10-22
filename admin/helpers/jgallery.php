<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_jgallery
 *
 * @copyright   Copyright (C) 2005 - 2015 JL TRYOEN All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
JLoader::import('components.com_jgallery.helpers.jthumbs', JPATH_ADMINISTRATOR);
use Joomla\CMS\Date\Date;
use Joomla\CMS\Log\Log;

class JGalleryImage
{
	public $filename;
	public $basename;
	public $moddate;
	public $urlfilename;
	public $urlshortfilename;
	public $comment;
	
	public function __construct($filename, $basename, $moddate, $urlfilename, $urlshortfilename, $comment="") {
		$this->filename = $filename;
		$this->basename = $basename;
		$this->moddate = $moddate;
		$this->urlfilename = $urlfilename;
		$this->urlshortfilename = $urlshortfilename;
		$this->comment = $comment;
	}

	
	
	static function savecsv($file, $results)
	{
		$fp = fopen($file, 'w');
		if ($fp) {
			if (count ($results)) {
				fputcsv($fp, array_keys(get_object_vars($results[0])), ";");
			}
			foreach ($results as $fields) {
				fputcsv($fp, get_object_vars($fields), ";");
			}
			fflush($fp);
			fclose($fp);
		} else
		{
			print "error writing to file" . $file;
		}
	}

	static function readcsv($file)
	{
		$results = array();
		$fp = fopen($file, 'r');
		if ($fp) {
			$header =  fgetcsv($fp, 1024, ";");
			while (!feof($fp) ) {
				$array = fgetcsv($fp, 1024, ";");
				if (is_array($array)) {
					$obj = new JImage(); 
					foreach($header as $field) {
						if (is_array($array) && count($array))
							$obj->$field = array_shift($array);
					}
					array_push($results, $obj);
				}
			}
			fclose($fp);
		}
		return $results;
	}
	

}

/**
 * JGallery component helper.
 *
 * @param   string  $submenu  The name of the active view.
 *
 * @return  void
 *
 * @since   1.6
 */
class JGalleryHelper
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
	
	public static function join_paths(...$spaths) {
		$arpath = array();
		$prefix = ($spaths[0][0] == DIRECTORY_SEPARATOR)? DIRECTORY_SEPARATOR : "";
		foreach($spaths as $spath) {
			array_push($arpath, ...array_filter(preg_split("~[/\\\\]+~" , $spath),
										function($k) {return ($k !="" && $k != ".");}));
		}
		$nbupper = 0;
		$arjoinpath = array();
		for($i = count($arpath)-1; $i >= 0;) {
			$spath = $arpath[$i];
			$prev = $i - 1;
			$found = false;
			$nbfound = 0;
			while (($spath == "..") && ($i >= 1))
			{
				$nbfound++;
				$i = $i - 1;
				$spath = $arpath[$i];
			}
			if ($nbfound == 0) {
				array_push($arjoinpath, $spath);
				$i--;
			} else {
				$i = max(0, $i - $nbfound);
			}
		}
		//JLog::add('join_paths=>:'. $prefix . implode(DIRECTORY_SEPARATOR, array_reverse($arjoinpath)), JLog::WARNING, 'jgallery');
		return $prefix . implode(DIRECTORY_SEPARATOR, array_reverse($arjoinpath));
	}
	
	public static function getrootdir() {
		return self::join_paths(JPATH_SITE, "images", JParametersHelper::get('rootdir'));
	}

	public static function guessDate($filename, &$date) {
		if(preg_match('/IMG[-_](\d{4})(\d{2})(\d{2})-(\d{2})(\d{2})(\d{2}).*/', $filename, $re))
		{
			$date = strtotime($re[1] . "-" . $re[2] . "-" . $re[3] . " " . $re[4] . ":" . $re[5] . ":" . $re[6] . " UCT");
		} elseif (preg_match('/IMG[-_](\d{4})(\d{2})(\d{2}).*/', $filename, $re)) {
			$date = strtotime($re[1] . "-" . $re[2] . "-" . $re[3]  . " UCT");
		} else {
			$date = -1;
		}
	}
	
	
	public static function getRoute($parentdir, $directory, $parent) {
		return JURI::root(true) . "/index.php?option=com_jgallery&view=jgallery&directory64=". base64_encode("${parentdir}/${directory}") ."&parent=" .$parent . "&Itemid=0";
	}
	
	public function sortFile($a, $b) {
		return cmp($a->filename, $b->filename);
	}
	public static function getFiles($rootdir, $directory,  $icon=true, $startdate=-1, $enddate=-1) {
		$jgallerydir = JGalleryHelper::join_paths(JPATH_SITE, $rootdir,  $directory);
		$jgalleryfile = JGalleryHelper::join_paths($jgallerydir, "gallery.csv");
		$exist = $modified = false;
		if (file_exists($jgalleryfile)) {
			$listfiles = JGalleryImage::readcsv($jgalleryfile);
			$exist = true;
		}
		else {
			$listfiles = array();
		}
		foreach (array("jpg", "JPG") as $ext) {			
			foreach (glob($jgallerydir . "/*.$ext") as $filename) {
				$pathinfo = pathinfo($filename);				
				$moddate = filemtime($filename);
				$exist = false;
				foreach ($listfiles as $file) {
					if ($file->filename == $pathinfo['filename']) {
						$exist = true;
						break;
					}
				}
				if ($exist) {
					continue;
				}
				$modified = true;
				$exif = exif_read_data($filename);
				if (($exif !== false)&& array_key_exists('DateTimeOriginal', $exif)) {
					$moddate = strtotime($exif['DateTimeOriginal'] . " UCT");
				} else {
					JGalleryHelper::guessDate(basename($filename), $moddate);
				}
				if (($startdate == -1) || (($moddate - $startdate) >= 0))	{
					if (($enddate == -1 ) ||  (($enddate - $moddate) >= 0)) {
						$urlfilename = JThumbsHelper::getthumbURL($rootdir, $directory,"large", $filename );
						$urlshortfilename = JThumbsHelper::getthumbURL($rootdir, $directory, "small", $filename );
						array_push($listfiles, new JGalleryImage($pathinfo['filename'],
													basename($filename),
													$moddate,
													$urlfilename,
													$urlshortfilename));
					}
				}				
			}
		}		
		if ($icon){
			usort($listfiles,sortFile);
		} else {
			usort($listfiles, cmp);
		}
		if (!$exist || $modified)
		{
			JGalleryImage::savecsv($jgalleryfile, $listfiles);
		}
		return $listfiles;
	}
	
	public static function ouputsync($directory, $listfiles, &$content, &$scriptDeclarations, &$scripts)
	{
		foreach ($listfiles  as $file) {			
			$urlfilename = $file['urlfilename'];
			$urlshortfilename = $file['urlshortfilename'];
			$content .= "<a data-fancybox=\"gallery\"  href=\"$urlfilename\"><img src=\"$urlshortfilename\"/></a>";
		}
		array_push($scriptDeclarations, '(function($) {
					$(document).ready(function() {
						setTimeout(function() {	initfancybox($);},500);
						})})(jQuery);');
	}

	public static function ouputasync($directory, $listfiles, &$content, &$scriptDeclarations, &$scripts)
	{
		$sid = "jgallery" . rand(1, 1024);
		$content .= '<div id="' . $sid . '">';
		$content .= '</div>';
		array_push($scripts, "jimages.js");
		array_push($scriptDeclarations, '(function($) {
					$(document).ready(function() {
							jimages_getimages($, "' . $sid .'", "' . JURI::root(true) .'","' . base64_encode($directory) .'",' . json_encode($listfiles) .');							
						})})(jQuery);');
		array_push($scriptDeclarations, '(function($) {
					$(document).ready(function() {
						setTimeout(function() {	initfancybox($);},500);
						})})(jQuery);');						
	}
	
	public static function ouputimg($rootdir, $directory, $file, $name, $icon,$width, &$content, &$scriptDeclarations, &$scripts)
	{
		$urlfilename = $file->urlfilename;
		if ($icon == 'small') {
			$urlshortfilename = $file->urlshortfilename;
			$width = JParametersHelper::get('thumb_' . $icon .'_width');
		} else {
			$urlshortfilename = JThumbsHelper::getthumb( JGalleryHelper::join_paths($rootdir, $directory), $icon, $file->basename);
			JParametersHelper::get('thumb_' . $icon .'_width');			
		}
		$content .= "<a data-fancybox=\"". $name ."\"  href=\"$urlfilename\"><img src=\"$urlshortfilename\"/ width=\"$width\"></a>";
		array_push($scriptDeclarations, '(function($) {
					$(document).ready(function() {
						setTimeout(function() {	initfancybox($);},500);
						})})(jQuery);');
	}
	
	public static function getDirectories($rootdir, $directory,  $parent=0) {
		$listdirs = array();
		$dir = JGalleryHelper::join_paths(JPATH_SITE, $rootdir,  $directory);
		if ($parent > 0 ) {
			array_push($listdirs, array("name" => "..", 
									"parent" => $parent -1,
									"url" => self::getRoute($directory, "..", $parent-1)));
		}
		foreach (glob(JGalleryHelper::join_paths($dir , "*")) as $dirname) {
			if (is_dir($dirname) && basename($dirname) != "thumbs" && basename($dirname) != "jw_sig") {
				array_push($listdirs,  array("name" => basename($dirname),
										"parent" => $parent,
										"url" => self::getRoute($directory, basename($dirname), $parent + 1)));
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
		if ( array_key_exists('name', $_params))
		{
			$name = $_params['name'];
		} else {
			$name = "gallery";
		}
		if ( array_key_exists('icon', $_params))
		{
			$icon = $_params['icon'];
		} else {
			$icon = "small";
		}
		if ( array_key_exists('width', $_params))
		{
			$width = $_params['width'];
		} else {
			$width = "?";
		}
		$directory = $_params['dir'];
		JHtml::_('jquery.framework');
		$document = JFactory::getDocument();
		$document->addScript('https://cdn.jsdelivr.net/npm/@fancyapps/ui/dist/fancybox.umd.js');
		$document->addStyleSheet('https://cdn.jsdelivr.net/npm/@fancyapps/ui/dist/fancybox.css');
		$document->addStyleSheet(JURI::root(true) . '/plugins/content/jgallery/jgallery.css');
		$scriptDeclarations = array();
		$scripts = array('jgallery.js');		
		if ( array_key_exists('img', $_params)) {
			$listfiles = self::getFiles($rootdir, $directory, false, $startdate, $enddate);
			$found = False;
			foreach ($listfiles as $file) {
				if ($file->basename == $_params['img']) {
					self::ouputimg($rootdir, $directory, $file, $name, $icon, $width, $content, $scriptDeclarations, $scripts);
					$found = true;
					break;
				}
			}
			if (!$found) {
				$content .= "image not found :" . $_params['img'] . " in " . $directory;
			}
		}else {
			//sub directories
			$listdirs = self::getDirectories($rootdir, $directory, $parent);
			$content .= "<h2>$directory</h2>";
			$nbcar = 0;
			$maxcar = 40;
			$i = 0;
			$maxitem = 8;
			$content .= '<table><tr>';
			foreach ($listdirs  as $dir) {
				$urlshortfilename = "/media/com_phocagallery/images/icon-folder-medium.png";
				$urlfilename =  $dir["url"];
				$dirname = $dir["name"];
				$content .= "<td><a   href=\"$urlfilename\">
							<img src=\"$urlshortfilename\"><input style=\"border: 0; text-overflow:ellipsis;\" size=\"12\" type=\"text\"  name=\"$dirname\" value=\"$dirname\" readonly>
							</a></td>";
				if ($i++ > $maxitem) {
					$content .= '</tr><tr>';
					$i = 0;
				}
			}
			$content .= "</tr></table>";
			$listfiles = self::getFiles($rootdir, $directory, false, $startdate, $enddate);

			self::ouputasync($directory, $listfiles, $content, $scriptDeclarations, $scripts);
		}
		$document = JFactory::getDocument();
		foreach ($scripts as $script) {
			 $document->addScript(JURI::root(true) . '/administrator/components/com_jgallery/helpers/' . $script);
		}

		foreach ($scriptDeclarations as $scriptDeclaration) {
			 $document->addScriptDeclaration($scriptDeclaration);
		}
		
		
		return $content;
	}

	static function gallery($id, &$content){
		$content .= '<div  id="jgallery'. $id .'" style="min-heigth:400px;height:400px"></div>';
	}


	static function savecomments($directory, $jcomments) {
		$rootdir = JParametersHelper::getrootdir();
		$jgalleryfiles = JGalleryHelper::getFiles($rootdir , $directory, true);
		$modif = false;
		foreach($jcomments as $array_key => $comment) {
			foreach($jgalleryfiles as $file)
			{
				if (($file->filename == $array_key)) {
					if ($file->comment != $comment) {
						$modif = true;
						$file->comment = $comment;
					}
					break;
				}					
			}
		}
		if ($modif)
		{
			$jgallerydir = JGalleryHelper::join_paths(JPATH_SITE, $rootdir,  $directory);
			$jgalleryfile = JGalleryHelper::join_paths($jgallerydir, "gallery.csv");
			JGalleryImage::savecsv($jgalleryfile, $jgalleryfiles);
			return "savecsv ok";
		}		
		else 
		{
			return "no modifications";
		}
	}
	
	static function deleteimage($rootdirectory, $directory, $jimage, $keep, &$errors) {
		$filename = self::join_paths(JPATH_SITE,$rootdirectory, $directory, $jimage);
		if (file_exists($filename)){
			if ($keep) {
				array_push($errors, "keep " . $filename);
			}
			else {
				unlink($filename);
				array_push($errors, "success deleting " . $filename);
			}
		}
		else {
			array_push($errors, "file does not exist " . $filename);
		}
	}
	
	static function deleteimages($directory, $jimages, $keep, &$errors) {
		$rootdir = JParametersHelper::getrootdir();
		$jgalleryfiles = JGalleryHelper::getFiles($rootdir , $directory, true);
		$modif = false;
		foreach ($jimages as $jimage)
		{
			self::deleteimage($rootdir, $directory, $jimage, $keep, $errors);
			JThumbsHelper::deletethumbs($rootdir, $directory, $jimage, $errors);
			$i = 0;
			$found = false;
			foreach($jgalleryfiles as $file)
			{
				if ($file->basename == $jimage) {
					$found = true;
					break;			
				}
				$i++;
			}
			if ($found) {
				array_splice($jgalleryfiles, $i, 1);
				$modif = true;				
			}
		}
		if ($modif) {
			$jgallerydir = JGalleryHelper::join_paths(JPATH_SITE, $rootdir,  $directory);
			$jgalleryfile = JGalleryHelper::join_paths($jgallerydir, "gallery.csv");
			JGalleryImage::savecsv($jgalleryfile, $jgalleryfiles);
			array_push($errors, "file saved");
		}		
		return $errors;
	}
}