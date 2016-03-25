<?php

/**
 * Description of Remember_Control
 *
 * @author KayWilhelm
 */
class Remember_Control {
	public static function prepare($data) {
		return explode("<br />", str_replace(array(", ", ",", "\r\n", "\n\r", "\n", "\r"), array("<br />", "<br />", "", "", "", ""), nl2br($data)));
	}
	
	public static function preUpdateCheck($data) {
		foreach($data AS $key => $mail) {
			if(!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
				unset($data[$key]);
			}
		}
		
		return $data;
	}
	
	public static function sanitizeSetting($data) {
		if(!is_array($data)) {
			$data = self::preUpdateCheck(self::prepare($data));
		}
		return maybe_serialize($data);
	}
	
	public static function update($data) {
		update_option("io_remember_user", $data);
	}
	
	public static function get() {
		return maybe_unserialize(get_option("io_remember_user", array()));
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
