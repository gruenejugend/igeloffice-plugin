<table border="0" cellpadding="5" cellspacing="0" width="100%">
    <tr>
        <th width="30%">An Registration erinnern:<br><i>(Mit einem Komma trennen)</i></th>
        <td width="70%">
            <textarea name="<?php echo Group_Util::POST_ATTRIBUT_REMEMBER; ?>" cols="50" rows="10"><?php if (is_array($remember) && count($remember) > 0) {
                    echo implode(", ", $remember);
                } ?></textarea>
        </td>
    </tr>
</table>