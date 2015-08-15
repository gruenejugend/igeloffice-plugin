<?php

require('../interfaces/ldapInterface.php');

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
			
	public function ldapConnector() {
		$ldapConn = ldap_connect(LDAP_HOST, LDAP_PORT);
		
		//TODO: LDAP Bind
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

	public function getGroupAttribute($group, $attribute) {
		
	}

	public function getUserAllPermissions($user) {
		
	}

	public function getUserAttribute($user, $attribute) {
		
	}

	public function getUserGroups($user) {
		
	}

	public function getUserPermissions($user) {
		
	}

	public function isQualified($user, $permission) {
		
	}

	public function setGroupAttribute($group, $attribute, $value) {
		
	}

	public function setUserAttribute($user, $attribute, $value) {
		
	}

	private userDN($user) {
		return 'cn='.$user.','.LDAP_USER_BASE;
	}

	private groupDN($group) {
		return 'cn='.$group.','.LDAP_GROUP_BASE;
	}

	private permissionDN($permission) {
		return 'cn='.$permission.','.LDAP_PERMISSION_BASE;
	}

}

