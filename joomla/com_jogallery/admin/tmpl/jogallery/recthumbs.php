<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_jogallery
 *
 * @copyright   Copyright (C) 2015 - 2025 Open Source Matters. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

use JLTRY\Component\JOGallery\Administrator\Helper\JOGalleryHelper;
use JLTRY\Component\JOGallery\Administrator\Helper\JODirectoryHelper;
use JLTRY\Component\JOGallery\Administrator\Helper\JParametersHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Toolbar\Toolbar;

HTMLHelper::_('jquery.framework');
HTMLHelper::_('behavior.formvalidator');
echo JODirectoryHelper::display(1, array("dir" => JOGalleryHelper::join_paths($this->directory),
                                    "rootdir" => JOGalleryHelper::join_paths($this->rootdir),
                                    "type" => "recthumbs"));
$bar = Toolbar::getInstance('toolbar');
$bar->appendButton('Custom', 
                    LayoutHelper::render('menurecthumbs', 
                                        array('id' => 1, 
                                            'small_width' => JParametersHelper::get('thumb_small_width'),
                                            'large_width' => JParametersHelper::get('thumb_large_width'),
                                        )),
                    "");
?>


<form action="<?php echo Route::_('index.php?option=com_jogallery&layout=thumbs&id=' . (int) $this->item->id); ?>"
    method="post" name="adminForm" id="adminForm" class="form-validate">
    <input type="hidden" name="task" value="jogallery.thumbs" />
    <?php echo HTMLHelper::_('form.token'); ?>
</form>
