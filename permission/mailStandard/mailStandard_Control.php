<?php

/**
 * Description of mailStandard_Control
 *
 * @author KWM
 */
class mailStandard_control {
	const MAIL_PERMISSION = 'Mail-Postfach';
	const MAIL_FORWARD_PERMISSION = 'Mail-Weiterleitung';
	const MAIL_ATTRIBUTE = 'mail';
	const MAIL_FORWARD_ATTRIBUTE = 'mailForwardingAddress';
	
	public static function getMailPermission() {
		return new Permission(get_page_by_title(mailStandard_control::MAIL_PERMISSION, OBJECT, Permission_Control::POST_TYPE)->ID);
	}
	
	public static function getMailForwardPermission() {
		return new Permission(get_page_by_title(mailStandard_control::MAIL_FORWARD_PERMISSION, OBJECT, Permission_Control::POST_TYPE)->ID);
	}
	
	public static function setMail($user_id) {
		$ldapConnector = ldapConnector::get();
		
		$mailStandard_model = new mailStandard_model($user_id);
		if(!$mailStandard_model->useMail) {
			$ldapConnector->setUserAttribute($mailStandard_model->user_login, mailStandard_control::MAIL_ATTRIBUTE, $mailStandard_model->mail, "replace", $mailStandard_model->user_email);
		} else {
			return new WP_Error("mailStandard_Mail_Exists", "Es existiert bereits ein Postfach.");
		}
	}
	
	public static function setMailForward($user_id) {
		$ldapConnector = ldapConnector::get();
		
		$mailStandard_model = new mailStandard_model($user_id);
		if(!$mailStandard_model->useMailForward) {
			$ldapConnector->setUserAttribute($mailStandard_model->user_login, mailStandard_control::MAIL_FORWARD_ATTRIBUTE, $mailStandard_model->mail);
		} else {
			return new WP_Error("mailStandard_Forward_Exists", "Es existiert bereits eine Weiterleitung.");
		}
	}
	
	public static function delMail($user_id) {
		$ldapConnector = ldapConnector::get();
		
		$mailStandard_model = new mailStandard_model($user_id);
		if($mailStandard_model->useMail) {
			$ldapConnector->setUserAttribute($mailStandard_model->user_login, mailStandard_control::MAIL_ATTRIBUTE, $mailStandard_model->user_email, "replace", $mailStandard_model->mail);
		} else {
			return new WP_Error("mailStandard_Mail_Nexists", "Es existiert kein Postfach.");
		}
	}
	
	public static function delMailForward($user_id) {
		$ldapConnector = ldapConnector::get();
		
		$mailStandard_model = new mailStandard_model($user_id);
		if($mailStandard_model->useMailForward) {
			$ldapConnector->delUserAttribute($mailStandard_model->user_login, mailStandard_control::MAIL_FORWARD_ATTRIBUTE, $mailStandard_model->mail);
		} else {
			return new WP_Error("mailStandard_Forward_Nexists", "Es existiert keine Weiterleitung.");
		}
	}
}
