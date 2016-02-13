<?php

/**
 * Description of mailinglisten_model
 *
 * @author KWM
 */
class mailinglisten_model extends User {
	public function __construct($id) {
		parent::__construct($id);
	}
	
	public function __get($name) {
		if(parent::__get($name)) {
			return parent::__get($name);
		}
		
		if($name == "isPermitted") {
			return User_Control::isPermitted($this->ID, mailinglisten_control::getPermission()->id);
		}
	}
}
