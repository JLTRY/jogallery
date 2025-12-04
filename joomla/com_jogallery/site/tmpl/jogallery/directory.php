<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_jogallery
 *
 * @copyright   Copyright (C) 2005 - 2015 JL TRYOEN All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
JLoader::import('components.com_jogallery.helpers.jdirectory', JPATH_ADMINISTRATOR);
echo JODirectoryHelper::display(1, array("directory" => JOGalleryHelper::join_paths($this->directory),
									 "rootdir" => JOGalleryHelper::join_paths($this->rootdir)));
?>

