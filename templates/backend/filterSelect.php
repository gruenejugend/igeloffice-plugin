<?php echo $title; ?><br>
<select name="<?php echo $name; ?>" size="5" multiple>
	<?php
		foreach($values AS $key_1 => $value_1) {
			if(!is_array($value_1)) {
				?><option value="<?php echo $value_1; ?>"<?php if(in_array($value_1, $selected)) { echo ' selected'; } ?>><?php echo $value_1; ?></option>
	<?php
			} else {
				?><optgroup label="<?php echo $key_1; ?>">
		<?php
				foreach($value_1 AS $key_2 => $values_2) {
					?><option value="<?php echo $key_2; ?>"<?php if(in_array($key_2, $selected)) { echo ' selected'; } ?>><?php echo $key_2; ?></option>
		<?php
				}
	?>
	</optgroup>
	<?php
			}
		}
	?>
</select>