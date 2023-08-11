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

/**
 * FolderGroup component helper.
 *
 * @param   string  $submenu  The name of the active view.
 *
 * @return  void
 *
 * @since   1.6
 */
class FolderGroupHelper
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
    
    public static function getRoute($parentdir, $directory, $parent, $id, $tmpl) {
        $route = JURI::root(true) . "/index.php?option=com_jgallery&view=foldergroup&parent=" .$parent . "&Itemid=0&id=" . $id . "&header=1";
        if ($directory !== null) {
            $route .= "&directory64=". base64_encode(JGalleryHelper::join_paths($parentdir,$directory));
        }
        if ($tmpl != null) {
            $route .= "&tmpl=" . $tmpl;
        }
        return $route;
	}
    
    public static function getDirectories($name, $rootdir, $directory,  $parent=0, $folders, $id, $tmpl) {
		$listdirs = array();
		$dir = JGalleryHelper::join_paths(JPATH_SITE, $rootdir,  $directory);
		if ($parent > 0 ) {
			array_push($listdirs, array("name" => ($parent == 1)?$name : dirname($directory), 
									"parent" => $parent - 1,
									"url" => self::getRoute($directory, "..", $parent-1, $id, $tmpl)));
            foreach (glob(JGalleryHelper::join_paths($dir , "*")) as $dirname) {
                if (is_dir($dirname) && !(in_array(basename($dirname), JDirectoryHelper::$_excludes))) {
                    array_push($listdirs,  array("name" => basename($dirname),
                                            "parent" => $parent,
                                            "url" => self::getRoute($directory, basename($dirname), $parent + 1, $id, $tmpl)));
                }
            }
		} else  {
            foreach ($folders as $folder) {
                array_push($listdirs,  array("name" => basename($folder),
                                            "parent" => $parent,
                                            "url" => self::getRoute(dirname($folder), basename($folder), $parent + 1, $id, $tmpl)));
            }
        } 
		return $listdirs;
	}	
	/**
	**
	* @param 
	*/       
	public static function display( $_params)
	{
		$content = "";
		$startdate = $enddate = -1;
		if (is_array( $_params )== false)
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
		JHtml::_('jquery.framework');
		$document = JFactory::getDocument();
		$document->addScript('https://cdn.jsdelivr.net/npm/@fancyapps/ui/dist/fancybox.umd.js');
		$document->addStyleSheet('https://cdn.jsdelivr.net/npm/@fancyapps/ui/dist/fancybox.css');
		$document->addStyleSheet(JURI::root(true) . '/plugins/content/jgallery/jgallery.css');
		$scriptDeclarations = array();
		$scripts = array('jgallery.js');
        if ($parent == 0) {
            $content .= "<h2>". $name . "</h2>";
        }
        $listdirs = self::getDirectories($name, $rootdir, $directory, $parent, $folders, $id, $tmpl);
        if ($directory != null) {
            $listfiles = JGalleryHelper::getFiles($rootdir, $directory, false, -1, -1);
        }
        if ($listdirs) {
            JGalleryHelper::ouputdirs($title, $listdirs, $content, $scriptDeclarations, $scripts);
        }
        if ($directory !== null) {
            JGalleryHelper::ouputasync($directory, $listfiles, -1, $content, $scriptDeclarations, $scripts);
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

	
}