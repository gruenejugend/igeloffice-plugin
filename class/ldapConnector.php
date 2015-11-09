<?php
/**
 * TODO: FRAGESTELLUNG, WAS PASSIERT BEI SET? WAS, WENN ATTRIBUTE ENTFERNT WERDEN? WAS, WENN HINZUGEFÜGT? WAS, WENN NUR GEÄNDERT?
 * TODO: Permissions werden nicht in LDAP erstellt
 */


/**
 * Description of ldapConnector
 *
 * @author Andreas Krischer, akbyte
 */
class ldapConnector extends LDAP {

	/**
	 * instance of this class
	 * @var ldapConnector
	 */
	private static $instance;

	/**
	 * get only instance of this class
	 * @param  boolean $bind if the class should bind to the LDAP server after connecting
	 * @return ldapConnector        instance of this class
	 */
	public static function get($bind = true) {
		if(self::$instance instanceof ldapConnector) {
			return self::$instance;
		}
		self::$instance = new ldapConnector();
		if($bind && !self::$instance->bind) {
			self::$instance->bind();
		}
		return self::$instance;
	}
	
	/**
	 * binds to ldap server
	 * @param  string $user username. if username and password are read from database & cookies
	 * @param  string $pass password - see username
	 * @return boolean       successfull or not
	 */
	public function bind($user = null, $pass = null) {
		if(is_null($user) && is_null($pass)) { //get ldap-password from db & cookie
			if(!is_user_logged_in()) {
				return false;
			}
			$user_id = get_current_user_id();
			$userinfo = get_userdata($user_id);
			$user = $userinfo->user_login;
			$username_hash = hash('sha256', $user);
			$pass_hash = base64_decode(get_user_meta($user_id, '_ldap_pass', true));
			$key = base64_decode($_COOKIE[$username_hash]);
			if(empty($pass_hash) || empty($key)) {
				return false;
			}
			require_once IGELOFFICE_PATH.'class/php-encryption/Crypto.php';
			$pass = Crypto::decrypt($pass_hash, $key);
		}
		return parent::bind($this->userDN($user), $pass);
	}

	/**
	 * this is called before every public LDAP query method.
	 * @param  string $method method name
	 * @param  array $args   arguments for method
	 * @return mixed         return of method
	 */
	public function __call($method, $args) {
		if(method_exists($this, $method)) {
			if(!$this->isBound()) {
				return false;
			}
			return call_user_func_array(array($this, $method), $args);
		}
	}

	/**
	 * adds a LDAP user
	 * @param string $firstname firstname of user
	 * @param string $surname   lastname of user
	 * @param string $mail      mail address of user
	 * @return boolean successful or not
	 * @todo check if DN already exists
	 * @todo password sending
	 */
	private function addUser($firstname, $surname, $mail) { //check if user or DN exists!
		if(empty($firstname) || empty($surname) || empty($mail)) {
			return new WP_Error('ldap_add_user_nodata', 'Der User benötigt einen Vornamen, Nachnamen und eine gültige E-Mail-Adresse.');
		}
		if(!ldap_add($this->res, $this->userDN($firstname.' '.$surname), array(
			'cn' => $firstname.' '.$surname,
			'sn' => $surname,
			'mail' => $mail,
			'mailAlternateAddress' => $mail,
			'objectClass' => array(
				'top',
				'person',
				'inetOrgPerson',
				'qmailUser'
			)
		))) {
			return $this->error();
		}
		return true;
	}
	
	private function addOrgaUser($firstname, $mail) { //check if user or DN exists!
		if(empty($firstname) || empty($mail)) {
			return new WP_Error('ldap_add_user_nodata', 'Der User benötigt einen Vornamen, Nachnamen und eine gültige E-Mail-Adresse.');
		}
		if(!ldap_add($this->res, $this->userDN($firstname), array(
			'cn' => $firstname,
			'sn' => $firstname,
			'mail' => $mail,
			'mailAlternateAddress' => $mail,
			'objectClass' => array(
				'top',
				'person',
				'inetOrgPerson',
				'qmailUser'
			)
		))) {
			return $this->error();
		}
		return true;
	}

	/**
	 * gives a user a LDAP permission
	 * @param string $user       user CN
	 * @param string $permission permission CN
	 * @return boolean successful or not
	 */
	private function addUserPermission($user, $permission) {
		if(!ldap_mod_add($this->res, $this->permissionDN($permission), array(
			'member' => $this->userDN($user)
		))) {
			return $this->error();
		}
		return true;
	}

	/**
	 * replaces all group members with the provided array of CN's
	 * @param array $users user CN's
	 * @param string $group group CN
	 * @return boolean successful or not
	 */
	private function addUsersToGroup($users, $group) {
		array_walk($users, array($this, 'userDN'));

		if(!ldap_mod_replace($this->res, $this->groupDN($group), array(
			'member' => $users
		))) {
			return $this->error();
		}

		return true;
	}

	/**
	 * set password for LDAP user
	 * @param string $user     user CN
	 * @param string $password password
	 * @return boolean successful or not
	 */
	private function setUserPassword($user, $password) {
		if(!ldap_mod_replace($this->res, $this->userDN($user), array(
			'userPassword' => hash('ssha', $password),
			'qmailGID' => intval(time() / 86400) //last password change - days since 01.01.1970
		))) {
			return $this->error();
		}
		return true;
	}

	/**
	 * gets an user attribute
	 * @param  string $user      user CN
	 * @param  string $attribute attribute name
	 * @return array            values for this attribute and "count" of values
	 */
	private function getUserAttribute($user, $attribute) {
		return $this->getAttribute($this->userDN($user), $attribute);
	}

	/**
	 * checks if a user exists in LDAP
	 * @param  string  $user user CN
	 * @return boolean       yes or no
	 */
	private function isLDAPUser($user) {	
		return $this->DNexists($this->userDN($user));
	}

	/**
	 * deletes LDAP user
	 * @param  string $user user CN
	 * @return boolean       successful or not
	 */
	private function delUser($user) {
		if(!ldap_delete($this->res, $this->userDN($user))) {
			return $this->error();
		}
		return true;
	}

	/**
	 * removes user from LDAP permission
	 * @param  string $user       user CN
	 * @param  string $permission permission CN
	 * @return boolean             successful or not
	 */
	private function delUserPermission($user, $permission) {
		if(!ldap_mod_del($this->res, $this->permissionDN($permission), array('member' => $this->userDN($user)))) {
			return $this->error();
		}
		return true;
	}

	/**
	 * removes user from LDAP group
	 * @param  string $user  user CN
	 * @param  string $group group CN
	 * @return boolean        successful or not
	 */
	private function delUserFromGroup($user, $group) {
		if(!ldap_mod_del($this->res, $this->groupDN($group), array('member' => $this->userDN($user)))) {
			return $this->error();
		}
		return true;
	}





	
	/**
	 * adds LDAP group
	 * @param string $group group CN
	 * @return boolean successful or not
	 */
	private function addGroup($group) {
		if(!ldap_add($this->res, $this->groupDN($group), array(
			'cn' => $group,
			'objectClass' => 'groupOfNames'
		))) {
			return $this->error();
		}
		return true;
	}

	/**
	 * adds an group to and group
	 * @param string $groupToAdd group CN to add as member
	 * @param string $group      group CN
	 */
	private function addGroupToGroup($groupToAdd, $group) {
		return $this->setAttribute($this->groupDN($group), 'member', $this->groupDN($groupToAdd));
	}

	/**
	 * gives a group a permission in LDAP
	 * @param string $group      group CN
	 * @param string $permission permission CN
	 * @return boolean successful or not
	 */
	private function addGroupPermission($group, $permission) {
		if(!ldap_mod_add($this->res, $this->permissionDN($permission), array(
			'member' => $this->groupDN($group)
		))) {
			return $this->error();
		}
		return true;
	}

	/**
	 * gets an attribute of a group
	 * @param  string $group     group CN
	 * @param  string $attribute attribute name
	 * @return array            values for attribute and value "count"
	 */
	private function getGroupAttribute($group, $attribute) {
		return $this->getAttribute($this->groupDN($group), $attribute);
	}
	
	/**
	 * returns a list of users in a group
	 * @param  string $group group name
	 * @return array        list of CNs
	 */
	private function getAllGroupMembers($group) {
		return $this->getCNList($this->groupDN($group), 'member', LDAP_USER_BASE);
	}
	
	/**
	 * returns a list of groups in a group
	 * @param  string $group group name
	 * @return array        list of CNs
	 */
	private function getAllGroupGroups($group) {
		return $this->getCNList($this->groupDN($group), 'member', LDAP_GROUP_BASE);
	}
	
	/**
	 * returns a list of owners of a group
	 * @param  string $group group name
	 * @return array        list of CNs
	 */
	private function getAllGroupLeaders($group) {
		return $this->getCNList($this->groupDN($group), 'owner');
	}
	
	private function getGroupPermissions($group) {
		return $this->getMemberOfList($this->groupDN($group), 'permissions');
	}

	/**
	 * deletes LDAP group
	 * @param  string $group group CN
	 * @return boolean        successful or not
	 */
	private function delGroup($group) {
		if(!ldap_delete($this->res, $this->groupDN($group))) {
			return $this->error();
		}
		return true;
	}

	/**
	 * removes a LDAP permission from a group
	 * @param  string $group      group CN
	 * @param  string $permission permission CN
	 * @return boolean             successful or not
	 */
	private function delGroupPermission($group, $permission) {
		if(!ldap_mod_del($this->res, $this->permissionDN($permission), array(
			'member' => $this->groupDN($group)
		))) {
			return $this->error();
		}
		return true;
	}

	/**
	 * deletes group from group
	 * @param  string $groupToDel child group to delete
	 * @param  string $group      group CN of parent group
	 * @return boolean             successful or not
	 */
	private function delGroupFromGroup($groupToDel, $group) {
		return $this->delAttribute($this->groupDN($group), array('member' => $this->groupDN($groupToDel)));
	}




	/**
	 * adds a LDAP-permission
	 * @param string $permission permission CN
	 * @return boolean successful or not
	 */
	private function addPermission($permission) {
		if(!ldap_add($this->res, $this->permissionDN($permission), array(
			'cn' => $permission,
			'objectClass' => 'groupOfNames'
		))) {
			return $this->error();
		}
		return true;
	}

	/**
	 * deletes permission
	 * @param  string $permission permission CN
	 * @return boolean             successful or not
	 */
	private function delPermission($permission) {
		if(!ldap_delete($this->res, $this->permissionDN($permission))) {
			return $this->error();
		}
		return true;
	}

	//TODO: VORSICHT - WAS BEI MEHRERE ATTRIBUTE? RÜCKGABE ALS ARRAY?
	private function getUserPermissions($user) {
		return $this->getMemberOfList($this->userDN($user), 'permissions');
	}
	
	private function getPermissionAttribute($permission, $attribute) {
		return $this->getAttribute($this->permissionDN($permission), $attribute);
	}
	
	private function getPermissionedUser($permission) {
		return $this->getCNList($this->permissionDN($permission), 'member', 'users');
	}

	private function getUserGroups($user) {
		return $this->getMemberOfList($this->userDN($user), 'groups');
	}

	//TODO: Sowohl Berechtigungen durch Permission und Group berücksichtigen
	private function isQualified($user, $permission) {
		$members = $this->getAttribute($this->permissionDN($permission), 'member');
		$user = $this->userDN($user);
		foreach($members as $member) {
			if($member == $user) {
				return true;
			}
		}
		return false;
	}
	
	/**
	 * Deletes an attribute from a group
	 * @param  string $group     Group name
	 * @param  string $attribute attribute
	 * @param  string $value     value to delete
	 * @return bool            successfull or not
	 */
	private function delGroupAttribute($group, $attribute, $value) {
		return $this->delAttribute($this->groupDN($group), array($attribute => $value));
	}
	
	/**
	 * Deletes an attribute from a permission
	 * @param  string $group     permission name
	 * @param  string $attribute attribute
	 * @param  string $value     value to delete
	 * @return bool            successfull or not
	 */
	private function delPermissionAttribute($permission, $attribute, $value) {
		return $this->delAttribute($this->permissionDN($permission), array($attribute => $value));
	}
	
	/**
	 * Deletes an attribute from an user
	 * @param  string $group     user name
	 * @param  string $attribute attribute
	 * @param  string $value     value to delete
	 * @return bool            successfull or not
	 */
	private function delUserAttribute($user, $attribute, $value) {
		return $this->delAttribute($this->userDN($user), array($attribute => $vale));
	}

	//TODO: Array bei Value
	private function setGroupAttribute($group, $attribute, $value, $mode = 'add', $old_value = null) {
		$this->setAttribute($this->groupDN($group), $attribute, $value, $mode, $old_value);
	}
	
	private function setPermissionAttribute($permission, $attribute, $value, $mode = 'add', $old_value = null) {
		$this->setAttribute($this->permissionDN($permission), $attribute, $value, $mode, $old_value);
	}

	//TODO: Was heißt hier $mode = 'add'? Manchmal muss ein Attribute als komplett neues Attribut hinzugefügt werden, manchmal geändert werden
	private function setUserAttribute($user, $attribute, $value, $mode = 'add', $old_value = null) {
		$this->setAttribute($this->userDN($user), $attribute, $value, $mode, $old_value);
	}	

	//TODO
	private function isServerDomain($domain) {
		return $this->DNexists($this->domainDN($domain));
	}



	// PRIVATE METHODS //

	/**
	 * connects to LDAP
	 */
	protected function __construct() {
		parent::__construct(LDAP_HOST, LDAP_PORT);
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

	private function domainDN($domain) {
		return 'cn='.$domain.','.LDAP_DOMAIN_BASE;
	}

}

