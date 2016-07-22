<table border="0" cellpadding="5" cellspacing="0" width="100%">
	<tr>
		<th width="30%">Mitglieder</th>
		<td width="70%">
			<select name="<?php echo Group_Util::POST_ATTRIBUT_USERS; ?>[]" id="<?php echo Group_Util::POST_ATTRIBUT_USERS; ?>" size="10" multiple>
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
			<input type="email" name="<?php echo Group_Util::POST_ATTRIBUT_NEW_MAILS; ?>" id="<?php echo Group_Util::POST_ATTRIBUT_NEW_MAILS; ?>" size="50" multiple>
		</td>
	</tr>
	<tr>
		<th width="30%">Mitglieder anhand Vorname und Nachname hinzuf&uuml;gen</th>
		<td width="70%">
			<i>Trennen mit Komma und folgendem Leerzeichen</i><br>
			<input type="text" name="<?php echo Group_Util::POST_ATTRIBUT_NEW_NAMES; ?>" id="<?php echo Group_Util::POST_ATTRIBUT_NEW_NAMES; ?>" size="50">
		</td>
	</tr>
</table>