<?php
/**
 * @package    jcoaching
 * @author     JLTryoen http://www.jltryoen.fr
 * @copyright  Copyright (C) 2007 - 2015 JLTryoen . All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl.html GNU/GPL
 */
defined('_JEXEC') or die();
use Joomla\CMS\HTML\HTMLHelper as JHtml;

JLoader::import('components.com_jgallery.helpers.jgallery', JPATH_ADMINISTRATOR);
JHtml::_('jquery.framework');
$data = JGalleryHelper::getFiles($this->rootdir , $this->directory, true);
JGalleryHelper::json_answer($data);