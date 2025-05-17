<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_jgallery
 *
 * @copyright   Copyright (C) 2015 - 2025 Open Source Matters. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
use JLTRY\Component\JGallery\Administrator\Helper\JThumbsHelper;
use JLTRY\Component\JGallery\Administrator\Helper\JGalleryHelper;
use Joomla\CMS\HTML\HTMLHelper;
HTMLHelper::_('jquery.framework');

$data = JThumbsHelper::generatethumbimage(JGalleryHelper::join_paths($this->rootdir),
									 JGalleryHelper::join_paths($this->directory),
									 $this->image,
									 $this->force,
                                     $this->small_width,
                                     $this->large_width);
JGalleryHelper::json_answer($data);


