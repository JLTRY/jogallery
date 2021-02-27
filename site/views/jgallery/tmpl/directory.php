<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_jgallery
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
JLoader::import('components.com_jgallery.helpers.jdirectory', JPATH_ADMINISTRATOR);
?>
<?php  echo JDirectoryHelper::display(1, array("dir" => JDirectoryHelper::join_paths($this->directory),
									 "rootdir" => JDirectoryHelper::join_paths($this->rootdir)));
?>

