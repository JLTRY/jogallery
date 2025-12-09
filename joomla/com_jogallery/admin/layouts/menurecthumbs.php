<?php 
/**
 * @package     Joomla.Administrator
 * @subpackage  com_jogallery
 *
 * @copyright   Copyright (C) 2015 - 2025 JL TRYOEN All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access to this file
defined('JPATH_BASE') or die;
$id = $displayData['id'];
$small_width = $displayData['small_width']; 
$large_width = $displayData['large_width']; 
?>
<div id="jmenuthumbs<?php echo $id;?>" class="form-group" style="height:auto;padding-left:15px;margin-left:15px;border: 1px solid; " >
    <table>
        <tr>
            <td>
                <button type="button" id="thumbs<?php echo $id; ?>" class="btn btn-primary">Thumbs</button>
            </td>
            <td>
                <label><input type="checkbox" name="checkall" value="checkall">checkall</label>
            </td>
            <td>
                <label><input type="checkbox" name="force" value="force">force</label>
            </td>
        </tr>
        <tr>
            <td>
                <label>small_width<input type="text" name="small_width" id="small_width<?php echo $id; ?>" value="<?php echo $small_width ?>" size="5"></label>
            </td>
            <td>
                <label>large_width<input type="text" name="small_width" id="large_width<?php echo $id; ?>" value="<?php echo $large_width ?>" size="5"></label>
            </td>
        </tr>
    </table>
</div>