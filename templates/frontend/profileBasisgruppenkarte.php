<h3>Informationen f&uuml;r die Basisgruppenkarte</h3>
<table class="form-table">
    <tr>
        <th style="padding: 5px;" valign="top"><label for="<?php echo User_Util::POST_ATTRIBUT_GRADE; ?>">L&auml;ngen- und Breitengeraden (z. B. aus Google-Maps)</label></th>
        <td style="padding: 5px;">
            <input type="text" name="<?php echo User_Util::POST_ATTRIBUT_GRADE; ?>" id="<?php echo User_Util::POST_ATTRIBUT_GRADE; ?>" value="<?php echo esc_attr( get_the_author_meta(User_Util::ATTRIBUT_GRADE, $user->ID ) ); ?>" class="regular-text" /><br />
            <span class="description">Gebe hier die L&auml;ngen- und Breitengerade an, die du zum Beispiel bei Google-Maps erh&auml;ltst.</span>
        </td>
    </tr>
    <tr>
        <th style="padding: 5px;" valign="top"><label for="<?php echo User_Util::POST_ATTRIBUT_BESCHREIBUNG; ?>">Beschreibung</label></th>
        <td style="padding: 5px;">
            <textarea name="<?php echo User_Util::POST_ATTRIBUT_BESCHREIBUNG; ?>" id="<?php echo User_Util::POST_ATTRIBUT_BESCHREIBUNG; ?>" cols="30" rows="5"><?php echo esc_attr( get_the_author_meta(User_Util::ATTRIBUT_BESCHREIBUNG, $user->ID ) ); ?></textarea>
        </td>
    </tr>
    <tr>
        <th style="padding: 5px;" valign="top"><label for="<?php echo User_Util::POST_ATTRIBUT_ADRESSE; ?>">Adresse eures regelm&auml;&szlig;igen Treffpunkts</label></th>
        <td style="padding: 5px;">
            <input type="text" name="<?php echo User_Util::POST_ATTRIBUT_ADRESSE; ?>" id="<?php echo User_Util::POST_ATTRIBUT_ADRESSE; ?>" value="<?php echo esc_attr( get_the_author_meta(User_Util::ATTRIBUT_ADRESSE, $user->ID ) ); ?>" class="regular-text" /><br />
            <span class="description">Zeilen mit Komma trennen.</span>
        </td>
    </tr>
    <tr>
        <th style="padding: 5px;" valign="top"><label for="<?php echo User_Util::POST_ATTRIBUT_IGEL; ?>">Logo eurer Gruppe</label></th>
        <td style="padding: 5px;">
            <?php

            $aggriCheck = "";
            $oekiCheck = "";
            if(esc_attr(get_the_author_meta(User_Util::ATTRIBUT_IGEL, $user->ID)) == User_Util::POST_ATTRIBUT_IGEL_AGGRI) {
                $aggriCheck = " checked";
            } else if(esc_attr(get_the_author_meta(User_Util::ATTRIBUT_IGEL, $user->ID)) == User_Util::POST_ATTRIBUT_IGEL_OEKI) {
                $oekiCheck = " checked";
            }

            ?>
            <input type="radio" name="<?php echo User_Util::POST_ATTRIBUT_IGEL; ?>" id="<?php echo User_Util::POST_ATTRIBUT_IGEL_AGGRI; ?>" value="<?php echo User_Util::POST_ATTRIBUT_IGEL_AGGRI; ?>"<?php echo $aggriCheck; ?>>
            <label for="<?php echo User_Util::POST_ATTRIBUT_IGEL_AGGRI; ?>">Aggri</label><br>
            <input type="radio" name="<?php echo User_Util::POST_ATTRIBUT_IGEL; ?>" id="<?php echo User_Util::POST_ATTRIBUT_IGEL_OEKI; ?>" value="<?php echo User_Util::POST_ATTRIBUT_IGEL_OEKI; ?>"<?php echo $oekiCheck; ?>>
            <label for="<?php echo User_Util::POST_ATTRIBUT_IGEL_OEKI; ?>">&Ouml;ki</label><br>
            <span class="description">Euer Logo f&uuml;r die Karte.</span>
        </td>
    </tr>
</table>