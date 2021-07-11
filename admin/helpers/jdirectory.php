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
JLoader::import('components.com_jgallery.helpers.jgallery', JPATH_ADMINISTRATOR);


class JDirectory
{
	protected $parent;
	protected $basename;
	protected $dirname;
	protected $children;
	function __construct($parent, $dirname, $basename)
	{
		$this->parent = $parent;
		$this->dirname = $dirname;
		$this->basename = $basename;
		$this->children = array();
	}
	
	public function getbasename() {
		return $this->basename;
	}
	
	public function getrelativepath() {
		if ($this->parent == null) {
			return "";
		} else {
			$parentpath = $this->parent->getrelativepath();
			if ($parentpath != "") {
				return $parentpath . "/" . $this->basename;
			} else {
				return $this->basename;
			}
		}
	}
	
	
	public function getbase64path() {
		return base64_encode(JGalleryHelper::join_paths($this->dirname, $this->basename));
	}
	
	public function insertDir($elem){
		
		$startIndex = 0;
		$stopIndex = count($this->children) - 1;
		$middle = 0;
		while($startIndex < $stopIndex){
			$middle = ceil(($stopIndex + $startIndex) / 2);
			if($elem->basename < $this->children[$middle]->basename){
				$stopIndex = $middle - 1;
			}else if($elem->basename >= $this->children[$middle]->basename){
				$startIndex = $middle;
			}
		}
		$offset = $elem->basename <= $this->children[$startIndex]->basename ? $startIndex : $startIndex + 1; 
		array_splice($this->children, $offset, 0, array($elem));
	}

	/* https://rosettacode.org/wiki/Walk_a_directory/Recursively#PHP */	
	public function findDirs($sdir, $sdir1, $excludes)
	{
		$subdirs = array();
		$dirs = array();
		foreach (new DirectoryIterator($sdir) as $fileInfo) {
			if ($fileInfo->isDot()) continue;
			$filename = $fileInfo->getFileName();
			$dir1 = JGalleryHelper::join_paths($sdir, $filename);
			if (is_dir($dir1) && !(in_array($filename, $excludes))) {
				$this->insertDir(new JDirectory($this, $sdir1, $filename));
				array_push($subdirs, $filename);
			}
		}
		foreach ($this->children as $subdir) {
			$subdir->findDirs(JGalleryHelper::join_paths($sdir, $subdir->getbasename()),
								JGalleryHelper::join_paths($sdir1, $subdir->getbasename()),
								$excludes);
		}
	}
	
	public function outputselect(&$content)
	{
		if ($this->parent == null)
		{
			$content .= '<div class="form-floating" id="findir'. $this->id .'">
						<select class="form-select" style="max-width:500px;" id="dirselect' . $this->id . '" aria-label="Floating label select example">
							<option selected>Open this select menu</option>';
		}
		if ($this->parent) {
			$content .= '<option value="'. $this->getbase64path() . '">' .
								JGalleryHelper::join_paths($this->dirname , $this->basename) . 
								'</option>';
		}
		foreach ($this->children as $child) {
			$child->outputselect($content);
		}
		if ($this->parent == null)
		{
			$content .= 		'</select></div>';
		}
	}
	
	public function outputarray(&$arr)
	{
		if ($this->parent != null) {
			array_push($arr, array("name" => JGalleryHelper::join_paths($this->dirname , $this->basename),
								"relative" =>$this->getrelativepath(),
								"value" => $this->getbase64path()));
		}
		foreach ($this->children as $child) {
			$child->outputarray($arr);
		}
	}
	
	public function outputjson(&$json)
	{
		$arr = array();
		$this->outputarray($arr);
		$json = json_encode($arr);
	}

	public function outputradio($sid, $sidg, &$content, &$scriptsdecl, &$scripts )
	{
		$json = "";
		$this->outputjson($json);
		array_push($scriptsdecl, '$(function () {
		initradiobox($, "#' . $sid .'" ,'. $json.',  fillgallery, [ "#' . $sidg .'", "' . JURI::root(true) .'"]);});');
		array_push($scripts, "radiobox.js");
	}	
}


class JRootDirectory extends JDirectory
{
	function __construct($dirname, $basename)
	{
		parent::__construct(null, $dirname, $basename);
	}
}



abstract class JDirectoryHelper
{
	static $_excludes =array("thumbs", "jw_sig", "th");
	
	public function sortDir($a, $b) {
		if ($a[0] == $b[0]) {
			return 0;
		}
        return ($a[0] < $b[0]) ? 1 : -1;
	}
	
	function findDirs($sid, $sidg, $dir, $sdir,  &$content, &$scriptsdecl, &$scripts) {
		$jroot = new JRootDirectory($id, $dir, $sdir);
		$jroot->findDirs($dir, $sdir, self::$_excludes);
		//$jroot->outputselect($content);
		$jroot->outputradio($sid, $sidg, $content, $scriptsdecl, $scripts);
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
		$dir = utf8_decode(html_entity_decode(JGalleryHelper::join_paths(JPATH_SITE, $rootdir,  $directory)));
		if (!is_dir($dir)) {
			$content .= "Directory does not exists :". $dir;
		} else {
			$scriptDeclarations = array();
			$scripts = array('jgallery.js');
			$sid = "findir". $id;
			$sidg = "jgallery" . $id;
			$content = '<div class="form-floating" id="'. $sid .'"></div>';
			JDirectoryHelper::findDirs($sid, $sidg, $dir, $directory, $content, $scriptDeclarations, $scripts);
			JGalleryHelper::gallery($id, $content);
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
