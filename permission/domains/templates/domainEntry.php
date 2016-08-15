Zunächst solltest du die Domains eintragen, die auf deine Homepage verweisen sollen. Es besteht die Möglichkeit, hier Domains zu nutzen, die nicht dem Bundesverband gehören und durch dich registriert worden sind.
<br><br>

Wichtig ist, dass du die DNS-Einstellungen der Domain änderst und auf unseren Server verweisen lässt. Bitte beachte dabei folgende Einstellungen:
<br><br>

<table border="0" cellpadding="0" cellspacing="0" width="100%">
    <tr>
        <td width="20%">@</td>
        <td width="10%">3600</td>
        <td width="10%">IN</td>
        <td width="10%">A</td>
        <td width="50%">gruene-jugend.de</td>
    </tr>
    <tr>
        <td width="20%">*</td>
        <td width="10%">3600</td>
        <td width="10%">IN</td>
        <td width="10%">A</td>
        <td width="50%">gruene-jugend.de</td>
    </tr>
</table><br><br>

Es besteht auch die Möglichkeit, Postfächer für diese Domain einzurichten. Solltest du deine Postfächer auf dem Server der GRÜNEN JUGEND unterbringen wollen, beachte bitte folgende DNS-Einstellungen:
<br><br>

<table border="0" cellpadding="0" cellspacing="0" width="100%">
    <tr>
        <td width="20%">imap</td>
        <td width="10%">3600</td>
        <td width="10%">IN</td>
        <td width="10%">A</td>
        <td width="50%">gruene-jugend.de</td>
    </tr>
    <tr>
        <td width="20%">mail</td>
        <td width="10%">3600</td>
        <td width="10%">IN</td>
        <td width="10%">A</td>
        <td width="50%">gruene-jugend.de</td>
    </tr>
    <tr>
        <td width="20%">smtp</td>
        <td width="10%">3600</td>
        <td width="10%">IN</td>
        <td width="10%">A</td>
        <td width="50%">gruene-jugend.de</td>
    </tr>
</table><br><br>

<b>Wenn du nicht noch mehr Webseiten pflegst, darf es sonst keine DNS-Einträge geben!</b><br><br>

<?php
if ($domain_submitted) {
    ?>
    <h2>Deine Domain wurde eingetragen. Sie muss erst noch bestätigt werden. Nach der Bestätigung musst du noch 24
        Stunden warten, bis die Domain für die WordPress-Seite benutzt werden kann.</h2>
    <?php
}
?>

<form action="<?php echo($_SERVER["REQUEST_URI"]); ?>" method="post">
    Trage anschließend deine Domains hier ein:<br>
    <input type="url" name="<?php echo Domain_Frontend_Util::POST_ATTRIBUT_DOMAIN_EINGABE; ?>" size="50"><br>
    <input type="submit" name="<?php echo Domain_Frontend_Util::POST_ATTRIBUT_DOMAIN_EINGABE_SUBMIT; ?>"
           value="Domain einstellen"><br><br>

    Deine bereits eingestellten Domains:<br>
    <table border="0" cellspacing="0" cellpadding="0" width="100%">
        <thead>
        <td width="50%">Domain</td>
        <td width="30%">Zustand</td>
        <td width="20%">Löschen</td>
        </thead>
        <tr>
            <td width="50%"></td>
            <td width="30%">Sofort nutzbar</td>
            <td width="20%"></td>
        </tr>
    </table>
</form>