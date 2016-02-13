<?php

/**
 * Description of mailStandard_view
 *
 * @author KWM
 */
class mailStandard_view {
	private static $user;
	
	public static function maskHandler() {
		self::$user = new mailStandard_model(get_current_user_id());
		
		if(!self::$user->isMailPermitted() && !self::$user->isMailForwardPermitted()) {
			return;
		}
		
		include '../wp-content/plugins/igeloffice/permission/mailStandard/templates/header.php';
		
		wp_nonce_field('io_mailStandard', 'io_mailStandard_nonce');
		
		if(self::$user->isMailPermitted()) {
			self::maskMail();
		}
		
		if(self::$user->isMailForwardPermitted()) {
			self::maskMailForward();
		}
		include '../wp-content/plugins/igeloffice/permission/mailStandard/templates/footer.php';
	}
	
	public static function maskMail() {
		if(isset($_POST['io_mailForward_submit'])) {
			self::mailExecution();
		}
		include '../wp-content/plugins/igeloffice/permission/mailStandard/templates/mail.php';
	}
	
	public static function maskMailForward() {
		if(isset($_POST['io_mailForward_submit'])) {
			self::mailForwardExecution();
		}
		include '../wp-content/plugins/igeloffice/permission/mailStandard/templates/mailForward.php';
	}
	
	public static function mailExecution() {
		if( !isset($_POST['io_mailStandard_nonce']) || 
			!wp_verify_nonce($_POST['io_mailStandard_nonce'], 'io_groups_member') || 
			defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
			return;
		}
		
		$mailStandard_model = new mailStandard_model(get_current_user_id());
		
		if(isset($_POST['io_mail']) && $_POST['io_mail'] == "true" && !isset($_POST['io_mailForward']) && !$mailStandard_model->useMail) {
			mailStandard_control::setMail(get_current_user_id());
		} else if(!isset($_POST['io_mail']) && $mailStandard_model->useMail) {
			mailStandard_control::delMail(get_current_user_id());
		}
	}
	
	public static function mailForwardExecution() {
		if( !isset($_POST['io_mailStandard_nonce']) || 
			!wp_verify_nonce($_POST['io_mailStandard_nonce'], 'io_groups_member') || 
			defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
			return;
		}
		
		$mailStandard_model = new mailStandard_model(get_current_user_id());
		
		if(isset($_POST['io_mailForward']) && $_POST['io_mailForward'] == "true" && !isset($_POST['io_mail']) && !$mailStandard_model->useMailForward) {
			mailStandard_control::setMailForward(get_current_user_id());
		} else if(!isset($_POST['io_mailForward']) && $mailStandard_model->useMailForward) {
			mailStandard_control::delMailForward(get_current_user_id());
		}
	}
}
