<table border="0" cellpadding="5" cellspacing="0" width="100%">
	<tr>
		<th width="30%">Mitglieder</th>
		<td width="70%">
			<select name="users[]" id="user" size="10" multiple>
				<?php 
				
				foreach($users AS $key_1 => $value_1) {
		?>					<option value="<?php echo $value_1; ?>" selected><?php echo get_userdata($value_1)->user_login; ?></option>
<?php
	}
				
				?>
			</select>
		</td>
	</tr>
	<tr>
		<th width="30%">Mitglieder anhand E-Mail-Adresse hinzuf&uuml;gen</th>
		<td width="70%">
			<i>Trennen mit Komma und folgendem Leerzeichen</i><br>
			<input type="email" name="new_mails" id="new_mails" size="50" multiple>
		</td>
	</tr>
	<tr>
		<th width="30%">Mitglieder anhand Vorname und Nachname hinzuf&uuml;gen</th>
		<td width="70%">
			<i>Trennen mit Komma und folgendem Leerzeichen</i><br>
			<input type="text" name="new_names" id="new_names" size="50">
		</td>
	</tr>
</table>