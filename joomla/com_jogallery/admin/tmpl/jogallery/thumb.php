<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_jogallery
 *
 * @copyright   Copyright (C) 2015 - 2025 Open Source Matters. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
use JLTRY\Component\JOGallery\Administrator\Helper\JThumbsHelper;
use JLTRY\Component\JOGallery\Administrator\Helper\JOGalleryHelper;
use Joomla\CMS\HTML\HTMLHelper;
HTMLHelper::_('jquery.framework');

$data = JThumbsHelper::generatethumbimage(JOGalleryHelper::join_paths($this->rootdir),
                                     JOGalleryHelper::join_paths($this->directory),
                                     $this->image,
                                     $this->force,
                                     $this->small_width,
                                     $this->large_width);
JOGalleryHelper::json_answer($data);


