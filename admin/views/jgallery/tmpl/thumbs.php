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
JLoader::import('components.com_jgallery.helpers.jthumbs', JPATH_ADMINISTRATOR);
use Joomla\CMS\HTML\HTMLHelper as JHtml;
use Joomla\CMS\Router\Route as JRoute;

JHtml::_('jquery.framework');
JHtml::_('behavior.formvalidator');
echo JThumbsHelper::display(1, array("dir" => JGalleryHelper::join_paths($this->directory),
									    "rootdir" => JGalleryHelper::join_paths($this->rootdir)));
?>


<form action="<?php echo JRoute::_('index.php?option=com_jgallery&layout=thumbs&id=' . (int) $this->item->id); ?>"
    method="post" name="adminForm" id="adminForm" class="form-validate">
	<input type="hidden" name="task" value="jgallery.thumbs" />
	<?php echo JHtml::_('form.token'); ?>
</form>
