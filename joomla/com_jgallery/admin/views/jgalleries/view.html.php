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
use Joomla\CMS\Language\Text as JText;
use Joomla\CMS\Toolbar\ToolbarHelper as JToolbarHelper;

/**
 * JGallerys View
 *
 * @since  0.0.1
 */
class JGalleryViewJGalleries extends JViewLegacy
{
	/**
	 * Display the Hello World view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 */
	function display($tpl = null)
	{
		
		// Get application
		$app = JFactory::getApplication();
		$context = "jgallery.list.admin.jgallery";
		// Get data from the model
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->state		= $this->get('State');
		$this->filter_order	= $app->getUserStateFromRequest($context.'filter_order', 'filter_order', 'directory', 'cmd');
		$this->filter_order_Dir = $app->getUserStateFromRequest($context.'filter_order_Dir', 'filter_order_Dir', 'asc', 'cmd');
		$this->filterForm    	= $this->get('FilterForm');
		$this->activeFilters 	= $this->get('ActiveFilters');

		// What Access Permissions does this user have? What can (s)he do?
		$this->canDo = JGalleryHelper::getActions();

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			//JError::raiseError(500, implode('<br />', $errors));

			//return false;
		}

		// Set the submenu
		JGalleryHelper::addSubmenu('jgalleries');

		// Set the toolbar and number of found items
		$this->addToolBar();

		// Display the template
		parent::display($tpl);

		// Set the document
		$document = JFactory::getDocument();
		$document->setTitle(JText::_('COM_JGALLERY_ADMINISTRATION'));
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
		$title = JText::_('COM_JGALLERY_MANAGER_JGALLERIES');

		if ($this->pagination->total)
		{
			$title .= "<span style='font-size: 0.5em; vertical-align: middle;'>(" . $this->pagination->total . ")</span>";
		}

		JToolBarHelper::title($title, 'jgallery');

		if ($this->canDo->get('core.create')) 
		{
			JToolBarHelper::addNew('jgallery.add', 'JTOOLBAR_NEW');
		}
		if ($this->canDo->get('core.edit')) 
		{
			JToolBarHelper::editList('jgallery.edit', 'JTOOLBAR_EDIT');
		}
		if ($this->canDo->get('core.delete')) 
		{
			JToolBarHelper::deleteList('', 'jgalleries.delete', 'JTOOLBAR_DELETE');
		}
		if ($this->canDo->get('core.edit')) 
		{
			JToolbarHelper::custom('jgallery.genthumbs', 'publish.png', 'publish_f2.png','JTOOLBAR_THUMBS', true);
		}
        if ($this->canDo->get('core.edit')) 
		{
			JToolbarHelper::custom('jgallery.genrecthumbs', 'publish.png', 'publish_f2.png','JTOOLBAR_GENRECTHUMBS', true);
		}
		if ($this->canDo->get('core.edit')) 
		{
			JToolbarHelper::custom('jgallery.comments', 'publish.png', 'publish_f2.png','JTOOLBAR_COMMENTS', true);
		}
		if ($this->canDo->get('core.admin')) 
		{
			JToolBarHelper::divider();
			JToolBarHelper::preferences('com_jgallery');
		}
	}
}