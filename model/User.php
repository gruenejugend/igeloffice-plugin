<?php

/**
 * Description of User
 *
 * @author KWM
 */
class User extends WP_User {
	private $art;
	private $aktiv;
	private $landesverband;
	private $permissions = array();
	private $groups = array();
	
	public function __construct($id) {
		parent::__construct($id);
	}
	
	public function __get($name) {
		if($name == 'art') {
			return get_user_meta($this->ID, "io_user_art", true);
		} else if($name == 'aktiv') {
			return get_user_meta($this->ID, "io_user_aktiv", true);
		} else if($name == 'landesverband') {
			return get_user_meta($this->ID, "io_user_lv", true);
		} else if($name == 'permissions') {
			$ldapConnector = ldapConnector::get();
			$permissions = $ldapConnector->getUserPermissions($this->user_login);
			foreach($permissions AS $permission) {
				$permission = explode(",", substr($permissions, 3))[0];
				array_push($this->permissions, new Permission(get_page_by_title($permission, OBJECT, Permission_Control::POST_TYPE)->ID));
			}
		} else if($name == 'groups') {
			$ldapConnector = ldapConnector::get();
			$groups = $ldapConnector->getUserGroups($this->user_login);
			foreach($groups AS $group) {
				$group = explode(",", substr($group, 3))[0];
				array_push($this->groups, new Group(get_page_by_title($group, OBJECT, 'io_group')->ID));
			}
		} else {
			return parent::__get($name);
		}
	}
}
