<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_jgallery
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license	 GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Uri\Uri;
JLoader::import('components.com_jgallery.helpers.jparameters', JPATH_ADMINISTRATOR);
JLoader::import('components.com_jgallery.helpers.jfoldergroup', JPATH_ADMINISTRATOR);
JLoader::import('components.com_jgallery.helpers.jgallerycategory', JPATH_ADMINISTRATOR);

/**
 * HTML View class for the JGalleryComponent
 *
 * @since  0.0.1
 */
class JGalleryViewFolderGroup extends JViewLegacy
{
	static $_id;
	function getparam($name, $param) {
		$found = false;
		$app	 = JFactory::getApplication();
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
		// Assign data to the view
		$this->id = -1;
		$this->getparam('parent', 'parent');
		if ($this->getparam('id', 'id')) {
			$model = new JGalleryModelFolderGroup;
			$model->setstate('folder.id', $this->id);
			$this->item = $model->getItem();
		}
		$this->getparam('tmpl', 'tmpl');
		$this->directory = null;
		if (!$this->getparam('directory', 'directory'))
		{
			if ($this->getparam('directory64', 'directory64')) {
				$this->directory = utf8_decode(base64_decode($this->directory64));
			}
		}
		$this->type = "directories";
		$this->getparam('type', 'type');
		if ($this->item != null){
			$this->folders = $this->item->folders;
			$this->name = $this->item->name;
			$this->id = $this->item->id;
			$catid = $this->item->catid;
		}
		$user = JFactory::getApplication()->getSession()->get('user');
		if (($catid == -1) || (($user!= null) && JGalleryCategoryHelper::usercanviewcategory($user, $catid)))
		{
			$canview = true;
		} else {
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
			// Display the view
			parent::display($tpl);
		}
	}
}
