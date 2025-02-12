<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_jgallery
 *
 * @copyright   Copyright (C) 2005 - 2015 JL Tryoen, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * JGallery View
 *
 * @since  0.0.1
 */
class JGalleryViewFolderGroup extends JViewLegacy
{
	protected $form;
	protected $item;
	protected $script;
	protected $canDo;
    
    function getparam($name, $param) {
		$found = false;
		$app     = JFactory::getApplication();
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
			$factory = JFactory::getApplication()->bootComponent('com_jgallery')->getMVCFactory();
			$modelGallery = $factory->createModel('FolderGroup', 'JGalleryModel');
			$modelGallery->setState("folder.id", $this->id);
			$this->item = $modelGallery->getItem($this->id);
		}
		$this->form = $this->get('Form');
		
		$this->script = $this->get('Script');

		// What Access Permissions does this user have? What can (s)he do?
		if (isset($this->item))
			$this->canDo = JGalleryHelper::getActions('foldergroup', $this->item->id);
		else	
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

		// Set the toolbar
		$this->addToolBar();

        
		// Display the template
		parent::display($tpl);

		// Set the document
		$this->setDocument(JFactory::getDocument());
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
		$input = JFactory::getApplication()->input;

		// Hide Joomla Administrator Main menu
		$input->set('hidemainmenu', true);

		$isNew = ($this->item->id == 0);

		JToolBarHelper::title($isNew ? JText::_('COM_JGALLERY_CREATE_NEW_GROUP')
		                             : JText::_('COM_JGALLERY_GROUP_EDIT'), 'FolderGroup');
		// Build the actions for new and existing records.
		if ($isNew)
		{
			// For new records, check the create permission.
			if ($this->canDo->get('core.create')) 
			{
				JToolBarHelper::apply('folderGroup.apply', 'JTOOLBAR_APPLY');
				JToolBarHelper::save('folderGroup.save', 'JTOOLBAR_SAVE');
				JToolBarHelper::custom('folderGroup.save2new', 'save-new.png', 'save-new_f2.png',
				                       'JTOOLBAR_SAVE_AND_NEW', false);
			}
			JToolBarHelper::cancel('folderGroup.cancel', 'JTOOLBAR_CANCEL');
		}
		else
		{
			if ($this->canDo->get('core.edit'))
			{
				// We can save the new record
				JToolBarHelper::apply('foldergroup.apply', 'JTOOLBAR_APPLY');
				JToolBarHelper::save('foldergroup.save', 'JTOOLBAR_SAVE');
 
				// We can save this record, but check the create permission to see
				// if we can return to make a new one.
				if ($this->canDo->get('core.create')) 
				{
					JToolBarHelper::custom('foldergroup.save2new', 'save-new.png', 'save-new_f2.png',
					                       'JTOOLBAR_SAVE_AND_NEW', false);
				}
			}
			if ($this->canDo->get('core.create')) 
			{
				JToolBarHelper::custom('folderGroup.save2copy', 'save-copy.png', 'save-copy_f2.png',
				                       'JTOOLBAR_SAVE_AS_COPY', false);
			}
			JToolBarHelper::cancel('foldergroup.cancel', 'JTOOLBAR_CLOSE');
		}
	}
	/**
	 * Method to set up the document properties
	 *
	 * @return void
	 */
	public function setDocument(Joomla\CMS\Document\Document  $document):void
	{
		$isNew = ($this->item->id == 0);
		$document->setTitle($isNew ? JText::_('COM_JGALLERY_FOLDERGROUP_CREATING')
		                           : JText::_('COM_JGALLERY_FOLDERGROUP_EDITING'));
		$document->addScript(JURI::root() . $this->script);
		$document->addScript(JURI::root() . "/administrator/components/com_jgallery"
		                                  . "/views/foldergroup/submitbutton.js");
		JText::script('COM_JCOACHING_JCOACHING_ERROR_UNACCEPTABLE');
	}
}
