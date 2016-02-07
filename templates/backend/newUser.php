<table class="form-table">
	<tr class="form-field form-required" id="io_user_art">
		<th scope="row"><label for="user_art">Nutzungsart <span class="description">(erforderlich)</span></label></th>
		<td>
			<input type="radio" name="user_art" id="user_art_user" value="user"<?php echo $userArtValue[0]; ?>> Normale*r Benutzer*in<br>
			<input type="radio" name="user_art" id="user_art_landesverband" value="landesverband"<?php echo $userArtValue[1]; ?>> Landesverband<br>
			<input type="radio" name="user_art" id="user_art_basisgruppe" value="basisgruppe"<?php echo $userArtValue[2]; ?>> Basisgruppe<br>
			<input type="radio" name="user_art" id="user_art_organisatorisch" value="organisatorisch"<?php echo $userArtValue[3]; ?>> Organisatorische*r Benutzer*in
		</td>
	</tr>
	<tr class="form-field form-required" id="io_orga_name">
		<th scope="row"><label for="orga_name">Orga-Name <span class="description">(erforderlich)</span></label></th>
		<td>
			<input type="text" name="orga_name" id="orga_name" class="input" value="<?php echo esc_attr(wp_unslash($orga_name)); ?>" size="25">
		</td>
	</tr>
	<tr class="form-field form-required" id="io_ort">
		<th scope="row"><label for="name">Ort <span class="description">(erforderlich)</span></label></th>
		<td>
			<input type="text" name="name" id="name" class="input" value="<?php echo esc_attr(wp_unslash($name)); ?>" size="25">
		</td>
	</tr>
	<tr class="form-field form-required" id="io_landesverband">
		<th scope="row"><label for="land">Bundesland <span class="description">(erforderlich)</span></label></th>
		<td>
			<select name="land" id="land">
				<option value="0">--- Bitte auswählen ---</option>
				<option<?php echo $landChecked[0];  ?> value="baden-wuerttemberg">Baden-W&uuml;rttemberg</option>
				<option<?php echo $landChecked[1];  ?> value="bayern">Bayern</option>
				<option<?php echo $landChecked[2];  ?> value="berlin">Berlin</option>
				<option<?php echo $landChecked[3];  ?> value="brandenburg">Brandenburg</option>
				<option<?php echo $landChecked[4];  ?> value="bremen">Bremen</option>
				<option<?php echo $landChecked[5];  ?> value="hamburg">Hamburg</option>
				<option<?php echo $landChecked[6];  ?> value="hessen">Hessen</option>
				<option<?php echo $landChecked[7];  ?> value="mecklenburg-vorpommern">Mecklenburg Vorpommern</option>
				<option<?php echo $landChecked[8];  ?> value="niedersachsen">Niedersachsen</option>
				<option<?php echo $landChecked[9];  ?> value="nordrhein-westfalen">Nordrhein-Westfalen</option>
				<option<?php echo $landChecked[10]; ?> value="rheinland-pfalz">Rheinland-Pfalz</option>
				<option<?php echo $landChecked[11]; ?> value="saarland">Saarland</option>
				<option<?php echo $landChecked[12]; ?> value="sachsen">Sachsen</option>
				<option<?php echo $landChecked[13]; ?> value="sachsen-anhalt">Sachsen-Anhalt</option>
				<option<?php echo $landChecked[14]; ?> value="schleswig-holstein">Schleswig-Holstein</option>
				<option<?php echo $landChecked[15]; ?> value="thueringen">Th&uuml;ringen</option>
			</select>
		</td>
	</tr>
	<tr class="form-field" id="io_groups">
		<th scope="row"><label for="groups">Gruppenmitgliedschaften </label></th>
		<td>
			<select name="groups[]" id="groups" multiple>
				<?php io_form_select($groups, $group_values); ?>
			</select>
		</td>
	</tr>
	<tr class="form-field" id="io_permissions">
		<th scope="row"><label for="permissions">Berechtigungen </label></th>
		<td>
			<select name="permissions[]" id="permissions" multiple>
				<?php io_form_select($permissions, $permission_values); ?>
			</select>
		</td>
	</tr>
</table>
	
<?php include 'js/newUserMask.php'; ?>