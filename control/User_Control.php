<?php

/**
 * Description of User_Control
 *
 * @author KWM
 */
class User_Control {
	public static function createUser($first_name, $last_name, $mail) {
		self::deletePost();
		$id = wp_insert_user(array(
			'user_login'	=> $first_name . ' ' . $last_name,
			'user_email'	=> $mail
		));
		
		$_POST[User_Util::POST_ATTRIBUT_ART] = User_Util::USER_ART_USER;
		$_POST[User_Util::POST_ATTRIBUT_FIRST_NAME] = $first_name;
		$_POST[User_Util::POST_ATTRIBUT_LAST_NAME] = $last_name;
		$_POST[User_Util::POST_ATTRIBUT_USERS_NONCE] = wp_create_nonce(User_Util::USERS_NONCE);
		
		self::createMeta($id);
		
		return $id;
	}

	public static function deletePost()
	{
		unset($_POST[User_Util::POST_ATTRIBUT_ART]);
		unset($_POST[User_Util::POST_ATTRIBUT_FIRST_NAME]);
		unset($_POST[User_Util::POST_ATTRIBUT_LAST_NAME]);
		unset($_POST[User_Util::POST_ATTRIBUT_USERS_NONCE]);
		unset($_POST[User_Util::POST_ATTRIBUT_LAND]);
	}

	public static function createMeta($user_id)
	{
		if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
			return;
		}
		
		if (!empty($_POST[User_Util::POST_ATTRIBUT_ART])) {
			if (!isset($_POST[User_Util::POST_ATTRIBUT_USERS_NONCE]) ||
				!wp_verify_nonce($_POST[User_Util::POST_ATTRIBUT_USERS_NONCE], User_Util::USERS_NONCE)) {
				return;
			}

			update_user_meta($user_id, User_Util::ATTRIBUT_ART, sanitize_text_field($_POST[User_Util::POST_ATTRIBUT_ART]));
			update_user_meta($user_id, User_Util::ATTRIBUT_AKTIV, 0);

			if ($_POST[User_Util::POST_ATTRIBUT_ART] == User_Util::USER_ART_USER) {
				update_user_meta($user_id, 'first_name', sanitize_text_field($_POST[User_Util::POST_ATTRIBUT_FIRST_NAME]));
				update_user_meta($user_id, 'last_name', sanitize_text_field($_POST[User_Util::POST_ATTRIBUT_LAST_NAME]));
			} elseif ($_POST[User_Util::POST_ATTRIBUT_ART] == User_Util::USER_ART_BASISGRUPPE) {
				update_user_meta($user_id, User_Util::ATTRIBUT_LANDESVERBAND, sanitize_text_field($_POST[User_Util::POST_ATTRIBUT_LAND]));
			}

			do_action("io_user_register", $user_id);

			if (get_user_meta($user_id, User_Util::ATTRIBUT_AKTIV, true) != 1) {
				Request_Control::create($user_id, "User");
			}
		}
	}
	
	public static function createLandesverband($landesverband, $mail) {
		self::deletePost();
		$id = wp_insert_user(array(
			'user_login'	=> $landesverband,
			'user_email'	=> $mail
		));
		
		$_POST[User_Util::POST_ATTRIBUT_ART] = User_Util::USER_ART_LANDESVERBAND;
		$_POST[User_Util::POST_ATTRIBUT_USERS_NONCE] = wp_create_nonce(User_Util::USERS_NONCE);
		
		self::createMeta($id);
		
		return $id;
	}
	
	public static function createBasisgruppe($ort, $landesverband, $mail) {
		self::deletePost();
		$id = wp_insert_user(array(
			'user_login'	=> $ort,
			'user_email'	=> $mail
		));
		
		$_POST[User_Util::POST_ATTRIBUT_ART] = User_Util::USER_ART_BASISGRUPPE;
		$_POST[User_Util::POST_ATTRIBUT_LAND] = $landesverband;
		$_POST[User_Util::POST_ATTRIBUT_USERS_NONCE] = wp_create_nonce(User_Util::USERS_NONCE);
		
		self::createMeta($id);
		
		return $id;
	}
	
	public static function createOrgauser($name, $mail) {
		self::deletePost();
		$id = wp_insert_user(array(
			'user_login'	=> $name,
			'user_email'	=> $mail
		));
		
		$_POST[User_Util::POST_ATTRIBUT_ART] = User_Util::USER_ART_ORGANISATORISCH;
		$_POST[User_Util::POST_ATTRIBUT_USERS_NONCE] = wp_create_nonce(User_Util::USERS_NONCE);
		
		self::createMeta($id);
		
		return $id;
	}

	public static function inLDAP($user_id) {
		$user = get_userdata($user_id);
		if( get_user_meta($user_id, User_Util::ATTRIBUT_AKTIV, true) != 1 &&
			LDAP_Proxy::isLDAPUser($user->user_login, $user->user_email) === true) {
			User_Control::aktivieren($user_id, false);
		}
	}

	public static function aktivieren($id, $add = true) {
		update_user_meta($id, User_Util::ATTRIBUT_AKTIV, 1);
		$user = get_userdata($id);
		$sendmail = false;
		if($add && !LDAP_Proxy::isLDAPUser($user->user_login)) {
			self::userLDAPAdd($id);
		} elseif($add && LDAP_Proxy::isLDAPUser($user->user_login)) {
			$ldapConnector = ldapConnector::get();
			
			if(str_replace("@gruene-jugend.de", "", $ldapConnector->getUserAttribute($user->user_login, "mail")[0]) == $ldapConnector->getUserAttribute($user->user_login, "mail")[0]) {
				$ldapConnector->setUserAttribute($user->user_login, "mail", $user->user_email, "replace", $ldapConnector->getUserAttribute($user->user_login, "mail")[0]);
			}
			
			if($ldapConnector->getUserAttribute($user->user_login, "mailAlternateAddress") == "") {
				$ldapConnector->setUserAttribute($user->user_login, "mailAlternateAddress", $user->user_email);
			} else {
				$ldapConnector->setUserAttribute($user->user_login, "mailAlternateAddress", $user->user_email, "replace", $ldapConnector->getUserAttribute($user->user_login, "mailAlternateAddress")[0]);
			}
			
			$sendmail = true;
		}
		
		do_action("io_user_activate", $id);
		
		if(!$add || $sendmail) {
			$message = __('Hallo,') . "\r\n\r\n";
			$message .= __('Du wurdest im IGELoffice registriert. Hiermit wird deine Registration bestätigt.') . "\r\n\r\n";
			$message .= sprintf(__('Dein Benutzer*innenname lautet: %s'), $user->user_login) . "\r\n\r\n";
			$message .= __('Dein Passwort hat sich nicht geändert, weil du bereits in unserem System registriert bist. Wenn du dein Passwort nicht weißt, nutze bitte die Passwort-Vergessen-Funktion.') . "\r\n\r\n";
			$message .= __('Wenn du dich nicht registriert hast, melde dich bitte umgehend an webmaster@gruene-jugend.de') . "\r\n\r\n";
			$message .= __('Bei technischen Problemen oder Schwierigkeiten, schreibe bitte KEINE Mail, sondern öffne ein Ticket unter https://support.gruene-jugend.de.') . "\r\n\r\n";
			$message .= __('Liebe Grüße,') . "\r\n";
			$message .= __('Dein IGELoffice') . "\r\n";
			
			$title = sprintf( __('[%s] Aktivierung deiner Registrierung'), wp_specialchars_decode(get_option('blogname'), ENT_QUOTES));
			$title = apply_filters( 'retrieve_password_title', $title);

			wp_mail($user->user_email, wp_specialchars_decode($title), $message, 'From: webmaster@gruene-jugend.de');
		}
	}
	
	public static function userLDAPAdd($user_id) {
		$user = new User($user_id);
		
		$ldapConnector = ldapConnector::get();
		if($user->art == User_Util::USER_ART_USER) {
			if(get_current_user_id() != 0) {
				$ldapConnector->addUser($user->first_name, $user->last_name, $user->user_email);
			} else {
				LDAP_Proxy::addUser($user->first_name, $user->last_name, $user->user_email);
			}
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
		LDAP_Proxy::changePW($user, $key);
		
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

		if ($message && !wp_mail($user_email, wp_specialchars_decode($title), $message, 'From: webmaster@gruene-jugend.de'))
			wp_die( __('The e-mail could not be sent.') . "<br />\n" . __('Possible reason: your host may have disabled the mail() function.') );

		return true;
	}

	public static function inSherpa($user_id)
	{
		$user = get_userdata($user_id);
		if (get_user_meta($user_id, User_Util::ATTRIBUT_AKTIV, true) != 1 &&
			get_user_meta($user_id, User_Util::ATTRIBUT_ART, true) == User_Util::USER_ART_USER &&
			LDAP_Proxy::isMember($user->first_name, $user->last_name, $user->user_email)
		) {
			User_Control::aktivieren($user_id);
		}
	}

	public static function delete($id)
	{
		$ldapConnector = ldapConnector::get();
		if ($ldapConnector->DNexists('cn=' . (new User($id))->user_login . ',ou=users,dc=gruene-jugend,dc=de')) {
			$ldapConnector->delUser((new User($id))->user_login);
		}
		wp_delete_user($id);
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
		$ldapConnector->addUsersToGroup(array((new User($id))->user_login), (new Group($group_id))->ldapName);
		LDAP_Proxy::setQuota(get_userdata($id), get_post_meta($group_id, "io_group_quota", true));
	}
	
	public static function delToGroup($id, $group_id) {
		$ldapConnector = ldapConnector::get();
		$ldapConnector->delUserFromGroup((new User($id))->user_login, (new Group($group_id))->ldapName);
	}
	
	public static function isPermitted($id, $permission_id) {
		$user = new User($id);
		$permission = new Permission($permission_id);
		$ldapConnector = ldapConnector::get();
		return $ldapConnector->isQualified($user->user_login, $permission->name);
	}
	
	public static function getValues() {
		$query = new WP_User_Query(array(
			'meta_key'		=> User_Util::ATTRIBUT_AKTIV,
			'meta_value'	=> '1'
		));
		
		$values = array();
		$users = $query->get_results();
		if(!empty($users)) {
			foreach($users AS $user) {
				$values[ucfirst(get_user_meta($user->ID, User_Util::ATTRIBUT_ART, true))][] = $user->ID;
			}
		}
		
		return $values;
	}
}
