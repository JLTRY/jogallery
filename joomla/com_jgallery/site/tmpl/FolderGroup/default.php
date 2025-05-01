<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_jgallery
 *
 * @copyright   Copyright (C) 2005 - 2015 JL TRYOEN All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

use JLTRY\Component\JGallery\Administrator\Helper\JGalleryHelper;
use JLTRY\Component\JGallery\Administrator\Helper\FolderGroupHelper;

echo FolderGroupHelper::display(array("folders" => $this->folders,
								   "rootdir" => JGalleryHelper::join_paths($this->rootdir),
                                   "parent" => $this->parent,
                                   "directory" => $this->directory,
                                   "id" => $this->id,
                                   "name" => $this->name,
                                   "tmpl" => $this->tmpl,
								   "type" => $this->type));


