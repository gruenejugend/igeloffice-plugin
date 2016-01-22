<?php

/**
 * Description of User_Control
 *
 * @author KWM
 */
class User_Control {
	public static function createUser($first_name, $last_name, $mail) {
		$id = wp_insert_user(array(
			'user_login'	=> $first_name . ' ' . $last_name,
			'user_email'	=> $mail,
			'first_name'	=> $first_name,
			'last_name'		=> $last_name
		));
		
		add_post_meta($id, 'io_user_art', 'User');
		add_post_meta($id, 'io_user_aktiv', false);
	}
	
	public static function createLandesverband($landesverband, $mail) {
		$id = wp_insert_user(array(
			'user_login'	=> $landesverband,
			'user_email'	=> $mail
		));
		
		add_post_meta($id, 'io_user_art', 'Landesverband');
		add_post_meta($id, 'io_user_aktiv', false);
	}
	
	public static function createBasisgruppe($ort, $landesverband, $mail) {
		$id = wp_insert_user(array(
			'user_login'	=> $ort,
			'user_email'	=> $mail
		));
		
		add_post_meta($id, 'io_user_art', 'Basisgruppe');
		add_post_meta($id, 'io_user_lv', $landesverband);
		add_post_meta($id, 'io_user_aktiv', false);
	}
	
	public static function createOrgauser($name, $mail) {
		$id = wp_insert_user(array(
			'user_login'	=> $name,
			'user_email'	=> $mail
		));
		
		add_post_meta($id, 'io_user_art', 'Orgauser');
		add_post_meta($id, 'io_user_aktiv', false);
	}
	
	public static function delete($id) {
		$ldapConnector = ldapConnector::get();
		if($ldapConnector->DNexists($ldapConnector->userDN((new User($id))->user_login))) {
			$ldapConnector->delUser((new User($id))->user_login);
		}
		wp_delete_user($id);
	}
	
	public static function aktivieren($id) {
		update_post_meta($id, 'io_user_aktiv', true);
		$user = new User($id);
		
		$ldapConnector = ldapConnector::get();
		if($user->art == 'User') {
			$ldapConnector->addUser($user->first_name, $user->last_name, $user->user_email);
		} else {
			$ldapConnector->addOrgaUser($user->user_login, $user->user_email);
		}
	}
	
	public static function addPermission($id, $permission_id) {
		$ldapConnector = ldapConnector::get();
		$ldapConnector->addUserPermission((new User($id))->user_login, (new Permission($permission_id))->name);
	}
	
	public static function delPermission($id, $permission_id) {
		$ldapConnector = ldapConnector::get();
		$ldapConnector->delUserPermission((new User($id))->user_login, (new Permission($permission_id))->name);
	}
	
	public static function addToGroup($id, $group_id) {
		$ldapConnector = ldapConnector::get();
		$ldapConnector->addUserToGroup((new User($id))->user_login, (new Group($group_id))->name);
	}
	
	public static function delToGroup($id, $group_id) {
		$ldapConnector = ldapConnector::get();
		$ldapConnector->delUserFromGroup((new User($id))->user_login, (new Group($group_id))->name);
	}
	
	public static function authentifizieruzng($user, $username, $password) {
		if($username == '' || $password == '') {
			return;
		}
		
		if(!($user instanceof WP_User)) {
			$user = get_user_by('login', $username);
			if(!($user instanceof WP_User)) {
				return new WP_Error('user_not_extists', 'Du bist anscheinend noch nicht im IGELoffice registriert.');
			}
		}
		
		$user_id = $user->ID;
		if(get_user_meta($user_id, "io_user_aktiv", true) == 1) {
			return new WP_Error("user_inaktiv", "<strong>FEHLER:</strong> Dein Account wurde noch nicht aktiviert!");
		}
		
		$ldapConn = ldapConnector::get(false);
		
		if($ldapConn->bind($username, $password)) {
			//save password hash in database and key in cookie
			require_once IGELOFFICE_PATH.'class/php-encryption/Crypto.php';
			$key = Crypto::createNewRandomKey();
			$hash = base64_encode(Crypto::encrypt($password, $key));
			setcookie(hash('sha256', $user_name), base64_encode($key));
			unset($key, $password);
			update_user_meta($user_id, '_ldap_pass', $hash);
		}
		elseif($user_name != null) {
			return new WP_Error('ldap_login_failed', 'Deine Zugangsdaten sind nicht korrekt.');
		}
		
		remove_action('authenticate', 'wp_authenticate_username_password', 20);
		return $user;
	}
	
	public static function createLDAP($user) {
		
	}
	
	public static function setPasswordForgotten($user_login) {
		
	}
	
	public static function getPasswordForgotten($key) {
		
	}
	
	public static function endPasswordForgotten($key, $password) {
		
	}
}
