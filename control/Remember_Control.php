<?php

/**
 * Description of Remember_Control
 *
 * @author KayWilhelm
 */
class Remember_Control {
	public static function prepare($data) {
		return explode("<br>", str_replace(array("\n", ", ", ",", "\r\n"), "<br>", $data));
	}
	
	public static function preUpdateCheck($data) {
		$error = array();
		
		foreach($data AS $key => $mail) {
			if(!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
				$error[] = $mail;
				unset($data[$key]);
			}
		}
		
		return array($error, array_values($data));
	}
	
	public static function update($data) {
		$serialize = maybe_serialize($data);
		
		update_option("io_remember", $serialize);
	}
	
	public static function get() {
		$var = maybe_unserialize(get_option("io_remember", array()));
		return is_array($var) ? $var : array($var);
	}
	
	public static function check() {
		$mails = self::get();
		
		foreach($mails AS $key => $mail) {
			if(get_user_by("email", $mail)) {
				unset($mails[$key]);
			}
		}
		
		self::update(array_values($mails));
		return array_values($mails);
	}
	
	public static function remember() {
		$mails = self::check();
		foreach($mails AS $mail) {
			$text = str_replace(array("[e-mail]", "[registrierung-link]", "[deaktivierung-link]"), array($mail, wp_registration_url(), site_url("wp-admin/profile.php?old_mail=" . $mail)), get_option("io_remember_text"));
			wp_mail($mail, get_option("io_remember_subject", "IGELoffice: Registrierungserinnerung!"), $text);
		}
	}
	
	public static function delete($old, $new, $user_login) {
		$user = get_user_by("email", $new);
		if($user && $user->user_login == $user_login) {
			$mails = self::get();
			$check = false;
			foreach($mails AS $key => $mail) {
				if($mail == $old) {
					unset($mails[$key]);
					$check = true;
					break;
				}
			}
			
			if($check) {
				self::update(array_values($mails));

				return true;
			}
		}
		return false;
	}
}
