<?php

/**
 * Description of mailStandard_view
 *
 * @author KWM
 */
class mailStandard_view {
	private static $user;
	
	private static $POST_ATTRIBUT_MAIL_FORWARD_SUBMIT = 'io_mailForward_submit';
	private static $POST_ATTRIBUT_MAIL_STANDARD_NONCE = 'io_mailStandard_nonce';
	private static $POST_ATTRIBUT_MAIL = 'io_mail';
	private static $POST_ATTRIBUT_MAIL_FORWARD = 'io_mailForward';
	private static $MAIL_STANDARD_NONCE = 'io_mailStandard';
	
	public static function maskHandler() {
		self::$user = new mailStandard_model(get_current_user_id());
		
		if(!self::$user->isMailPermitted && !self::$user->isMailForwardPermitted) {
			return;
		}
		
		include 'wp-content/plugins/igeloffice/permission/mailStandard/templates/header.php';
		
		wp_nonce_field(self::$MAIL_STANDARD_NONCE, self::$POST_ATTRIBUT_MAIL_STANDARD_NONCE);
		
		if(self::$user->isMailPermitted) {
			self::maskMail();
		}
		
		if(self::$user->isMailForwardPermitted) {
			self::maskMailForward();
		}
		include 'wp-content/plugins/igeloffice/permission/mailStandard/templates/footer.php';
	}
	
	public static function maskMail() {
		if(isset($_POST[self::$POST_ATTRIBUT_MAIL_FORWARD_SUBMIT])) {
			self::mailExecution();
		}
		include 'wp-content/plugins/igeloffice/permission/mailStandard/templates/mail.php';
	}
	
	public static function maskMailForward() {
		if(isset($_POST[self::$POST_ATTRIBUT_MAIL_FORWARD_SUBMIT])) {
			self::mailForwardExecution();
		}
		include 'wp-content/plugins/igeloffice/permission/mailStandard/templates/mailForward.php';
	}
	
	public static function mailExecution() {
		if( !isset($_POST[self::$POST_ATTRIBUT_MAIL_STANDARD_NONCE]) || 
			!wp_verify_nonce($_POST[self::$POST_ATTRIBUT_MAIL_STANDARD_NONCE], self::$MAIL_STANDARD_NONCE) || 
			defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
			return;
		}
		
		$mailStandard_model = new mailStandard_model(get_current_user_id());
		
		if(isset($_POST[self::$POST_ATTRIBUT_MAIL]) && $_POST[self::$POST_ATTRIBUT_MAIL] == "true" && !isset($_POST[self::$POST_ATTRIBUT_MAIL_FORWARD]) && !$mailStandard_model->useMail) {
			mailStandard_control::setMail(get_current_user_id());
		} else if(!isset($_POST[self::$POST_ATTRIBUT_MAIL]) && $mailStandard_model->useMail) {
			mailStandard_control::delMail(get_current_user_id());
		}
	}
	
	public static function mailForwardExecution() {
		if( !isset($_POST[self::$POST_ATTRIBUT_MAIL_STANDARD_NONCE]) || 
			!wp_verify_nonce($_POST[self::$POST_ATTRIBUT_MAIL_STANDARD_NONCE], self::$MAIL_STANDARD_NONCE) || 
			defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
			return;
		}
		
		$mailStandard_model = new mailStandard_model(get_current_user_id());
		
		if(isset($_POST[self::$POST_ATTRIBUT_MAIL_FORWARD]) && $_POST[self::$POST_ATTRIBUT_MAIL_FORWARD] == "true" && !isset($_POST[self::$POST_ATTRIBUT_MAIL]) && !$mailStandard_model->useMailForward) {
			mailStandard_control::setMailForward(get_current_user_id());
		} else if(!isset($_POST[self::$POST_ATTRIBUT_MAIL_FORWARD]) && $mailStandard_model->useMailForward) {
			mailStandard_control::delMailForward(get_current_user_id());
		}
	}
}
