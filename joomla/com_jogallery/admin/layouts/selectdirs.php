<?php 
/**
 * @package     Joomla.Administrator
 * @subpackage  com_jogallery
 *
 * @copyright   Copyright (C) 2015 - 2025 JL TRYOEN All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

$sid = $displayData['sid']; 
$selectdirs = $displayData['selectdirs']; 
?>
<!-- layout selectdirs -->
<select class="form-select" size=1 style="max-width:500px;margin-left:10px;max-height:5em;height:5em;" id="<?php echo $sid;?>">
    <optgroup style="max-height:5em;" label="">
    <option selected>Open this select menu</option>
    <?php foreach($selectdirs as $selectdir): ?>
            <option value="<?php echo $selectdir[0]; ?>">
                <?php echo $selectdir[1]; ?>
            </option>
    <?php endforeach; ?>
    </optgroup>
</select>
<!-- layout selectdirs end-->