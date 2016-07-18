<?php

/**
 * Description of LDAP_Procy
 *
 * @author KWM
 */
final class LDAP_Proxy {
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

	private static final function login()
	{
		$res = ldap_connect(LDAP_HOST, LDAP_PORT);
		if ($res === false) {
			return;
		}
		ldap_set_option($res, LDAP_OPT_PROTOCOL_VERSION, 3); //we only support LDAPv3!
		$bind = ldap_bind($res, LDAP_PROXY_USER, LDAP_PROXY_PW);
		if (!$bind) {
			return;
		}
		return $res;
	}

	private static final function logout($res)
	{
		ldap_close($res);
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
		
		$dn = 'cn=' . $user->user_login . ',ou=users,dc=gruene-jugend,dc=de';
		
		if(!ldap_mod_replace($res, $dn, array(
			'userPassword' => "{SHA}" . base64_encode(pack( "H*", sha1($password))),
			'qmailGID' => intval(time() / 86400) //last password change - days since 01.01.1970
		))) {
			self::logout($res);
			return LDAP::error();
		}
		self::logout($res);
		return true;
	}

	public static final function addUserPermission($user, $permission)
	{
		$res = self::login();
		if (!ldap_mod_add($res, 'cn=' . $permission . ',' . LDAP_PERMISSION_BASE, array(
			'member' => 'cn=' . $user . ',' . LDAP_USER_BASE
		))
		) {
			self::logout($res);
			return LDAP::error();
		}
		self::logout($res);
		return true;
	}

	public static final function addUsersToGroup($user, $group)
	{
		$res = self::login();
		if (!ldap_mod_add($res, 'cn=' . $group . ',' . LDAP_GROUP_BASE, array(
			'member' => 'cn=' . $user . ',' . LDAP_USER_BASE
		))
		) {
			self::logout($res);
			return LDAP::error();
		}
		self::logout($res);
		return true;
	}
	
	public static final function addUser($firstname, $surname, $mail) {
		$res = self::login();
		if(empty($firstname) || empty($surname) || empty($mail)) {
			return new WP_Error('ldap_add_user_nodata', 'Der User benötigt einen Vornamen, Nachnamen und eine gültige E-Mail-Adresse.');
		}
		if(!ldap_add($res, 'cn='.$firstname.' '.$surname.','.LDAP_USER_BASE, array(
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
			self::logout($res);
			return false;
		}
		self::logout($res);
		return true;
	}
	
	/*
	 * CODE: description
	 * NEWSLETTER ART: deliveryMode
	 * ZEITPUNKT: qmailGID
	 * ANFRAGE ART: o
	 */
	
	public static final function isSherpaMember($mail) {
		$res = self::login();
		
		$search = ldap_search($res, "ou=sherpaMembers,dc=gruene-jugend,dc=de", "(mail=" . $mail . ")", array("givenName", "cn"));
		
		$pruef = $search && ldap_count_entries($res, $search) > 0;
		self::logout($res);
		return $pruef;
	}
	
	public static final function setSherpaMemberCode($mail, $art) {
		$res = self::login();
		$search = ldap_search($res, "ou=sherpaMembers,dc=gruene-jugend,dc=de", "(mail=" . $mail . ")", array("description", "qmailGID", "o"));
		$entries = ldap_get_entries($res, $search);
		unset($entries["count"]);
		
		$dn = $entries[0]["dn"];
		
		$key = wp_generate_password( 10, false, false );
		if(!empty($entries[0]["description"][0])) {
			ldap_mod_del($res, $dn, array("description" => $entries[0]["description"][0]));
		}
		ldap_mod_add($res, $dn, array("description" => $key));
		
		$time = (time()-(time()%86400)) / 86400;
		if(!empty($entries[0]["qmailgid"][0])) {
			ldap_mod_del($res, $dn, array("qmailGID" => $entries[0]["qmailgid"][0]));
		}
		ldap_mod_add($res, $dn, array("qmailGID" => $time));
		
		if(!empty($entries[0]["o"][0])) {
			ldap_mod_del($res, $dn, array("o" => $entries[0]["o"][0]));
		}
		ldap_mod_add($res, $dn, array("o" => $art));
		
		self::logout($res);
		return $key;
	}
	
	public static final function isSherpaKey($key) {
		$res = self::login();
		
		$search = ldap_search($res, "ou=sherpaMembers,dc=gruene-jugend,dc=de", "(description=" . $key . ")", array("cn", "o", "qmailGID"));
		
		if(!$search || ldap_count_entries($res, $search) == 0) {
			self::logout($res);
			return false;
		}
		
		$entries = ldap_get_entries($res, $search);
		
		if(((time()-(time()%86400)) / 86400) > ($entries[0]["qmailgid"][0]+3)) {
			self::logout($res);
			return false;
		}
		self::logout($res);
		return $entries[0]["o"][0];
	}

	public static final function setSherpaLoeschen($key)
	{
		$args = self::setSherpa($key);
		ldap_mod_add($args[0], $args[1], array("deliveryMode" => "N"));
		self::logout($args[0]);
	}
	
	private static final function setSherpa($key) {
		$res = self::login();
		$search = ldap_search($res, "ou=sherpaMembers,dc=gruene-jugend,dc=de", "(description=" . $key . ")", array("description", "qmailGID", "deliveryMode", "o"));
		$entries = ldap_get_entries($res, $search);
		
		$dn = $entries[0]["dn"];
		if(!empty($entries[0]["description"][0])) {
			ldap_mod_del($res, $dn, array("description" => $entries[0]["description"][0]));
		}
		if(!empty($entries[0]["qmailgid"][0])) {
			ldap_mod_del($res, $dn, array("qmailGID" => $entries[0]["qmailgid"][0]));
		}
		if(!empty($entries[0]["deliverymode"][0])) {
			ldap_mod_del($res, $dn, array("deliveryMode" => $entries[0]["deliverymode"][0]));
		}
		if(!empty($entries[0]["o"][0])) {
			ldap_mod_del($res, $dn, array("o" => $entries[0]["o"][0]));
		}
		return array($res, $dn);
	}
	
	public static final function setSherpaEintragen($key) {
		$args = self::setSherpa($key);
		ldap_mod_add($args[0], $args[1], array("deliveryMode" => "J"));
		self::logout($args[0]);
	}
	
	public static final function setSherpaChange($key, $alt, $neu) {
		$res = self::login();
		
		$search = ldap_search($res, "ou=sherpaMembers,dc=gruene-jugend,dc=de", "(description=" . $key . ")", array("description", "qmailgid", "o", "mail"));
		$entries = ldap_get_entries($res, $search);
		
		$dn = $entries[0]["dn"];
		if($entries[0]["mail"][0] != $alt) {
			self::logout($res);
			return false;
		}
		
		ldap_mod_add($res, $dn, array("mailAlternateAddress" => $neu));
		
		$key = wp_generate_password( 10, false, false );
		if(!empty($entries[0]["description"][0])) {
			ldap_mod_del($res, $dn, array("description" => $entries[0]["description"][0]));
		}
		ldap_mod_add($res, $dn, array("description" => $key));
		
		$time = (time()-(time()%86400)) / 86400;
		if(!empty($entries[0]["qmailgid"][0])) {
			ldap_mod_del($res, $dn, array("qmailGID" => $entries[0]["qmailgid"][0]));
		}
		ldap_mod_add($res, $dn, array("qmailGID" => $time));
		
		if(!empty($entries[0]["o"][0])) {
			ldap_mod_del($res, $dn, array("o" => $entries[0]["o"][0]));
		}
		ldap_mod_add($res, $dn, array("o" => "c"));
		
		self::logout($res);
		
		return $key;
	}
	
	public static final function setSherpaChangeFinal($key) {
		$res = self::login();
		
		$search = ldap_search($res, "ou=sherpaMembers,dc=gruene-jugend,dc=de", "(description=" . $key . ")", array("description", "qmailgid", "o", "mail", "mailAlternateAddress", "mailReplyText"));
		$entries = ldap_get_entries($res, $search);
		
		$dn = $entries[0]["dn"];
		if(!empty($entries[0]["description"][0])) {
			ldap_mod_del($res, $dn, array("description" => $entries[0]["description"][0]));
		}
		if(!empty($entries[0]["qmailgid"][0])) {
			ldap_mod_del($res, $dn, array("qmailGID" => $entries[0]["qmailgid"][0]));
		}
		if(!empty($entries[0]["o"][0])) {
			ldap_mod_del($res, $dn, array("o" => $entries[0]["o"][0]));
		}
		ldap_mod_add($res, $dn, array("mail" => $entries[0]["mailalternateaddress"][0]));
		if(!empty($entries[0]["mail"][0])) {
			ldap_mod_del($res, $dn, array("mail" => $entries[0]["mail"][0]));
		}
		if(!empty($entries[0]["mailalternateaddress"][0])) {
			ldap_mod_del($res, $dn, array("mailAlternateAddress" => $entries[0]["mailalternateaddress"][0]));
		}
		if(!empty($entries[0]["mailreplytext"][0])) {
			ldap_mod_del($res, $dn, array("mailReplyText" => $entries[0]["mailreplytext"][0]));
		}
		ldap_mod_add($res, $dn, array("mailReplyText" => "J"));
		
		self::logout($res);
	}
	
	public static final function isMember($vorname, $nachname, $mail) {
		$res = self::login();
		
		$search = ldap_search($res, "ou=sherpaMembers,dc=gruene-jugend,dc=de", "(&(givenName=" . $vorname . ")(sn=" . $nachname . ")(mail=" . $mail . "))", array("cn"));
		
		if(!$search) {
			return false;
		}
		
		return $search && ldap_count_entries($res, $search) > 0;
	}
}
