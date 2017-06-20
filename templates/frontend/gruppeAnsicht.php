<?php
/**
 * Created by PhpStorm.
 * User: KWM
 * Date: 11.06.2017
 * Time: 00:13
 */

?>

<h1>Gruppe: <?php echo $group->name; ?></h1>

Hier kannst du Gruppen-Mitgliedschaften bearbeiten, in dem du zum Beispiel Mitgliedschaften entfernst oder neue hinzuf&uuml;gst. Beachte bitte, dass die L&ouml;schung von Mitgliedschaften dazu f&uuml;hrt, dass vergebene Berechtigungen entzogen werden.<br><br>

<h2>Neue Mitglieder hinzuf&uuml;gen</h2>

Wenn du ein neues Mitglied hinzuf&uuml;gen m&ouml;chtest, gebe hier den Namen oder die E-Mail-Adresse der Person ein, die du hinzuf&uuml;gen m&ouml;chtest. Beachte bitte, dass die jeweilige Person im IGELoffice registriert sein muss. Bei Eingabe einer Mail-Adresse werden nicht-registrierte Benutzer*innen an eine Registration erinnert.<br><br>

<form action="<?php echo io_get_current_url(); ?>" method="post">
    <?php wp_nonce_field(Group_Util::MEMBER_NONCE, Group_Util::POST_ATTRIBUT_MEMBER_NONCE); ?>
    <input type="hidden" name="<?php echo Group_Util::POST_ATTRIBUT_FRONTEND_GROUP; ?>" value="<?php echo $group->id; ?>">
    <b>&Uuml;ber Namen hinzuf&uuml;gen (pro Zeile, ein Name):</b><br>
    <textarea name="<?php echo Group_Util::POST_ATTRIBUT_FRONTEND_USER_NEU; ?>" cols="20" rows="5"></textarea><br><br>

    <b>&Uuml;ber Mail-Adresse hinzuf&uuml;gen (pro Zeile, eine E-Mail-Adresse):</b><br>
    <textarea name="<?php echo Group_Util::POST_ATTRIBUT_FRONTEND_MAIL_NEU; ?>" cols="100" rows="5"></textarea><br><br>
    
    <input type="submit" name="<?php echo Group_Util::POST_ATTRIBUT_FRONTEND_NEU_SUBMIT; ?>" value="Hinzuf&uuml;gen">
</form>

<h2>Antr&auml;ge zur Mitgliedschaft bearbeiten</h2>

<?php

    $requests = Groups_Frontend_View::getGroupRequests($group);

    if(count($requests) == 0) {
        ?><b>Keine Antr&auml;ge vorhanden.</b><?php
    } else {
        ?>

        Folgende Antr&auml;ge zur Mitgliedschaft liegen vor:

    <form action="<?php echo io_get_current_url(); ?>" method="post">
        <?php wp_nonce_field(Group_Util::INFO_NONCE, Group_Util::POST_ATTRIBUT_INFO_NONCE); ?>
        <input type="hidden" name="<?php echo Group_Util::POST_ATTRIBUT_FRONTEND_GROUP; ?>" value="<?php echo $group->id; ?>">
        <table cellpadding="0" cellspacing="0" border="0" width="100%">
            <tr>
                <td width="75%"><b>Antragssteller*in</b></td>
                <td width="25%"><b>Aktion</b></td>
            </tr>
            <?php

            foreach ($requests AS $request) {
                $user = new User($request->steller_in);

                ?>

            <tr>
                <td width="75%"><?php echo $user->user_login; ?></td>
                <td width="25%">
                    <input type="radio" name="<?php echo Group_Util::POST_ATTRIBUT_FRONTEND_REQUEST_STATUS.$request->ID; ?>" value="<?php echo Group_Util::POST_ATTRIBUT_FRONTEND_REQUEST_STATUS_A_U; ?>" checked> Unbearbeitet<br>
                    <input type="radio" name="<?php echo Group_Util::POST_ATTRIBUT_FRONTEND_REQUEST_STATUS.$request->ID; ?>" value="<?php echo Group_Util::POST_ATTRIBUT_FRONTEND_REQUEST_STATUS_A_A; ?>"> Annehmen<br>
                    <input type="radio" name="<?php echo Group_Util::POST_ATTRIBUT_FRONTEND_REQUEST_STATUS.$request->ID; ?>" value="<?php echo Group_Util::POST_ATTRIBUT_FRONTEND_REQUEST_STATUS_A_R; ?>"> Ablehnen
                </td>
            </tr>

                <?php
            }

            ?>
        </table>

        <input type="submit" name="<?php echo Group_Util::POST_ATTRIBUT_FRONTEND_REQUEST_SUBMIT; ?>" value="Antr&auml;ge bearbeiten">
    </form>

<?php
    }

?>

<h2>Mitgliedschaften bearbeiten</h2>

Hier siehst du bestehende Mitgliedschaften zu dieser Gruppe. Mit der Auswahl der Box rechts neben dem Namen kannst du bestimmen, wer die Gruppe verlassen soll.<br><br>

<form action="<?php echo io_get_current_url(); ?>" method="post">
    <?php wp_nonce_field(Group_Util::STANDARD_NONCE, Group_Util::POST_ATTRIBUT_STANDARD_NONCE); ?>
    <input type="hidden" name="<?php echo Group_Util::POST_ATTRIBUT_FRONTEND_GROUP; ?>" value="<?php echo $group->id; ?>">
    <table cellpadding="0" cellspacing="0" border="0" width="100%">
        <tr>
            <td width="75%"><b>Name</b></td>
            <td width="25%"><b>Entfernen?</b></td>
        </tr>
        <?php

        foreach ($group->users AS $user) {
            ?>
        <tr>
            <td width="75%"><?php echo $user->user_login; ?></td>
            <td width="25%"><input type="checkbox" name="<?php echo Group_Util::POST_ATTRIBUT_FRONTEND_USER.$user->ID; ?>" value="1"></td>
        </tr>
        <?php
        }

        ?>
    </table>

    <input type="submit" name="<?php echo Group_Util::POST_ATTRIBUT_FRONTEND_USER_SUBMIT; ?>" value="Mitglieder entfernen">
</form>
