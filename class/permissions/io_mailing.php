<?php

/**
 * Masken für die Berechtigungsverwaltung Mailing
 *
 * @author KWM
 */
class io_mailing {
	public static function mask() {
		$ldapConn = ldapConnector::get();
		
		//TODOs beachten
		if(	$ldapConn->isQualified(wp_get_current_user()->user_login, "Mail-Weiterleitung") ||
			$ldapConn->isQualified(wp_get_current_user()->user_login, "Mail-Postfach") ||
				//TODO: Mailweiterleitung Attribut
			$ldapConn->getUserAttribute(wp_get_current_user()->user_login, "") != "" ||
				//TODO: Mailpostfach Attribut
				//TODO: Prüfung ob Mail als Server Domain da ist, wenn ja: Postfach, wenn nein: kein Postfach
			$ldapConn->getUserAttribute(wp_get_current_user()->user_login, "") != "") {
			
			?>

Deine E-Mail-Adresse der GRÜNEN JUGEND lautet:<br><br>
<b><?php echo(wp_get_current_user()->user_firstname . '.' . wp_get_current_user()->user_lastname); ?>@gruene-jugend.de</b><br><hr>

<form action="<?php echo($_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]); ?>" method="post">
			<?php
			
			if(	$ldapConn->isQualified(wp_get_current_user()->user_login, "Mail-Weiterleitung") ||
					//TODO: Mailweiterleitung Attribut
				$ldapConn->getUserAttribute(wp_get_current_user()->user_login, "") != "") {
				self::mailForwarding();
			}
			
			if(	$ldapConn->isQualified(wp_get_current_user()->user_login, "Mail-Postfach") ||
					//TODO: Mailpostfach Attribut
				$ldapConn->getUserAttribute(wp_get_current_user()->user_login, "") != "") {
				self::mailBox();
			}
			
			//TODO: Checked!!!
			?>
	<input type="submit" name="io_mail_submit" value="Bestätigen">
</form>	
<?php
		}
	}
	
	private static function mailForwarding() {
		wp_nonce_field('io_mail_forward', 'io_mail_forward_nonce');
		
		if(isset($_POST['io_mail_submit'])) {
			self::mailForwardingSave();
		}
		
		?>

<h1>Weiterleitung</h1>

Mit einer Weiterleitung werden alle E-Mails an <b><?php echo(wp_get_current_user()->user_firstname . '.' . wp_get_current_user()->user_lastname); ?>@gruene-jugend.de</b> an deine private E-Mail-Adresse weitergeleitet. Die private E-Mail-Adresse ist jene, die du im IGELoffice angegeben hast.<br><br>

Um E-Mails von <?php echo(wp_get_current_user()->user_firstname . '.' . wp_get_current_user()->user_lastname); ?>@gruene-jugend.de verschicken zu können, benötigst du ein Postfach. Eine Weiterleitung kann parallel zum Postfach existieren.

<input type="checkbox" name="io_mail_forward" value="true"> <b>Ja, ich möchte eine Weiterleitung an meine private Mail-Adresse <?php echo (wp_get_current_user()->user_email); ?></b><br><hr>

		<?php
	}
	
	private static function mailForwardingSave() {
		if( !isset($_POST['io_mail_forward_nonce']) || 
			!wp_verify_nonce($_POST['io_mail_forward_nonce'], 'io_mail_forward') || 
			defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
			return;
		}
		
		$ldapConn = ldapConnector::get();
		
		//TODO: Weiterleitung löschen
		//TODO: Mailweiterleitung Attribut
		if($ldapConn->getUserAttribute(wp_get_current_user()->user_login, "") != "" && !isset($_POST['io_mail_forward'])) {
			
			?><b>Die Weiterleitung wurde gelöscht.</b><?php
		}
		
		//TODO: Weiterleitung einrichten
		if(isset($_POST['io_mail_forward']) && $_POST['io_mail_forward'] == true) {
			
			?><b>Die Weiterleitung wurde eingerichtet.</b><?php
		}
	}
	
	private static function mailBox() {
		wp_nonce_field('io_mail_postfach', 'io_mail_postfach_nonce');
		
		if(isset($_POST['io_mail_submit'])) {
			self::mailBoxSave();
		}
		
		?>
		
			<h1>Postfach</h1>
			
			Mit einem Postfach bist du in der Lage E-Mails zu Empfangen und gesondert zu verwalten. Ebenso ist es die einzige Möglichkeit, E-Mails mit deiner Mail-Adresse bei der GRÜNEN JUGEND zu verfassen und zu verschicken.<br><br>
			
			<h2>Daten zum Login</h2>
			Login-Name: <b><?php echo(wp_get_current_user()->user_firstname . '.' . wp_get_current_user()->user_lastname); ?>@gruene-jugend.de</b>
			Passwort: <b>Dein IGELoffice-Passwort</b>
			
			<h2>Webinterface</h2>
			Du findest das Webinterface unter <b><a href="http://mail.gruene-jugend.de" target="_blank">mail.gruene-jugend.de</a></b>
			
			<h2>IMAP für Mail-Client</h2>
			Um dein Postfach in einem Mail-Client einzurichten, beachte bitte folgende Konfigurationen:<br><br>
			
			IMAP-Server: <b>mail.gruene-jugend.de</b><br>
			IMAP-Port: <b>143</b><br>
			Verbindungssicherheit: <b>STARTTLS</b><br>
			Benutzer*innen*name: <b><?php echo(wp_get_current_user()->user_firstname . '.' . wp_get_current_user()->user_lastname); ?>@gruene-jugend.de</b><br>
			Passwort: <b>Dein IGELoffice-Passwort</b><br><br>
			
			<b>POP3 ist nicht zu empfehlen und daher technisch nicht möglich.</b>
			
			<h2>SMTP für Mail-Client</h2>
			Um E-Mails von deinem Mail-Client aus zu versenden, beachte bitte folgende Konfigurationen:<br><br>
			
			SMTP-Server: <b>mail.gruene-jugend.de</b><br>
			SMTP-Port: <b>25</b><br>
			Verbindungssicherheit: <b>STARTTLS</b><br>
			Benutzer*innen*name: <b><?php echo(wp_get_current_user()->user_firstname . '.' . wp_get_current_user()->user_lastname); ?>@gruene-jugend.de</b><br>
			Passwort: <b>Dein IGELoffice-Passwort</b><br><br>
			
			<?php //TODO: Checked ?>
			<input type="checkbox" name="io_mail_box" value="true"> <b>Ja, ich möchte meine E-Mail-Adresse als Postfach nutzen <?php echo (wp_get_current_user()->user_email); ?></b><br><br>
			
			<b>Hinweis:</b> Mit dem Ende eines Amtes oder einer ähnlichen Aufgabe, die dich legitimiert ein Postfach zu besitzen, endet auch die Berechtigung deine E-Mail-Adresse auch als Postfach einzurichten. Nach deiner Amtszeit kannst du das Postfach aber weiterhin nutzen, bis du es hier deaktivierst. Nach einer Deaktivierung kann dein Postfach nicht wieder aktiviert werden, solltest du dafür keine Legitimation besitzen.<br><br>
			
			Auf Grund der Sicherheit behält sich der*die Webmaster vor, einzelne Postfächer eigenständig auf kurze Zeit oder auf Dauer zu deaktivieren. Dies ist insbesondere der Fall, wenn das Postfach mehrere Monate inaktiv war.
		<?php
	}
	
	private static function mailBoxSave() {
		if( !isset($_POST['io_mail_box_nonce']) || 
			!wp_verify_nonce($_POST['io_mail_box_nonce'], 'io_mail_box') || 
			defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
			return;
		}
		
		$ldapConn = ldapConnector::get();
		
		//TODO: Postfach löschen
		//TODO: Mailpostfach Attribut
		if($ldapConn->getUserAttribute(wp_get_current_user()->user_login, "") != "" && !isset($_POST['io_mail_box'])) {
			
			?><b>Dein Postfach wurde gelöscht.</b><?php
		}
		
		//TODO: Postfach einrichten
		if(isset($_POST['io_mail_box']) && $_POST['io_mail_box'] == true) {
			
			?><b>Dein Postfach wurde eingerichtet.</b><?php
		}
	}
	
	
	
	
	
	
	
	
	private static function mailForwardings() {
		
	}
	
	private static function mailForwardingsSave() {
		
	}
	
	private static function mailBoxes() {
		
	}
	
	private static function mailBoxesSave() {
		
	}
}
