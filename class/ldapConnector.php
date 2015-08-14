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
		$ldapConn = ldap_connect("localhost", "389");
		/*
		 * BIND?
		 */
	}
	
	/*
	 * User Initials
	 */
	public function addUser($firstname, $surname) {
		
	}
	
	public function addTechnicalUser($name) {
		
	}

	public function delUser($user) {
		
	}
	
	
	
	
	
	
	
	public function addGroup($group) {
		
	}

	public function addGroupPermission($group, $permission) {
		
	}

	public function addGroupToGroup($groupToAdd, $group) {
		
	}

	public function addPermission($permission) {
		
	}


	public function addUserPermission($user, $permission) {
		
	}

	public function addUserToGroup($user, $group) {
		
	}

	public function delGroup($group) {
		
	}

	public function delGroupPermission($group, $permission) {
		
	}

	public function delGroupToGroup($groupToDel, $group) {
		
	}

	public function delPermission($permission) {
		
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

}

