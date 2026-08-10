<?php

/**
 *
* @copyright Copyright (C) 2015-2025 Jean-Luc TRYOEN. All rights reserved.
* @license GNU/GPL
*
* Version 1.0
*
*/

namespace JLTRY\Plugin\Content\JODirectory\Extension;


use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Utility\Utility;
use Joomla\CMS\Event\Content\ContentPrepareEvent;
use Joomla\Event\SubscriberInterface;
use JLTRY\Component\JOGallery\Administrator\Model\JOGalleryModel;
use JLTRY\Component\JOGallery\Administrator\Helper\JParametersHelper;
use JLTRY\Component\JOGallery\Administrator\Helper\JOGalleryHelper;
use JLTRY\Component\JOGallery\Administrator\Helper\JODirectoryHelper;
use JLTRY\Component\JOGallery\Administrator\Helper\JOGalleryCategoryHelper;
use JLTRY\Component\JOGallery\Administrator\Helper\FoldergroupHelper;


// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

define('PF_REGEX_JDIRECTORYI_PATTERN', "#{jdirectory (.*?)}#s");
/**
* Directory Content Plugin
*
*/
class JODirectory extends CMSPlugin implements SubscriberInterface
{
    protected static $_ID = 0;
/**
    * Constructor
    *
    * @param object $subject The object to observe
    * @param object $params The object that holds the plugin parameters
    */
    function __construct(&$subject, $params)
    {
        parent::__construct($subject, $params);
        JODirectory::$_ID++;
    }



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
 
     /**
     * The content plugin that inserts the galleries into content items
     *
     * @param    Event $event The event object
     *
     * @return true if anything has been inserted into the content object
     */
    public function onContentPrepare(ContentPrepareEvent $event)
    {
        //Escape fast
        if (!$this->getApplication()->isClient('site')) {
            return;
        }
        //Escape fast
        if (!$this->params->get('enabled', 1)) {
            return true;
        }
        // use this format to get the arguments for both Joomla 4 and Joomla 5
        // In Joomla 4 a generic Event is passed
        // In Joomla 5 a concrete ContentPrepareEvent is passed
        [$context, $article, $params, $page] = array_values($event->getArguments());


        if (strpos($article->text, '{jdirectory') === false) {
            return true;
        }
        preg_match_all(PF_REGEX_JDIRECTORYI_PATTERN, $article->text, $matches);
// Number of plugins
        $count = count($matches[0]);
         // plugin only processes if there are any instances of the plugin in the text
        if ($count) {
            for ($i = 0; $i < $count; $i++) {
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
                    $p_content = JODirectoryHelper::display(JODirectory::$_ID, $_result);
                    $article->text = str_replace("{jdirectory " . $matches[1][$i] . "}", $p_content, $article->text);
                }
            }
        }
        return true;
    }
}
