<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_jogallery
 *
 * @copyright   Copyright (C) 2015 - 2025 JLTRYOEN. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
JLoader::import('components.com_jogallery.helpers.jcomments', JPATH_ADMINISTRATOR);
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;

HTMLHelper::_('jquery.framework');
HTMLHelper::_('behavior.formvalidator');
?>
<?php   echo JCommentsHelper::display(1, array("dir" => JOGalleryHelper::join_paths($this->directory),
									    "rootdir" => JOGalleryHelper::join_paths($this->rootdir)));?>


<form action="<?php echo Route::_('index.php?option=com_jogallery&layout=thumbs&id=' . (int) $this->item->id); ?>"
    method="post" name="adminForm" id="adminForm" class="form-validate">
	<input type="hidden" name="task" value="jogallery.thumbs" />
	<?php echo HTMLHelper::_('form.token'); ?>
</form>