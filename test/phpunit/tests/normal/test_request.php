<?php

/**
 * Description of test_group
 *
 * @author KWM
 */
class test_request extends PHPUnit_Framework_TestCase {
	public static $user_ids = array();
	public static $users = array();
	public static $group_ids = array();
	public static $group = array();
	public static $permission_ids = array();
	public static $permission = array();
	public static $request_ids = array();
	
	public static function setUpBeforeClass() {
		self::$group_ids[0] = Group_Control::create("Test5", "TestOben1", "TestUnten1");
		self::$group_ids[1] = Group_Control::create("Test6", "TestOben1", "TestUnten2");
		
		self::$group[0] = new Group(self::$group_ids[0]);
		self::$group[1] = new Group(self::$group_ids[1]);
		
		self::$user_ids[0] = User_Control::createUser("Test1", "Tester1", "test1@test1.de");
		self::$user_ids[1] = User_Control::createUser("Test2", "Tester2", "test2@test2.de");
		
		self::$users[0] = new User(self::$user_ids[0]);
		self::$users[1] = new User(self::$user_ids[1]);
		
		self::$permission_ids[0] = Permission_Control::create("Test1", "TestOben1", "TestUnten1");
		self::$permission_ids[1] = Permission_Control::create("Test2", "TestOben1", "TestUnten2");
		
		self::$permission[0] = new Permission(self::$permission_ids[0]);
		self::$permission[1] = new Permission(self::$permission_ids[1]);
	}
	
	public function test_control_create() {
		self::$request_ids[0] = get_posts(array(
			'post_type'			=> Request_Control::POST_TYPE,
			'meta_query'		=> array(
				array(
					'key'				=> 'io_request_steller_in',
					'value'				=> self::$user_ids[0]
				)
			)
		))[0]->ID;
		self::$request_ids[1] = get_posts(array(
			'post_type'			=> Request_Control::POST_TYPE,
			'meta_query'		=> array(
				array(
					'key'				=> 'io_request_steller_in',
					'value'				=> self::$user_ids[1]
				)
			)
		))[0]->ID;
		self::$request_ids[2] = Request_Control::create(self::$user_ids[0], "Group", self::$group_ids[0]);
		self::$request_ids[3] = Request_Control::create(self::$user_ids[1], "Group", self::$group_ids[1]);
		self::$request_ids[4] = Request_Control::create(self::$user_ids[0], "Permission", self::$permission_ids[0]);
		self::$request_ids[5] = Request_Control::create(self::$user_ids[1], "Permission", self::$permission_ids[1]);
		
		/*
		 * Test Request 1
		 */
		$post = get_post(self::$request_ids[0]);
		
		$this->assertNotNull($post);
		$this->assertEquals($post->post_title,											"User-Aktivierung " . self::$users[0]->user_login);
		$this->assertEquals(get_post_meta($post->ID, "io_request_art", true),			"User-Aktivierung");
		$this->assertEquals(get_post_meta($post->ID, "io_request_steller_in", true),	self::$user_ids[0]);
		$this->assertEquals(get_post_meta($post->ID, "io_request_status", true),		"Gestellt");
		
		/*
		 * Test Request 2
		 */
		$post = get_post(self::$request_ids[1]);
		
		$this->assertNotNull($post);
		$this->assertEquals($post->post_title,											"User-Aktivierung " . self::$users[1]->user_login);
		$this->assertEquals(get_post_meta($post->ID, "io_request_art", true),			"User-Aktivierung");
		$this->assertEquals(get_post_meta($post->ID, "io_request_steller_in", true),	self::$user_ids[1]);
		$this->assertEquals(get_post_meta($post->ID, "io_request_status", true),		"Gestellt");
		
		/*
		 * Test Request 3
		 */
		$post = get_post(self::$request_ids[2]);
		
		$this->assertNotNull($post);
		$this->assertEquals($post->post_title,											"Gruppen-Mitgliedschaft " . self::$users[0]->user_login . " bei " . self::$group[0]->name);
		$this->assertEquals(get_post_meta($post->ID, "io_request_art", true),			"Gruppen-Mitgliedschaft");
		$this->assertEquals(get_post_meta($post->ID, "io_request_steller_in", true),	self::$user_ids[0]);
		$this->assertEquals(get_post_meta($post->ID, "io_request_requested_id", true),	self::$group_ids[0]);
		$this->assertEquals(get_post_meta($post->ID, "io_request_status", true),		"Gestellt");
		
		/*
		 * Test Request 4
		 */
		$post = get_post(self::$request_ids[3]);
		
		$this->assertNotNull($post);
		$this->assertEquals($post->post_title,											"Gruppen-Mitgliedschaft " . self::$users[1]->user_login . " bei " . self::$group[1]->name);
		$this->assertEquals(get_post_meta($post->ID, "io_request_art", true),			"Gruppen-Mitgliedschaft");
		$this->assertEquals(get_post_meta($post->ID, "io_request_steller_in", true),	self::$user_ids[1]);
		$this->assertEquals(get_post_meta($post->ID, "io_request_requested_id", true),	self::$group_ids[1]);
		$this->assertEquals(get_post_meta($post->ID, "io_request_status", true),		"Gestellt");
		
		/*
		 * Test Request 5
		 */
		$post = get_post(self::$request_ids[4]);
		
		$this->assertNotNull($post);
		$this->assertEquals($post->post_title,											"Berechtigungs-Antrag " . self::$users[0]->user_login . " zu " . self::$permission[0]->name);
		$this->assertEquals(get_post_meta($post->ID, "io_request_art", true),			"Berechtigungs-Antrag");
		$this->assertEquals(get_post_meta($post->ID, "io_request_steller_in", true),	self::$user_ids[0]);
		$this->assertEquals(get_post_meta($post->ID, "io_request_requested_id", true),	self::$permission_ids[0]);
		$this->assertEquals(get_post_meta($post->ID, "io_request_status", true),		"Gestellt");
		
		/*
		 * Test Request 6
		 */
		$post = get_post(self::$request_ids[5]);
		
		$this->assertNotNull($post);
		$this->assertEquals($post->post_title,											"Berechtigungs-Antrag " . self::$users[1]->user_login . " zu " . self::$permission[1]->name);
		$this->assertEquals(get_post_meta($post->ID, "io_request_art", true),			"Berechtigungs-Antrag");
		$this->assertEquals(get_post_meta($post->ID, "io_request_steller_in", true),	self::$user_ids[1]);
		$this->assertEquals(get_post_meta($post->ID, "io_request_requested_id", true),	self::$permission_ids[1]);
		$this->assertEquals(get_post_meta($post->ID, "io_request_status", true),		"Gestellt");
	}
	
	public function test_model() {
		$request[0] = new Request(self::$request_ids[0]);
		$this->assertEquals($request[0]->name,			"User-Aktivierung " . self::$users[0]->user_login);
		$this->assertEquals($request[0]->art,			"User-Aktivierung");
		$this->assertEquals($request[0]->steller_in,	self::$user_ids[0]);
		$this->assertEquals($request[0]->status,		"Gestellt");
		
		$request[1] = new Request(self::$request_ids[1]);
		$this->assertEquals($request[1]->name,			"User-Aktivierung " . self::$users[1]->user_login);
		$this->assertEquals($request[1]->art,			"User-Aktivierung");
		$this->assertEquals($request[1]->steller_in,	self::$user_ids[1]);
		$this->assertEquals($request[1]->status,		"Gestellt");
		
		$request[2] = new Request(self::$request_ids[2]);
		$this->assertEquals($request[2]->name,			"Gruppen-Mitgliedschaft " . self::$users[0]->user_login . " bei " . self::$group[0]->name);
		$this->assertEquals($request[2]->art,			"Gruppen-Mitgliedschaft");
		$this->assertEquals($request[2]->steller_in,	self::$user_ids[0]);
		$this->assertEquals($request[2]->requested_id,	self::$group_ids[0]);
		$this->assertEquals($request[2]->status,		"Gestellt");
		
		$request[3] = new Request(self::$request_ids[3]);
		$this->assertEquals($request[3]->name,			"Gruppen-Mitgliedschaft " . self::$users[1]->user_login . " bei " . self::$group[1]->name);
		$this->assertEquals($request[3]->art,			"Gruppen-Mitgliedschaft");
		$this->assertEquals($request[3]->steller_in,	self::$user_ids[1]);
		$this->assertEquals($request[3]->requested_id,	self::$group_ids[1]);
		$this->assertEquals($request[3]->status,		"Gestellt");
		
		$request[4] = new Request(self::$request_ids[4]);
		$this->assertEquals($request[4]->name,			"Berechtigungs-Antrag " . self::$users[0]->user_login . " zu " . self::$permission[0]->name);
		$this->assertEquals($request[4]->art,			"Berechtigungs-Antrag");
		$this->assertEquals($request[4]->steller_in,	self::$user_ids[0]);
		$this->assertEquals($request[4]->requested_id,	self::$permission_ids[0]);
		$this->assertEquals($request[4]->status,		"Gestellt");
		
		$request[5] = new Request(self::$request_ids[5]);
		$this->assertEquals($request[5]->name,			"Berechtigungs-Antrag " . self::$users[1]->user_login . " zu " . self::$permission[1]->name);
		$this->assertEquals($request[5]->art,			"Berechtigungs-Antrag");
		$this->assertEquals($request[5]->steller_in,	self::$user_ids[1]);
		$this->assertEquals($request[5]->requested_id,	self::$permission_ids[1]);
		$this->assertEquals($request[5]->status,		"Gestellt");
	}
	
	public function test_control_count() {
		$this->assertEquals(Request_Control::count(), 6);
	}
	
	public function test_control_approve() {
		Request_Control::approve(self::$request_ids[0]);
		Request_Control::approve(self::$request_ids[2]);
		Request_Control::approve(self::$request_ids[4]);
		
		$request[0] = new Request(self::$request_ids[0]);
		$request[2] = new Request(self::$request_ids[2]);
		$request[4] = new Request(self::$request_ids[4]);
		
		$this->assertEquals(get_post_meta(self::$request_ids[0], "io_request_status", true), "Angenommen");
		$this->assertEquals(get_post_meta(self::$request_ids[2], "io_request_status", true), "Angenommen");
		$this->assertEquals(get_post_meta(self::$request_ids[4], "io_request_status", true), "Angenommen");
		
		$this->assertEquals($request[0]->status,		"Angenommen");
		$this->assertEquals($request[2]->status,		"Angenommen");
		$this->assertEquals($request[4]->status,		"Angenommen");
		
		$this->assertEquals(1, self::$users[0]->aktiv);
		
		$check = false;
		foreach(self::$group[0]->users AS $user) {
			if($user->user_login == self::$users[0]->user_login) {
				$check = true;
				break;
			}
		}
		$this->assertTrue($check);
		
		$check = false;
		foreach(self::$permission[0]->users AS $user) {
			if($user->user_login == self::$users[0]->user_login) {
				$check = true;
				break;
			}
		}
		$this->assertTrue($check);
	}
	
	public function test_control_reject() {
		Request_Control::reject(self::$request_ids[3]);
		Request_Control::reject(self::$request_ids[5]);
		
		$request[3] = new Request(self::$request_ids[3]);
		$request[5] = new Request(self::$request_ids[5]);
		
		$this->assertEquals(get_post_meta(self::$request_ids[3], "io_request_status", true), "Abgelehnt");
		$this->assertEquals(get_post_meta(self::$request_ids[5], "io_request_status", true), "Abgelehnt");
		
		$this->assertEquals($request[3]->status,		"Abgelehnt");
		$this->assertEquals($request[5]->status,		"Abgelehnt");
		
		$check = false;
		foreach(self::$group[1]->users AS $user) {
			if($user->user_login == self::$users[1]->user_login) {
				$check = true;
				break;
			}
		}
		$this->assertFalse($check);
		
		$check = false;
		foreach(self::$permission[1]->users AS $user) {
			if($user->user_login == self::$users[1]->user_login) {
				$check = true;
				break;
			}
		}
		$this->assertFalse($check);
		
		Request_Control::reject(self::$request_ids[1]);
		
		$request[1] = new Request(self::$request_ids[1]);
		
		$this->assertEquals(get_post_meta(self::$request_ids[1], "io_request_status", true), "Abgelehnt");
		
		$this->assertEquals($request[1]->status,		"Abgelehnt");
		
		$this->assertFalse(get_userdata(self::$user_ids[1]));
	}
	
	public static function tearDownAfterClass() {
		User_Control::delete(self::$user_ids[0]);
		User_Control::delete(self::$user_ids[1]);
		Group_Control::delete(self::$group_ids[0]);
		Group_Control::delete(self::$group_ids[1]);
		Permission_Control::delete(self::$permission_ids[0]);
		Permission_Control::delete(self::$permission_ids[1]);
	}
}