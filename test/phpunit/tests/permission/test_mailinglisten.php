<?php

/**
 * Description of test_mailinglisten
 *
 * @author KWM
 */
class test_mailinglisten extends PHPUnit_Framework_TestCase {
	public static $user_ids = array();
	public static $permission_ids = array();
	public static $mailinglisten_model = array();
	
	public static function setUpBeforeClass() {
		self::$user_ids[0] = User_Control::createUser("AATest1", "Tester1", "test1@test1.de");
		self::$user_ids[1] = User_Control::createLandesverband("AANRW", "test2@test2.de");
		
		User_Control::aktivieren(self::$user_ids[0]);
		User_Control::aktivieren(self::$user_ids[1]);
		
		self::$permission_ids[0] = Permission_Control::create("ListenAbo");
		
		User_Control::addPermission(self::$user_ids[0], self::$permission_ids[0]);
		
		self::$mailinglisten_model[0] = new mailinglisten_model(self::$user_ids[0]);
		self::$mailinglisten_model[1] = new mailinglisten_model(self::$user_ids[1]);
	}
	
	public function test_model() {
		$this->assertTrue(self::$mailinglisten_model[0]->isPermitted);
		$this->assertFalse(self::$mailinglisten_model[1]->isPermitted);
	}
	
	public static function tearDownAfterClass() {
		User_Control::delete(self::$user_ids[0]);
		User_Control::delete(self::$user_ids[1]);
		
		Permission_Control::delete(self::$permission_ids[0]);
	}
}
