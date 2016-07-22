<table border="0" cellpadding="5" cellspacing="0" width="100%">
	<tr>
		<th width="30%">Mitglieder</th>
		<td width="70%">
			<select name="<?php echo Permission_Util::POST_ATTRIBUT_USERS; ?>[]" id="<?php echo Permission_Util::POST_ATTRIBUT_USERS; ?>" size="10" multiple>
				<?php io_form_select(User_Control::getValues(), $users, "", true); ?>
			</select>
		</td>
	</tr>
	<tr>
		<th width="30%">Gruppen</th>
		<td width="70%">
			<select name="<?php echo Permission_Util::POST_ATTRIBUT_GROUPS; ?>[]" id="<?php echo Permission_Util::POST_ATTRIBUT_GROUPS; ?>" size="10" multiple>
				<?php io_form_select(Group_Control::getValues(), $groups); ?>
			</select>
		</td>
	</tr>
</table>