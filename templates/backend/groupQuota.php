<table border="0" cellpadding="5" cellspacing="0" width="100%">
    <tr>
        <th width="30%">Mail-Quota der Gruppe (leer, wenn keine Quota):</th>
        <td width="40%"><input type="number" name="<?php echo Group_Util::POST_ATTRIBUT_QUOTA_SIZE; ?>" size="10" value="<?php echo str_replace(".", ",", $quota); ?>"></td>
        <td width="30%">
			<select name="<?php echo Group_Util::POST_ATTRIBUT_QUOTA_TYPE; ?>" size="1">
				<option<?php echo $einheit == Group_Util::POST_ATTRIBUT_QUOTA_B ? " selected" : ""; ?>
					value="<?php echo Group_Util::POST_ATTRIBUT_QUOTA_B; ?>">Byte (B)
				</option>
				<option<?php echo $einheit == Group_Util::POST_ATTRIBUT_QUOTA_KB ? " selected" : ""; ?>
					value="<?php echo Group_Util::POST_ATTRIBUT_QUOTA_KB; ?>">Kilo Byte (KB)
				</option>
				<option<?php echo $einheit == Group_Util::POST_ATTRIBUT_QUOTA_MB ? " selected" : ""; ?>
					value="<?php echo Group_Util::POST_ATTRIBUT_QUOTA_MB; ?>">Mega Byte (MB)
				</option>
				<option<?php echo $einheit == Group_Util::POST_ATTRIBUT_QUOTA_GB ? " selected" : ""; ?>
					value="<?php echo Group_Util::POST_ATTRIBUT_QUOTA_GB; ?>">Giga Byte (GB)
				</option>
			</select>
		</td>
    </tr>
</table>