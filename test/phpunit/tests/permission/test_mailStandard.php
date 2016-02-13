<?php

/**
 * Description of test_mailStandard
 *
 * @author deb139e
 */
class test_mailStandard extends PHPUnit_Framework_TestCase {
	public static $user_ids = array();
	public static $permission_ids = array();
	public static $mailStandard_model = array();
	
	public static function setUpBeforeClass() {
		self::$user_ids[0] = User_Control::createUser("AATest1", "Tester1", "test1@test1.de");
		self::$user_ids[1] = User_Control::createLandesverband("AANRW", "test2@test2.de");
		self::$user_ids[2] = User_Control::createBasisgruppe("AATestgruppe1", "NRW", "test3@test3.de");
		self::$user_ids[3] = User_Control::createOrgauser("AATest2", "test4@test4.de");
		
		User_Control::aktivieren(self::$user_ids[0]);
		User_Control::aktivieren(self::$user_ids[1]);
		User_Control::aktivieren(self::$user_ids[2]);
		User_Control::aktivieren(self::$user_ids[3]);
		
		self::$permission_ids[0] = Permission_Control::create("Mail-Postfach");
		self::$permission_ids[1] = Permission_Control::create("Mail-Weiterleitung");
		
		User_Control::addPermission(self::$user_ids[0], self::$permission_ids[0]);
		User_Control::addPermission(self::$user_ids[1], self::$permission_ids[0]);
		User_Control::addPermission(self::$user_ids[2], self::$permission_ids[0]);
		
		User_Control::addPermission(self::$user_ids[2], self::$permission_ids[1]);
		User_Control::addPermission(self::$user_ids[3], self::$permission_ids[1]);
		
		self::$mailStandard_model[0] = new mailStandard_model(self::$user_ids[0]);
		self::$mailStandard_model[1] = new mailStandard_model(self::$user_ids[1]);
		self::$mailStandard_model[2] = new mailStandard_model(self::$user_ids[2]);
		self::$mailStandard_model[3] = new mailStandard_model(self::$user_ids[3]);
	}
	
	public function test_model() {
		$this->assertTrue(self::$mailStandard_model[0]->isMailPermitted);
		$this->assertTrue(self::$mailStandard_model[1]->isMailPermitted);
		$this->assertTrue(self::$mailStandard_model[2]->isMailPermitted);
		$this->assertFalse(self::$mailStandard_model[3]->isMailPermitted);
		
		$this->assertFalse(self::$mailStandard_model[0]->isMailForwardPermitted);
		$this->assertFalse(self::$mailStandard_model[1]->isMailForwardPermitted);
		$this->assertTrue(self::$mailStandard_model[2]->isMailForwardPermitted);
		$this->assertTrue(self::$mailStandard_model[3]->isMailForwardPermitted);
		
		$this->assertEquals("AATest1.Tester1@gruene-jugend.de",	self::$mailStandard_model[0]->mail);
		$this->assertEquals("AANRW@gruene-jugend.de",			self::$mailStandard_model[1]->mail);
		$this->assertEquals("AATestgruppe1@gruene-jugend.de",	self::$mailStandard_model[2]->mail);
		$this->assertEquals("AATest2@gruene-jugend.de",			self::$mailStandard_model[3]->mail);
		
		$this->assertFalse(self::$mailStandard_model[0]->useMail);
		$this->assertFalse(self::$mailStandard_model[1]->useMail);
		$this->assertFalse(self::$mailStandard_model[2]->useMail);
		$this->assertFalse(self::$mailStandard_model[3]->useMail);
		
		$this->assertFalse(self::$mailStandard_model[0]->useMailForward);
		$this->assertFalse(self::$mailStandard_model[1]->useMailForward);
		$this->assertFalse(self::$mailStandard_model[2]->useMailForward);
		$this->assertFalse(self::$mailStandard_model[3]->useMailForward);
		
		$ldapConn = ldapConnector::get();
		$ldapConn->setUserAttribute(self::$mailStandard_model[0]->user_login, "mail", "AATest1.Tester1@gruene-jugend.de", "replace", "test1@test1.de");
		$ldapConn->setUserAttribute(self::$mailStandard_model[1]->user_login, "mail", "AANRW2@gruene-jugend.de", "replace", "test2@test2.de");
		$ldapConn->setUserAttribute(self::$mailStandard_model[2]->user_login, "mailForwardingAddress", "AATestgruppe1@gruene-jugend.de");
		$ldapConn->setUserAttribute(self::$mailStandard_model[3]->user_login, "mailForwardingAddress", "AATest3@gruene-jugend.de");
		
		$this->assertTrue(self::$mailStandard_model[0]->useMail);
		$this->assertTrue(self::$mailStandard_model[1]->useMail);
		$this->assertFalse(self::$mailStandard_model[2]->useMail);
		$this->assertFalse(self::$mailStandard_model[3]->useMail);
		
		$this->assertFalse(self::$mailStandard_model[0]->useMailForward);
		$this->assertFalse(self::$mailStandard_model[1]->useMailForward);
		$this->assertTrue(self::$mailStandard_model[2]->useMailForward);
		$this->assertTrue(self::$mailStandard_model[3]->useMailForward);
		
		$this->assertEquals("AATest1.Tester1@gruene-jugend.de",	self::$mailStandard_model[0]->mail);
		$this->assertEquals("AANRW2@gruene-jugend.de",			self::$mailStandard_model[1]->mail);
		$this->assertEquals("AATestgruppe1@gruene-jugend.de",	self::$mailStandard_model[2]->mail);
		$this->assertEquals("AATest3@gruene-jugend.de",			self::$mailStandard_model[3]->mail);
		
		$ldapConn->setUserAttribute(self::$mailStandard_model[0]->user_login, "mail", "AATest1.Tester2@gruene-jugend.de");
		$ldapConn->setUserAttribute(self::$mailStandard_model[2]->user_login, "mailForwardingAddress", "AATest4@gruene-jugend.de");
		self::$mailStandard_model[5] = new mailStandard_model(self::$user_ids[0]);
		self::$mailStandard_model[6] = new mailStandard_model(self::$user_ids[2]);
		$this->assertTrue((self::$mailStandard_model[5]->isMailPermitted instanceof WP_Error));
		$this->assertTrue((self::$mailStandard_model[6]->isMailPermitted instanceof WP_Error));
		$ldapConn->delUserAttribute(self::$mailStandard_model[0]->user_login, "mail", "AATest1.Tester2@gruene-jugend.de");
		$ldapConn->delUserAttribute(self::$mailStandard_model[2]->user_login, "mailForwardingAddress", "AATest4@gruene-jugend.de");
	}
	
	public function test_control_del() {
		mailStandard_control::delMail(self::$user_ids[0]);
		mailStandard_control::delMail(self::$user_ids[1]);
		mailStandard_control::delMailForward(self::$user_ids[2]);
		mailStandard_control::delMailForward(self::$user_ids[3]);
		
		$this->assertFalse(self::$mailStandard_model[0]->useMail);
		$this->assertFalse(self::$mailStandard_model[1]->useMail);
		$this->assertFalse(self::$mailStandard_model[2]->useMailForward);
		$this->assertFalse(self::$mailStandard_model[3]->useMailForward);
	}
	
	public function test_control_set() {
		mailStandard_control::setMail(self::$user_ids[0]);
		mailStandard_control::setMail(self::$user_ids[1]);
		mailStandard_control::setMailForward(self::$user_ids[2]);
		mailStandard_control::setMailForward(self::$user_ids[3]);
		
		$this->assertTrue(self::$mailStandard_model[0]->useMail);
		$this->assertTrue(self::$mailStandard_model[1]->useMail);
		$this->assertTrue(self::$mailStandard_model[2]->useMailForward);
		$this->assertTrue(self::$mailStandard_model[3]->useMailForward);
	}
	
	public static function tearDownAfterClass() {
		User_Control::delete(self::$user_ids[0]);
		User_Control::delete(self::$user_ids[1]);
		User_Control::delete(self::$user_ids[2]);
		User_Control::delete(self::$user_ids[3]);
		
		Permission_Control::delete(self::$permission_ids[0]);
		Permission_Control::delete(self::$permission_ids[1]);
	}
}
