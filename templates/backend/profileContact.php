<h3>Informationen f&uuml;r die Karte</h3>
<table class="form-table">
    <tr>
        <th><label for="<?php echo User_Util::POST_ATTRIBUT_FACEBOOK; ?>">Facebook</label></th>
        <td>
            <input type="text" name="<?php echo User_Util::POST_ATTRIBUT_FACEBOOK; ?>" id="<?php echo User_Util::POST_ATTRIBUT_FACEBOOK; ?>" value="<?php echo esc_attr( get_the_author_meta(User_Util::ATTRIBUT_FACEBOOK, $user->ID ) ); ?>" class="regular-text" /><br />
            <span class="description">Gebe deinen Facebook-Namen ein.</span>
        </td>
    </tr>
    <tr>
        <th><label for="<?php echo User_Util::POST_ATTRIBUT_TWITTER; ?>">Twitter</label></th>
        <td>
            <input type="text" name="<?php echo User_Util::POST_ATTRIBUT_TWITTER; ?>" id="<?php echo User_Util::POST_ATTRIBUT_TWITTER; ?>" value="<?php echo esc_attr( get_the_author_meta(User_Util::ATTRIBUT_TWITTER, $user->ID ) ); ?>" class="regular-text" /><br />
            <span class="description">Gebe deinen Twitter-Name beginnend mit einem @ an.</span>
        </td>
    </tr>
    <tr>
        <th><label for="<?php echo User_Util::POST_ATTRIBUT_INSTAGRAM; ?>">Instagram</label></th>
        <td>
            <input type="text" name="<?php echo User_Util::POST_ATTRIBUT_INSTAGRAM; ?>" id="<?php echo User_Util::POST_ATTRIBUT_INSTAGRAM; ?>" value="<?php echo esc_attr( get_the_author_meta(User_Util::ATTRIBUT_INSTAGRAM, $user->ID ) ); ?>" class="regular-text" /><br />
            <span class="description">Gebe deinen Instagram-Name ein:</span>
        </td>
    </tr>
    <tr>
        <th><label for="<?php echo User_Util::POST_ATTRIBUT_GRADE; ?>">L&auml;ngen- und Breitengeraden (z. B. aus Google-Maps)</label></th>
        <td>
            <input type="text" name="<?php echo User_Util::POST_ATTRIBUT_GRADE; ?>" id="<?php echo User_Util::POST_ATTRIBUT_GRADE; ?>" value="<?php echo esc_attr( get_the_author_meta(User_Util::ATTRIBUT_GRADE, $user->ID ) ); ?>" class="regular-text" /><br />
            <span class="description">Gebe hier die L&auml;ngen- und Breitengerade an, die du zum Beispiel bei Google-Maps erh&auml;ltst.</span>
        </td>
    </tr>
    <tr>
        <th><label for="<?php echo User_Util::POST_ATTRIBUT_BESCHREIBUNG; ?>">Beschreibung</label></th>
        <td>
            <textarea name="<?php echo User_Util::POST_ATTRIBUT_BESCHREIBUNG; ?>" id="<?php echo User_Util::POST_ATTRIBUT_BESCHREIBUNG; ?>" cols="30" rows="5"><?php echo esc_attr( get_the_author_meta(User_Util::ATTRIBUT_BESCHREIBUNG, $user->ID ) ); ?></textarea>
        </td>
    </tr>
</table>