<?php

class io_owncloud {
	public static function mask() {
		if(self::ownCloudIsPermitted()) {
?>Die ownCloud ist die zentrale Datenverwaltung der GRÜNEN JUGEND. Sie ist dazu da, Dateien von Gremien, Arbeitsgruppen oder ähnlichem zentral zu verwalten und zu speichern. Es dient ausserdem dem zentralen Wissensmanagement der GRÜNEN JUGEND und unterstützt dabei die ehrenamtliche Arbeit.<br><br>

Unsere ownCloud ist allerdings kein Angebot zur persönlichen Cloud-Nutzung. Die Speicherung von Daten für sich selbst ist nicht erlaubt und nicht möglich.<br><hr>

<?php
//TODO: LDAP Attribut ownCloudAccount für jeden LDAP Nutzer, der ownCloud bereits nutzt
			self::ownCloudNutzung();
		}
	}
	
	private static function ownCloudNutzung() {
		wp_nonce_field('io_oc_nutzung', 'io_oc_nutzung_nonce');
		
?>
<h1>ownCloud-Nutzung</h1>

Du bist berechtigt, die ownCloud zu nutzen. Du kannst dich bereits einloggen.<br><br>

<b>Beachte bitte:</b> Die ownCloud des Bundesverbandes ist nicht dein persönlicher Cloud-Space. Du hast nicht die Möglichkeit, Daten in deinem persönlichen Space hochzuladen oder sie dorthin zu verschieben.<br><br>

Durch deine Gruppen-Mitgliedschaften werden deinem ownCloud-Zugang diverse Ordner freigegeben. Dieser zentrale Space ist deine einzige Möglichkeit, Dateien anzuschauen, zu verwalten, zu bearbeiten, herunterzuladen oder hochzuladen. Bitte beachte dabei den Sinn und Zweck des jeweiligen Ordners, der sich anhand dessen Namens ableiten kann.<br><br>

<h2>ownCloud-Zugang</h2>
ownCloud-URL: <b><a href="https://cloud.gruene-jugend.de" target="_blank">cloud.gruene-jugend.de</a></b><br>
Benutzer*innen*name: <b><?php echo wp_get_current_user()->user_login; ?></b><br>
Passwort: <b>Dein IGELoffice-Passwort</b>

<?php
	}
	
	public static function ownCloudIsPermitted() {
		$ldapConn = ldapConnector::get();
		
		//return $ldapConn->isQualified(wp_get_current_user()->user_login, "ownCloud-Benutzung") ||
		//	$ldapConn->getUserAttribute(wp_get_current_user()->user_login, 'ownCloudAccount');
		return true;
	}
}
