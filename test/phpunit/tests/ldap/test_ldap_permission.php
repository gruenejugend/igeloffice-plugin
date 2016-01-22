<?php

/**
 * Description of test_ldap_user
 *
 * @author KWM
 */
class test_ldap_user extends PHPUnit_Framework_TestCase {
	public static $permission_ids = array();

	public static function setUpBeforeClass() {
		self::$permission_ids[0] = Permission_Control::create("Test1", "TestOben1", "TestUnten1");
		self::$permission_ids[1] = Permission_Control::create("Test2", "TestOben1", "TestUnten2");
		self::$permission_ids[2] = Permission_Control::create("Test3", "TestOben2", "TestUnten3");
		self::$permission_ids[3] = Permission_Control::create("Test4", "TestOben3");
		
		self::$ldap = ldapConnector::get();
	}
	
	public function test_create() {
		$this->assertTrue(self::$ldap->DNexists("cn=Test1,ou=permissions,dc=gruene-jugend,dc=de"));
		$this->assertTrue(self::$ldap->DNexists("cn=Test2,ou=permissions,dc=gruene-jugend,dc=de"));
		$this->assertTrue(self::$ldap->DNexists("cn=Test3,ou=permissions,dc=gruene-jugend,dc=de"));
		$this->assertTrue(self::$ldap->DNexists("cn=Test4,ou=permissions,dc=gruene-jugend,dc=de"));
	}
	
	public function test_delete() {
		Permission_Control::delete(self::$permission_ids[0]);
		Permission_Control::delete(self::$permission_ids[1]);
		Permission_Control::delete(self::$permission_ids[2]);
		Permission_Control::delete(self::$permission_ids[3]);
		
		$this->assertFalse(self::$ldap->DNexists("cn=Test1,ou=permissions,dc=gruene-jugend,dc=de"));
		$this->assertFalse(self::$ldap->DNexists("cn=Test2,ou=permissions,dc=gruene-jugend,dc=de"));
		$this->assertFalse(self::$ldap->DNexists("cn=Test3,ou=permissions,dc=gruene-jugend,dc=de"));
		$this->assertFalse(self::$ldap->DNexists("cn=Test4,ou=permissions,dc=gruene-jugend,dc=de"));
	}
}
