<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_jogallery
 *
 * @copyright   Copyright (C) 2015 - 2025 Open Source Matters. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */ 

namespace JLTRY\Component\JOGallery\Administrator\View\JOGalleries;

use JLTRY\Component\JOGallery\Administrator\Helper\JOGalleryHelper;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\String\StringHelper;


// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects


/**
 * JGalleries View
 *
 * @since  0.0.1
 */
class HtmlView extends BaseHtmlView
{
    /**
     * Display the Gelleries view
     *
     * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
     *
     * @return  void
     */
    function display($tpl = null)
    {
        // Get application
        $app = Factory::getApplication();
        $context = "jogallery.list.admin.jogallery";
        // Get data from the model
        $model = $this->getModel();
        $this->items		= $this->get('Items');
        $this->pagination	= $this->get('Pagination');
        $this->state		= $this->get('State');
        $this->filter_order	= $app->getUserStateFromRequest($context.'filter_order', 'filter_order', 'directory', 'cmd');
        $this->filter_order_Dir = $app->getUserStateFromRequest($context.'filter_order_Dir', 'filter_order_Dir', 'asc', 'cmd');
        $this->filterForm    	= $this->get('FilterForm');
        $this->activeFilters 	= $this->get('ActiveFilters');

        // What Access Permissions does this user have? What can (s)he do?
        $this->canDo = JOGalleryHelper::getActions();

        // Check for errors.
        if (is_array($errors) && count($errors = $this->get('Errors')))
        {
            Factory::getApplication()->enqueueMessage( implode('<br />', $errors),'error');
            return false;
        }

        // Set the submenu
        JOGalleryHelper::addSubmenu('jogalleries');

        // Set the toolbar and number of found items
        $this->addToolBar();

        // Display the template
        parent::display($tpl);

        // Set the document
        $document = Factory::getDocument();
        $document->setTitle(Text::_('COM_JOGALLERY_ADMINISTRATION'));
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
        $title = Text::_('COM_JOGALLERY_MANAGER_JGALLERIES');

        if ($this->pagination->total)
        {
            $title .= "<span style='font-size: 0.5em; vertical-align: middle;'>(" . $this->pagination->total . ")</span>";
        }

        ToolBarHelper::title($title, 'jogallery');

        if ($this->canDo->get('core.create')) 
        {
            ToolBarHelper::addNew('jogallery.add', 'JTOOLBAR_NEW');
        }
        if ($this->canDo->get('core.edit')) 
        {
            ToolBarHelper::editList('jogallery.edit', 'JTOOLBAR_EDIT');
        }
        if ($this->canDo->get('core.delete')) 
        {
            ToolBarHelper::deleteList('', 'jogalleries.delete', 'JTOOLBAR_DELETE');
        }
        if ($this->canDo->get('core.edit')) 
        {
            ToolBarHelper::custom('jogallery.genthumbs', 'publish.png', 'publish_f2.png','JTOOLBAR_THUMBS', true);
        }
        if ($this->canDo->get('core.edit')) 
        {
            ToolBarHelper::custom('jogallery.genrecthumbs', 'publish.png', 'publish_f2.png','JTOOLBAR_GENRECTHUMBS', true);
        }
        if ($this->canDo->get('core.edit')) 
        {
            ToolBarHelper::custom('jogallery.comments', 'publish.png', 'publish_f2.png','JTOOLBAR_COMMENTS', true);
        }
        if ($this->canDo->get('core.admin')) 
        {
            ToolBarHelper::divider();
            ToolBarHelper::preferences('com_jogallery');
        }
    }
}