<?php
/**
 * @package	 Joomla.Administrator
 * @subpackage  com_jgallery
 *
 * @copyright   Copyright (C) 2015 - 2025 JL TRYOEN All rights reserved.
 * @license	 GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace JLTRY\Component\JGallery\Administrator\Helper;
use JLTRY\Component\JGallery\Administrator\Helper\JDirectoryHelper;
use JLTRY\Component\JGallery\Administrator\Helper\JParametersHelper;
use JLTRY\Component\JGallery\Administrator\Helper\JThumbsHelper;
use Joomla\CMS\Date\Date;
use Joomla\CMS\Log\Log;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Image\Image;
use Joomla\CMS\Object\CMSObject;
use Joomla\CMS\Access\Access;
use Joomla\CMS\Layout\LayoutHelper;


// No direct access to this file
defined('_JEXEC') or die('Restricted access');



class JGalleryImage
{
	public static $IMG_EXTENSIONS = array("jpg", "JPG", "jpeg", "JPEG", "png");
	public static $VIDEO_EXTENSIONS = array("mp4", "m2ts", "MOV", "mov");
	public $filename;
	public $basename;
	public $moddate;
	public $urlfilename;
	public $urlshortfilename;
	public $comment;
	public $dirname;
	public $relative;
	public $video;

	public function __construct($dirname, $filename, $basename, $moddate, $comment="", $urlfilename ="", $urlshortfilename="",$relative = false) {
		$this->dirname =  str_replace(JPATH_SITE, "", $dirname);
		$this->filename = $filename;
		$this->basename = $basename;
		$this->moddate = $moddate;
		$this->comment = $comment;
		$this->relative = $relative;
		$this->urlfilename = $urlfilename;
		$this->urlshortfilename = $urlshortfilename;
		$this->video = false;
		$fileext = pathinfo($basename)['extension'];
		foreach (self::$VIDEO_EXTENSIONS as $ext) {
			if ($fileext == $ext) {
				$this->video = true;
				$this->urlfilename = $basename;
				break;
			}
		}
	}


	public static function createfrom($object, $dirname) {
		if (isset($object->relative) && $object->relative) {
			$relative = true;
		}
		else {
			$relative = false;
		}
		return new JGalleryImage($dirname, $object->filename, $object->basename, $object->moddate, $object->comment, $object->urlfilename, $object->urlshortfilename, $relative);
	}

	public static function geturlfilename($dirname, $url, $relative)
	{
		if (($relative == false) || (substr($url, 0, 1) == "/"))
		{
			return $url;
		}
		else
		{
			return str_replace("//", "/", Uri::root(true) . "/" . str_replace(DIRECTORY_SEPARATOR, "/", $dirname . "/" . $url));
		}
	}


	public function getthumbbounds($mode, &$width, &$height)
	{
		$thumbimage = JThumbsHelper::getthumb(JGalleryHelper::join_paths(JPATH_SITE, $this->dirname), $mode, $this->basename);
		$imageInfo = getimagesize($thumbimage);
		$width = $imageInfo[0]; // Largeur en pixels
		$height = $imageInfo[1]; // Hauteur en pixels
	}


	public static function toArray($object, $full = true) {
		$ar = get_object_vars($object);
		if ($full) {
			$ar["urlfilename"] = self::geturlfilename($object->dirname, $object->urlfilename, $object->relative);
			$ar["urlshortfilename"] = self::geturlfilename($object->dirname, $object->urlshortfilename, $object->relative);
			$width = $height = "auto";
			$object->getthumbbounds("large", $width, $height);
			$ar["width"] = $width;
			$ar["height"] = $height;
		}
		return $ar;
	}


	static function savecsv($file, $results, $isarray = false)
	{
		$fp = fopen($file, 'w');
		if ($fp) {
			if (count ($results)) {
				fputcsv($fp, array_keys(get_object_vars((object)$results[0])), ";");
			}
			foreach ($results as $fields) {
				fputcsv($fp, get_object_vars((object)$fields), ";");
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
					$obj = new \stdClass;
					foreach($header as $field) {
						if (is_array($array) && count($array))
							$obj->$field = array_shift($array);
					}
					array_push($results, self::createfrom($obj, dirname($file)));
				}
			}
			fclose($fp);
		}
		return $results;
	}
};



/**
 * JGallery component helper.
 *
 * @param   string  $submenu  The name of the active view.
 *
 * @return  void
 *
 */
class JGalleryHelper
{
	public static function loadLibrary ($libraries = array('jquery' => true))
	{
		$document = Factory::getDocument();
		/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
		$wa = $document->getWebAssetManager();
		$wr = $wa->getRegistry();
		$wr->addExtensionRegistryFile("com_jgallery");
		if ($document->getType() != 'html')
		{
			return;
		}
		if (isset($libraries['inline']))
		{
			call_user_func_array([$wa, 'addInlineScript'], $libraries['inline']);
		}
		if (isset($libraries['jquery']))
		{
			HTMLHelper::_('jquery.framework');
		}
		if (isset($libraries['bootstrap.tooltip']))
		{
			HTMLHelper::_('bootstrap.tooltip');
		}
		if (isset($libraries['jimages']))
		{
			$wa->useScript('com_jgallery.jimages');
			$wa->useStyle('com_jgallery.jimages');
		}
		if (isset($libraries['psw_images']))
		{
			$wa->useScript('com_jgallery.psw_images');
			$wa->useStyle('com_jgallery.psw_images');
		}
		if (isset($libraries['jgallery']))
		{
			$wa->useScript('com_jgallery.jgallery');
			$wa->useStyle('com_jgallery.jgallery');
		}
		if (isset($libraries['jselectdirs']))
		{
			$wa->useScript('com_jgallery.jselectdirs');
		}
		if (isset($libraries['jdirectories']))
		{
			$wa->useScript('com_jgallery.jdirectories');
		}
		if (isset($libraries['jthumbs']))
		{
			$wa->useScript('com_jgallery.jthumbs');
		}
		if (isset($libraries['jrecthumbs']))
		{
			$wa->useScript('com_jgallery.jrecthumbs');
		}
		if (isset($libraries['jcomments']))
		{
			$wa->useScript('com_jgallery.jcomments');
		}
		if (isset($libraries['radiobox']))
		{
			$wa->useScript('com_jgallery.radiobox');
		}
		if (isset($libraries['multicheckbox']))
		{
			$wa->useScript('com_jgallery.multicheckbox');
			$wa->useStyle('com_jgallery.multicheckbox');
		}
		if (isset($libraries['fancybox']))
		{
			$wa->useScript('fancybox');
			$wa->useStyle('fancybox');
			HTMLHelper::_('jquery.framework');
			$wa->addInlineScript('window.addEventListener("DOMContentLoaded", 
									function() {
										initfancybox(jQuery);
										});', 
										['position' => 'after'],
										[],
										['fancybox', 'com_jgallery.jimages', "jquery"]);
		}
		if (isset($libraries['lazyload']))
		{
			$wa->useScript('lazyload');
		}
		if (isset($libraries['photoswipe']))
		{
			//$wa->useScript('photoswipe');
			$wa->useStyle('photoswipe');
		}
	}

	public static function getVar(...$params) {
		if (version_compare(JVERSION, '4.0', 'ge')){
			return call_user_func_array(array(Factory::getApplication()->input, 'getVar'), $params);
		}
		else {
			return call_user_func_array('JRequest::getVar', $params);
		}
	}
	
	public static function json_answer($data)
	{
		$Jsession = Factory::getSession();
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
		Factory::getApplication()->close();
	}
	/**
	 * Configure the Linkbar.
	 */
	public static function addSubmenu($submenu) 
	{
		// set some global property
		$document = Factory::getDocument();
		$document->addStyleDeclaration('.icon-48-jgallery ' .
									   '{background-image: url(../media/com_jgallery/images/tux-48x48.png);}');
		if ($submenu == 'categories') 
		{
			$document->setTitle(Text::_('COM_HELLOWORLD_ADMINISTRATION_CATEGORIES'));
		}
	}

	/**
	 * Get the actions
	 */
	public static function getActions($messageId = 0)
	{
		$result	= new CMSObject;

		if (empty($messageId)) {
			$assetName = 'com_jgallery';
		}
		else {
			$assetName = 'com_jgallery.message.'.(int) $messageId;
		}

		//$actions = Access::getActions('com_jgallery', 'component');
		$actions = Access::getActionsFromFile(JPATH_COMPONENT_ADMINISTRATOR . '/access.xml');

		foreach ($actions as $action) {
			$result->set($action->name, Factory::getUser()->authorise($action->name, $assetName));
		}

		return $result;
	}
	
	public static function join_paths(...$spaths)
	{
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
		//Log::add('join_paths=>:'. $prefix . implode(DIRECTORY_SEPARATOR, array_reverse($arjoinpath)), Log::WARNING, 'jgallery');
		return $prefix . implode(DIRECTORY_SEPARATOR, array_reverse($arjoinpath));
	}
	
	public static function getrootdir()
	{
		return self::join_paths(JPATH_SITE, JParametersHelper::getrootdir());
	}

	public static function guessDate($filename, &$date)
	{
		if(preg_match('/IMG[-_](\d{4})(\d{2})(\d{2})-(\d{2})(\d{2})(\d{2}).*/', $filename, $re) ||
		   preg_match('/PXL[-_](\d{4})(\d{2})(\d{2})-(\d{2})(\d{2})(\d{2}).*/', $filename, $re) ||
		   preg_match('/VID[-_](\d{4})(\d{2})(\d{2})-(\d{2})(\d{2})(\d{2}).*/', $filename, $re))
		{
			$date = strtotime($re[1] . "-" . $re[2] . "-" . $re[3] . " " . $re[4] . ":" . $re[5] . ":" . $re[6] . " UCT");
		} elseif (preg_match('/IMG[-_](\d{4})(\d{2})(\d{2}).*/', $filename, $re) ||
		   preg_match('/PXL[-_](\d{4})(\d{2})(\d{2}).*/', $filename, $re) ||
		   preg_match('/VID[-_](\d{4})(\d{2})(\d{2}).*/', $filename, $re)) {
			$date = strtotime($re[1] . "-" . $re[2] . "-" . $re[3]  . " UCT");
		} else {
			$date = -1;
		}
	}


	public static function sortFile($a, $b)
	{
		return ($a['moddate']- $b['moddate']);
	}


	public static function getFiles($rootdir, $directory, $media = "ALL", $startdate=-1, $enddate=-1)
	{
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
		foreach (JGalleryImage::$IMG_EXTENSIONS as $ext) {
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
					self::guessDate(basename($filename), $moddate);
				}
				$urlfilename = JThumbsHelper::getthumbformat("large", basename($filename));
				$urlshortfilename = JThumbsHelper::getthumbformat("small", basename($filename));
				array_push($listfiles, new JGalleryImage(JGalleryHelper::join_paths($rootdir, $directory),
											$pathinfo['filename'],
											basename($filename),
											$moddate,
											"",
											$urlfilename,
											$urlshortfilename,
											true));
			}
		}
		foreach (JGalleryImage::$VIDEO_EXTENSIONS as $ext) {
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
				$relative = true;
				self::guessDate($pathinfo['basename'], $moddate);
				$urlfilename = basename($filename);
				$urlshortfilename = JThumbsHelper::getthumbformat("small", $pathinfo['filename'] . ".jpg");
				if (!file_exists(JGalleryHelper::join_paths($jgallerydir, $urlshortfilename))) {
					$urlshortfilename="/images/th.webp";
				}
				array_push($listfiles, new JGalleryImage(JGalleryHelper::join_paths($rootdir, $directory),
											$pathinfo['filename'],
											basename($filename),
											$moddate,
											"",
											$urlfilename,
											$urlshortfilename,
											$relative));
			}
		}
		$cur_class = static::class;
		if (!$exist || $modified)
		{
			$listfilteredfiles = array();
			foreach ($listfiles as $file) {
				array_push($listfilteredfiles, JGalleryImage::toArray($file, false));
			}
			usort($listfilteredfiles, array($cur_class,'sortFile'));
			JGalleryImage::savecsv($jgalleryfile, $listfilteredfiles);
		}
		$listfilteredfiles = array();
		foreach ($listfiles as $file) {
			$moddate = $file->moddate;
			if (($startdate == -1) || (($moddate - $startdate) >= 0)){
				if (($enddate == -1 ) ||  (($moddate != -1) && ($enddate - $moddate) >= 0)) {
					//filter files
					if (($media == "ALL") || ($file->video && ($media == "VIDEOS")) || (!$file->video && ($media == "IMAGES"))) {
						array_push($listfilteredfiles, JGalleryImage::toArray($file, true));
					}
				}
			}
		}
		return $listfilteredfiles;
	}


	public static function outputfancybox($id, $directory, $listfiles, $page, &$content)
	{
		$sid = "jgallery" . $id;
		$content .= LayoutHelper::render('jgallery', array('id' => $id), JPATH_ADMINISTRATOR . '/components/com_jgallery/layouts');
		JGalleryHelper::loadLibrary(array("jimages"=> true, "fancybox" => true));
		JGalleryHelper::loadLibrary(array("inline" => array('(function($) {
														$(document).ready(function() {
																jimages_getimages($, "' . $sid .'", ' . json_encode($listfiles) .');
															})})(jQuery);',
													['position' => 'after'],
													[],
													['com_jgallery.jimages'])));
	}

	public static function outputphotoswipe($id, $directory, $listfiles, $page, &$content)
	{
		$sid = "jgallery" . $id;
		$content .= LayoutHelper::render('jgallery', array('id' => $id), JPATH_ADMINISTRATOR . '/components/com_jgallery/layouts');
		JGalleryHelper::loadLibrary(array("psw_images" => true, "photoswipe" => true));
		JGalleryHelper::loadLibrary(array("inline" => array('import {init_psw,psw_images_getimages} from "' . Uri::root() . '/media/com_jgallery/js/psw_images.js";
														(function($) {
																$(document).ready(function() {
																psw_images_getimages($, "' . $sid .'", ' . json_encode($listfiles) .');
																init_psw($, "' . $sid .'");
															})})(jQuery);',
													['position' => 'after'],
													['type' => 'module'],
													["photoswipe"],
													)));
	}

	public static function outputimg($rootdir, $directory, $file, $name, $icon,$width, &$content)
	{
		$urlfilename = $file['urlfilename'];
		if ($icon == 'small') {
			$urlshortfilename = $file['urlshortfilename'];
		} else {
			$urlshortfilename = JThumbsHelper::getthumburl( JGalleryHelper::join_paths($rootdir, $directory), $icon, $file->basename);
		}
		if ($width == "?") {
			$width = JParametersHelper::get('thumb_' . $icon .'_width');
		}
		$content .= "<a data-fancybox=\"". $name ."\"  href=\"$urlfilename\"><img src=\"$urlshortfilename\"/ width=\"$width\"></a>";
		JGalleryHelper::loadLibrary(array( "fancybox" => true));
	}


// $dir is the full path sdir is the directory as parameter
	public static function outputdirs($galid, $id, $dir, $directory, $parentlevel, &$content, $type='radio', $media = "ALL", $lightbox = "fancybox") {
		JDirectoryHelper::loadLibrary();
		$document = Factory::getDocument();
		$jroot = new JRootDirectory($dir, $directory, $parentlevel, $galid);
		if (($jroot->findDirs($dir, $directory, JDirectory::$_excludes, true) > 0) || ($jroot->parentlevel > 0)) {
			$jroot->outputdirs($type, $id, $content, $type, $media, $lightbox);
		}
	}
	
	public static function outputfiles($id, $directory, $listfiles, $page, &$content, $lightbox='fancybox') {
		$content .= "<!--" . $type . "-->";
		switch($lightbox) {
			case 'fancybox':
				self::outputfancybox($id, $directory, $listfiles, $page, $content);
				break;
			case 'photoswipe':
				self::outputphotoswipe($id, $directory, $listfiles, $page, $content);
				break;
		}
	}

	/**
	* Function to display JGallery
	*
	* @param $_params parameters
	*/	   
	public static function display( $_params)
	{
		$content = "";
		$startdate = $enddate = -1;
		if (is_array( $_params )== false)
		{
			return  "errorf:" . print_r($_params, true);
		}
		if (! array_key_exists('directory', $_params) && 
			! array_key_exists('dir', $_params))
		{
			return  "errorf: missing dir/directory param" . print_r($_params, true);
		}
		$directory = null;
		$keys= array('dir', 'directory');
		foreach ($keys as $key) {
			if (array_key_exists($key, $_params)) {
				$directory = $_params[$key];
				break;
			}
		}
		if ($directory == null) {
			return  "errorf: missing dir/directory param" . print_r($_params, true);
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
		if ( array_key_exists('title', $_params))
		{
			$title = (bool)$_params['title'];
		} else {
			$width = true;
		}
		if ( array_key_exists('page', $_params))
		{
			$page = $_params['page'];
		} else {
			$page = -1;
		}
		if ( array_key_exists('id', $_params))
		{
			$galid = $_params['id'];
		} else {
			$galid = -1;
		}
		if ( array_key_exists('media', $_params))
		{
			$media = $_params['media'];
		} else {
			$media = "IMAGES";
		}
		if ( array_key_exists('lightbox', $_params))
		{
			$lightbox = $_params['lightbox'];
		} else {
			$lightbox = "fancybox";
		}
		if ( array_key_exists('type', $_params))
		{
			$type = $_params['type'];
		} else {
			$type = 'directories';
		}
		$document = Factory::getDocument();
		if ( array_key_exists('img', $_params)) {
			$listfiles = self::getFiles($rootdir, $directory, "ALL", $startdate, $enddate);
			$found = False;
			foreach ($listfiles as $file) {
				if ($file['basename'] == $_params['img']) {
					self::outputimg($rootdir, $directory, $file, $name, $icon, $width, $content);
					$found = true;
					break;
				}
			}
			if (!$found) {
				$content .= "image not found :" . $_params['img'] . " in " . $directory;
			}
		}else {
			//sub directories
			$sdir = html_entity_decode(JGalleryHelper::join_paths(JPATH_SITE, $rootdir,  $directory));
			$id = rand(1,1024);
			self::outputdirs($galid, $id, $sdir, $directory, $parent, $content, $type, $media, $lightbox);
			$listfiles = self::getFiles($rootdir, $directory, $media, $startdate, $enddate);
			if ($parent != 0 && count($listfiles)) {
				$content .= "<hr/>";
			}
			self::outputfiles($id, $directory, $listfiles, $page, $content, $lightbox);
		}
		return $content;
	}

	static function savecomments($directory, $jcomments) {
		$rootdir = JParametersHelper::getrootdir();
		$jgalleryfiles = JGalleryHelper::getFiles($rootdir , $directory);
		$modif = false;
		foreach($jcomments as $array_key => $comment) {
			$i = 0;
			foreach($jgalleryfiles as $file)
			{
				if (($file['filename'] == $array_key)) {
				if ($file['comment'] != $comment) {
						$modif = true;
						$jgalleryfiles[$i]['comment'] = $comment;
					}
					break;
				}
				$i++;
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

	static function deleteimage($rootdirectory, $directory, $Image, $keep, &$errors)
	{
		$filename = self::join_paths(JPATH_ROOT, $rootdirectory, $directory, $Image);
		Log::add("deleteimage:" . $filename, Log::WARNING, 'com_jgallery');
		if (file_exists($filename)){
			if ($keep) {
				array_push($errors, "keep " . $filename);
				Log::add("deleteimage:keep:" . $filename, Log::WARNING, 'com_jgallery');
			}
			else {
				unlink($filename);
				Log::add("deleteimage:delete:" . $filename, Log::WARNING, 'com_jgallery');
				array_push($errors, "success deleting " . $filename);
			}
		}
		else {
			array_push($errors, "file does not exist " . $filename);
			Log::add("deleteimage:dose not exist:" . $filename, Log::WARNING, 'com_jgallery');
		}
	}

	static function deleteimages($directory, $Images, $keep, &$errors)
	{
		$rootdir = JParametersHelper::getrootdir();
		$jgalleryfiles = JGalleryHelper::getFiles($rootdir , $directory);
		$modif = false;
		foreach ($Images as $Image)
		{
			Log::add("delete" . $rootdir . "/" . $directory . "/" . $Image, Log::WARNING, 'com_jgallery'); 
			self::deleteimage($rootdir, $directory, $Image, $keep, $errors);
			Log::add("delete end:" . $rootdir . "/" . $directory . "/" . $Image, Log::WARNING, 'com_jgallery'); 
			JThumbsHelper::deletethumbs($rootdir, $directory, $Image, $errors);
			Log::add("delete end thumbs:" . $rootdir . "/" . $directory . "/" . $Image, Log::WARNING, 'com_jgallery'); 
			$i = 0;
			$found = false;
			foreach($jgalleryfiles as $file)
			{
				if ($file['basename'] == $Image) {
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







