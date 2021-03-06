<?php

/**
 * Description of owncloud_model
 *
 * @author KWM
 */
class cloud_model extends User {
	public function __construct($id) {
		parent::__construct($id);
	}
	
	public function __get($name) {
		if(parent::__get($name)) {
			return parent::__get($name);
		}
		
		if($name == "isPermitted") {
			return User_Control::isPermitted($this->ID, cloud_control::getPermission()->id);
		} elseif($name == "isSpacePermitted") {
            return User_Control::isPermitted($this->ID, cloud_control::getCloudSpacePermission()->id);
        } elseif($name == "hasSpace") {
		    return get_page_by_title("Cloud ".$this->user_login, OBJECT, Group_Util::POST_TYPE) != null;
        }
	}
}
