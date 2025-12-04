<?php 
// No direct access to this file
defined('JPATH_BASE') or die;
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