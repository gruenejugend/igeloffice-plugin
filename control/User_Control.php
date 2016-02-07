<?php

/**
 * Description of User_Control
 *
 * @author KWM
 */
class User_Control {
	public static function deletePost() {
		unset($_POST['user_art']);
		unset($_POST['first_name']);
		unset($_POST['last_name']);
		unset($_POST['io_users_nonce']);
		unset($_POST['land']);
	}
	
	public static function createUser($first_name, $last_name, $mail) {
		self::deletePost();
		$id = wp_insert_user(array(
			'user_login'	=> $first_name . ' ' . $last_name,
			'user_email'	=> $mail
		));
		
		$_POST['user_art'] = 'user';
		$_POST['first_name'] = $first_name;
		$_POST['last_name'] = $last_name;
		$_POST['io_users_nonce'] = wp_create_nonce('io_users');
		
		self::createMeta($id);
		
		return $id;
	}
	
	public static function createLandesverband($landesverband, $mail) {
		self::deletePost();
		$id = wp_insert_user(array(
			'user_login'	=> $landesverband,
			'user_email'	=> $mail
		));
		
		$_POST['user_art'] = 'landesverband';
		$_POST['io_users_nonce'] = wp_create_nonce('io_users');
		
		self::createMeta($id);
		
		return $id;
	}
	
	public static function createBasisgruppe($ort, $landesverband, $mail) {
		self::deletePost();
		$id = wp_insert_user(array(
			'user_login'	=> $ort,
			'user_email'	=> $mail
		));
		
		$_POST['user_art'] = 'basisgruppe';
		$_POST['land'] = $landesverband;
		$_POST['io_users_nonce'] = wp_create_nonce('io_users');
		
		self::createMeta($id);
		
		return $id;
	}
	
	public static function createOrgauser($name, $mail) {
		self::deletePost();
		$id = wp_insert_user(array(
			'user_login'	=> $name,
			'user_email'	=> $mail
		));
		
		$_POST['user_art'] = 'organisatorisch';
		$_POST['io_users_nonce'] = wp_create_nonce('io_users');
		
		self::createMeta($id);
		
		return $id;
	}
	
	public static function createMeta($user_id) {
		if(!empty($_POST['user_art'])) {
			if( !isset($_POST['io_users_nonce']) || 
				!wp_verify_nonce($_POST['io_users_nonce'], 'io_users') || 
				defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
				return;
			}
			
			update_user_meta($user_id, "io_user_art", sanitize_text_field($_POST['user_art']));
			update_user_meta($user_id, "io_user_aktiv", 0);
			
			if(LDAP_Proxy::isLDAPUser(get_userdata($user_id)->user_login, get_userdata($user_id)->user_email) === true) {
				User_Control::aktivieren($user_id, false);
			}
			
			if($_POST['user_art'] == "user") {
				update_user_meta($user_id, 'first_name', sanitize_text_field($_POST['first_name']));
				update_user_meta($user_id, 'last_name', sanitize_text_field($_POST['last_name']));
			} elseif($_POST['user_art'] == "basisgruppe") {
				update_user_meta($user_id, "io_user_lv", sanitize_text_field($_POST['land']));
			}
		}
	}
	
	public static function delete($id) {
		$ldapConnector = ldapConnector::get();
		if($ldapConnector->DNexists('cn='.(new User($id))->user_login.',ou=users,dc=gruene-jugend,dc=de')) {
			$ldapConnector->delUser((new User($id))->user_login);
		}
		wp_delete_user($id);
	}
	
	public static function aktivieren($id, $add = true) {
		update_user_meta($id, 'io_user_aktiv', 1);
		if($add && !LDAP_Proxy::isLDAPUser(get_userdata($id)->user_login)) {
			self::userLDAPAdd($id);
		} elseif($add && LDAP_Proxy::isLDAPUser(get_userdata($id)->user_login)) {
			$ldapConnector = ldapConnector::get();
			$ldapConnector->setUserAttribute(get_userdata($id)->user_login, "mail", get_userdata($id)->user_email, "replace", $ldapConnector->getUserAttribute(get_userdata($id)->user_login, "mail")[0]);
			if($ldapConnector->getUserAttribute(get_userdata($id)->user_login, "mailAlternateAddress") == "") {
				$ldapConnector->setUserAttribute(get_userdata($id)->user_login, "mailAlternateAddress", get_userdata($id)->user_email);
			} else {
				$ldapConnector->setUserAttribute(get_userdata($id)->user_login, "mailAlternateAddress", get_userdata($id)->user_email, "replace", $ldapConnector->getUserAttribute(get_userdata($id)->user_login, "mailAlternateAddress")[0]);
			}
		}
	}
	
	public static function userLDAPAdd($user_id) {
		$user = new User($user_id);
		
		$ldapConnector = ldapConnector::get();
		if($user->art == 'User') {
			$ldapConnector->addUser($user->first_name, $user->last_name, $user->user_email);
		} else {
			$ldapConnector->addOrgaUser($user->user_login, $user->user_email);
		}
		
		/**
		 * Fires before errors are returned from a password reset request.
		 *
		 * @since 2.1.0
		 */
		do_action( 'lostpassword_post' );

		// Redefining user_login ensures we return the right case in the email.
		$user_login = $user->user_login;
		$user_email = $user->user_email;

		/**
		 * Fires before a new password is retrieved.
		 *
		 * @since 1.5.1
		 *
		 * @param string $user_login The user login name.
		 */
		do_action( 'retrieve_password', $user_login );

		/**
		 * Filter whether to allow a password to be reset.
		 *
		 * @since 2.7.0
		 *
		 * @param bool true           Whether to allow the password to be reset. Default true.
		 * @param int  $user_data->ID The ID of the user attempting to reset a password.
		 */
		$allow = apply_filters( 'allow_password_reset', true, $user->ID );

		if ( ! $allow ) {
			return new WP_Error( 'no_password_reset', __('Password reset is not allowed for this user') );
		} elseif ( is_wp_error( $allow ) ) {
			return $allow;
		}

		// Generate something random for a password reset key.
		$key = wp_generate_password( 10, true, false );

		wp_set_password($key, $user_id);
		LDAP_Proxy::changePW($user_login, $key);
		
		$message = __('Hallo,') . "\r\n\r\n";
		$message .= __('Du wurdest im IGELoffice registriert. Hiermit wird deine Registration bestätigt.') . "\r\n\r\n";
		$message .= sprintf(__('Dein Benutzer*innenname lautet: %s'), $user_login) . "\r\n\r\n";
		$message .= __('Dein Passwort lautet: ') . $key . "\r\n\r\n";
		$message .= __('Bitte ändere dein Passwort umgehend nach dem ersten Login.') . "\r\n\r\n";
		$message .= __('Wenn du dich nicht registriert hast, melde dich bitte umgehend an webmaster@gruene-jugend.de') . "\r\n\r\n";
		$message .= __('Bei technischen Problemen oder Schwierigkeiten, schreibe bitte KEINE Mail, sondern öffne ein Ticket unter https://support.gruene-jugend.de.') . "\r\n\r\n";
		$message .= __('Liebe Grüße,') . "\r\n";
		$message .= __('Dein IGELoffice') . "\r\n";
		

		if ( is_multisite() )
			$blogname = $GLOBALS['current_site']->site_name;
		else
			/*
			 * The blogname option is escaped with esc_html on the way into the database
			 * in sanitize_option we want to reverse this for the plain text arena of emails.
			 */
			$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

		$title = sprintf( __('[%s] Aktivierung deiner Registrierung'), $blogname );

		/**
		 * Filter the subject of the password reset email.
		 *
		 * @since 2.8.0
		 *
		 * @param string $title Default email title.
		 */
		$title = apply_filters( 'retrieve_password_title', $title );

		/**
		 * Filter the message body of the password reset mail.
		 *
		 * @since 2.8.0
		 * @since 4.1.0 Added `$user_login` and `$user_data` parameters.
		 *
		 * @param string  $message    Default mail message.
		 * @param string  $key        The activation key.
		 * @param string  $user_login The username for the user.
		 * @param WP_User $user_data  WP_User object.
		 */
		$message = apply_filters( 'retrieve_password_message', $message, $key, $user_login, get_userdata($user_id) );

		if ( $message && !wp_mail( $user_email, wp_specialchars_decode( $title ), $message ) )
			wp_die( __('The e-mail could not be sent.') . "<br />\n" . __('Possible reason: your host may have disabled the mail() function.') );

		return true;
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
		$ldapConnector->addUsersToGroup(array((new User($id))->user_login), (new Group($group_id))->name);
	}
	
	public static function delToGroup($id, $group_id) {
		$ldapConnector = ldapConnector::get();
		$ldapConnector->delUserFromGroup((new User($id))->user_login, (new Group($group_id))->name);
	}
	
	public static function getValues() {
		$query = new WP_User_Query(array(
			'meta_key'		=> 'io_user_aktiv',
			'meta_value'	=> '1'
		));
		
		$values = array();
		$users = $query->get_results();
		if(!empty($users)) {
			foreach($users AS $user) {
				$values[ucfirst(get_user_meta($user->ID, "io_user_art", true))][] = $user->ID;
			}
		}
		
		return $values;
	}
}
