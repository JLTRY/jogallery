<?php

/**
 * @package     Joomla.Administrator
 * @subpackage  com_jogallery
 *
 * @copyright   Copyright (C) 2015 - 2025 JL Tryoen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;
HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('formbehavior.chosen', 'select');
?>
<form action="<?php echo Route::_('index.php?option=com_jogallery&layout=edit&id=' . (int) $this->item->id); ?>"
    method="post" name="adminForm" id="adminForm" class="form-validate">
    <div class="form-horizontal">
        <?php foreach ($this->form->getFieldsets() as $name => $fieldset) :
            ?>
            <fieldset class="adminform">
                <legend><?php echo Text::_($fieldset->label); ?></legend>
                <div class="row-fluid">
                    <div class="span10">
                        <?php foreach ($this->form->getFieldset($name) as $field) :
                            ?>
                            <div class="control-group">
                                <div class="control-label"><?php echo $field->label; ?></div>
                                <div class="controls"><?php echo $field->input; ?></div>
                            </div>
                            <?php
                        endforeach; ?>
                    </div>
                </div>
            </fieldset>
            <?php
        endforeach; ?>
    </div>
    <input type="hidden" name="task" value="foldergroup.edit" />
    <?php echo HTMLHelper::_('form.token'); ?>
</form>
