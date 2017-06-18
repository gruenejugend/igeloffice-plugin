<h3>Verantwortliche Person</h3>
<table class="form-table">
    <tr>
        <th style="padding: 5px;" style="padding: 5px;" valign="top"><label for="<?php echo User_Util::POST_ATTRIBUT_VERANTWORTLICHE_PERSON; ?>">Ansprechbare Person f&uuml;r den Bundesverband</label></th>
        <td style="padding: 5px;">
            <input type="text" name="<?php echo User_Util::POST_ATTRIBUT_VERANTWORTLICHE_PERSON; ?>" id="<?php echo User_Util::POST_ATTRIBUT_VERANTWORTLICHE_PERSON; ?>" value="<?php echo esc_attr( get_the_author_meta(User_Util::ATTRIBUT_VERANTWORTLICHE_PERSON, $user->ID ) ); ?>" class="regular-text" /><br />
            <span class="description">Gebe hier den*die Ansprechpartner*in f&uuml;r den Bundesvorstand an. Denk dran, diesen Eintrag regelm&auml;&szlig;ig zu aktualisieren.</span>
        </td>
    </tr>
    <tr>
        <th style="padding: 5px;" style="padding: 5px;" valign="top"><label for="<?php echo User_Util::POST_ATTRIBUT_VERANTWORTLICHE_HANDY; ?>">Handynummer der ansprechbaren Person</label></th>
        <td style="padding: 5px;">
            <input type="text" name="<?php echo User_Util::POST_ATTRIBUT_VERANTWORTLICHE_HANDY; ?>" id="<?php echo User_Util::POST_ATTRIBUT_VERANTWORTLICHE_HANDY; ?>" value="<?php echo esc_attr( get_the_author_meta(User_Util::ATTRIBUT_VERANTWORTLICHE_HANDY, $user->ID ) ); ?>" class="regular-text" /><br />
            <span class="description">Gebe hier die Handynummer der ansprechbaren Person ein. Denk dran, diesen Eintrag regelm&auml;&szlig;ig zu aktualisieren.</span>
        </td>
    </tr>
    <tr>
        <th style="padding: 5px;" style="padding: 5px;" valign="top"><label for="<?php echo User_Util::POST_ATTRIBUT_VERANTWORTLICHE_MAIL; ?>">Mailadresse der ansprechbaren Person</label></th>
        <td style="padding: 5px;">
            <input type="email" name="<?php echo User_Util::POST_ATTRIBUT_VERANTWORTLICHE_MAIL; ?>" id="<?php echo User_Util::POST_ATTRIBUT_VERANTWORTLICHE_MAIL; ?>" value="<?php echo esc_attr( get_the_author_meta(User_Util::ATTRIBUT_VERANTWORTLICHE_MAIL, $user->ID ) ); ?>" class="regular-text" /><br />
            <span class="description">Gebe hier die Mail-Adresse der ansprechbaren Person ein. Denk dran, diesen Eintrag regelm&auml;&szlig;ig zu aktualisieren.</span>
        </td>
    </tr>
</table>
