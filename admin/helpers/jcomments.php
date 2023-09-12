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
JLoader::import('components.com_jgallery.helpers.jparameters', JPATH_ADMINISTRATOR);
JLoader::import('components.com_jgallery.helpers.jgallery', JPATH_ADMINISTRATOR);
JLoader::import('components.com_jgallery.helpers.jdirectory', JPATH_ADMINISTRATOR);



abstract class JCommentsHelper
{
	
	public static function display($id, $_params)
	{
		$content = "";
		if (is_array( $_params )== false)
		{
			return  "errorf:" . print_r($_params, true);
		}
		if (! array_key_exists('dir', $_params))
		{
			return  "errorf: missing dir param" . print_r($_params, true);
		}
		if ( array_key_exists('rootdir', $_params))
		{
			$rootdir = $_params['rootdir'];
		} else {
			$rootdir = ".";
		}
		$directory = $_params['dir'];
		$dir = utf8_decode(html_entity_decode(JGalleryHelper::join_paths(JPATH_SITE, $rootdir,  $directory)));
		if (!is_dir($dir)) {
			$content .= "Directory does not exists :". $dir;
		} else {

			$scriptDeclarations = array();
			$scripts = array();
			JDirectoryHelper::outputdirs($id, $dir, $directory, $content,  $scriptDeclarations, $scripts, 'selectcomments');
			$document = JFactory::getDocument();
			foreach ($scripts as $script) {
				 $document->addScript(JURI::root(true) . '/administrator/components/com_jgallery/helpers/' . $script);
			}
			foreach ($scriptDeclarations as $scriptDeclaration) {
				 $document->addScriptDeclaration($scriptDeclaration);
			}
		}		
		return $content;
	}
}	