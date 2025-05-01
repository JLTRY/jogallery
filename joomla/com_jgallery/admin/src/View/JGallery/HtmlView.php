<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_jgallery
 *
 * @copyright   Copyright (C) 2015 - 2025 Open Source Matters. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
 
namespace JLTRY\Component\JGallery\Administrator\View\JGallery;

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

use JLTRY\Component\JGallery\Administrator\Model\JGalleryModel;
use JLTRY\Component\JGallery\Administrator\Helper\JParametersHelper;
use JLTRY\Component\JGallery\Administrator\Helper\JGalleryHelper;
use Joomla\CMS\Log\Log;
use Joomla\CMS\MVC\Factory\MVCFactory;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Document\Document;



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
	protected $id;

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
	
	public static function authorise($what)
	{
		return Factory::getUser()->authorise($what, "com_jgallery");
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
		$this->id = 0;
		$this->getparam('id', 'id');
		if (!$this->id) {
			$this->getparam('cid', 'cid');
			if (is_array($this->cid)) {
				$this->id = $this->cid[0];
			}
		}
		if ($this->id) {
			$modelGallery = new JGalleryModel;
			$modelGallery->setState("jgallery.id", $this->id);
			$this->item = $modelGallery->getItem($this->id);
		}
		if (($this->item)&& ($this->item->directory)){
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
		$this->small_width = $this->large_width = 0;
		$this->getparam('small_width', 'small_width');
		$this->getparam('large_width', 'large_width');
		if ($this->getparam('image64', 'image64')){
			$this->image = base64_decode($this->image64);
		}
		if ($this->getparam('imageall', 'imageall')){
			$this->imageall = true;
		}
		$this->rootdir = JParametersHelper::getrootdir();
		$this->script = $this->get('Script');
		$form  = $this->form = $this->get('Form');
		if ($form) {
			$form->setFieldAttribute("directory", "directory", $this->rootdir);
		}
		// What Access Permissions does this user have? What can (s)he do?
		$this->canDo = $this->item && JGalleryHelper::getActions($this->item->id);
		
		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode('<br />', $errors));

			return false;
		}

		// Set the toolbar
		$this->addToolBar();
		$language = Factory::getLanguage();
		$language->load('joomla', JPATH_ADMINISTRATOR);
		$language->load('com_JGALLERY', JPATH_ADMINISTRATOR, null, true);
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
		$isNew = (($this->item) && ($this->item->id == 0));
		switch ($this->getLayout())
		{
			case "thumbs":
			case "recthumbs":
				$id = 'COM_JGALLERY_MANAGER_THUMBS';
				break;
			case "comments":
				$id = 'COM_JGALLERY_MANAGER_COMMENTS';
				break;
			default: 
				$id = $isNew ? 'COM_JGALLERY_MANAGER_JGALLERY_NEW': 'COM_JGALLERY_MANAGER_JGALLERY_EDIT';
				break;
		}
		ToolbarHelper::title(Text::_($id), 'jgallery');
		// Build the actions for new and existing records.
		if ($isNew)
		{
			$layout = $this->getLayout();
			$cancreate = $this->canDo->get('core.create');
			// For new records, check the create permission.
			if (!in_array($this->getLayout(), array("recthumbs", "thumbs", "comments")) && $this->authorise('core.create')) 
			{
				ToolbarHelper::apply('jgallery.apply', 'JTOOLBAR_APPLY');
				ToolbarHelper::save('jgallery.save', 'JTOOLBAR_SAVE');
				ToolbarHelper::custom('jgallery.save2new', 'save-new.png', 'save-new_f2.png',
									   'JTOOLBAR_SAVE_AND_NEW', false);
			}
			ToolbarHelper::cancel('jgallery.cancel', 'JTOOLBAR_CANCEL');
			ToolbarHelper::spacer("30");
		}
		else
		{
			if ($this->getLayout() &&  $this->authorise('core.edit'))
			{
				// We can save the new record
				ToolbarHelper::apply('jgallery.apply', 'JTOOLBAR_APPLY');
				ToolbarHelper::save('jgallery.save', 'JTOOLBAR_SAVE');
 
				// We can save this record, but check the create permission to see
				// if we can return to make a new one.
				if ($this->get('core.create')) 
				{
					ToolbarHelper::custom('jgallery.save2new', 'save-new.png', 'save-new_f2.png',
										   'JTOOLBAR_SAVE_AND_NEW', false);
				}
			}
			if ($this->getLayout() == "default" &&  $this->authorise('core.create')) 
			{
				ToolbarHelper::custom('jgallery.save2copy', 'save-copy.png', 'save-copy_f2.png',
									   'JTOOLBAR_SAVE_AS_COPY', false);
			}
			ToolbarHelper::cancel('jgallery.cancel', 'JTOOLBAR_CLOSE');
			ToolbarHelper::spacer("30");
		}
	}
	/**
	 * Method to set up the document properties
	 *
	 * @return void
	 */
	public function setDocument(Document $document): void
	{
		$isNew = ($this->item) && ($this->item->id == 0);
		
		$document->setTitle($isNew ? Text::_('COM_JGALLERY_JGALLERY_CREATING')
								   : Text::_('COM_JGALLERY_JGALLERY_EDITING'));
		Text::script('COM_JGALLERY_JGALLERY_ERROR_UNACCEPTABLE');
	}
}
