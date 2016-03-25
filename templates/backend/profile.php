<table class="form-table">
	<tr class="form-field" id="io_user_art">
		<th scope="row"><label for="user_art">Nutzungsart</label></th>
		<td>
			<input type="text" id="user_art" name="user_art" value="<?php echo ucfirst($user->art); ?>">
		</td>
	</tr><?php if($user->art == 'basisgruppe') {
?>
	<tr class="form-field" id="io_landesverband">
		<th scope="row"><label for="landesverband">Landesverband</label></th>
		<td>
			<input type="text" id="landesverband" name="landesverband" value="<?php echo ucfirst($user->landesverband); ?>">
		</td>
	</tr><?php }
?>
	<tr class="form-field" id="io_groups">
		<th scope="row"><label for="groups">Gruppenmitgliedschaften </label></th>
		<td>
			<select name="groups[]" id="groups" multiple>
				<?php io_form_select($groups, $groups_values); ?>
			</select>
		</td>
	</tr>
	<tr class="form-field" id="io_permissions">
		<th scope="row"><label for="permissions">Berechtigungen </label></th>
		<td>
			<select name="permissions[]" id="permissions" multiple>
				<?php io_form_select($permissions, $permissions_values); ?>
			</select>
		</td>
	</tr><?php if(current_user_can('administrator')) {
?>
	<tr class="form-field" id="io_user_aktiv">
		<th scope="row"><label for="user_aktiv">User Aktiv</label></th>
		<td>
			<input type="checkbox" id="user_aktiv" name="user_aktiv" value="true"<?php if($user->aktiv == 1) { ?> disabled checked readonly<?php } ?>>
		</td>
	</tr><?php }
?>
</table>

<?php include 'js/profile.php'; ?>