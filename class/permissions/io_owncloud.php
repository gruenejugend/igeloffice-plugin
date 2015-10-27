<?php

class io_owncloud {
	public static function mask() {
		$ldapConn = ldapConnector::get();
		
		if(self::ownCloudIsPermitted()) {
?>Die ownCloud ist die zentrale Datenverwaltung der GRÜNEN JUGEND. Sie ist dazu da, Dateien von Gremien, Arbeitsgruppen oder ähnlichem zentral zu verwalten und zu speichern. Es dient ausserdem dem zentralen Wissensmanagement der GRÜNEN JUGEND und unterstützt dabei die ehrenamtliche Arbeit.<br><br>

Unsere ownCloud ist allerdings kein Angebot zur persönlichen Cloud-Nutzung. Die Speicherung von Daten für sich selbst ist nicht erlaubt und nicht möglich.<br><hr>

<form action="<?php echo($_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]); ?>" method="post">
<?php
//TODO: LDAP Attribut ownCloudAccount für jeden LDAP Nutzer, der ownCloud bereits nutzt
		self::ownCloudNutzung($ldapConn->getUserAttribute(wp_get_current_user()->user_login, 'ownCloudAccount') ? " checked" : "");
?>
	<input type="submit" name="io_oc_submit" value="Bestätigen">
</form>
<?php
		}
	}
	
	private static function ownCloudNutzung($checked) {
		wp_nonce_field('io_oc_nutzung', 'io_oc_nutzung_nonce');
		
		if(isset($_POST['io_oc_submit'])) {
			self::ownCloudNutzungSave();
		}
?>
<h1>ownCloud-Nutzung</h1>

Du bist berechtigt, die ownCloud zu nutzen. Du musst nur noch deinen IGELoffice-Account für die ownCloud zulassen, in dem du die untere Checkbox betätigst.<br><br>

<b>Beachte bitte:</b> Die ownCloud des Bundesverbandes ist nicht dein persönlicher Cloud-Space. Du hast nicht die Möglichkeit, Daten in deinem persönlichen Space hochzuladen oder sie dorthin zu verschieben.<br><br>

Durch deine Gruppen-Mitgliedschaften werden deinem ownCloud-Zugang diverse Ordner freigegeben. Dieser zentrale Space ist deine einzige Möglichkeit, Dateien anzuschauen, zu verwalten, zu bearbeiten, herunterzuladen oder hochzuladen. Bitte beachte dabei den Sinn und Zweck des jeweiligen Ordners, der sich anhand des Namens ableiten kann.<br><br>

<h2>ownCloud-Zugang</h2>
ownCloud-URL: <b><a href="https://owncloud.gruene-jugend.de" target="_blank">owncloud.gruene-jugend.de</a></b><br>
Benutzer*innen*name: <b><?php echo wp_get_current_user()->user_login; ?></b><br>
Passwort: <b>Dein IGELoffice-Passwort</b><br><br>

<input type="checkbox" name="io_oc_nutzung" value="true"<?php echo $checked; ?>> <b>Ja, ich möchte einen ownCloud-Zugang</b><br><hr>

<?php
	}
	
	private static function ownCloudNutzungSave() {
		if( !isset($_POST['io_oc_nutzung_nonce']) || 
			!wp_verify_nonce($_POST['io_oc_nutzung_nonce'], 'io_oc_nutzung') || 
			defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
			return;
		}
		
		$ldapConn = ldapConnector::get();
		
		//Wenn Haken entnommen
		if($ldapConn->getUserAttribute(wp_get_current_user()->user_login, 'ownCloudAccount') && !isset($_POST['io_oc_nutzung'])) {
			$ldapConn->delUserAttribute(wp_get_current_user()->user_login, 'ownCloudAccount');
			
			?><b>Dein ownCloud-Zugang wurde gelöscht.</b><?php
		}
		
		//Wenn Haken gesetzt
		if(isset($_POST['io_oc_nutzung']) && $_POST['io_oc_nutzung'] == "true" && !$ldapConn->getUserAttribute(wp_get_current_user()->user_login, 'ownCloudAccount')) {
			$ldapConn->setUserAttribute(wp_get_current_user()->user_login, 'ownCloudAccount', 1);
			
			?><b>Die ownCloud-Zugang wurde erstellt.</b><?php
		}
	}
	
	public static function ownCloudIsPermitted() {
		$ldapConn = ldapConnector::get();
		
		//return $ldapConn->isQualified(wp_get_current_user()->user_login, "ownCloud-Benutzung") ||
		//	$ldapConn->getUserAttribute(wp_get_current_user()->user_login, 'ownCloudAccount');
		return true;
	}
}
