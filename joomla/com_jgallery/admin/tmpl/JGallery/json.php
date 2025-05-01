<?php
/**
 * @package    JGallery
 * @author     JLTryoen http://www.jltryoen.fr
 * @copyright  Copyright (C) 2015 - 2025 JLTryoen . All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl.html GNU/GPL
 */
defined('_JEXEC') or die();
use JLTRY\Component\JGallery\Administrator\Helper\JGalleryHelper;
use Joomla\CMS\HTML\HTMLHelper;


HTMLHelper::_('jquery.framework');
$data = JGalleryHelper::getFiles($this->rootdir , $this->directory, true);
JGalleryHelper::json_answer($data);