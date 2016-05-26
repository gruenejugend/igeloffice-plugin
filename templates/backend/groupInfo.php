<table border="0" cellpadding="5" cellspacing="0" width="100%">
	<tr>
		<th width="30%">Oberkategorie Text</th>
		<td width="70%"><input type="text" name="oberkategorie_txt"></td>
	</tr>
	<tr>
		<th width="30%">Oberkategorie Selektion</th>
		<td width="70%">
			<select name="oberkategorie_sel" size="1">
				<option value="-1">--- Ausw&auml;hlen ---</option>
<?php			foreach($group AS $oberkategorie => $values) {
					if($oberkategorie != "Nicht Kategorisiert") {
						$checked = "";
						if($oberkategorie == $oberkategorie_sel) {
							$checked = " selected";
						}
						?><option value="<?php echo $oberkategorie; ?>"<?php echo $checked; ?>><?php echo $oberkategorie; ?></option>
						<?php
					}
}
?>			</select>
		</td>
	</tr>
	<tr>
		<th width="30%">Unterkategorie Text</th>
		<td width="70%"><input type="text" name="unterkategorie_txt"></td>
	</tr>
	<tr>
		<th width="30%">Unterkategorie Selektion</th>
		<td width="70%">
			<select name="unterkategorie_sel" size="1">
				<option value="-1">--- Ausw&auml;hlen ---</option>
<?php			foreach($group AS $oberkategorie => $values) {
					if($oberkategorie != "Nicht Kategorisiert") {
					?><optgroup label="<?php echo $oberkategorie; ?>">
						<?php foreach($values AS $unterkategorie => $value) {
							if($unterkategorie != "Nicht Kategorisiert") {
								$checked = "";
								if($unterkategorie == $unterkategorie_sel) {
									$checked = " selected";
								}
								?><option value="<?php echo $unterkategorie; ?>"<?php echo $checked; ?>><?php echo $unterkategorie; ?></option>
								<?php
							}
						} ?>
					</optgroup>
<?php
					}
}
?>			</select>
		</td>
	</tr>
</table>