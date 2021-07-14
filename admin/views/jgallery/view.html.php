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
JLoader::import('components.com_jgallery.helpers.jparameters', JPATH_ADMINISTRATOR);
use Joomla\CMS\Log\Log;
/**
 * JGallery View
 *
 * @since  0.0.1
 */
class JGalleryViewJGallery extends JViewLegacy
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
	 * Display the Hello World view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 */
	public function display($tpl = null)
	{
		// Get the Data		
		$this->item = $this->get('Item');
		if (($this->item)&& ($this->directory)){
			$this->directory = $this->item->directory;
		}
		else {
			if (!$this->getparam('directory', 'directory'))
			{
				if ($this->getparam('directory64', 'directory64')) {
					$this->directory = utf8_decode(base64_decode($this->directory64));
				}
			}
		}
		$this->getparam('force', 'force');
		if ($this->getparam('image64', 'image64')){
			$this->image = base64_decode($this->image64);
		}
		
		$this->rootdir = "images/" . JParametersHelper::get('rootdir');
		$this->script = $this->get('Script');
		$form  = $this->form = $this->get('Form');		
		if ($form) {
			$form->setFieldAttribute("directory", "directory", $this->rootdir);
		}
		// What Access Permissions does this user have? What can (s)he do?
		$this->canDo = JGalleryHelper::getActions($this->item->id);
		
		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode('<br />', $errors));

			return false;
		}

		// Set the toolbar
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
		$input = JFactory::getApplication()->input;

		// Hide Joomla Administrator Main menu
		$input->set('hidemainmenu', true);

		$isNew = ($this->item->id == 0);
		Log::add('my error message' . $this->getLayout(), Log::ERROR, 'my-error-category');
		JToolBarHelper::title($isNew ? JText::_('COM_JGALLERY_MANAGER_JGALLERY_NEW')
		                             : JText::_('COM_JGALLERY_MANAGER_JGALLERY_EDIT'), 'jgallery');
		// Build the actions for new and existing records.
		if ($isNew)
		{
			// For new records, check the create permission.
			if ($this->getLayout() != "thumbs" && $this->canDo->get('core.create')) 
			{
				JToolBarHelper::apply('jgallery.apply', 'JTOOLBAR_APPLY');
				JToolBarHelper::save('jgallery.save', 'JTOOLBAR_SAVE');
				JToolBarHelper::custom('jgallery.save2new', 'save-new.png', 'save-new_f2.png',
				                       'JTOOLBAR_SAVE_AND_NEW', false);
			}
			JToolBarHelper::cancel('jgallery.cancel', 'JTOOLBAR_CANCEL');
		}
		else
		{
			

			
			if ($this->getLayout() && $this->canDo->get('core.edit'))
			{
				// We can save the new record
				JToolBarHelper::apply('jgallery.apply', 'JTOOLBAR_APPLY');
				JToolBarHelper::save('jgallery.save', 'JTOOLBAR_SAVE');
 
				// We can save this record, but check the create permission to see
				// if we can return to make a new one.
				if ($this->canDo->get('core.create')) 
				{
					JToolBarHelper::custom('jgallery.save2new', 'save-new.png', 'save-new_f2.png',
					                       'JTOOLBAR_SAVE_AND_NEW', false);
				}
			}
			if ($this->layout == "default" && $this->canDo->get('core.create')) 
			{
				JToolBarHelper::custom('jgallery.save2copy', 'save-copy.png', 'save-copy_f2.png',
				                       'JTOOLBAR_SAVE_AS_COPY', false);
			}
			JToolBarHelper::cancel('jgallery.cancel', 'JTOOLBAR_CLOSE');
		}
	}
	/**
	 * Method to set up the document properties
	 *
	 * @return void
	 */
	protected function setDocument() 
	{
		$isNew = ($this->item->id == 0);
		$document = JFactory::getDocument();
		$document->setTitle($isNew ? JText::_('COM_JGALLERY_JGALLERY_CREATING')
		                           : JText::_('COM_JGALLERY_JGALLERY_EDITING'));
		$document->addScript(JURI::root() . $this->script);
		$document->addScript(JURI::root() . "/administrator/components/com_jgallery"
		                                  . "/views/jgallery/submitbutton.js");
		JText::script('COM_JGALLERY_JGALLERY_ERROR_UNACCEPTABLE');
	}
}
