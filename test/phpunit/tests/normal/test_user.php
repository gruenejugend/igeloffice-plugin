<?php

/**
 * Description of test_user
 *
 * @author KWM
 */
class test_user extends PHPUnit_Framework_TestCase {
	public static $user_ids = array();

	public static function setUp() {
		
	}
	
	public static function test_control_create() {
		self::$user_ids[0] = User_Control::createUser("Test1", "Tester1", "test1@test1.de");
		self::$user_ids[1] = User_Control::createLandesverband("NRW", "test2@test2.de");
		self::$user_ids[2] = User_Control::createBasisgruppe("Testgruppe1", "NRW", "test3@test3.de");
		self::$user_ids[3] = User_Control::createOrgauser("Test2", "test4@test4.de");
		
		/*
		 * Test User 1
		 */
		$user = get_userdata(self::$user_ids[0]);
		$this->assertEquals($user->first_name,															"Test1");
		$this->assertEquals($user->last_name,															"Tester1");
		$this->assertEquals($user->user_login,															"Test1 Tester1");
		$this->assertEquals($user->user_email,															"test1@test1.de");
		$this->assertEquals(get_user_meta(self::$user_ids[0], "io_user_art",		true),				"User");
		$this->assertFalse(get_user_meta(self::$user_ids[0], "io_user_aktiv",		true));
		
		
		/*
		 * Test User 2
		 */
		$user = get_userdata(self::$user_ids[1]);
		$this->assertNull($user->first_name);
		$this->assertNull($user->last_name);
		$this->assertEquals($user->user_login,															"NRW");
		$this->assertEquals($user->user_email,															"test2@test2.de");
		$this->assertEquals(get_user_meta(self::$user_ids[1], "io_user_art",		true),				"Landesverband");
		$this->assertFalse(get_user_meta(self::$user_ids[1], "io_user_aktiv",		true));
		
		/*
		 * Test User 3
		 */
		$user = get_userdata(self::$user_ids[2]);
		$this->assertNull($user->first_name);
		$this->assertNull($user->last_name);
		$this->assertEquals($user->user_login,															"Testgruppe1");
		$this->assertEquals($user->user_email,															"test3@test3.de");
		$this->assertEquals(get_user_meta(self::$user_ids[2], "io_user_art",		true),				"Basisgruppe");
		$this->assertFalse(get_user_meta(self::$user_ids[2], "io_user_aktiv",		true));
		$this->assertEquals(get_user_meta(self::$user_ids[2], "io_user_lv",			true),				"NRW");
		
		/*
		 * Test User 4
		 */
		$user = get_userdata(self::$user_ids[3]);
		$this->assertNull($user->first_name);
		$this->assertNull($user->last_name);
		$this->assertEquals($user->user_login,															"Test2");
		$this->assertEquals($user->user_email,															"test4@test4.de");
		$this->assertEquals(get_user_meta(self::$user_ids[3], "io_user_art",		true),				"Orgauser");
		$this->assertFalse(get_user_meta(self::$user_ids[3], "io_user_aktiv",		true));
	}
	
	public static function test_model() {
		$user[0] = new User(self::$user_ids[0]);
		$user[1] = new User(self::$user_ids[1]);
		$user[2] = new User(self::$user_ids[2]);
		$user[3] = new User(self::$user_ids[3]);
		
		/*
		 * Test User 1
		 */
		$this->assertEquals($user[0]->art,				"User");
		$this->assertFalse($user[0]->aktiv);
		$this->assertNull($user[0]->landesverband);
		
		/*
		 * Test User 2
		 */
		$this->assertEquals($user[1]->art,				"Landesverband");
		$this->assertFalse($user[1]->aktiv);
		$this->assertNull($user[1]->landesverband);
		
		/*
		 * Test User 3
		 */
		$this->assertEquals($user[2]->art,				"Basisgruppe");
		$this->assertFalse($user[2]->aktiv);
		$this->assertEquals($user[2]->landesverband,	"NRW");
		
		/*
		 * Test User 4
		 */
		$this->assertEquals($user[3]->art,				"Orgauser");
		$this->assertFalse($user[3]->aktiv);
		$this->assertNull($user[3]->landesverband);
	}
	
	public static function test_control_delete() {
		User_Control::delete(self::$user_ids[0]);
		User_Control::delete(self::$user_ids[1]);
		User_Control::delete(self::$user_ids[2]);
		User_Control::delete(self::$user_ids[3]);
		
		$this->assertFalse(get_userdata(self::$user_ids[0]));
		$this->assertFalse(get_userdata(self::$user_ids[1]));
		$this->assertFalse(get_userdata(self::$user_ids[2]));
		$this->assertFalse(get_userdata(self::$user_ids[3]));
	}
}
