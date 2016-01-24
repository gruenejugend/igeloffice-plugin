<?php

/**
 * Description of test_permission
 *
 * @author KWM
 */
class test_permission extends PHPUnit_Framework_TestCase {
	public static $permission_ids = array();
	
	public function test_control_create() {
		self::$permission_ids[0] = Permission_Control::create("Test1", "TestOben1", "TestUnten1");
		self::$permission_ids[1] = Permission_Control::create("Test2", "TestOben1", "TestUnten2");
		self::$permission_ids[2] = Permission_Control::create("Test3", "TestOben2", "TestUnten3");
		self::$permission_ids[3] = Permission_Control::create("Test4", "TestOben3");
		
		/*
		 * Test Permission 1
		 */
		$post = get_post(self::$permission_ids[0]);
		
		$this->assertNotNull($post);
		$this->assertEquals($post->post_title,													"Test1");
		$this->assertEquals(get_post_meta(self::$permission_ids[0], 'io_permission_ok', true),	"TestOben1");
		$this->assertEquals(get_post_meta(self::$permission_ids[0], 'io_permission_uk', true),	"TestUnten1");
		
		/*
		 * Test Permission 2
		 */
		$post = get_post(self::$permission_ids[1]);
		
		$this->assertNotNull($post);
		$this->assertEquals($post->post_title,													"Test2");
		$this->assertEquals(get_post_meta(self::$permission_ids[1], 'io_permission_ok', true),	"TestOben1");
		$this->assertEquals(get_post_meta(self::$permission_ids[1], 'io_permission_uk', true),	"TestUnten2");
		
		/*
		 * Test Permission 3
		 */
		$post = get_post(self::$permission_ids[2]);
		
		$this->assertNotNull($post);
		$this->assertEquals($post->post_title,													"Test3");
		$this->assertEquals(get_post_meta(self::$permission_ids[2], 'io_permission_ok', true),	"TestOben2");
		$this->assertEquals(get_post_meta(self::$permission_ids[2], 'io_permission_uk', true),	"TestUnten3");
		
		/*
		 * Test Permission 4
		 */
		$post = get_post(self::$permission_ids[3]);
		
		$this->assertNotNull($post);
		$this->assertEquals($post->post_title,													"Test4");
		$this->assertEquals(get_post_meta(self::$permission_ids[3], 'io_permission_ok', true),	"TestOben3");
		$this->assertEquals('', get_post_meta(self::$permission_ids[3], 'io_permission_uk', true));
	}
	
	public function test_control_update() {
		Permission_Control::update(self::$permission_ids[0], "oberkategorie",	"TestOben2");
		Permission_Control::update(self::$permission_ids[0], "unterkategorie",	"TestUnten3");
		$this->assertEquals(get_post_meta(self::$permission_ids[0], 'io_permission_ok', true),	"TestOben2");
		$this->assertEquals(get_post_meta(self::$permission_ids[0], 'io_permission_uk', true),	"TestUnten3");
	}
	
	public function test_model() {
		$permission[0] = new Permission(self::$permission_ids[0]);
		$permission[1] = new Permission(self::$permission_ids[1]);
		$permission[2] = new Permission(self::$permission_ids[2]);
		$permission[3] = new Permission(self::$permission_ids[3]);
		
		/*
		 * Test Permission 1
		 */
		$this->assertEquals($permission[0]->name,			"Test1");
		$this->assertEquals($permission[0]->oberkategorie,	"TestOben2");
		$this->assertEquals($permission[0]->unterkategorie,	"TestUnten3");
		
		/*
		 * Test Permission 2
		 */
		$this->assertEquals($permission[1]->name,			"Test2");
		$this->assertEquals($permission[1]->oberkategorie,	"TestOben1");
		$this->assertEquals($permission[1]->unterkategorie,	"TestUnten2");
		
		/*
		 * Test Permission 3
		 */
		$this->assertEquals($permission[2]->name,			"Test3");
		$this->assertEquals($permission[2]->oberkategorie,	"TestOben2");
		$this->assertEquals($permission[2]->unterkategorie,	"TestUnten3");
		
		/*
		 * Test Permission 4
		 */
		$this->assertEquals($permission[3]->name,			"Test4");
		$this->assertEquals($permission[3]->oberkategorie,	"TestOben3");
		$this->assertEquals('', $permission[3]->unterkategorie);
	}
	
	public function test_control_delete() {
		Permission_Control::delete(self::$permission_ids[0]);
		Permission_Control::delete(self::$permission_ids[1]);
		Permission_Control::delete(self::$permission_ids[2]);
		Permission_Control::delete(self::$permission_ids[3]);
		
		/*
		 * Test Permission 1
		 */
		$post = get_post(self::$permission_ids[0]);
		$this->assertNull($post);
		
		/*
		 * Test Permission 2
		 */
		$post = get_post(self::$permission_ids[1]);
		$this->assertNull($post);
		
		/*
		 * Test Permission 3
		 */
		$post = get_post(self::$permission_ids[2]);
		$this->assertNull($post);
		
		/*
		 * Test Permission 4
		 */
		$post = get_post(self::$permission_ids[3]);
		$this->assertNull($post);
	}
}
