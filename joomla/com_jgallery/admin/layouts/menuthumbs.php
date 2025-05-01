<?php 
// No direct access to this file
defined('JPATH_BASE') or die;
$id = $displayData['id']; ?>
<div id="jmenuthumbs<?php echo $id;?>" class="form-group" style="height:auto;padding-left:15px;margin-left:15px;border: 1px solid; " >
	<table >
		<tr>
			<td><button type="button" id="thumbs<?php echo $id; ?>" class="btn btn-primary">Thumbs</button></td>
			<td><label><input type="checkbox" name="checkall" value="checkall">checkall</label></td>
			<td><label><input type="checkbox" name="force" value="force">force</label></td>
			<td><button type="button" id="delete<?php echo $id; ?>" class="btn btn-primary">Delete</button></td>
			<td><label><input type="checkbox" name="keep" value="keep">keep</label></td>
		</tr>
		<tr>
			<td><label>small_width<input type="text" name="small_width" id="small_width<?php echo $id; ?>" value="256" size="5"></label></td>
			<td><label>large_width<input type="text" name="small_width" id="large_width<?php echo $id; ?>" value="1024" size="5"></label></td>
		</tr>
	</table>
</div>
