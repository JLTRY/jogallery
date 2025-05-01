<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_jgallery
 *
 * @copyright   Copyright (C) 2005 - 2015 JL Tryoen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace JLTRY\Component\JGallery\Administrator\View\FolderGroup;

use JLTRY\Component\JGallery\Administrator\Helper\JParametersHelper;
use JLTRY\Component\JGallery\Administrator\Helper\JGalleryHelper;
use JLTRY\Component\JGallery\Administrator\Model\FolderGroupModel;
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

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * JGallery View
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
			$modelGallery = new FolderGroupModel;
			$modelGallery->setState("folder.id", $this->id);
			$this->item = $modelGallery->getItem($this->id);
		}
		$this->form = $this->get('Form');

		// What Access Permissions does this user have? What can (s)he do?
		if (isset($this->item))
			$this->canDo = JGalleryHelper::getActions('foldergroup', $this->item->id);
		else	
			$this->canDo = JGalleryHelper::getActions('foldergroup');
		// Check for errors.
		if (count($errors = $this->get('Errors')))
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

		ToolbarHelper::title($isNew ? Text::_('COM_JGALLERY_CREATE_NEW_GROUP')
									 : Text::_('COM_JGALLERY_GROUP_EDIT'), 'FolderGroup');
		// Build the actions for new and existing records.
		if ($isNew)
		{
			// For new records, check the create permission.
			if ($this->canDo->get('core.create')) 
			{
				ToolbarHelper::apply('folderGroup.apply', 'Toolbar_APPLY');
				ToolbarHelper::save('folderGroup.save', 'Toolbar_SAVE');
				ToolbarHelper::custom('folderGroup.save2new', 'save-new.png', 'save-new_f2.png',
									   'Toolbar_SAVE_AND_NEW', false);
			}
			ToolbarHelper::cancel('folderGroup.cancel', 'Toolbar_CANCEL');
		}
		else
		{
			if ($this->canDo->get('core.edit'))
			{
				// We can save the new record
				ToolbarHelper::apply('foldergroup.apply', 'Toolbar_APPLY');
				ToolbarHelper::save('foldergroup.save', 'Toolbar_SAVE');
 
				// We can save this record, but check the create permission to see
				// if we can return to make a new one.
				if ($this->canDo->get('core.create')) 
				{
					ToolbarHelper::custom('foldergroup.save2new', 'save-new.png', 'save-new_f2.png',
										   'Toolbar_SAVE_AND_NEW', false);
				}
			}
			if ($this->canDo->get('core.create')) 
			{
				ToolbarHelper::custom('folderGroup.save2copy', 'save-copy.png', 'save-copy_f2.png',
									   'Toolbar_SAVE_AS_COPY', false);
			}
			ToolbarHelper::cancel('foldergroup.cancel', 'Toolbar_CLOSE');
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
		$document->setTitle($isNew ? Text::_('COM_JGALLERY_FOLDERGROUP_CREATING')
									: Text::_('COM_JGALLERY_FOLDERGROUP_EDITING'));
		Text::script('com_jgallery_JGallery_ERROR_UNACCEPTABLE');
	}
}
