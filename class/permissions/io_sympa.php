<?php

class io_sympa {
	public static function mask() {
?>
<h1>Sympa - Mailinglisten in der GRÜNEN JUGEND</h1>

Die Mailinglisten der GRÜNEN JUGEND werden mittels Sympa betrieben. Sympa ist ein eigenständiges System, weshalb es keine Konfigurationen oder Einstellungen aus dem IGELoffice heraus bedarf.<br><br>

Mitglieder von Gruppen innerhalb des IGELoffices werden automatisch als Subscriber oder sogar als Moderator*innen* einer Liste automatisch hinzugefügt.<br><br>

Die Berechtigungen innerhalb von Sympa sind also fest an deine Gruppen-Mitgliedschaft gebunden. Es bedarf nur noch einer Registration innerhalb von Sympa.<br><br>

Du findest Sympa unter <b><a href="https://listen.gruene-jugend.de/sympa" target="_blank">listen.gruene-jugend.de/sympa</a></b>. Registrieren kannst du dich oben links unter "First-Login?".
<?php
	//TODO: Was heißt es, wenn LDAP Gruppen einer Liste hinzugefügt werden? Zugriff via LDAP oder via deren E-Mail-Adresse?
	}
}
