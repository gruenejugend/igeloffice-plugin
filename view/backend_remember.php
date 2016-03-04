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
	
	public static function otherMail($user) {
		if(!empty($_GET['old_mail'])) {
			if(filter_var($_GET['old_mail'], FILTER_VALIDATE_EMAIL) && Remember_Control::delete(sanitize_text_field($_GET['old_mail']), $user->user_email, $user->user_login)) {
				add_action('admin_notices', array('backend_remember', 'oldMailDelete'));
			} else {
				add_action('admin_notices', array('backend_remember', 'oldMailFail'));
			}
		}
	}
	
	public static function oldMailDelete() {
		?>
		
	<div class="updated">
		<p>E-Mail wurde aus der Erinnerungssteuerung gel&ouml;scht.</p>
	</div>	
		 
		<?php

		remove_action('admin_notices', array('backend_remember', 'oldMailDelete'));
	}
	
	public static function oldMailFail() {
		?>
		
	<div class="error">
		<p>Es besteht ein Problem. Entweder war die angegebene Mail-Adresse falsch oder sie stand nicht auf der Erinnerungsliste.</p>
	</div>	
		 
		<?php

		remove_action('admin_notices', array('backend_remember', 'oldMailFail'));
	}
	
	public static function schedule() {
		wp_schedule_event(1457193600, "hourly", array("backend_remember", "scheduleHook"));
	}
	
	public static function scheduleExec() {
		Remember_Control::remember();
	}
}
