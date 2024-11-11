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

use Joomla\CMS\Uri\Uri; 
use Joomla\CMS\MVC\View\HtmlView as JViewLegacy;
use Joomla\CMS\Factory as JFactory;

JLoader::import('components.com_jgallery.helpers.jparameters', JPATH_ADMINISTRATOR);
JLoader::import('components.com_jgallery.helpers.jgallerycategory', JPATH_ADMINISTRATOR);

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
		$canview = false;
		//$this->item = $this->get('Item');
		$catid = -1;
		$this->id = -1;
		if ($this->item == null) {
			$app     = JFactory::getApplication();
			$jinput   = $app->getInput();
			$id = $jinput->getString('id', null);
			if ($id !== null) {
				$model = new JGalleryModelJGallery;
				$model->setState('jgallery.id', (int)$id);
				$model->getState();
				$this->setModel($model);
				$this->item = $model->getItem();
				$catid = $this->item->catid;
				$this->id = $id;
			}
		}
		if (!$this->getparam('directory', 'directory'))
		{
			if ($this->getparam('directory64', 'directory64')) {
				$this->directory = utf8_decode(base64_decode($this->directory64));
			}
		}
		if ($this->item && !$this->directory) {
			$this->directory = $this->item->directory;
		}
		$user = JFactory::getApplication()->getSession()->get('user');
		if (($user->id != 0) && (($catid == -1) || JGalleryCategoryHelper::usercanviewcategory($user, $catid)))
		{
			$canview = true;
		}else {
			JFactory::getLanguage()->load('com_content', JPATH_SITE, null ,true);
			echo "<jdoc:include type=\"message\" />";
			JFactory::getApplication()->enqueueMessage(JText::_('COM_CONTENT_ERROR_LOGIN_TO_VIEW_ARTICLE'), 'error');
			$document = JFactory::getDocument();
			$renderer = $document->loadRenderer('module');
			$Module = JModuleHelper::getModule('mod_login');
			$uri = Uri::getInstance();
			$Module->params = "return=" . base64_encode($uri->toString()); 
			echo $renderer->render($Module);
		}
		if ($canview) {
			$this->rootdir = JParametersHelper::getrootdir();
			if (!is_string($this->directory))
			{
				$errors = array("directory not defined");
			} else {
				$errors = $this->get('Errors');
			}
			$this->getparam('image', 'image');
			$this->page= -1;
			$this->getparam('page', 'page');
			if ($this->getparam('image64', 'image64')){
				$this->image = utf8_decode(base64_decode($this->image64));
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
}
