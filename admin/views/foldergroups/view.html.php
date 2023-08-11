<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_jcoaching
 *
 * @copyright   Copyright (C) 2005 - 2015 JL Tryoen, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * JCoachings View
 *
 * @since  0.0.1
 */
class JGalleryViewfoldergroups extends JViewLegacy
{
	/**
	 * Display the Calendars view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 */
	function display($tpl = null)
	{
		
		// Get application
		$app = JFactory::getApplication();
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
		if (count($errors = $this->get('Errors')))
		{
			JFactory::getApplication()->enqueueMessage( implode('<br />', $errors),'error');

			return false;
		}
        $lang = JFactory::getLanguage();
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
		$this->setDocument();
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
		$title = JText::_('COM_JGALLERY_MANAGER_GROUPS');

		if ($this->pagination->total)
		{
			$title .= "<span style='font-size: 0.5em; vertical-align: middle;'>(" . $this->pagination->total . ")</span>";
		}

		JToolBarHelper::title($title, 'foldergroup');

		if ($this->canDo->get('core.create')) 
		{
			JToolBarHelper::addNew('foldergroup.add', 'JTOOLBAR_NEW');
		}
		if ($this->canDo->get('core.edit')) 
		{
			JToolBarHelper::editList('foldergroup.edit', 'JTOOLBAR_EDIT');
		}
		if ($this->canDo->get('core.delete')) 
		{
			JToolBarHelper::deleteList('', 'foldergroups.delete', 'JTOOLBAR_DELETE');
		}
		if ($this->canDo->get('core.admin')) 
		{
			JToolBarHelper::divider();
			JToolBarHelper::preferences('com_jcoaching');
		}
	}
	/**
	 * Method to set up the document properties
	 *
	 * @return void
	 */
	protected function setDocument() 
	{
		$document = JFactory::getDocument();
		$document->setTitle(JText::_('COM_JGALLERY_ADMINISTRATION'));
	}
}