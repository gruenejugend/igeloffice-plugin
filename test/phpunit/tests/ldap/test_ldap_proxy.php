<?php

/**
 * Description of test_ldap_user
 *
 * @author KWM
 */
class test_ldap_proxy extends PHPUnit_Framework_TestCase {
	public static $user_ids = array();
	public static $user = array();
	
	public static function setUpBeforeClass() {
		self::$user_ids[0] = User_Control::createUser("Test1", "Tester1", "test1@test1.de");
		self::$user[0] = new User(self::$user_ids[0]);
	}
	
	public function test_isLDAPUser() {
		$this->assertFalse(LDAP_Proxy::isLDAPUser(self::$user[0]->user_login));
		User_Control::aktivieren(self::$user_ids[0]);
		$this->assertTrue(LDAP_Proxy::isLDAPUser(self::$user[0]->user_login));
	}
	
	public static function tearDownAfterClass() {
		User_Control::delete(self::$user_ids[0]);
	}
}
