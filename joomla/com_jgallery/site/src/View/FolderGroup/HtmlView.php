<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_jgallery
 *
 * @copyright   Copyright (C) 2015 - 2025 JLTRYOEN. All rights reserved.
 * @license	 GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace JLTRY\Component\JGallery\Site\View\FolderGroup;

use JLTRY\Component\JGallery\Administrator\Model\JGalleryModel;
use JLTRY\Component\JGallery\Administrator\Helper\JParametersHelper;
use JLTRY\Component\JGallery\Administrator\Helper\JGalleryHelper;
use JLTRY\Component\JGallery\Administrator\Helper\JGalleryCategoryHelper;
use JLTRY\Component\JGallery\Site\Model\FolderGroupModel;

use Joomla\CMS\Log\Log;
use Joomla\CMS\MVC\Factory\MVCFactory;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Uri\Uri; 
use Joomla\CMS\Factory as Factory;
use Joomla\CMS\Language\Text as Text;
use Joomla\CMS\Helper\ModuleHelper;


/**
 * JGallery View
 *
 * @since  0.0.1
 */
class HtmlView extends BaseHtmlView
{
	static $_id;
	function getparam($name, $param) {
		$found = false;
		$app	 = Factory::getApplication();
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
			$model = new FolderGroupModel;
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
		$user = Factory::getApplication()->getSession()->get('user');
		if (($catid == -1) || (($user!= null) && JGalleryCategoryHelper::usercanviewcategory($user, $catid)))
		{
			$canview = true;
		} else {
			Factory::getLanguage()->load('com_content', JPATH_SITE, null ,true);
			echo "<jdoc:include type=\"message\" />";
			Factory::getApplication()->enqueueMessage(Text::_('COM_CONTENT_ERROR_LOGIN_TO_VIEW_ARTICLE'), 'error');
			$document = Factory::getDocument();
			$renderer = $document->loadRenderer('module');
			$Module = ModuleHelper::getModule('mod_login');
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
