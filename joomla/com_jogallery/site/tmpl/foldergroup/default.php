<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_jogallery
 *
 * @copyright   Copyright (C) 2005 - 2015 JL TRYOEN All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

use JLTRY\Component\JOGallery\Administrator\Helper\JOGalleryHelper;
use JLTRY\Component\JOGallery\Administrator\Helper\FoldergroupHelper;

echo FoldergroupHelper::display(array("folders" => $this->folders,
                                   "rootdir" => JOGalleryHelper::join_paths($this->rootdir),
                                   "parent" => $this->parent,
                                   "directory" => $this->directory,
                                   "id" => $this->id,
                                   "name" => $this->name,
                                   "tmpl" => $this->tmpl,
                                   "type" => $this->type,
                                   "media" => $this->media));


