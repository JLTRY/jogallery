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

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
HTMLHelper::_('jquery.framework');
//HTMLHelper::_('formbehavior.chosen', 'select');

$listOrder     = $this->escape($this->filter_order);
$listDirn      = $this->escape($this->filter_order_Dir);
?>
<form action="index.php?option=com_jogallery&view=jogalleries" method="post" id="adminForm" name="adminForm">
    <div class="row-fluid">
        <div class="span6">
            <?php echo Text::_('COM_JOGALLERY_JGALLERIE_FILTER'); ?>
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
            <th width="1%"><?php echo Text::_('COM_JOGALLERY_NUM'); ?></th>
            <th width="2%">
                <?php echo HTMLHelper::_('grid.checkall'); ?>
            </th>
            <th width="90%">
                <?php echo HTMLHelper::_(
                    'grid.sort',
                    'COM_JOGALLERY_JOGALLERYS_NAME',
                    'directory',
                    $listDirn,
                    $listOrder
                ); ?>
            </th>
            <th width="5%">
                <?php echo HTMLHelper::_('grid.sort', 'COM_JOGALLERY_PUBLISHED', 'published', $listDirn, $listOrder); ?>
            </th>
            <th width="2%">
                <?php echo HTMLHelper::_('grid.sort', 'COM_JOGALLERY_ID', 'id', $listDirn, $listOrder); ?>
            </th>
        </tr>
        </thead>
        <tfoot>
            <tr>
                <td colspan="5">
                    <?php if ($this->pagination) {
                        echo $this->pagination->getListFooter();
                    }?>
                </td>
            </tr>
        </tfoot>
        <tbody>
            <?php if (!empty($this->items)) :
                ?>
                <?php foreach ($this->items as $i => $row) :
                    $link = Route::_('index.php?option=com_jogallery&task=jogallery.edit&id=' . $row->id);
                    ?>
                    <tr>
                        <td><?php echo $this->pagination->getRowOffset($i); ?></td>
                        <td>
                            <?php echo HTMLHelper::_('grid.id', $i, $row->id); ?>
                        </td>
                        <td>
                            <a href="<?php echo $link; ?>" 
                                title="<?php echo Text::_('COM_JOGALLERY_EDIT_JOGALLERY'); ?>">
                                <?php echo $row->directory; ?>
                            </a>
                        </td>
                        <td align="center">
                            <?php echo HTMLHelper::_(
                                'jgrid.published',
                                $row->published,
                                $i,
                                'jogalleries.',
                                true,
                                'cb'
                            ); ?>
                        </td>
                        <td align="center">
                            <?php echo $row->id; ?>
                        </td>
                    </tr>
                    <?php
                endforeach; ?>
                <?php
            endif; ?>
        </tbody>
    </table>
    <input type="hidden" name="task" value=""/>
    <input type="hidden" name="boxchecked" value="0"/>
    <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>"/>
    <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>"/>
    <?php echo HTMLHelper::_('form.token'); ?>
</form>
