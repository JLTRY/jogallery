<?php

/**
 * @package     Joomla.Site
 * @subpackage  com_jogallery
 *
 * @copyright   Copyright (C) 2005 - 2015 JL TRYOEN All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use JLTRY\Component\JOGallery\Administrator\Helper\JOGalleryHelper;

// No direct access to this file
// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

echo JOGalleryHelper::display(array(    "dir" => JOGalleryHelper::joinPaths($this->directory),
                                    "rootdir" => JOGalleryHelper::joinPaths($this->rootdir),
                                    "id" => $this->id,
                                    "parent" => $this->parent,
                                    "page" => $this->page));
