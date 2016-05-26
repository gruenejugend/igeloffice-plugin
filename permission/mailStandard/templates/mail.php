<h1>Postfach - Standardverwaltung</h1>

Mit einem Postfach bist du in der Lage E-Mails zu Empfangen und gesondert von deinen privaten Postfächern zu verwalten. Ebenso ist es die einzige Möglichkeit, E-Mails mit deiner E-Mail-Adresse zu verfassen und zu verschicken.<br><br>

<h2>Webinterface</h2>

Mittels des Webinterfaces kannst du dein Postfach online im Browser verwalten. Hier kannst du auch E-Mails lesen und schreiben.<br><br>

Du findest das Webinterface unter <b><a href="https://mail.gruene-jugend.de" target="_blank">mail.gruene-jugend.de</a></b>.<br>
Login-Name: <b><?php echo self::$user->mail; ?></b><br>
Passwort: <b>Dein IGELoffice-Passwort</b><br><br>



<h2>IMAP für den Mail-Empfang in einem Mail-Client</h2>

Um dein Postfach in einem Mail-Client einzurichten, beachte bitte folgende Konfigurationen:<br><br>

IMAP-Server: <b>mail.gruene-jugend.de</b><br>
IMAP-Port: <b>143</b><br>
Verbindungssicherheit: <b>STARTTLS</b><br>
Benutzer*innenname: <b><?php echo self::$user->mail; ?></b><br>
Passwort: <b>Dein IGELoffice-Passwort</b><br><br>

<b>POP3 als E-Mail-Protokoll ist nicht zu empfehlen und daher technisch nicht möglich.</b><br><br>

<h2>SMTP für den Mail-Versand in einem Mail-Client</h2>

Um E-Mails von deinem Mail-Client aus zu versenden, beachte bitte folgende Konfigurationen:<br><br>

SMTP-Server: <b>mail.gruene-jugend.de</b><br>
SMTP-Port: <b>25</b><br>
Verbindungssicherheit: <b>STARTTLS</b><br>
Benutzer*innenname: <b><?php echo self::$user->mail; ?></b><br>
Passwort: <b>Dein IGELoffice-Passwort</b><br><br>

<input class="chb" type="checkbox" name="io_mail" id="io_mail" value="true"<?php if(self::$user->useMail) { echo ' checked'; } ?>> <b>Ja, ich möchte meine E-Mail-Adresse als Postfach nutzen</b><br><br>

<h3>Bitte beachte folgende wichtige Hinweise:</h3>
Zur Zeit ist entweder eine Weiterleitung oder ein Postfach möglich. Beides kann derzeit nicht eingerichtet werden! Wählst du hier das Postfach aus, wird deine Weiterleitung gelöscht.<br><br>

Bei Problemen bei der Einrichtung deines Postfaches in einem Client, <b>vergewissere dich bitte, dass die oben genannten Konfigurationen zu 100% so eingetragen sind, wie sie hier stehen</b>. Die meisen Problemmeldungen mit E-Mail-Postf&auml;chern lassen sich durch falsche Konfigurationen erklären.<br><br>

Mit dem Ende eines Amtes oder einer &auml;hnlichen Aufgabe, die dich legitimiert ein Postfach zu besitzen, endet auch die Berechtigung deine E-Mail-Adresse als Postfach einzurichten. Wir erlauben es dir aber, dein Postfach nach deiner Amtszeit weiter zu nutzen, bis du es hier deaktivierst. Nach einer Deaktivierung kann dein Postfach nicht wieder aktiviert werden, solltest du keine entsprechende Legitimation daf&uml;r besitzen.<br><br>

Es kann sein, dass mit der Deaktivierung einer E-Mail-Adresse der s&auml;mtliche Inhalt des Postfaches verloren geht. Bitte kümmere dich entsprechend um eine Sicherung.<br><br>

Auf Grund der Sicherheit behält sich der*die Webmaster vor, einzelne Postf&auml;cher eigenständig auf kurze Zeit oder auf Dauer zu deaktivieren. Dies ist insbesondere der Fall, wenn das Postfach mehrere Monate inaktiv war.<br><hr><br>