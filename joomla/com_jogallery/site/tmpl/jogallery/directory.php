<?php

/**
 * @package     Joomla.Site
 * @subpackage  com_jogallery
 *
 * @copyright   Copyright (C) 2005 - 2015 JL TRYOEN All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access to this file
// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

JLoader::import('components.com_jogallery.helpers.jdirectory', JPATH_ADMINISTRATOR);
echo JODirectoryHelper::display(1, array("directory" => JOGalleryHelper::joinPaths($this->directory),
                                     "rootdir" => JOGalleryHelper::joinPaths($this->rootdir)));
