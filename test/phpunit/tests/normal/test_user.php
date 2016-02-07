<?php

/**
 * Description of test_user
 *
 * @author KWM
 */
class test_user extends PHPUnit_Framework_TestCase {
	public static $user_ids = array();
	
	public function test_control_create() {
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
		$this->assertEquals(get_user_meta(self::$user_ids[0], "io_user_art",		true),				"user");
		$this->assertEquals(get_user_meta(self::$user_ids[0], "io_user_aktiv",		true),				0);
		
		
		/*
		 * Test User 2
		 */
		$user = get_userdata(self::$user_ids[1]);
		$this->assertEquals($user->first_name, '');
		$this->assertEquals($user->last_name, '');
		$this->assertEquals($user->user_login,															"NRW");
		$this->assertEquals($user->user_email,															"test2@test2.de");
		$this->assertEquals(get_user_meta(self::$user_ids[1], "io_user_art",		true),				"landesverband");
		$this->assertEquals(get_user_meta(self::$user_ids[1], "io_user_aktiv",		true),				0);
		
		/*
		 * Test User 3
		 */
		$user = get_userdata(self::$user_ids[2]);
		$this->assertEquals($user->first_name, '');
		$this->assertEquals($user->last_name, '');
		$this->assertEquals($user->user_login,															"Testgruppe1");
		$this->assertEquals($user->user_email,															"test3@test3.de");
		$this->assertEquals(get_user_meta(self::$user_ids[2], "io_user_art",		true),				"basisgruppe");
		$this->assertEquals(get_user_meta(self::$user_ids[2], "io_user_aktiv",		true),				0);
		$this->assertEquals(get_user_meta(self::$user_ids[2], "io_user_lv",			true),				"NRW");
		
		/*
		 * Test User 4
		 */
		$user = get_userdata(self::$user_ids[3]);
		$this->assertEquals($user->first_name, '');
		$this->assertEquals($user->last_name, '');
		$this->assertEquals($user->user_login,															"Test2");
		$this->assertEquals($user->user_email,															"test4@test4.de");
		$this->assertEquals(get_user_meta(self::$user_ids[3], "io_user_art",		true),				"organisatorisch");
		$this->assertEquals(get_user_meta(self::$user_ids[3], "io_user_aktiv",		true),				0);
	}
	
	public function test_model() {
		$user[0] = new User(self::$user_ids[0]);
		$user[1] = new User(self::$user_ids[1]);
		$user[2] = new User(self::$user_ids[2]);
		$user[3] = new User(self::$user_ids[3]);
		
		/*
		 * Test User 1
		 */
		$this->assertEquals($user[0]->art,				"user");
		$this->assertEquals($user[0]->aktiv,			0);
		$this->assertEquals($user[0]->landesverband,	"");
		
		/*
		 * Test User 2
		 */
		$this->assertEquals($user[1]->art,				"landesverband");
		$this->assertEquals($user[1]->aktiv,			0);
		$this->assertEquals($user[1]->landesverband,	"");
		
		/*
		 * Test User 3
		 */
		$this->assertEquals($user[2]->art,				"basisgruppe");
		$this->assertEquals($user[2]->aktiv,			0);
		$this->assertEquals($user[2]->landesverband,	"NRW");
		
		/*
		 * Test User 4
		 */
		$this->assertEquals($user[3]->art,				"organisatorisch");
		$this->assertEquals($user[3]->aktiv,			0);
		$this->assertEquals($user[3]->landesverband,	"");
	}
	
	public function test_control_delete() {
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
