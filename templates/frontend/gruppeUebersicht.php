<?php
/**
 * Created by PhpStorm.
 * User: KWM
 * Date: 10.06.2017
 * Time: 22:07
 */

/*
 * Vielleicht hier auch Datenschutz-Erklärung?
 */

?>

<h1>Gruppenverwaltung</h1>

&Uuml;ber Gruppen besteht die M&ouml;glichkeit, anderen Nutzer*innen des IGELoffices Berechtigungen zu verleihen. Dabei steuern Gruppen &uuml;ber die Gruppen-Mitgliedschaft wer zu einer bestimmten Sache berechtigt ist und wer nicht. Die Berechtigung wird mit dem Beginn der Gruppen-Mitgliedschaft verliehen und mit dem Ende der Mitgliedschaft wieder entzogen.<br><br>

Als Gruppenleiter der folgenden Gruppen bestimmst du, wer Mitglied der jeweiligen Gruppe ist. Dabei kannst du Mitglieder entfernen und wieder hinzuf&uuml;gen. Beachte dabei den Namen der Gruppe, sowie dessen Funktion.<br><br>

<table cellpadding="0" cellspacing="0" width="100%" border="0">
    <tr>
        <td width="25%"><b>Gruppenname</b></td>
        <td width="50%"><b>Funktion der Gruppe</b></td>
        <td width="25%"><b>Aktion</b></td>
    </tr>
    <?php

        function getGruppenFunktion($group) {
            if($group->oberkategorie == "Cloud" && $group->unterkategorie == "User") {
                return "Zulassung von Usern zum eigenen Cloud-Space";
            } else if($group->oberkategorie == "WordPress") {
                return "Zulassung von Usern zu WordPress - Bitte Namen der Gruppe für Rolle beachten";
            } else {
                return "Sonstiges, Name der Gruppe beachten";
            }
        }

        foreach($groups AS $group) {
            ?>
    <tr>
        <td width="25%"><?php echo $group->name; ?></td>
        <td width="50%"><?php echo getGruppenFunktion($group); ?></td>
        <td width="25%"><a href="<?php

            $url = io_get_current_url();
            if(str_replace("?", "", $url) != $url) {
                echo $url . "&gruppe=".$group->id;
            } else {
                echo $url . "?gruppe=".$group->id;
            }

            ?>">Gruppe bearbeiten</a></td>
    </tr>
            <?php
        }

    ?>
</table>
