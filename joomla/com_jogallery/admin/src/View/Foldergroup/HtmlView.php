<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_jogallery
 *
 * @copyright   Copyright (C) 2005 - 2015 JL Tryoen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace JLTRY\Component\JOGallery\Administrator\View\Foldergroup;

use JLTRY\Component\JOGallery\Administrator\Helper\JParametersHelper;
use JLTRY\Component\JOGallery\Administrator\Helper\JOGalleryHelper;
use JLTRY\Component\JOGallery\Administrator\Model\FoldergroupModel;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\String\StringHelper;
use Joomla\CMS\Document\Document;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * JOGallery View
 *
 * @since  0.0.1
 */
class HtmlView extends BaseHtmlView
{
    protected $form;
    protected $item;
    protected $script;
    protected $canDo;
    
    function getparam($name, $param) {
        $found = false;
        $app     = Factory::getApplication();
        $input   = $app->getInput();
        if ($input->get($param) !== null) {
            $this->{$name} = $input->get($param);
            $found = true;
        } else  if (method_exists($app, 'getParams')) {
            $params  = $app->getParams();
            if ($params->get($param) !== null) {
                $this->{$name} = $params->get($param);
                $found = true;
            }
        } 
        return $found;
    }



    /**
     * Display the Calendar view
     *
     * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
     *
     * @return  void
     */
    public function display($tpl = null)
    {
        // Get the Data
        $this->item = $this->get('Item');
        $this->getparam('id', 'id');
        if ($this->id) {
            $modelGallery = new FoldergroupModel;
            $modelGallery->setState("folder.id", $this->id);
            $this->item = $modelGallery->getItem($this->id);
        }
        $this->media = "ALL";
        $this->getparam('media', 'media');
        $this->form = $this->get('Form');

        // What Access Permissions does this user have? What can (s)he do?
        if (isset($this->item))
            $this->canDo = JOGalleryHelper::getActions('foldergroup', $this->item->id);
        else	
            $this->canDo = JOGalleryHelper::getActions('foldergroup');
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

        // Set the toolbar
        $this->addToolBar();

        // Display the template
        parent::display($tpl);

        // Set the document
        $this->setDocument(Factory::getDocument());
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
        $input = Factory::getApplication()->input;

        // Hide Joomla Administrator Main menu
        $input->set('hidemainmenu', true);

        $isNew = ($this->item->id == 0);

        ToolbarHelper::title($isNew ? Text::_('COM_JOGALLERY_CREATE_NEW_GROUP')
                                     : Text::_('COM_JOGALLERY_GROUP_EDIT'), 'Foldergroup');
        // Build the actions for new and existing records.
        if ($isNew)
        {
            // For new records, check the create permission.
            if ($this->canDo->get('core.create')) 
            {
                ToolbarHelper::apply('foldergroup.apply', 'JTOOLBAR_APPLY');
                ToolbarHelper::save('foldergroup.save', 'JTOOLBAR_SAVE');
                ToolbarHelper::custom('foldergroup.save2new', 'save-new.png', 'save-new_f2.png',
                                       'JTOOLBAR_SAVE_AND_NEW', false);
            }
            ToolbarHelper::cancel('foldergroup.cancel', 'JTOOLBAR_CANCEL');
        }
        else
        {
            if ($this->canDo->get('core.edit'))
            {
                // We can save the new record
                ToolbarHelper::apply('foldergroup.apply', 'JTOOLBAR_APPLY');
                ToolbarHelper::save('foldergroup.save', 'JTOOLBAR_SAVE');
 
                // We can save this record, but check the create permission to see
                // if we can return to make a new one.
                if ($this->canDo->get('core.create')) 
                {
                    ToolbarHelper::custom('foldergroup.save2new', 'save-new.png', 'save-new_f2.png',
                                           'JTOOLBAR_SAVE_AND_NEW', false);
                }
            }
            if ($this->canDo->get('core.create')) 
            {
                ToolbarHelper::custom('foldergroup.save2copy', 'save-copy.png', 'save-copy_f2.png',
                                       'JTOOLBAR_SAVE_AS_COPY', false);
            }
            ToolbarHelper::cancel('foldergroup.cancel', 'JTOOLBAR_CLOSE');
        }
    }
    /**
     * Method to set up the document properties
     *
     * @return void
     */
    public function setDocument(Document $document): void
    {
        $isNew = ($this->item->id == 0);
        $document->setTitle($isNew ? Text::_('COM_JOGALLERY_FOLDERGROUP_CREATING')
                                    : Text::_('COM_JOGALLERY_FOLDERGROUP_EDITING'));
        Text::script('com_jogallery_JOGallery_ERROR_UNACCEPTABLE');
    }
}
