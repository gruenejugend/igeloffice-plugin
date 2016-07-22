<?php

/**
 * Description of Auth_Backend_View
 *
 * @author KWM
 */
class Auth_Backend_View {
	public static function authentifizierung($user, $user_name, $password) {
		if(empty($user_name) || empty($password)) {
			return;
		}
		
		if(!($user instanceof WP_User)) {
			$user = get_user_by('login', $user_name);
			if(!($user instanceof WP_User)) {
				return new WP_Error('user_not_extists', 'Du bist anscheinend noch nicht im IGELoffice registriert.');
			}
		}

		$user_id = $user->ID;
		
		if(get_user_meta($user_id, User_Util::ATTRIBUT_AKTIV, true) != 1) {
			return new WP_Error("user_inaktiv", "<strong>FEHLER:</strong> Dein Account wurde noch nicht aktiviert!");
		}
		
		try {
			$ldapConn = ldapConnector::get(false);
			
			if($ldapConn->bind($user_name, $password)) {
				//save password hash in database and key in cookie
				require_once IGELOFFICE_PATH.'control/php-encryption/Crypto.php';
				$key = Crypto::createNewRandomKey();
				$hash = base64_encode(Crypto::encrypt($password, $key));
				setcookie(hash('sha256', $user_name), base64_encode($key));
				unset($key, $password);
				update_user_meta($user_id, '_ldap_pass', $hash);
			}
			elseif($user_name != null) {
				return new WP_Error('ldap_login_failed', 'Deine Zugangsdaten sind nicht korrekt.');
			}
		} catch(Exception $ex) {
			return new WP_Error('ldap_login_failed', 'Deine Zugangsdaten sind nicht korrekt.');
		}

		return $user;
	}
}
