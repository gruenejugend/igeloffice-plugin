<h3>Social Media Kontakte</h3>
<table class="form-table">
    <tr>
        <th style="padding: 5px;" valign="top"><label for="<?php echo User_Util::POST_ATTRIBUT_FACEBOOK; ?>">Facebook</label></th>
        <td style="padding: 5px;">
            <input type="text" name="<?php echo User_Util::POST_ATTRIBUT_FACEBOOK; ?>" id="<?php echo User_Util::POST_ATTRIBUT_FACEBOOK; ?>" value="<?php echo esc_attr( get_the_author_meta(User_Util::ATTRIBUT_FACEBOOK, $user->ID ) ); ?>" class="regular-text" /><br />
            <span class="description">Gebe deinen Facebook-Namen ein.</span>
        </td>
    </tr>
    <tr>
        <th style="padding: 5px;" valign="top"><label for="<?php echo User_Util::POST_ATTRIBUT_TWITTER; ?>">Twitter</label></th>
        <td style="padding: 5px;">
            <input type="text" name="<?php echo User_Util::POST_ATTRIBUT_TWITTER; ?>" id="<?php echo User_Util::POST_ATTRIBUT_TWITTER; ?>" value="<?php echo esc_attr( get_the_author_meta(User_Util::ATTRIBUT_TWITTER, $user->ID ) ); ?>" class="regular-text" /><br />
            <span class="description">Gebe deinen Twitter-Name beginnend mit einem @ an.</span>
        </td>
    </tr>
    <tr>
        <th style="padding: 5px;" valign="top"><label for="<?php echo User_Util::POST_ATTRIBUT_INSTAGRAM; ?>">Instagram</label></th>
        <td style="padding: 5px;">
            <input type="text" name="<?php echo User_Util::POST_ATTRIBUT_INSTAGRAM; ?>" id="<?php echo User_Util::POST_ATTRIBUT_INSTAGRAM; ?>" value="<?php echo esc_attr( get_the_author_meta(User_Util::ATTRIBUT_INSTAGRAM, $user->ID ) ); ?>" class="regular-text" /><br />
            <span class="description">Gebe deinen Instagram-Name ein:</span>
        </td>
    </tr>
</table>