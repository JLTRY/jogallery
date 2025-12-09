<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_jogallery
 *
 * @copyright   Copyright (C) 2015 - 2025 JLTRYOEN. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access to this file
// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

JLoader::import('components.com_jogallery.helpers.jogallery', JPATH_ADMINISTRATOR);
JLoader::import('components.com_jogallery.helpers.jthumbs', JPATH_ADMINISTRATOR);
$data = JThumbsHelper::generatethumbimage(JOGalleryHelper::join_paths($this->rootdir),
                                     JOGalleryHelper::join_paths($this->directory),
                                     $this->image,
                                     false);
JOGalleryHelper::json_answer($data);
?>

