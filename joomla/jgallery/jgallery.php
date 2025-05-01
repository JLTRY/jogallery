<?php
/**
 * 
* @copyright Copyright (C) 2012 Jean-Luc TRYOEN. All rights reserved.
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
use JLTRY\Component\JGallery\Administrator\Helper\FolderGroupHelper;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Factory;
use Joomla\Event\Event;
use Joomla\Event\SubscriberInterface;

// Check to ensure this file is included in Joomla!
defined( '_JEXEC' ) or die( 'Restricted access' );

define('PF_REGEX_JGALLERYI_PATTERN', "#{jgallery (.*?)}#s");



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
		$app	 = Factory::getApplication();
		$input   = $app->getInput();
		if ($input->get($param) !== null) {
			$this->{$name} = $input->get($param);
			$found = true;
		}
		return $found;
	}


	/**
	 * The content plugin that inserts the galleries into content items
	 *
	 * @param	Event $event The event object
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
		preg_match_all(PF_REGEX_JGALLERYI_PATTERN, $row->text, $matches);
		// Number of plugins
		$count = count($matches[0]);
		 // plugin only processes if there are any instances of the plugin in the text
		if ($count) {
			for ($i = 0; $i < $count; $i++)
			{
				$_params = array();
				if ($this->getparam('page', 'page')) {
					$_params['page'] = $this->page;
				}
				if (@$matches[1][$i]) {
					$inline_params = $matches[1][$i];
					$pairs = explode('|', trim($inline_params));
					foreach ($pairs as $pair) {
						$pos = strpos($pair, "=");
						$key = substr($pair, 0, $pos);
						$value = substr($pair, $pos + 1);
						$_params[$key] = $value;
					}
					$_params['rootdir'] = JParametersHelper::getrootdir();
					if (array_key_exists('img', $_params)) {
						$p_content = JGalleryHelper::display($_params);
					}elseif (array_key_exists('browse', $_params)) {
						$p_content = JDirectoryHelper::display(1, $_params);
					} elseif (array_key_exists('group', $_params)){
						$id = $_params['group'];
						$_params['id'] = $id;
						$p_content = FolderGroupHelper::display($_params);
					}
					else {
						$p_content = "<!-- display -->".
									JGalleryHelper::display($_params) .
									"<!-- display end -->";
					}
					$row->text = str_replace("{jgallery " . $matches[1][$i] . "}", $p_content, $row->text);
				}
			}
		}
		return true; 
	}
}
