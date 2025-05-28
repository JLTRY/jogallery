<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_jgallery
 *
 * @copyright   Copyright (C) 2015 - 2025 JLTRYOEN. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace JLTRY\Component\JGallery\Site\View\JGallery;

use JLTRY\Component\JGallery\Site\Model\JGalleryModel;
use JLTRY\Component\JGallery\Administrator\Helper\JParametersHelper;
use JLTRY\Component\JGallery\Administrator\Helper\JGalleryHelper;
use JLTRY\Component\JGallery\Administrator\Helper\JGalleryCategoryHelper;
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
		$app     = Factory::getApplication();
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
		$canview = false;
		//$this->item = $this->get('Item');
		$catid = -1;
		$this->id = -1;
		$this->getparam('id', 'id');
		$this->media = "ALL";
		$this->getparam('media', 'media');
		if ($this->item == null) {
			if ($this->id !== -1) {
				$model = new JGalleryModel;
				$this->setModel($model);
				$this->item = $model->getItem((int)$this->id);
				$catid = $this->item->catid;
			}
		}
		if (!$this->getparam('directory', 'directory'))
		{
			if ($this->getparam('directory64', 'directory64')) {
				$this->directory = utf8_decode(base64_decode($this->directory64));
			}
		}
		if ($this->item && !$this->directory) {
			$this->directory = $this->item->directory;
		}
		$user = Factory::getApplication()->getSession()->get('user');
		if (($catid == -1) || JGalleryCategoryHelper::usercanviewcategory($user, $catid))
		{
			$canview = true;
		}else {
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
			if (!is_string($this->directory))
			{
				$errors = array("directory not defined");
			} else {
				$errors = $this->get('Errors');
			}
			$this->getparam('image', 'image');
			$this->page= -1;
			$this->getparam('page', 'page');
			if ($this->getparam('image64', 'image64')){
				$this->image = utf8_decode(base64_decode($this->image64));
			}
			$this->parent = false;
			$this->getparam('parent', 'parent');
			// Check for errors.
			if (is_array($errors) && count($errors))
			{
				Log::add(implode('<br />', $errors), Log::WARNING, 'jerror');
				return false;
			}
			// Display the view
			parent::display($tpl);
		}
	}
}
