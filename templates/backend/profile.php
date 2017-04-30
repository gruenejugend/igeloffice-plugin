<h3>Benutzerkonten-Informationen</h3>
<table class="form-table">
	<tr class="form-field" id="<?php echo User_Util::ATTRIBUT_ART; ?>">
		<th scope="row"><label for="<?php echo User_Util::POST_ATTRIBUT_ART; ?>">Nutzungsart</label></th>
		<td>
			<input type="text" id="<?php echo User_Util::POST_ATTRIBUT_ART; ?>" name="<?php echo User_Util::POST_ATTRIBUT_ART; ?>" value="<?php echo ucfirst($user->art); ?>" readonly>
		</td>
	</tr><?php if($user->art == 'basisgruppe') {
?>
	<tr class="form-field" id="io_<?php echo User_Util::POST_ATTRIBUT_LANDESVERBAND; ?>">
		<th scope="row"><label for="<?php echo User_Util::POST_ATTRIBUT_LANDESVERBAND; ?>">Landesverband</label></th>
		<td>
			<input type="text" id="<?php echo User_Util::POST_ATTRIBUT_LANDESVERBAND; ?>" name="<?php echo User_Util::POST_ATTRIBUT_LANDESVERBAND; ?>" value="<?php echo ucfirst($user->landesverband); ?>" readonly>
		</td>
	</tr><?php }
?>
	<tr class="form-field" id="io_<?php echo User_Util::POST_ATTRIBUT_GROUPS; ?>">
		<th scope="row"><label for="<?php echo User_Util::POST_ATTRIBUT_GROUPS; ?>">Gruppenmitgliedschaften </label></th>
		<td>
			<select name="<?php echo User_Util::POST_ATTRIBUT_GROUPS; ?>[]" id="<?php echo User_Util::POST_ATTRIBUT_GROUPS; ?>" size="10" multiple>
				<?php io_form_select($groups, $groups_values); ?>
			</select>
		</td>
	</tr>
	<tr class="form-field" id="io_<?php echo User_Util::POST_ATTRIBUT_PERMISSIONS; ?>">
		<th scope="row"><label for="<?php echo User_Util::POST_ATTRIBUT_PERMISSIONS; ?>">Berechtigungen </label></th>
		<td>
			<select name="<?php echo User_Util::POST_ATTRIBUT_PERMISSIONS; ?>[]" id="<?php echo User_Util::POST_ATTRIBUT_PERMISSIONS; ?>" size="10" multiple>
				<?php io_form_select($permissions, $permissions_values); ?>
			</select>
		</td>
	</tr><?php if(current_user_can('administrator')) {
?>
	<tr class="form-field" id="<?php echo User_Util::ATTRIBUT_AKTIV; ?>">
		<th scope="row"><label for="<?php echo User_Util::POST_ATTRIBUT_AKTIV; ?>">User Aktiv</label></th>
		<td>
			<input type="checkbox" id="<?php echo User_Util::POST_ATTRIBUT_AKTIV; ?>" name="<?php echo User_Util::POST_ATTRIBUT_AKTIV; ?>" value="true"<?php if($user->aktiv == 1) { ?> disabled checked readonly<?php } ?>>
		</td>
	</tr><?php }
?>
</table>

<?php include 'js/profile.php'; ?>