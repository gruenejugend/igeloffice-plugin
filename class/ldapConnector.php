<?php

use \Defuse\Crypto\Crypto;

/**
 * TODO: FRAGESTELLUNG, WAS PASSIERT BEI SET? WAS, WENN ATTRIBUTE ENTFERNT WERDEN? WAS, WENN HINZUGEFÜGT? WAS, WENN NUR GEÄNDERT?
 */


/**
 * Description of ldapConnector
 *
 * @author KWM
 */
class ldapConnector implements ldapInterface {
	private $ldapConn;
	private $user;
	private $permissions;
	private $groups;
	private $res;

	private static $instance;

	public static function get($bind = true) {
		if(self::$instance instanceof ldapConnector) {
			return self::$instance;
		}
		self::$instance = new ldapConnector();
		if($bind) {
			self::$instance->bind($user, $pass);
		}
		return self::$instance;
	}
			
	private function __construct() {
		ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
		$this->res = ldap_connect(LDAP_HOST, LDAP_PORT);
	}
	
	public function bind($user = null, $pass = null) {
		if(is_null($user) && is_null($pass)) { //get ldap-password from db & cookie
			if(!is_user_logged_in()) {
				return false;
			}
			$userinfo = get_currentuserinfo();
			$user = $userinfo->user_login;
			$username_hash = hash('sha256', $user);
			$pass_hash = get_user_meta($user->ID, '_ldap_pass', true);
			$key = $_COOKIE[$username_hash];
			if(empty($pass_hash) || empty($key)) {
				return false;
			}
			$pass = Crypto::decrypt(base64_decode($pass_hash), $key);

		}
		return ldap_bind($this->res, $this->userDN($user), $pass);
	}

	/*
	 * User Initials
	 */
	public function addUser($firstname, $surname) {
		if(ldap_add($this->res, $this->userDN($firstname.' '.$surname), array(
			'cn' => $firstname.' '.$surname,
			'sn' => $surname,
			'objectClass' => array(
				'inetOrgPerson',
				'qmailUser'
			)
		))) {
			return true;
		}
		return false;
	}
	
	public function addTechnicalUser($name) {
		//TODO
	}

	public function delUser($user) {
		if(ldap_delete($this->res, $this->userDN($user))) {
			return true;
		}
		return false;
	}
	
	
	
	
	
	
	
	public function addGroup($group) {
		if(ldap_add($this->res, 'cn='.$group.','.LDAP_GROUP_BASE, array(
			'cn' => $group,
			'objectClass' => 'groupOfNames'
		))) {
			return true;
		}
		return false;
	}

	public function addGroupPermission($group, $permission) {
		if(ldap_mod_add($this->res, $this->permissionDN($permission), array(
			'member' => $this->groupDN($group)
		))) {
			return true;
		}
		return false;
	}

	public function addGroupToGroup($groupToAdd, $group) {
		//TODO
	}

	public function addPermission($permission) {
		if(ldap_add($this->res, $this->permissionDN($permission), array(
			'cn' => 'permission',
			'objectClass' => 'groupOfNames'
		))) {
			return true;
		}
		return false;
	}


	public function addUserPermission($user, $permission) {
		if(ldap_mod_add($this->res, $this->permissionDN($permission), array(
			'member' => $this->userDN($user)
		))) {
			return true;
		}
		return false;
	}

	//TODO: Annahme und Verarbeitung Array
	public function addUserToGroup($user, $group) {
		if(ldap_mod_add($this->res, $this->groupDN($group), array(
			'member' => $this->userDN($user)
		))) {
			return true;
		}
		return false;
	}

	public function delGroup($group) {
		if(ldap_delete($this->res, $this->groupDN($group))) {
			return true;
		}
		return false;
	}

	public function delGroupPermission($group, $permission) {
		if(ldap_mod_del($this->res, $this->permissionDN($permission), array(
			'member' => $this->groupDN($group)
		))) {
			return true;
		}
		return false;
	}

	public function delGroupToGroup($groupToDel, $group) {
		//TODO
	}

	public function delPermission($permission) {
		if(ldap_delete($this->res, $this->permissionDN($permission))) {
			return true;
		}
		return false;
	}

	

	public function delUserPermission($user, $permission) {
		
	}

	public function delUserToGroup($user, $group) {
		
	}

	//TODO: VORSICHT - WAS BEI MEHRERE ATTRIBUTE? RÜCKGABE ALS ARRAY?
	public function getGroupAttribute($group, $attribute) {
		
	}

	//TODO: VORSICHT - WAS BEI MEHRERE ATTRIBUTE? RÜCKGABE ALS ARRAY?
	public function getUserAllPermissions($user) {
		
	}

	//TODO: VORSICHT - WAS BEI MEHRERE ATTRIBUTE? RÜCKGABE ALS ARRAY?
	public function getUserAttribute($user, $attribute) {
		
	}
	
	//TODO: VORSICHT - WAS BEI MEHRERE ATTRIBUTE? RÜCKGABE ALS ARRAY?
	public function getPermissionAttribute($permission, $attribute) {
		
	}

	public function getUserGroups($user) {
		
	}

	public function getUserPermissions($user) {
		
	}

	public function isQualified($user, $permission) {
		
	}

	//TODO: Array bei Value
	public function setGroupAttribute($group, $attribute, $value) {
		
	}

	public function setUserAttribute($user, $attribute, $value) {
		
	}
	
	public function setPermissionAttribute($permissions, $attribute, $value) {
		
	}

	private function userDN($user) {
		return 'cn='.$user.','.LDAP_USER_BASE;
	}

	private function groupDN($group) {
		return 'cn='.$group.','.LDAP_GROUP_BASE;
	}

	private function permissionDN($permission) {
		return 'cn='.$permission.','.LDAP_PERMISSION_BASE;
	}

}

