<?php

/**
 * Description of mailPermission_model
 *
 * @author KWM
 */
class mailStandard_model extends User {		
	public function __construct($id) {		
		parent::__construct($id);
	}
	
	public function __get($name) {
		if(parent::__get($name)) {
			return parent::__get($name);
		}
		
		$ldapConnector = ldapConnector::get();
		$mail = $ldapConnector->getUserAttribute($this->user_login, mailStandard_control::MAIL_ATTRIBUTE);
		$mailForward = $ldapConnector->getUserAttribute($this->user_login, mailStandard_control::MAIL_FORWARD_ATTRIBUTE);
		unset($mail['count']);
		unset($mailForward['count']);
		if(count($mail) > 1 || count($mailForward) > 1) {
			return new WP_Error("mailStandard_Error", "Es liegen mehrere Einträge für mail oder mailForwardAddress vor. Berechtigungssteuerung hier nicht richtig.");
		}
		
		if($name == "isMailPermitted") {
			return User_Control::isPermitted($this->ID, mailStandard_control::getMailPermission()->id);
		} else if($name == "isMailForwardPermitted") {
			return User_Control::isPermitted($this->ID, mailStandard_control::getMailForwardPermission()->id);
		} else if($name == "useMail") {
			$mailAttribute = $ldapConnector->getUserAttribute($this->user_login, mailStandard_control::MAIL_ATTRIBUTE);
			if(str_replace("@gruene-jugend.de", "", $mailAttribute[0]) != $mailAttribute[0]) {
				return true;
			}
			return false;
		} else if($name == "useMailForward") {
			$mailForwardAttribute = $ldapConnector->getUserAttribute($this->user_login, mailStandard_control::MAIL_FORWARD_ATTRIBUTE);
			if($mailForwardAttribute != "" && str_replace("@gruene-jugend.de", "", $mailForwardAttribute[0]) != $mailForwardAttribute[0]) {
				return true;
			}
			return false;
		} else if($name == "mail") {
			if($this->useMail) {
				return $ldapConnector->getUserAttribute($this->user_login, mailStandard_control::MAIL_ATTRIBUTE)[0];
			}
			
			if($this->useMailForward) {
				return $ldapConnector->getUserAttribute($this->user_login, mailStandard_control::MAIL_FORWARD_ATTRIBUTE)[0];
			}
			
			if($this->art == "user") {
				return $this->first_name . "." . $this->last_name . "@gruene-jugend.de";
			}
			
			return $this->user_login . "@gruene-jugend.de";
		} 
	}
}
