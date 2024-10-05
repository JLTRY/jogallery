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

use Joomla\CMS\Date\Date;
use Joomla\CMS\Log\Log;

JLoader::import('components.com_jgallery.helpers.jgallery', JPATH_ADMINISTRATOR);


class JFolderGroup extends JDirectory
{
	private $_folders;

	function __construct($name, $dirname, $folders, $parent, $id, $tmpl)
	{
		parent::__construct(null, $dirname, $dirname, $parent);
		$this->_folders = $folders;
		$this->tmpl = $tmpl;
		$this->id= $id;
		$this->name = $name;
	}

	function findDirs($sdir, $sdir1, $excludes, $recurse= false)
	{
		foreach ($this->_folders as $folder) {
			$this->insertDir(new JDirectory($this, $sdir, $folder));
		}
	}
	
	public function getRoute($parentdir, $dir, $parent, $id =0, $tmpl=null, $name="") {
		$route = JURI::root(true) . "/index.php?option=com_jgallery&view=foldergroup&parent=" .$parent . "&Itemid=0&id=" . $id . "&header=1";
		if ($dir !== null) {
			$route .= "&directory64=". base64_encode(utf8_encode(JGalleryHelper::join_paths($parentdir,$dir)));
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
		$dir = $this->dirname;
		if ($this->parentlevel > 0 ) {
			array_push($listdirs, array("name" => ($this->parentlevel == 1)? $this->name : basename(dirname($dir)), 
									"parent" => $this->parentlevel - 1,
									"url" => self::getRoute(basename(dirname($dir)), ".", $this->parentlevel-1, $this->id, $this->tmpl, $this->name)));
			foreach (glob(JGalleryHelper::join_paths($dir , "*")) as $dirname) {
				if (is_dir($dirname) && !(in_array(basename($dirname), JDirectory::$_excludes))) {
					array_push($listdirs,  array("name" => basename($dirname),
											"parent" => $this->parentlevel,
											"url" => self::getRoute(basename($dir), basename($dirname), $this->parentlevel + 1, $this->id, $this->tmpl, $this->name)));
				}
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
		$db = JFactory::getDBO();
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
		if (! array_key_exists('folders', $_params))
		{
			return  "errorf: missing folders param" . print_r($_params, true);
		} else {
			$folders = $_params['folders'];
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
		if ( array_key_exists('name', $_params))
		{
			$name = $_params['name'];
		} else {
			$name = "Gallery";
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
		if ($parent == 0) {
			$content .= "<h2>". $name . "</h2>";
		}
		$scriptsdeclarations = array();
		$scripts = array('jgallery.js');
		$css = array();
		$document = JFactory::getDocument();
		JGalleryHelper::addFancybox($document);
		$scriptDeclarations = array();
		$scripts = array('jgallery.js');
		$document = JFactory::getDocument();
		$dir = utf8_decode(html_entity_decode(JGalleryHelper::join_paths(JPATH_SITE, $rootdir,  $directory)));
		$jroot = new JFolderGroup($name, $dir, $folders, $parent, $id, $tmpl);
		$jroot->findDirs($directory, $directory, JDirectory::$_excludes, true);
		$jroot->outputdirs($type, $id, $content, $scriptsdeclarations, $scripts, $css, $type);
		if ($directory != null && $parent != 0 ) {
			$content .= "<hr/>";
			$listfiles = JGalleryHelper::getFiles($rootdir, $directory, false, -1, -1);
			JGalleryHelper::outputasync(rand(1,1024), $directory, $listfiles, -1, $content, $scriptsdeclarations, $scripts);
		}
		foreach ($scripts as $script) {
			if (preg_match('/http/', $script)) {
				$document->addScript($script);
			} else {
				$document->addScript(JUri::root(true) . '/administrator/components/com_jgallery/helpers/' . $script);
			}
		}
		foreach ($scriptsdeclarations as $scriptDeclaration) {
			 $document->addScriptDeclaration($scriptDeclaration);
		}
		foreach ($css as $cssi) {
			 $document->addStyleSheet($cssi);
		}

		return $content;
	}
};
