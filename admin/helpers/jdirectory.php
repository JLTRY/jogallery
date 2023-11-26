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
use Joomla\CMS\HTML\HTMLHelper as JHtml;
use Joomla\CMS\Factory as JFactory;
use Joomla\CMS\Uri\Uri as JUri;
use Joomla\CMS\Image\Image as JImage;
use Joomla\CMS\Object\CMSObject as JObject;
use Joomla\CMS\Access\Access as JAccess;

JLoader::import('components.com_jgallery.helpers.jparameters', JPATH_ADMINISTRATOR);
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
		return base64_encode(utf8_encode(JGalleryHelper::join_paths($this->dirname, $this->basename)));
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
	public function findDirs($sdir, $sdir1, $excludes, $root=False, $recurse = False)
	{
		$subdirs = array();
		$dirs = array();
		foreach (new DirectoryIterator($sdir) as $fileInfo) {
			if ($fileInfo->isDot()) continue;
			$filename = $fileInfo->getFileName();
			$dir1 = JGalleryHelper::join_paths($sdir, $filename);
			if (is_dir($dir1) && !(in_array($filename, $excludes))) {
				$this->insertDir(new JDirectory($this, $sdir1, $filename));
			}
		}
        if ($recurse) {
            foreach ($this->children as $subdir) {
                $subdir->findDirs(JGalleryHelper::join_paths($sdir, $subdir->getbasename()),
                                    JGalleryHelper::join_paths($sdir1, $subdir->getbasename()),
                                    $excludes);	
            }
        }
	}
	
	public function outputselectthumbs($id, &$content, &$scriptsdecl, &$scripts, &$css)
	{
		$sid = 'jgalleryselect' . $id;
        $divid = "float" . $id;
		$urlroot = JUri::root(true);
		if (($this->parent == null) && (count($this->children)))		
		{
			$content .= '<select class="form-select" style="max-width:500px;margin-left:10px" id="' . $sid . '" >
							<option selected>Open this select menu</option>';
		}
		if (true) {
			$content .= '<option value="'. $this->getbase64path() . '">' .
								JGalleryHelper::join_paths(($this->parent)?$this->dirname:"." , $this->basename) . 
								'</option>';
		}
		foreach ($this->children as $child) {
			$child->outputselectthumbs($id, $content, $scriptsdecl, $scripts, $css);
		}
		if ($this->parent == null)
		{
			$content .= 		'</select>';
			array_push($scripts, "jthumbs.js");
			array_push($scripts, "tabselectimages.js");
			array_push($scripts, "jimages.js");
			array_push($scripts, 'https://cdn.jsdelivr.net/npm/@fancyapps/ui/dist/fancybox.umd.js');
			array_push($css, 'https://cdn.jsdelivr.net/npm/@fancyapps/ui/dist/fancybox.css');
			array_push($css, JUri::root(true) . '/plugins/content/jgallery/jgallery.css');
		}
		if (($this->parent == null) && (count($this->children))) {
			array_push($scriptsdecl, '(function($) {
					$(document).ready(function() {                                                 
                         $("#toolbar").append($("#'. $sid .'").detach());
                         $("#toolbar").append($("#jgallery'. $id .'").detach());
						 jthumbs_getimages($, "' . $sid .'", "' . $id . '", "' . $urlroot .'");
						})})(jQuery);');
		}
		if ($this->parent == null)
		{
			$content .=  '<div id="jgallery' . $id . '" class="form-group" style="height:auto;margin-left:10px" ></div>
					<div id="jgallerylog' . $id . '" class="form-group"  >log</div>
					<div id="jimages' . $id . '" style="height:auto"></div>';
		}		
	}
        
	
	public function outputselectcomments($id, &$content, &$scriptsdecl, &$scripts )
	{
		$urlroot = JUri::root(true);
		$app = JFactory::getApplication();
		if ($app->isClient('administrator'))
		{
			$urlroot .= "/administrator";
		}

		if (($this->parent == null) && (count($this->children)))
		{
			$sid = 'jgalleryselect' . $id;
			$content .= '<div class="form-floating">
						<select class="form-select" style="max-width:500px;;margin-left:10px" id="' . $sid . '" aria-label="Floating label select example">
							<option selected>Open this select menu</option>';
		}
		$content .= '<option value="'. $this->getbase64path() . '">' .
							JGalleryHelper::join_paths(($this->parent)?$this->dirname:"." , $this->basename) . 
					'</option>';
		foreach ($this->children as $child) {
			$child->outputselectcomments($id, $content, $scriptsdecl, $scripts );
		}
		if ($this->parent == null)
		{
			$content .= 		'</select></div>';
			array_push($scripts, "jcomments.js");
			array_push($scripts, "tabselectimages.js");
			array_push($scripts, "jimages.js");
			$document = JFactory::getDocument();
			$document->addScript('https://cdn.jsdelivr.net/npm/@fancyapps/ui/dist/fancybox.umd.js');
			$document->addStyleSheet('https://cdn.jsdelivr.net/npm/@fancyapps/ui/dist/fancybox.css');
			$document->addStyleSheet(JUri::root(true) . '/plugins/content/jgallery/jgallery.css');			
		}
		if (($this->parent == null) && (count($this->children)))
		{
			array_push($scriptsdecl, '(function($) {
					$(document).ready(function() {
                         $("#toolbar").append($("#'. $sid .'").detach());
                         $("#toolbar").append($("#jgallery'. $id .'").detach());
						 jcomments_getimages($, "' . $sid .'", "' . $id . '", "' . $urlroot .'");
						})})(jQuery);');
		}
		if ($this->parent == null) {
			array_push($scriptsdecl, '(function($) {
					$(document).ready(function() {
						setTimeout(function() {	initfancybox($);},500);
						})})(jQuery);');
		}
		if ($this->parent == null) {
			$content .=  '<div id="jgallery' . $id . '" class="form-group" style="height:auto;margin-left:10px" ></div>
					<div id="jgallerylog' . $id . '" class="form-group" >log</div>
					<div id="jimages' . $id . '" style="height:auto"></div>';
		}
	}
	
	
	public function outputarray(&$arr)
	{
		if (($this->parent != null) || (!count($this->children))) {
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

	public function outputradio($id, &$content, &$scriptsdecl, &$scripts )
	{
		$json = "";
		$this->outputjson($json);
		$sid = "findir" . $id;
		$sidg = "jgallery" . $id;
		$content = '<div class="form-floating" id="'. $sid .'"></div>';
		array_push($scriptsdecl, '$(document).ready(function() {
                initradiobox($, "#' . $sid .'" ,'. $json.',  fillgallery, [ "#' . $sidg .'", "' . JUri::root() .'"]);
            });'
        );
		array_push($scripts, "radiobox.js");
	}
    
	
    public function outputrecthumbs($id, &$content, &$scriptsdecl, &$scripts, &$css )
	{
		$json = "";
		$this->outputjson($json);
		$sid = "findir" . $id;
		$sidg = "jimages" . $id;
		$content = '<div class="form-floating" id="'. $sid .'"></div>';
        $content .=  '<div id="jgallery' . $id . '" class="form-group" style="height:auto;margin-left:10px" ></div>
					<div id="jgallerylog' . $id . '" class="form-group" style="min-height: 30px;" ></div>
					<div id="jimages' . $id . '" style="height:auto"></div>';
        array_push($scripts, "multicheckbox.js");
        array_push($css, Uri::base() . JGalleryHelper::join_paths("components", "com_jgallery", "helpers", "multicheckbox.css"));
        array_push($css, Uri::root() . JGalleryHelper::join_paths("templates/bootstrap4/css/template.css"));
        array_push($scripts, "jdirectories.js");
        array_push($scripts, "radiobox.js");
        array_push($scripts, "jgallery.js");		
        array_push($scripts, "jrecthumbs.js");	                    
		array_push($scriptsdecl, '$(document).ready(function() {
                jrecthumbs_getdirectories($, "#' . $sid .'" , ' . $id .' , "' . JUri::root() .'",' . $json .' );
                $("#toolbar").append($("#'. $sid .'").detach());
                $("#toolbar").append($("#jgallery'. $id .'").detach());                
            });'
        );
	}			
}


class JRootDirectory extends JDirectory
{

	function __construct($dirname, $basename)
	{
		parent::__construct(null, $dirname, $basename);
	}
	
	public function getbase64path() {
		return base64_encode(utf8_encode(JGalleryHelper::join_paths(".", $this->basename)));
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
	

	
	public static function outputdirs($id, $dir, $sdir, &$content, &$scriptsdecl, &$scripts, &$css, $type='radio') {
		$jroot = new JRootDirectory($dir, $sdir);
		$jroot->findDirs($dir, $sdir, self::$_excludes, $root, true);
		$sid = "findir" . $id;
		$sidg = "jgallery" . $id;
		switch($type) {
			case 'selectthumbs':
				$jroot->outputselectthumbs($id, $content, $scriptsdecl, $scripts, $css);
				break;
			case 'selectcomments':
				$jroot->outputselectcomments($id, $content, $scriptsdecl, $scripts);
				break;				
            case 'selectdirs':
				$jroot->outputselectdirs($id, $content, $scriptsdecl, $scripts, $css);
				break;
            case 'recthumbs':
				$jroot->outputrecthumbs($id, $content, $scriptsdecl, $scripts, $css);
				break;    
			default: 
				$jroot->outputradio($id, $content, $scriptsdecl, $scripts);
				break;
		}
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
            $css = array();
			JDirectoryHelper::outputDirs($id, $dir, $directory, $content, $scriptDeclarations, $scripts, $css);            
			JGalleryHelper::gallery($id, $content);

			$document = JFactory::getDocument();
			foreach ($scripts as $script) {
                if (preg_match('/http/', $script)) {
                    $document->addScript($script);
                } else {
                    $document->addScript(JUri::root(true) . '/administrator/components/com_jgallery/helpers/' . $script);
                }
			}
			foreach ($scriptDeclarations as $scriptDeclaration) {
				 $document->addScriptDeclaration($scriptDeclaration);
			}
            foreach ($css as $cssi) {
				 $document->addStyleSheet($cssi);
			}
		}        
		return $content;
	}
}
