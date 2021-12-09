<?php
/**
 * 
* @copyright Copyright (C) 2012 Jean-Luc TRYOEN. All rights reserved.
* @license GNU/GPL
*
* Version 1.0
*
*/

// Check to ensure this file is included in Joomla!
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport( 'joomla.plugin.plugin' );
define('PF_REGEX_JGALLERYI_PATTERN', "#{jgallery (.*?)}#s");

JLoader::import('components.com_jgallery.helpers.jgallery', JPATH_ADMINISTRATOR);
JLoader::import('components.com_jgallery.helpers.jdirectory', JPATH_ADMINISTRATOR);


/**
* WikipediaArticle Content Plugin
*
*/
class plgContentJGallery extends JPlugin
{
	/**
	* Constructor
	*
	* @param object $subject The object to observe
	* @param object $params The object that holds the plugin parameters
	*/
	function __construct( &$subject, $params )
	{
		parent::__construct( $subject, $params );
	}
	
	function getparam($name, $param) {
		$found = false;
		$app     = JFactory::getApplication();
		$input   = $app->getInput();
		if ($input->get($param) !== null) {
			$this->{$name} = $input->get($param);
			$found = true;
		}
		return $found;
	}

	/**
	* Example prepare content method in Joomla 1.5
	*
	* Method is called by the view
	*
	* @param object The article object. Note $article->text is also available
	* @param object The article params
	* @param int The 'page' number
	*/
	function onPrepareContent( &$article, &$params, $limitstart )
	{
		return $this->OnPrepareRow($article);
	}

 	/**
	* Example prepare content method in Joomla 1.6/1.7/2.5
	*
	* Method is called by the view
	*
	* @param object The article object. Note $article->text is also available
	* @param object The article params
	*/   
	function onContentPrepare($context, &$row, &$params, $page = 0)
	{
		return $this->OnPrepareRow($row);
	}
	
	function onPrepareRow(&$row) 
	{  
		//Escape fast
        if (!$this->params->get('enabled', 1)) {
            return true;
        }
		
 		if ( strpos( $row->text, '{jgallery' ) === false ) {
            return true;
		}
		preg_match_all(PF_REGEX_JGALLERYI_PATTERN, $row->text, $matches);
		// Number of plugins
		$count = count($matches[0]);
		 // plugin only processes if there are any instances of the plugin in the text
		if ($count) {			
			for ($i = 0; $i < $count; $i++)
			{
				$_result = array();
				if ($this->getparam('page', 'page')) {
					$_result['page'] = $this->page;
				}
				if (@$matches[1][$i]) {
					$inline_params = $matches[1][$i];
					$pairs = explode('|', trim($inline_params));
					foreach ($pairs as $pair) {
						$pos = strpos($pair, "=");
						$key = substr($pair, 0, $pos);
						$value = substr($pair, $pos + 1);
						$_result[$key] = $value;
					}
					$_result['rootdir'] = JParametersHelper::getrootdir();
					if (array_key_exists('img', $_result)) {
						$p_content = JGalleryHelper::display($_result);								
					}elseif (array_key_exists('browse', $_result)) {
						$p_content = JDirectoryHelper::display(1, $_result);
					} else {
						$p_content = JGalleryHelper::display($_result);								
					}					
					$row->text = str_replace("{jgallery " . $matches[1][$i] . "}", $p_content, $row->text);
				}
			}
		}
		return true; 
	}
    
}
