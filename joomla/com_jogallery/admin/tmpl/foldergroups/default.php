<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_jogallery
 *
 * @copyright   Copyright (C) 2015 - 2025 JL Tryoen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
use JLTRY\Component\JOGallery\Administrator\Helper\JThumbsHelper;
use JLTRY\Component\JOGallery\Administrator\Helper\JOGalleryHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;

HTMLHelper::_('formbehavior.chosen', 'select');

$listOrder     = $this->escape($this->filter_order);
$listDirn      = $this->escape($this->filter_order_Dir);


?>
<form action="index.php?option=com_jogallery&view=foldergroups" method="post" id="adminForm" name="adminForm">
    <div class="row-fluid">
        <div class="span6">
            <?php //echo Text::_('OM_CATEGORIES_ITEMS_SEARCH_FILTER'); ?>
            <?php
                /*echo JLayoutHelper::render(
                    'joomla.searchtools.default',
                    array('view' => $this)
                );*/
            ?>
        </div>
    </div>
    <table class="table table-striped table-hover">
        <thead>
        <tr>
            <th width="3%">
                <?php echo HTMLHelper::_('grid.checkall'); ?>
            </th>
            <th width="90%">
                <?php echo HTMLHelper::_('grid.sort', 'COM_MEDIA_NAME', 'name', $listDirn, $listOrder); ?>
            </th>
            <th width="5%">
                <?php echo HTMLHelper::_('grid.sort', 'COM_CONTENT_PUBLISHED', 'published', $listDirn, $listOrder); ?>
            </th>
            <th width="2%">
                <?php echo HTMLHelper::_('grid.sort', 'COM_CONTENT_ID_LABEL', 'id', $listDirn, $listOrder); ?>
            </th>
        </tr>
        </thead>
        <tfoot>
            <tr>
                <td colspan="4">
                    <?php if ($this->pagination) { echo   $this->pagination->getListFooter(); }?>
                </td>
            </tr>
        </tfoot>
        <tbody>
            <?php if (!empty($this->items)) : ?>
                <?php foreach ($this->items as $i => $row) :
                    $link = Route::_('index.php?option=com_jogallery&task=foldergroup.edit&id=' . $row->id);
                ?>
                    <tr>
                        <td>
                            <?php echo HTMLHelper::_('grid.id', $i, $row->id); ?>
                        </td>					
                        <td>
                            <a href="<?php echo $link; ?>" title="<?php echo Text::_('COM_JOGALLERY_EDIT_GROUP') . " "  . $row->name; ?>">
                                <?php echo $row->name; ?>
                            </a>
                        </td>
                        <td align="center">
                            <?php echo HTMLHelper::_('jgrid.published', $row->published, $i, 'foldergroups.', true, 'cb'); ?>
                        </td>
                        <td align="center">
                            <?php echo $row->id; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
    <input type="hidden" name="task" value=""/>
    <input type="hidden" name="boxchecked" value="0"/>
    <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>"/>
    <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>"/>
    <?php echo HTMLHelper::_('form.token'); ?>
</form>

