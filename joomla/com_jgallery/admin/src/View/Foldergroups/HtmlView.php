<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_jgallery
 *
 * @copyright   Copyright (C) 2005 - 2015 JL Tryoen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */


namespace JLTRY\Component\JGallery\Administrator\View\Foldergroups;

use JLTRY\Component\JGallery\Administrator\Helper\JGalleryHelper;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\String\StringHelper;

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
/*
 * JFoldergroups View
 *
 * @since  0.0.1
 */
class HtmlView extends BaseHtmlView
{
	/**
	 * Display the Foldergroups view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 */
	function display($tpl = null)
	{
		
		// Get application
		$app = Factory::getApplication();
		$context = "jgallery.list.admin.foldergroups";
		// Get data from the model
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->state		= $this->get('State');
		$this->filter_order	= $app->getUserStateFromRequest($context.'filter_order', 'filter_order', 'name', 'cmd');
		$this->filter_order_Dir = $app->getUserStateFromRequest($context.'filter_order_Dir', 'filter_order_Dir', 'asc', 'cmd');
		$this->filterForm    	= $this->get('FilterForm');
		$this->activeFilters 	= $this->get('ActiveFilters');

		// What Access Permissions does this user have? What can (s)he do?
		$this->canDo = JGalleryHelper::getActions('foldergroup');

		// Check for errors.
		if (is_array($errors) && count($errors = $this->get('Errors')))
		{
			Factory::getApplication()->enqueueMessage( implode('<br />', $errors),'error');
			return false;
		}
        $lang = Factory::getLanguage();
        $extensions = ['com_installer', 'com_media', 'com_menus', 'com_content'];
        $base_dir =  JPATH_ADMINISTRATOR;
        $language_tag = $lang->getTag();
        $reload = true;
        foreach ($extensions as $extension ) { 
            $lang->load($extension, $base_dir, $language_tag, $reload);
        }
		// Set the submenu
		JGalleryHelper::addSubmenu('foldergroups');

		// Set the toolbar and number of found items
		$this->addToolBar();

		// Display the template
		parent::display($tpl);

		// Set the document
		$document = Factory::getDocument();
		$document->setTitle(Text::_('COM_JGALLERY_ADMINISTRATION'));;
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function addToolBar()
	{
		$title = Text::_('COM_JGALLERY_MANAGER_GROUPS');

		if ($this->pagination->total)
		{
			$title .= "<span style='font-size: 0.5em; vertical-align: middle;'>(" . $this->pagination->total . ")</span>";
		}

		ToolbarHelper::title($title, 'foldergroup');

		if ($this->canDo->get('core.create')) 
		{
			ToolbarHelper::addNew('foldergroup.add', 'JTOOLBAR_NEW');
		}
		if ($this->canDo->get('core.edit')) 
		{
			ToolbarHelper::editList('foldergroup.edit', 'JTOOLBAR_EDIT');
		}
		if ($this->canDo->get('core.delete')) 
		{
			ToolbarHelper::deleteList('', 'foldergroups.delete', 'JTOOLBAR_DELETE');
		}
		if ($this->canDo->get('core.admin')) 
		{
			ToolbarHelper::divider();
			ToolbarHelper::preferences('com_jgallery');
		}
	}

}