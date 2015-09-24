<?php
/**
 * TODO: FRAGESTELLUNG, WAS PASSIERT BEI SET? WAS, WENN ATTRIBUTE ENTFERNT WERDEN? WAS, WENN HINZUGEFÜGT? WAS, WENN NUR GEÄNDERT?
 * TODO: Permissions werden nicht in LDAP erstellt
 */


/**
 * Description of ldapConnector
 *
 * @author KWM
 */
class ldapConnector {

	/**
	 * LDAP connection resource
	 * @var resource
	 */
	private $res;

	/**
	 * true if object is bound to LDAP
	 * @var boolean
	 */
	private $bind = false;

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
		$this->bind = ldap_bind($this->res, $this->userDN($user), $pass);
		return $this->bind;
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
	 */
	private function addUser($firstname, $surname, $mail) { //check if user or DN exists!
		if(empty($firstname) || empty($surname) || empty($mail)) {
			return new WP_Error('ldap_add_user_nodata', 'Der User benötigt einen Vornamen, Nachnamen und eine gültige E-Mail-Adresse.');
		}
		if(!ldap_add($this->res, $this->userDN($firstname.' '.$surname), array(
			'cn' => $firstname.' '.$surname,
			'sn' => $surname,
			'mail' => $mail,
			'mailAlternate' => $mail,
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
	 * adds an group to and group. this isn't implementet in LDAP yet, so this method does nothing.
	 * @param string $groupToAdd group CN
	 * @param string $group      group CN
	 * @todo implement this in LDAP server
	 */
	private function addGroupToGroup($groupToAdd, $group) {
		
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
	
	//TODO
	private function getAllGroupMembers($group) {
		return $this->getCNList($this->groupDN($group), 'member');
	}
	
	//TODO
	private function getAllGroupGroups($group) {
		
	}
	
	//TODO
	private function getAllGroupLeaders($group) {
		return $this->getCNList($this->groupDN($group), 'owner');
	}
	
	//TODO!
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
	 * @param  string $groupToDel group CN
	 * @param  string $group      group CN
	 * @return boolean             successful or not
	 * @see ldapConnector::addGroupToGroup() why this doen't work at the moment
	 * @todo implement
	 */
	private function delGroupToGroup($groupToDel, $group) {
		
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
	


	//TODO: Array bei Value
	private function setGroupAttribute($group, $attribute, $value) {
		
	}
	
	private function setPermissionAttribute($permissions, $attribute, $value) {
		
	}

	//TODO
	private function isServerDomain($domain) {
		return $this->DNexists($this->domainDN($domain));
	}



	/**
	 * closes LDAP connection
	 */
	public function __destruct() {
		ldap_close($this->res);
	}


	// PRIVATE METHODS //

	/**
	 * connects to LDAP
	 */
	private function __construct() {
		ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3); //we only support LDAPv3!
		$this->res = ldap_connect(LDAP_HOST, LDAP_PORT);
		if($this->res === false) {
			$this->error();
		}
	}

	/**
	 * check if object is bound to LDAP server and triggers an E_USER_ERROR error if not. Use this before every LDAP query which should work.
	 * @return boolean if is bound to LDAP server
	 */
	private function isBound() {
		if(!$this->bind) {
			return $this->error();
		}
		return true;
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

	private function getAttribute($dn, $attribute) {
		$read = ldap_read($this->res, $dn, '(objectclass=*)', array($attribute));
		if($read === false) {
			return $this->error();
		}
		$read = ldap_first_entry($read);
		if($read === false) {
			return $this->error();
		}
		$data = ldap_get_attributes($this->res, $read);
		if(!is_array($data)) {
			return $this->error();
		}
		return $data[$attribute];
	}

	private function getCNList($dn, $attribute, $ou = '*') {
		$data = $this->getAttribute($dn, $attribute, $ou);
		return $this->DNtoCN($data);
	}

	private function DNtoCN($dns, $ou = '*') {
		if(!is_array($dns)) {
			$dns = array($dns);
		}
		return array_map(function($dn) {
			$dn = ldap_explode_dn($dn, 1);
			if($ou == '*' || $dn[1] == $ou) {
				return $dn[0];
			}
		}, $dns);
	}

	private function getMemberOfList($dn, $ou = '*') {
		$data = $this->getAttribute($dn, 'memberOf');
		$return = array();
		foreach($data as $dat) {
			$dat = ldap_explode_dn($dat, 1);
			if($ou == '*' || $dat[1] == $ou) {
				$return[] = $dat[0];
			}
		}
		return $dat;
	}

	private function search($base, $filter) {
		$serach = ldap_search($this->res, $base, $filter);
		if($search === false) {
			return $this->error();
		}
		if(ldap_count_entries($this->res, $search)	> 0) {
			return $search;
		}
		return false;
	}

	private function searchCN($base, $cn) {
		return $this->search($base, '(cn='.$cn.')');
	}

	private function DNexists($dn) {
		$read = ldap_read($this->res, $dn, '(objectclass=*)', array());
		if($read === false) {
			return $this->error();
		}
		$count = ldap_count_entries($read);
		if($count !== false || $count > 0) {
			return true;
		}
		return false;
	}

	/**
	 * sets a value for an attribute in LDAP
	 * @param string $dn        LDAP DN
	 * @param string $attr      Attribute
	 * @param string|int|array $val       new value. may be an array
	 * @param string $mod       'add' or 'replace'. if 'replace' you can provide $old_value
	 * @param string|int $old_value old value if replacing. if empty all values will be replaced
	 */
	private function setAttribute($dn, $attr, $val, $mode = 'add', $old_value = null) {
		if($mode == 'add') {
			if(ldap_mod_add($this->res, $dn, array($attr => $val))) {
				return true;
			}
			return $this->error();
		}
		elseif($mode ==  'replace') {
			//TODO
		}

		return false;
	}

	/**
	 * LDAP error handling
	 * @return boolean always false
	 */
	private function error() {
		trigger_error('An internal LDAP error occured. Please contact the system administrator and provide him*her this message: '.ldap_error($this->res), E_USER_ERROR);
		return false;
	}

}

