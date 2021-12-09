<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_jgallery
 *
 * @copyright   Copyright (C) 2005 - 2015 JL TRYOEN All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
JLoader::import('components.com_jgallery.helpers.jgallery', JPATH_ADMINISTRATOR);
?>
<?php  
	echo JGalleryHelper::display(array("dir" => JGalleryHelper::join_paths($this->directory),
								   "rootdir" => JGalleryHelper::join_paths($this->rootdir),
								   "parent" => $this->parent,
								   "page" => $this->page));
?>

