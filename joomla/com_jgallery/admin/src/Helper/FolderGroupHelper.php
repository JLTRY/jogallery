<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_jgallery
 *
 * @copyright   Copyright (C) 2015 - 2025 JL TRYOEN All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace JLTRY\Component\JGallery\Administrator\Helper;
use JLTRY\Component\JGallery\Administrator\Model\FolderGrouModel;
use JLTRY\Component\JGallery\Administrator\Helper\JDirectoryHelper;
use JLTRY\Component\JGallery\Administrator\Helper\JParametersHelper;
use JLTRY\Component\JGallery\Administrator\Helper\JGalleryHelper;
use JLTRY\Component\JGallery\Administrator\Table\FolderGroup;
use JLTRY\Component\JGallery\Administrator\Model\FolderGroupModel;

use Joomla\CMS\Date\Date;
use Joomla\CMS\Log\Log;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Factory;
use Joomla\CMS\Layout\LayoutHelper;

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

JDirectoryHelper::loadLibrary();

class JFolderGroup extends JDirectory
{
	private $_folders;

	function __construct($dirname, $basename, $parent, $id, $tmpl)
	{
		parent::__construct(null, $dirname, $basename, $parent);
		$this->tmpl = $tmpl;
		$this->id= $id;
		$model = new FolderGroupModel;
		//$model->setstate($model->getName() . '.id', $id);
		$mod = $model->getItem($id);
		if ($mod !== null) {
			$this->_folders = json_decode($mod->folders);
			$this->name = $mod->name;
		}
	}

	function findDirs($sdir, $sdir1, $excludes, $recurse = false)
	{
		if ($this->parentlevel > 0) {
			return parent::findDirs($sdir, $sdir1, $excludes, false);
		}
		foreach ($this->_folders as $folder) {
			$this->insertDir(new JDirectory($this, "", $folder));
		}
	}
	
	public function getRoute($parentdir, $dir, $parent, $id =0, $tmpl=null) {
		$route = Uri::root(true) . "/index.php?option=com_jgallery&view=foldergroup&parent=" .$parent . "&Itemid=0&id=" . $id . "&header=1&XDEBUG_SESSION_START=test";
		if ($dir !== null) {
			$route .= "&directory64=". base64_encode(utf8_encode(JGalleryHelper::join_paths($parentdir, $dir)));
		}
		if ($tmpl != null) {
			$route .= "&tmpl=" . $tmpl;
		}
		if ($name != "") {
			$route .= "&name=" . $name;
		}
		return $route;
	}
	
	public function getjsondirectories() {
		$listdirs = array();
		if ($this->parentlevel > 0 ) {
			array_push($listdirs, array("name" => ($this->parentlevel == 1)? $this->name : basename(dirname($this->basename)), 
									"parent" => $this->parentlevel - 1,
									"url" => self::getRoute($this->basename, ($this->parentlevel == 1)?".": "..", $this->parentlevel-1, $this->id, $this->tmpl)));
			foreach ($this->children as $directory) {
				array_push($listdirs,  array("name" => $directory->basename,
										"parent" => $this->parentlevel,
										"url" => self::getRoute($directory->dirname, $directory->basename, $this->parentlevel + 1, $this->id, $this->tmpl)));
			}
		} else  {
			foreach ($this->_folders as $folder) {
				array_push($listdirs,  array("name" => basename($folder),
											"parent" => $this->parentlevel,
											"url" => self::getRoute(dirname($folder), ($this->parentlevel == -1)? null: basename($folder), $this->parentlevel + 1, $this->id, $this->tmpl)));
			}
		} 
		return json_encode($listdirs);
	}

}

/**
 * FolderGroup component helper.
 *
 * @param   string  $submenu  The name of the active view.
 *
 * @return  void
 *
 * @since   1.6
 */
abstract class FolderGroupHelper
{
	public static function getcategoryaccess($catID) {
		$db = Factory::getDBO();
		$db->setQuery("SELECT access FROM #__categories WHERE id = ".$catID." LIMIT 1;");
		$access = $db->loadResult();
		return $access;
	}

	public static function usercanviewcategory($user, $catid)
	{
		$levels = $user->getAuthorisedViewLevels();
		$access = self::getcategoryaccess($catid);
		$ok = in_array($access, $levels);
		return $ok;
	}

	/**
	**
	* @param 
	*/	   
	public static function display( $_params)
	{
		$content = "";
		if (is_array($_params )== false)
		{
			return  "errorf:" . print_r($_params, true);
		}
		if ( array_key_exists('parent', $_params))
		{
			$parent = $_params['parent'];
		} else {
			$parent = 0;
		}
		if ( array_key_exists('rootdir', $_params))
		{
			$rootdir = $_params['rootdir'];
		} else {
			$rootdir = ".";
		}
		if ( array_key_exists('directory', $_params))
		{
			$directory = $_params['directory'];
			if ($parent > 1) {
				$name = $directory;
			}
		} else {
			$directory = null;
		}
		if ( array_key_exists('id', $_params))
		{
			$id = $_params['id'];
		} else {
			$id = -1;
		}
		if ( array_key_exists('tmpl', $_params))
		{
			$tmpl = $_params['tmpl'];
		} else {
			$tmpl = null;
		}
		if ( array_key_exists('type', $_params))
		{
			$type = $_params['type'];
		} else {
			$type = 'directories';
		}
		if ( array_key_exists('media', $_params))
		{
			$media = $_params['media'];
		} else {
			$media = "IMAGES";
		}
		if ($parent == 0) {
			$content .= "<h2>". $name . "</h2>";
		}
		JGalleryHelper::loadLibrary(array("fancybox" => true, "jgallery" => true));
		$dir = utf8_decode(html_entity_decode(JGalleryHelper::join_paths(JPATH_SITE, $rootdir,  $directory)));
		$jroot = new JFolderGroup($dir, $directory, $parent, $id, $tmpl);
		$jroot->findDirs($dir, $directory, JDirectory::$_excludes, true);
		$jroot->outputdirs($type, $id, $content);
		if ($directory != null && $parent != 0 ) {
			$content .= "<hr/>";
			$id = rand(1,1024);
			$listfiles = JGalleryHelper::getFiles($rootdir, $directory, false, -1, -1);
			$content .= LayoutHelper::render('jgallery', array('id' => $id), JPATH_ADMINISTRATOR . '/components/com_jgallery/layouts');
			//filter files
			$listfilteredfiles = array();
			foreach($listfiles as $file) {
				if (($media == "ALL") || ($file["video"] && ($media == "VIDEOS")) || (!$file["video"] && ($media == "IMAGES"))) {
					array_push($listfilteredfiles, $file);
				}
			}
			JGalleryHelper::outputasync($id, $directory, $listfilteredfiles, -1, $content);
		}
		return $content;
	}
};
