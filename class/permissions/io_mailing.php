<?php

//TODO: Generelle Frage, Umgang mit mehreren E-Mail-Adressen
//TODO: Aliase als Weiterleitung, Alias: alias != self::$mailBox
//TODO: Antrag auf Alias

/**
 * Masken für die Berechtigungsverwaltung Mailing
 *
 * @author KWM
 */
class io_mailing {
	private static $mail;
	private static $mailBox;
	
	public static function mask() {
		$ldapConn = ldapConnector::get();
		
		self::$mail = io_case_mail_change(wp_get_current_user()->user_firstname) . '.' . io_case_mail_change(wp_get_current_user()->user_lastname) . '@gruene-jugend.de';
		if(	self::$mail == self::getGJMail($ldapConn->getUserAttribute(wp_get_current_user()->user_login, 'mail')) &&
			self::isGJMail($ldapConn->getUserAttribute(wp_get_current_user()->user_login, 'mail'))) {
			self::$mailBox = self::$mail;
		} else if(self::isGJMail($ldapConn->getUserAttribute(wp_get_current_user()->user_login, 'mail'))) {
			self::$mailBox = self::getGJMail($ldapConn->getUserAttribute(wp_get_current_user()->user_login, 'mail'));
		} else {
			self::$mailBox = self::$mail;
		}
		
		if(self::mailIsPermitted()) {
			
			?>

Deine E-Mail-Adresse der GRÜNEN JUGEND lautet:<br><br>
<b><?php 
		echo self::$mail; 

		if(self::$mailBox != self::$mail) {
			echo ' (Postfach: ' . self::$mailBox . ')';
		}

?></b><br><hr>

<form action="<?php echo($_SERVER["REQUEST_URI"]); ?>" method="post">
			<?php
			
			if(self::mailForwardIsPermitted()) {
				self::mailForwarding(self::isGJMailForward($ldapConn->getUserAttribute(wp_get_current_user()->user_login, "mailForwardingAddress"), self::$mail) ? " checked" : "");
			}
			
			if(self::mailPostboxIsPermitted()) {
				self::mailBox(self::isGJMail($ldapConn->getUserAttribute(wp_get_current_user()->user_login, 'mail')) ? " checked" : "");
			}
			
			?>
	<input type="submit" name="io_mail_submit" value="Bestätigen">
</form>	

<script type="text/javascript">
	$(".chb").each(function() {
		$(this).change(function()
		{
			$(".chb").prop('checked',false);
			$(this).prop('checked',true);
		});
	});
</script>
<?php
		}
	}
	
	private static function mailForwarding($checked) {
		wp_nonce_field('io_mail_forward', 'io_mail_forward_nonce');
		
		if(isset($_POST['io_mail_submit'])) {
			self::mailForwardingSave();
		} else {
		
		?>

<h1>Weiterleitung</h1>

Mit einer Weiterleitung werden alle E-Mails an <b><?php echo(io_case_mail_change(wp_get_current_user()->user_firstname) . '.' . io_case_mail_change(wp_get_current_user()->user_lastname)); ?>@gruene-jugend.de</b> an deine private E-Mail-Adresse weitergeleitet. Die private E-Mail-Adresse ist jene, die du im IGELoffice angegeben hast.<br><br>

Um E-Mails von <?php echo(io_case_mail_change(wp_get_current_user()->user_firstname) . '.' . io_case_mail_change(wp_get_current_user()->user_lastname)); ?>@gruene-jugend.de verschicken zu können, benötigst du ein Postfach. Eine Weiterleitung kann parallel zum Postfach existieren.<br><br>

<input class="chb" type="checkbox" name="io_mail_forward" value="true"<?php echo $checked; ?>> <b>Ja, ich möchte eine Weiterleitung an meine private Mail-Adresse <?php echo (wp_get_current_user()->user_email); ?></b><br><br>

<b>ACHTUNG: Zur Zeit ist entweder eine Weiterleitung oder ein Postfach möglich. Beides kann derzeit nicht eingerichtet werden! Wählst du hier die Weiterleitung aus, wird dein Postfach gelöscht.</b><br><hr>
		<?php
		}
	}
	
	private static function mailForwardingSave() {
		if( !isset($_POST['io_mail_forward_nonce']) || 
			!wp_verify_nonce($_POST['io_mail_forward_nonce'], 'io_mail_forward') || 
			defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
			return;
		}
		
		$ldapConn = ldapConnector::get();
		
		if(self::isGJMailForward($ldapConn->getUserAttribute(wp_get_current_user()->user_login, "mailForwardingAddress"), self::$mail) && !isset($_POST['io_mail_forward'])) {
			$ldapConn->delUserAttribute(wp_get_current_user()->user_login, 'mailForwardingAddress', self::$mail)[0];
			
			//Wenn Postfach gesetzt ist, dann Mail-Attribut der privaten Adresse hinzufügen
			if(self::isGJMail($ldapConn->getUserAttribute(wp_get_current_user()->user_login, 'mail'))) {
				//TODO: Abhänging der LDAP-Änderung, Hinzufügen des Attributs
				$ldapConn->delUserAttribute(wp_get_current_user()->user_login, 'mail', $ldapConn->getUserAttribute(wp_get_current_user()->user_login, 'mailAlternateAddress')[0]);
			}
			
			?><b>Die Weiterleitung wurde gelöscht.</b><?php
		}
		
		if(isset($_POST['io_mail_forward']) && $_POST['io_mail_forward'] == true && !self::isGJMailForward($ldapConn->getUserAttribute(wp_get_current_user()->user_login, "mailForwardingAddress"), self::$mail)) {
			$ldapConn->setUserAttribute(wp_get_current_user()->user_login, 'mailForwardingAddress', self::$mail)[0];

			//Wenn Postfach gesetzt ist, dann Mail-Attribut der privaten Adresse hinzufügen
			if(self::isGJMail($ldapConn->getUserAttribute(wp_get_current_user()->user_login, 'mail'))) {
				//TODO: Abhänging der LDAP-Änderung, Hinzufügen des Attributs
				$ldapConn->setUserAttribute(wp_get_current_user()->user_login, 'mail', $ldapConn->getUserAttribute(wp_get_current_user()->user_login, 'mailAlternateAddress')[0]);
			}
			
			?><b>Die Weiterleitung wurde eingerichtet.</b><?php
		}
	}
	
	private static function mailBox($checked) {
		wp_nonce_field('io_mail_box', 'io_mail_box_nonce');
		
		if(isset($_POST['io_mail_submit'])) {
			self::mailBoxSave();
		} else {
		
		?>
		
			<h1>Postfach</h1>
			
			Mit einem Postfach bist du in der Lage E-Mails zu Empfangen und gesondert zu verwalten. Ebenso ist es die einzige Möglichkeit, E-Mails mit deiner Mail-Adresse bei der GRÜNEN JUGEND zu verfassen und zu verschicken.<br><br>
			
			<h2>Webinterface</h2>
			Du findest das Webinterface unter <b><a href="http://mail.gruene-jugend.de" target="_blank">mail.gruene-jugend.de</a></b><br>
			Login-Name: <b><?php echo self::$mailBox; ?></b><br>
			Passwort: <b>Dein IGELoffice-Passwort</b><br>
			
			<h2>IMAP für Mail-Client</h2>
			Um dein Postfach in einem Mail-Client einzurichten, beachte bitte folgende Konfigurationen:<br><br>
			
			IMAP-Server: <b>mail.gruene-jugend.de</b><br>
			IMAP-Port: <b>143</b><br>
			Verbindungssicherheit: <b>STARTTLS</b><br>
			Benutzer*innen*name: <b><?php echo self::$mailBox; ?></b><br>
			Passwort: <b>Dein IGELoffice-Passwort</b><br><br>
			
			<b>POP3 ist nicht zu empfehlen und daher technisch nicht möglich.</b>
			
			<h2>SMTP für Mail-Client</h2>
			Um E-Mails von deinem Mail-Client aus zu versenden, beachte bitte folgende Konfigurationen:<br><br>
			
			SMTP-Server: <b>mail.gruene-jugend.de</b><br>
			SMTP-Port: <b>25</b><br>
			Verbindungssicherheit: <b>STARTTLS</b><br>
			Benutzer*innen*name: <b><?php echo self::$mailBox; ?></b><br>
			Passwort: <b>Dein IGELoffice-Passwort</b><br><br>
			
			<input class="chb" type="checkbox" name="io_mail_box" value="true"<?php echo $checked; ?>> <b>Ja, ich möchte meine E-Mail-Adresse als Postfach nutzen</b><br><br>	
			
			<b>ACHTUNG: Zur Zeit ist entweder eine Weiterleitung oder ein Postfach möglich. Beides kann derzeit nicht eingerichtet werden! Wählst du hier das Postfach aus, wird die Weiterleitung gelöscht..</b><br><br>

			<b>Hinweis:</b> Mit dem Ende eines Amtes oder einer ähnlichen Aufgabe, die dich legitimiert ein Postfach zu besitzen, endet auch die Berechtigung deine E-Mail-Adresse auch als Postfach einzurichten. Nach deiner Amtszeit kannst du das Postfach aber weiterhin nutzen, bis du es hier deaktivierst. Nach einer Deaktivierung kann dein Postfach nicht wieder aktiviert werden, solltest du dafür keine Legitimation besitzen.<br><br>
			
			Auf Grund der Sicherheit behält sich der*die Webmaster vor, einzelne Postfächer eigenständig auf kurze Zeit oder auf Dauer zu deaktivieren. Dies ist insbesondere der Fall, wenn das Postfach mehrere Monate inaktiv war.<br><hr>
		<?php
		}
	}
	
	private static function mailBoxSave() {
		if( !isset($_POST['io_mail_box_nonce']) || 
			!wp_verify_nonce($_POST['io_mail_box_nonce'], 'io_mail_box') || 
			defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
			return;
		}
		
		$ldapConn = ldapConnector::get();
		
		if(self::isGJMail($ldapConn->getUserAttribute(wp_get_current_user()->user_login, 'mail')) && !isset($_POST['io_mail_box'])) {
			//Wenn eine Weiterleitung besteht
			if(self::isGJMailForward($ldapConn->getUserAttribute(wp_get_current_user()->user_login, "mailForwardingAddress"), self::$mail)) {
				//TODO: Abhänging der LDAP-Änderung, Hinzufügen des Attributs
				$ldapConn->delUserAttribute(wp_get_current_user()->user_login, 'mail', self::$mail);
			} else {
				$ldapConn->setUserAttribute(wp_get_current_user()->user_login, 'mail', $ldapConn->getUserAttribute(wp_get_current_user()->user_login, 'mailAlternateAddress')[0]);
			}
			
			?><b>Dein Postfach wurde gelöscht.</b><?php
		}
		
		if(isset($_POST['io_mail_box']) && $_POST['io_mail_box'] == true && !self::isGJMail($ldapConn->getUserAttribute(wp_get_current_user()->user_login, 'mail'))) {
			$ldapConn->setUserAttribute(wp_get_current_user()->user_login, 'mailAlternateAddress', $ldapConn->getUserAttribute(wp_get_current_user()->user_login, 'mail')[0]);
			
			//TODO: LDAP Änderung, hier: Veränderung des Attributes
			$ldapConn->setUserAttribute(wp_get_current_user()->user_login, 'mail', self::$mail, "replace", $ldapConn->getUserAttribute(wp_get_current_user()->user_login, "mailAlternateAddress")[0]);
			
			//Wenn eine Weiterleitung besteht, Weiterleitung weiter aufrecht halten
			if(self::isGJMailForward($ldapConn->getUserAttribute(wp_get_current_user()->user_login, "mailForwardingAddress"), self::$mail)) {
				//TODO: Abhänging der LDAP-Änderung, Hinzufügen des Attributs
				$ldapConn->setUserAttribute(wp_get_current_user()->user_login, 'mail', $ldapConn->getUserAttribute(wp_get_current_user()->user_login, 'mailAlternateAddress')[0]);
			}
			
			?><h2>Dein Postfach wurde eingerichtet.</h2><?php
		}
	}
	
	private static function isGJMail($array) {
		if(count($array) > 0) {
			foreach($array AS $mail) {
				if(strpos($mail, '@gruene-jugend.de')) {
					return true;
				}
			}
		}
		return false;
	}
	
	private static function getGJMail($array) {
		if(count($array) > 0) {
			foreach($array AS $mail) {
				if(strpos($mail, '@gruene-jugend.de')) {
					return $mail;
				}
			}
		}
		return false;
	}
	
	private static function isGJMailForward($array, $forward) {
		if(count($array) > 0) {
			foreach($array AS $mail) {
				if($mail == $forward) {
					return true;
				}
			}
		}
		return false;
	}
	
	public static function mailIsPermitted() {
		return self::mailForwardIsPermitted() || self::mailPostboxIsPermitted();
	}
	
	public static function mailForwardIsPermitted() {
		$ldapConn = ldapConnector::get();
		
		return	($ldapConn->isQualified(wp_get_current_user()->user_login, "Mail-Weiterleitung") ||
				self::isGJMailForward($ldapConn->getUserAttribute(wp_get_current_user()->user_login, "mailForwardingAddress"), self::$mail)) &&
				!in_array("Bundesvorstand", $ldapConn->getUserGroups(wp_get_current_user()->user_login));
	}
	
	public static function mailPostboxIsPermitted() {
		$ldapConn = ldapConnector::get();
		
		return	($ldapConn->isQualified(wp_get_current_user()->user_login, "Mail-Postfach") ||
				self::isGJMail($ldapConn->getUserAttribute(wp_get_current_user()->user_login, 'mail'))) &&
				!in_array("Bundesvorstand", $ldapConn->getUserGroups(wp_get_current_user()->user_login));;
	}
	
	
	
	
	//TODO: Oben beachten
	private static function mailForwardings() {
		
	}
	
	private static function mailForwardingsSave() {
		
	}
	
	private static function mailBoxes() {
		
	}
	
	private static function mailBoxesSave() {
		
	}
}
