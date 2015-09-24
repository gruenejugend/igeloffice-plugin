<?php

class LDAP {

	/**
	 * LDAP connection resource
	 * @var resource
	 */
	protected $res;

	/**
	 * true if object is bound to LDAP
	 * @var boolean
	 */
	protected $bind = false;

	protected function __construct($host, $post = 389) {
		$this->res = ldap_connect(LDAP_HOST, LDAP_PORT);
		if($this->res === false) {
			$this->error();
		}
		ldap_set_option($this->res, LDAP_OPT_PROTOCOL_VERSION, 3); //we only support LDAPv3!
	}

	protected function bind($userDN, $pass) {
		$this->bind = ldap_bind($this->res, $userDN, $pass);
		return $this->bind;
	}

	/**
	 * check if object is bound to LDAP server and triggers an E_USER_ERROR error if not. Use this before every LDAP query which should work.
	 * @return boolean if is bound to LDAP server
	 */
	protected function isBound() {
		if(!$this->bind) {
			return $this->error();
		}
		return true;
	}

	protected function getAttribute($dn, $attribute) {
		$read = ldap_read($this->res, $dn, '(objectclass=*)', array($attribute));
		if($read === false) {
			return $this->error();
		}
		$read = ldap_first_entry($this->res, $read);
		if($read === false) {
			return $this->error();
		}
		$data = ldap_get_attributes($this->res, $read);
		if(!is_array($data)) {
			return $this->error();
		}
		return $data[$attribute];
	}

	protected function getCNList($dn, $attribute, $ou = '*') {
		$data = $this->getAttribute($dn, $attribute, $ou);
		return $this->DNtoCN($data);
	}

	protected function DNtoCN($dns, $ou = '*') {
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

	protected function getMemberOfList($dn, $ou = '*') {
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

	protected function search($base, $filter) {
		$serach = ldap_search($this->res, $base, $filter);
		if($search === false) {
			return $this->error();
		}
		if(ldap_count_entries($this->res, $search)	> 0) {
			return $search;
		}
		return false;
	}

	protected function searchCN($base, $cn) {
		return $this->search($base, '(cn='.$cn.')');
	}

	protected function DNexists($dn) {
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
	 * @param string|int|array $value     new value. may be an array
	 * @param string $mode      'add' or 'replace'. if 'replace' you can provide $old_value
	 * @param string|int $old_value old value if replacing. if empty all values will be replaced. if old_value is not found, 'add' will be executed
	 */
	protected function setAttribute($dn, $attr, $value, $mode = 'add', $old_value = null) {
		if($mode == 'add') {
			if(ldap_mod_add($this->res, $dn, array($attr => $value))) {
				return true;
			}
			return $this->error();
		}
		elseif($mode ==  'replace') {
			ldap_mod_del($this->res, $dn, array($attr => $old_value));
			if(ldap_mod_add($this->res, $dn, array($attr => $value))) {
				return true;
			}
			return $this->error();
		}

		return false;
	}

	/**
	 * LDAP error handling
	 * @return boolean always false
	 */
	protected function error() {
		trigger_error('An internal LDAP error occured. Please contact the system administrator and provide him*her this message: '.ldap_error($this->res), E_USER_ERROR);
		return false;
	}
}