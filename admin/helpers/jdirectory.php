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
JLoader::import('components.com_jgallery.helpers.jgallery', JPATH_ADMINISTRATOR);

abstract class JDirectoryHelper
{
	static $_excludes =array("thumbs", "jw_sig", "th");
	public static function join_paths(...$paths) {
		return preg_replace('~[/\\\\]+~', DIRECTORY_SEPARATOR, implode(DIRECTORY_SEPARATOR, $paths));
	}
	/* https://rosettacode.org/wiki/Walk_a_directory/Recursively#PHP */
	function findDirs1($sdir, $sdir1, &$content){
		$subdirs = array();
		foreach (new DirectoryIterator($sdir) as $fileInfo) {
			if ($fileInfo->isDot()) continue;
			$filename = $fileInfo->getFileName();
			$dir1 = JDirectoryHelper::join_paths($sdir, $filename);
			if (is_dir($dir1) && !(in_array($filename, self::$_excludes))) {
				$content .=  '<option value="'. base64_encode(JDirectoryHelper::join_paths($sdir1, $filename)) . '">' .
							basename($sdir) ."/". $filename . 
							'</option>';
				array_push($subdirs, $filename);			
			}
		}
		foreach ($subdirs as $subdir) {
			JDirectoryHelper::findDirs1(JDirectoryHelper::join_paths($sdir, $subdir),
									JDirectoryHelper::join_paths($sdir1, $subdir) ,
									$content);
		}	
		
	}
	
	function findDirs($id, $dir, $sdir,  &$content) {
		$content .= '<div class="form-floating" id="findir'. $id .'">
						<select class="form-select" id="dirselect' . $id . '" aria-label="Floating label select example">
							<option selected>Open this select menu</option>';
		JDirectoryHelper::findDirs1($dir, $sdir, $content);			
		$content .= 		'</select>						
					</div>';								
	}
	
	   
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
		$dir = utf8_decode(html_entity_decode(JDirectoryHelper::join_paths(JPATH_SITE, $rootdir,  $directory)));
		if (!is_dir($dir)) {
			$content .= "Directory does not exists :". $dir;
		} else {
			JDirectoryHelper::findDirs($id, $dir, $directory, $content);
			JGalleryHelper::gallery($id, $content);
			$document = JFactory::getDocument();	
			$url = JURI::root(true) . '/administrator/components/com_jgallery/helpers/jgallery.js';
			$document->addScript($url);
			$scriptDeclarations = array("(function($) {
					$(document).ready(function() {
						 fillgallery($, " . $id .");
						})})(jQuery);");
			foreach ($scriptDeclarations as $scriptDeclaration) {
				 $document->addScriptDeclaration($scriptDeclaration);
			}
		}
		return $content;
	}
}
