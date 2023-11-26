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
use Joomla\CMS\MVC\View\HtmlView as JViewLegacy;
use Joomla\CMS\Factory as JFactory;

JLoader::import('components.com_jgallery.helpers.jparameters', JPATH_ADMINISTRATOR);
/**
 * HTML View class for the JGalleryComponent
 *
 * @since  0.0.1
 */
class JGalleryViewJGallery extends JViewLegacy
{
	static $_id;
	function getparam($name, $param) {
		$found = false;
		$app     = JFactory::getApplication();
		$input   = $app->getInput();
		$params  = $app->getParams();
		if ($params->get($param) !== null) {
			$this->{$name} = $params->get($param);
			$found = true;
		} elseif ($input->get($param) !== null) {
			$this->{$name} = $input->get($param);
			$found = true;
		}
		return $found;
	}
	/**
	 * Display the Gallery view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 */
	function display($tpl = null)
	{
		$this->item = $this->get('Item');
		if ($this->item){
			$this->directory = $this->item->directory;
		}
		else {
			if (!$this->getparam('directory', 'directory'))
			{
				if ($this->getparam('directory64', 'directory64')) {
					$this->directory = base64_decode($this->directory64);
				}
			}
		}
		$this->rootdir = JParametersHelper::getrootdir();
		if (!is_string($this->directory))
		{
			$errors = array("directory not defined");
		} else
		{
			$errors = $this->get('Errors');
		}
		$this->getparam('image', 'image');
		$this->page= -1;
		$this->getparam('page', 'page');
		if ($this->getparam('image64', 'image64')){
			$this->image = base64_decode($this->image64);
		}
		$this->parent = false;
		$this->getparam('parent', 'parent');	
		// Check for errors.
		if (count($errors))
		{
			JLog::add(implode('<br />', $errors), JLog::WARNING, 'jerror');
			return false;
		}
		// Display the view
		parent::display($tpl);
	}
}
