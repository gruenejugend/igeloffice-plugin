<table border="0" cellpadding="5" cellspacing="0" width="100%">
    <tr>
        <th width="30%">Für wen soll diese Gruppe nicht angezeigt werden:</th>
        <td width="70%">
            <select name="sichtbarkeit[]" id="sichtbarkeit" size="10" multiple>
                <?php

                    $user_arten = User_Util::USER_ARTEN;

                    foreach($user_arten AS $key => $user_art) {
                        if(is_array($user_art)) {
                            ?>              <optgroup label="<?php echo $key; ?>">
<?php
                            foreach($user_art AS $key_2 => $user_art_2) {
                                ?>                <option value="<?php echo $key . "_" . $key_2; ?>"<?php echo isset($sichtbarkeit[$key][$key_2]) ? " selected" : ""; ?>><?php echo $user_art_2; ?></option>
<?php
                            }

                            ?>              </optgroup>
<?php
                        } else {
                            ?>              <option value="<?php echo $key; ?>"<?php echo isset($sichtbarkeit[$key]) ? " selected" : ""; ?>><?php echo $user_art; ?></option>
<?php
                        }
                    }

                ?>
            </select>
        </td>
    </tr>
</table>