<?php

/**
 * Description of backend_remember
 *
 * @author KayWilhelm
 */
class backend_remember {
	public static function maskHandler() {
		include '../wp-content/plugins/igeloffice/templates/backend/settings/remember.php';
	}
	
	public static function menu() {
		add_submenu_page('options-general.php', 'Erinnerungen', 'Erinnerungseinstellungen', 'manage_options', 'io_remember', array('backend_remember', 'maskHandler'));
	}
	
	public static function subjectElement() {
		include '../wp-content/plugins/igeloffice/templates/backend/settings/rememberSubject.php';
	}
	
	public static function textElement() {
		include '../wp-content/plugins/igeloffice/templates/backend/settings/rememberText.php';
	}
	
	public static function textHintElement() {
		include '../wp-content/plugins/igeloffice/templates/backend/settings/rememberTextHint.php';
	}
	
	public static function userElement() {
		include '../wp-content/plugins/igeloffice/templates/backend/settings/rememberUser.php';
	}
	
	public static function registerSettings() {
		add_settings_section("io_remember", "Erinnerungseinstellungen", null, "io_remember");
		
		add_settings_field("io_remember_subject", "Betreff der Erinnerungsmail", array("backend_remember", "subjectElement"), "io_remember", "io_remember");
		add_settings_field("io_remember_text", "Text der Erinnerungsmail", array("backend_remember", "textElement"), "io_remember", "io_remember");
		add_settings_field("io_remember_text_hint", null, array("backend_remember", "textHintElement"), "io_remember", "io_remember");
		add_settings_field("io_remember_user", "Zu erinnernde", array("backend_remember", "userElement"), "io_remember", "io_remember");
		
		register_setting("io_remember", "io_remember_subject");
		register_setting("io_remember", "io_remember_text");
		register_setting("io_remember", "io_remember_user", array("Remember_Control", "sanitizeSetting"));
	}
	
	public static function otherMail() {
		$user = get_userdata(get_current_user_id());
		if(!empty($_GET['old_mail']) && get_current_user_id() != 0 && function_exists("get_current_screen") && (get_current_screen()->parent_file == 'users.php' || get_current_screen()->parent_file == 'profile.php')) {
			$mail = str_replace("%40", "@", $_GET['old_mail']);
			if(filter_var($mail, FILTER_VALIDATE_EMAIL) && Remember_Control::delete(sanitize_text_field($mail), $user->user_email, $user->user_login)) {
				echo self::oldMailDelete();
			} else {
				echo self::oldMailFail();
			}
		}
	}
	
	public static function oldMailDelete() {
		return '	<div class="updated">
		<p>E-Mail wurde aus der Erinnerungssteuerung gel&ouml;scht.</p>
	</div>
';
	}
	
	public static function oldMailFail() {
		return '	<div class="error">
		<p>Es besteht ein Problem. Entweder war die angegebene Mail-Adresse falsch oder sie stand nicht auf der Erinnerungsliste.</p>
	</div>
';
	}
}
