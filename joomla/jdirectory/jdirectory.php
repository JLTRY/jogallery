<?php
/**
 * 
* @copyright Copyright (C) 2015-2025 Jean-Luc TRYOEN. All rights reserved.
* @license GNU/GPL
*
* Version 1.0
*
*/

use JLTRY\Component\JOGallery\Administrator\Model\JOGalleryModel;
use JLTRY\Component\JOGallery\Administrator\Helper\JParametersHelper;
use JLTRY\Component\JOGallery\Administrator\Helper\JOGalleryHelper;
use JLTRY\Component\JOGallery\Administrator\Helper\JODirectoryHelper;
use JLTRY\Component\JOGallery\Administrator\Helper\JOGalleryCategoryHelper;
use JLTRY\Component\JOGallery\Administrator\Helper\FoldergroupHelper;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Factory;
use Joomla\Event\Event;
use Joomla\Event\SubscriberInterface;

// Check to ensure this file is included in Joomla!
defined( '_JEXEC' ) or die( 'Restricted access' );
define('PF_REGEX_JDIRECTORYI_PATTERN', "#{jdirectory (.*?)}#s");




/**
* Directory Content Plugin
*
*/
class plgContentJODirectory extends JPlugin
{
    protected static $_ID = 0;
    /**
    * Constructor
    *
    * @param object $subject The object to observe
    * @param object $params The object that holds the plugin parameters
    */
    function __construct( &$subject, $params )
    {
        parent::__construct( $subject, $params );
        plgContentJODirectory::$_ID++;
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

        if ( strpos( $row->text, '{jdirectory' ) === false ) {
            return true;
        }
        preg_match_all(PF_REGEX_JDIRECTORYI_PATTERN, $row->text, $matches);
        // Number of plugins
        $count = count($matches[0]);
         // plugin only processes if there are any instances of the plugin in the text
        if ($count) {
            for ($i = 0; $i < $count; $i++)
            {
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
                    $p_content = JODirectoryHelper::display(plgContentJODirectory::$_ID, $_result);
                    $row->text = str_replace("{jdirectory " . $matches[1][$i] . "}", $p_content, $row->text);
                }
            }
        }
        return true; 
    }
    
}
