<h3>Lieferadresse f&uuml;r Materiallieferungen</h3>
<table class="form-table">
    <tr>
        <td colspan="2">
            Der Bundesverband produziert in manchen F&auml;llen Materialien und schickt sie an die Ortsgruppen. Dabei kann es sein, dass eine solche Lieferung auch unangek&uuml;ndigt versendet werden kann. In allen F&auml;llen ist die ungefragte Zusendung kostenfrei.
        </td>
    </tr>
    <tr>
        <th style="padding: 5px;" valign="top"><label for="<?php echo User_Util::POST_ATTRIBUT_LIEFERADRESSE_ORT; ?>">Stelle oder Person, an die geliefert werden soll</label></th>
        <td style="padding: 5px;">
            <input type="text" name="<?php echo User_Util::POST_ATTRIBUT_LIEFERADRESSE_ORT; ?>" id="<?php echo User_Util::POST_ATTRIBUT_LIEFERADRESSE_ORT; ?>" value="<?php echo esc_attr( get_the_author_meta(User_Util::ATTRIBUT_LIEFERADRESSE_ORT, $user->ID ) ); ?>" class="regular-text" /><br />
            <span class="description">Eine Person, Gesch&auml;ftsstelle oder Sonstiges.</span>
        </td>
    </tr>
    <tr>
        <th style="padding: 5px;" valign="top"><label for="<?php echo User_Util::POST_ATTRIBUT_LIEFERADRESSE_ZUSATZ; ?>">Zusatzinformationen zur Adresse</label></th>
        <td style="padding: 5px;">
            <input type="text" name="<?php echo User_Util::POST_ATTRIBUT_LIEFERADRESSE_ZUSATZ; ?>" id="<?php echo User_Util::POST_ATTRIBUT_LIEFERADRESSE_ZUSATZ; ?>" value="<?php echo esc_attr( get_the_author_meta(User_Util::ATTRIBUT_LIEFERADRESSE_ZUSATZ, $user->ID ) ); ?>" class="regular-text" /><br />
            <span class="description">Beliebige, zus&auml;tzliche Information.</span>
        </td>
    </tr>
    <tr>
        <th style="padding: 5px;" valign="top"><label for="<?php echo User_Util::POST_ATTRIBUT_LIEFERADRESSE_STRASSE; ?>">Strasse mit Hausnummer</label></th>
        <td style="padding: 5px;">
            <input type="text" name="<?php echo User_Util::POST_ATTRIBUT_LIEFERADRESSE_STRASSE; ?>" id="<?php echo User_Util::POST_ATTRIBUT_LIEFERADRESSE_STRASSE; ?>" value="<?php echo esc_attr( get_the_author_meta(User_Util::ATTRIBUT_LIEFERADRESSE_STRASSE, $user->ID ) ); ?>" class="regular-text" /><br />
        </td>
    </tr>
    <tr>
        <th style="padding: 5px;" valign="top"><label for="<?php echo User_Util::POST_ATTRIBUT_LIEFERADRESSE_PLZ; ?>">Postleitzahl</label></th>
        <td style="padding: 5px;">
            <input type="number" name="<?php echo User_Util::POST_ATTRIBUT_LIEFERADRESSE_PLZ; ?>" id="<?php echo User_Util::POST_ATTRIBUT_LIEFERADRESSE_PLZ; ?>" value="<?php echo esc_attr( get_the_author_meta(User_Util::ATTRIBUT_LIEFERADRESSE_PLZ, $user->ID ) ); ?>" class="regular-text" /><br />
        </td>
    </tr>
    <tr>
        <th style="padding: 5px;" valign="top"><label for="<?php echo User_Util::POST_ATTRIBUT_LIEFERADRESSE_STADT; ?>">Ort</label></th>
        <td style="padding: 5px;">
            <input type="text" name="<?php echo User_Util::POST_ATTRIBUT_LIEFERADRESSE_STADT; ?>" id="<?php echo User_Util::POST_ATTRIBUT_LIEFERADRESSE_STADT; ?>" value="<?php echo esc_attr( get_the_author_meta(User_Util::ATTRIBUT_LIEFERADRESSE_STADT, $user->ID ) ); ?>" class="regular-text" /><br />
        </td>
    </tr>
</table>