<table border="0" cellpadding="5" cellspacing="0" width="100%">
	<tr>
		<th width="30%">Berechtigungen</th>
		<td width="70%">
			<select name="permissions[]" id="permissions" size="10" multiple>
				<?php io_form_select(Permission_Control::getValues(), $permissions); ?>
			</select>
		</td>
	</tr>
</table>