<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_jgallery
 *
 * @copyright   Copyright (C) 2015 - 2025 Open Source Matters. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
use JLTRY\Component\JGallery\Administrator\Helper\JGalleryHelper;
use JLTRY\Component\JGallery\Administrator\Helper\JDirectoryHelper;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Layout\LayoutHelper;

HTMLHelper::_('jquery.framework');
HTMLHelper::_('behavior.formvalidator');
?>
<?php echo JDirectoryHelper::display(1, array("dir" => JGalleryHelper::join_paths($this->directory),
											"rootdir" => JGalleryHelper::join_paths($this->rootdir),
											"type" => "selectcomments"));
$bar = Toolbar::getInstance('toolbar');
$bar->appendButton('Custom', 
					JDirectoryHelper::display(1, 
									array("dir" => JGalleryHelper::join_paths($this->directory),
										"rootdir" => JGalleryHelper::join_paths($this->rootdir),
										"type" => "selectdirsmenu")),
					"");
$bar->appendButton('Custom', 
					LayoutHelper::render('menucomments', array('id' => 1)),
					"");
?>


<form  method="post" name="adminForm" id="adminForm" class="form-validate">
	<input type="hidden" name="task" value="jgallery.thumbs" />
	<?php echo HTMLHelper::_('form.token'); ?>
</form>