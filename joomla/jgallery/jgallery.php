<?php
/**
 * 
* @copyright Copyright (C) 2015-2025 Jean-Luc TRYOEN. All rights reserved.
* @license GNU/GPL
*
* Version 1.0
*
*/

use JLTRY\Component\JGallery\Administrator\Model\JGalleryModel;
use JLTRY\Component\JGallery\Administrator\Helper\JParametersHelper;
use JLTRY\Component\JGallery\Administrator\Helper\JGalleryHelper;
use JLTRY\Component\JGallery\Administrator\Helper\JDirectoryHelper;
use JLTRY\Component\JGallery\Administrator\Helper\JGalleryCategoryHelper;
use JLTRY\Component\JGallery\Administrator\Helper\FoldergroupHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Utility\Utility;
use Joomla\Event\Event;
use Joomla\Event\SubscriberInterface;


// Check to ensure this file is included in Joomla!
defined( '_JEXEC' ) or die( 'Restricted access' );

define('JG_REGEX_VARIABLES', '((?:\s?[a-zA-Z0-9_-]+=\"[^\"]+\")+|(?:\|?[a-zA-Z0-9_-]+=[^\"}]+)+)');
define('JG_REGEX_JGALLERY_PATTERN', "#{jgallery\s?". JG_REGEX_VARIABLES ."\s?}#s");




/**
* JGallery Content Plugin
*
*/
class plgContentJGallery extends CMSPlugin  implements SubscriberInterface
{

    /**
     * Returns an array of events this subscriber will listen to.
     *
     * @return  array
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'onContentPrepare' => 'onContentPrepare'
        ];
    }

    function getparam($name, $param) {
        $found = false;
        $app     = Factory::getApplication();
        $input   = $app->getInput();
        if ($input->get($param) !== null) {
            $this->{$name} = $input->get($param);
            $found = true;
        }
        return $found;
    }


    static function parseAttributes($string, &$retarray)
    {
        $pairs = explode('|', trim($string));
        foreach ($pairs as $pair) {
            if ($pair == "") {
                continue;
            }
            $pos = strpos($pair, "=");
            $key = substr($pair, 0, $pos);
            $value = substr($pair, $pos + 1);
            $retarray[$key] = $value;
        }
    }


    /**
     * The content plugin that inserts the galleries into content items
     *
     * @param    Event $event The event object
     *
     * @return true if anything has been inserted into the content object
     */
    public function onContentPrepare(Event $event)
    {
        if (version_compare(JVERSION, '5', 'lt')) {
            [$context, $row, $params, $page] = $event->getArguments();
        } 
         else {
            $context = $event['context'];
            $row = $event['subject'];
            $params = $event['params'];
        }
        //Escape fast
        if (!$this->params->get('enabled', 1)) {
            return true;
        }
         if ( strpos( $row->text, '{jgallery' ) === false ) {
            return true;
        }
        $app = Factory::getApplication();
        if ( $app->isClient('administrator') ) {
            return true;
        }
        $regexp = JG_REGEX_JGALLERY_PATTERN;
        $row->text = preg_replace_callback($regexp,
            function($matches){
                if (@$matches[1]) {
                    if ( strpos( $matches[1], "\"") === false ) {
                        $params = array();
                        self::parseAttributes($matches[1], $params);
                    } else {
                        $params = Utility::parseAttributes($matches[1]);
                    }
                    if ($this->getparam('page', 'page')) {
                        $params['page'] = $this->page;
                    }
                    $params['rootdir'] = JParametersHelper::getrootdir();
                    if (array_key_exists('img', $params)) {
                        $p_content = JGalleryHelper::display($params);
                    }elseif (array_key_exists('browse', $params)) {
                        $p_content = JDirectoryHelper::display(rand(1,1024), $params);
                    } elseif (array_key_exists('group', $params)){
                        $id = $params['group'];
                        $params['id'] = $id;
                        $p_content = FolderGroupHelper::display($params);
                    }
                    else {
                        $p_content = "<!-- display -->".
                                    JGalleryHelper::display($params) .
                                    "<!-- display end -->";
                    }
                    return $p_content;
                }
            }, $row->text);
        return true; 
    }
}
