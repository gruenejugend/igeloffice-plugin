<h1>Weiterleitung - Standardverwaltung</h1>

Mit einer Weiterleitung werden alle E-Mails an <b><?php echo self::$user->mail; ?></b> an deine private E-Mail-Adresse weitergeleitet. Die private E-Mail-Adresse ist jene, die du im IGELoffice angegeben hast.<br><br>

Um E-Mails von <b><?php echo self::$user->mail; ?></b> verschicken zu können, benötigst du ein Postfach.<br><br>

<input class="chb" type="checkbox" name="io_mailForward" id="io_mailForward" value="true"<?php if(self::$user->useMailForward) { echo ' checked'; } ?>> <b>Ja, ich möchte eine Weiterleitung an meine private E-Mail-Adresse</b><br><br>

<h3>Bitte beachte folgende wichtige Hinweise:</h3>
Zur Zeit ist entweder eine Weiterleitung oder ein Postfach möglich. Beides kann derzeit nicht eingerichtet werden! Wählst du hier das Postfach aus, wird deine Weiterleitung gelöscht.<br><br>

Es besteht die M&ouml;glichkeit, dass manche E-Mails nicht bei deinem privaten Postfach ankommen. Das liegt an der Sicherheits&&uuml;berpr&uuml;fung deines Anbieters für deine private E-Mail-Adresse.<br><hr><br>