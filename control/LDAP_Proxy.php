<?php

/**
 * Description of LDAP_Procy
 *
 * @author KWM
 */
final class LDAP_Proxy {
	private static final function login() {
		$res = ldap_connect(LDAP_HOST, LDAP_PORT);
		if($res === false) {
			return;
		}
		ldap_set_option($res, LDAP_OPT_PROTOCOL_VERSION, 3); //we only support LDAPv3!
		$bind = ldap_bind($res, LDAP_PROXY_USER, LDAP_PROXY_PW);
		if(!$bind) {
			return;
		}
		return $res;
	}
	
	private static final function logout($res) {
		ldap_close($res);
	}
	
	public static final function isLDAPUser($user_login, $user_email = null) {
		$res = self::login();
		
		$dn = 'cn=' . $user_login . ',ou=users,dc=gruene-jugend,dc=de';
		
		try {
			$read = ldap_read($res, $dn, '(objectclass=*)', array());
			if($read === false) {
				self::logout($res);
				return false;
			}
			
			$count = ldap_count_entries($res, $read);
			if($count !== false || $count > 0) {
				if($user_email && (self::getAttribute($res, $dn, "mail") == $user_email || self::getAttribute($res, $dn, "mailAlternateAddress") == $user_email)) {
					return true;
				} elseif($user_email) {
					return "mailNotThere";
				}
				
				self::logout($res);
				return true;
			}
		} catch(Exception $ex) {
			if(substr($ex->getMessage(), 0, 35) == 'ldap_read(): Search: No such object') {
				self::logout($res);
				return false;
			}
			self::logout($res);
			echo $ex->getTraceAsString();
			die;
		}
		self::logout($res);
		return false;
	}
	
	private static final function getAttribute($res, $dn, $attribute) {
		$read = ldap_read($res, $dn, '(objectclass=*)', array($attribute));
		if($read === false) {
			return $this->error();
		}
		$read = ldap_first_entry($res, $read);
		if($read === false) {
			return $this->error();
		}
		$data = ldap_get_attributes($res, $read);
		if(!is_array($data)) {
			return $this->error();
		}
		return $data[$attribute][0];
	}
	
	public static final function changePW($user, $password) {
		$res = self::login();
		
		$dn = 'cn=' . $user . ',ou=users,dc=gruene-jugend,dc=de';
		
		if(!ldap_mod_replace($res, $dn, array(
			'userPassword' => "{SHA}" . base64_encode(pack( "H*", sha1($password))),
			'qmailGID' => intval(time() / 86400) //last password change - days since 01.01.1970
		))) {
			self::logout($res);
			return $this->error();
		}
		self::logout($res);
		return true;
	}
}
