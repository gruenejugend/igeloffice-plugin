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

	protected function __construct() {
		$this->res = ldap_connect(LDAP_HOST, LDAP_PORT);
		if($this->res === false) {
			Log_Control::writeLog("ldap.php", "construct: " . ldap_error($this->res));
			$this->error();
		}
		ldap_set_option($this->res, LDAP_OPT_PROTOCOL_VERSION, 3); //we only support LDAPv3!
	}

	/**
	 * LDAP error handling
	 * @return boolean always false
	 */
	protected function error()
	{
		trigger_error('An internal LDAP error occured. Please contact the system administrator and provide him*her this message: ' . ldap_error($this->res), E_USER_ERROR);
		return false;
	}

	/**
	 * closes LDAP connection
	 */
	public function __destruct()
	{
		ldap_close($this->res);
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
			Log_Control::writeLog("ldap.php", "isBound: " . ldap_error($this->res));
			return $this->error();
		}
		return true;
	}

	/**
	 * gets a list of CNs from a attribute values
	 * @param  string $dn DN
	 * @param  string $attribute attribute
	 * @param  string $ou ou filter for DN's
	 * @return array              list of CNs
	 */
	protected function getCNList($dn, $attribute, $ou = false)
	{
		$data = $this->getAttribute($dn, $attribute);
		if ($ou) {
			return $this->DNtoCN($data, $ou);
		}
		return $this->DNtoCN($data);
	}

	protected function getAttribute($dn, $attribute, $filter = '(objectclass=*)') {
		$read = ldap_read($this->res, $dn, $filter, array($attribute));
		if($read === false) {
			Log_Control::writeLog("ldap.php", "getAttribute, read: " . ldap_error($this->res));
			return $this->error();
		}
		$read = ldap_first_entry($this->res, $read);
		if($read === false) {
			Log_Control::writeLog("ldap.php", "getAttribute, first_entry: " . ldap_error($this->res));
			return $this->error();
		}
		$data = ldap_get_attributes($this->res, $read);
		if(!is_array($data)) {
			Log_Control::writeLog("ldap.php", "getAttribute, get_attributes: " . ldap_error($this->res));
			return $this->error();
		}
		return $data[$attribute];
	}

	protected function DNtoCN($dns, $ou = '*') {
		if(!is_array($dns)) {
			$dns = array($dns);
		}
		$return = array();
		foreach($dns as $dn) {
			$dn = ldap_explode_dn($dn, 1);
			if($ou == '*' || $dn[1] == $ou) {
				$return[] = urldecode(str_replace("\\", "%", $dn[0]));
			}
		}
		return $return;
	}

	protected function getMemberOfList($dn, $ou = '*') {
		$data = $this->getAttribute($dn, 'memberOf');
		$return = array();
		if(count($data) > 0) {
			foreach($data as $dat) {
				$dat = ldap_explode_dn($dat, 1);
				if($ou == '*' || $dat[1] == $ou) {
					$return[] = $dat[0];
				}
			}
		}
		return $return;
	}

	protected function searchCN($base, $cn, $attributes = array())
	{
		$search = $this->search($base, '(cn=' . $cn . ')', $attributes);
		if (count($search) == 1) {
			return $search[0];
		}
		return false;
	}

	protected function search($base, $filter = '', $attributes = array()) {
		$search = ldap_search($this->res, $base, $filter, $attributes);
		if($search === false) {
			Log_Control::writeLog("ldap.php", "search: " . ldap_error($this->res));
			return $this->error();
		}
		if(ldap_count_entries($this->res, $search)	> 0) {
			$result = ldap_get_entries($this->res, $search);
			unset($result['count']); //nobody needs this shit
			return $result;
		}
		return false;
	}

	protected function DNexists($dn) {
		try {
			$read = ldap_read($this->res, $dn, '(objectclass=*)', array());
			if($read === false) {
				return false;
			}
			
			$count = ldap_count_entries($this->res, $read);
			if($count !== false || $count > 0) {
				return true;
			}
		} catch(Exception $ex) {
			if(substr($ex->getMessage(), 0, 35) == 'ldap_read(): Search: No such object') {
				return false;
			}
			Log_Control::writeLog("ldap.php", "DNexists: " . $ex->getTraceAsString());
			echo $ex->getTraceAsString();
			die;
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
			Log_Control::writeLog("ldap.php", "setAttribute, add: " . ldap_error($this->res));
			return $this->error();
		}
		elseif($mode == 'replace') {
			if(ldap_mod_add($this->res, $dn, array($attr => $value))) {
				ldap_mod_del($this->res, $dn, array($attr => $old_value));
				return true;
			}
			Log_Control::writeLog("ldap.php", "setAttribute, replace: " . ldap_error($this->res));
			return $this->error();
		}

		return false;
	}

	/**
	 * deletes an attribute of an DN
	 * @param  string $dn     DN
	 * @param  array $values attributes to delete. if you want to delete all values of an atttribute use array('attribute' => ''). otherwise use array('attribute'=>'value').
	 * @return bool         successfull or not
	 */
	protected function delAttribute($dn, $values) {
		if(!ldap_mod_del($this->res, $dn, $values)) {
			Log_Control::writeLog("ldap.php", "delAttribute: " . ldap_error($this->res));
			$this->error();
		}
		return true;
	}
}