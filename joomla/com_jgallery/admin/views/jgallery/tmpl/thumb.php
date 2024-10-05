<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_jgallery
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\HTML\HTMLHelper as JHtml;

JHtml::_('jquery.framework');
JLoader::import('components.com_jgallery.helpers.jgallery', JPATH_ADMINISTRATOR);
JLoader::import('components.com_jgallery.helpers.jthumbs', JPATH_ADMINISTRATOR);
$data = JThumbsHelper::generatethumbimage(JGalleryHelper::join_paths($this->rootdir),
									 JGalleryHelper::join_paths($this->directory),
									 $this->image,
									 $this->force,
                                     $this->small_width,
                                     $this->large_width);
JGalleryHelper::json_answer($data);


