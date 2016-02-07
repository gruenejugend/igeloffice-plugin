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
	
	public function test_get_values() {
		self::$permission_ids[0] = Permission_Control::create("AATest01", "TestOK1", "TestUK1");
		self::$permission_ids[1] = Permission_Control::create("AATest02", "TestOK1", "TestUK1");
		self::$permission_ids[2] = Permission_Control::create("AATest03", "TestOK1");
		self::$permission_ids[3] = Permission_Control::create("AATest04", "TestOK1");
		self::$permission_ids[4] = Permission_Control::create("AATest05", "TestOK2", "TestUK2");
		self::$permission_ids[5] = Permission_Control::create("AATest06", "TestOK2", "TestUK2");
		self::$permission_ids[6] = Permission_Control::create("AATest07", "TestOK2", "TestUK3");
		self::$permission_ids[7] = Permission_Control::create("AATest08", "TestOK2", "TestUK3");
		self::$permission_ids[8] = Permission_Control::create("AATest09", "TestOK3", "TestUK4");
		self::$permission_ids[9] = Permission_Control::create("AATest10", "TestOK3", "TestUK5");
		self::$permission_ids[10] = Permission_Control::create("AATest11", "TestOK3", "TestUK6");
		self::$permission_ids[11] = Permission_Control::create("AATest12", "TestOK3", "TestUK7");
		self::$permission_ids[12] = Permission_Control::create("AATest13", "TestOK4");
		self::$permission_ids[13] = Permission_Control::create("AATest14", "TestOK4");
		self::$permission_ids[14] = Permission_Control::create("AATest15");
		self::$permission_ids[15] = Permission_Control::create("AATest16");
		
		$values = array(
			//Oberkategorie Ebene
				//Unterkategorie Ebene
					//Gruppenebene
			'TestOK1' => array(
				'TestUK1' => array(
					self::$permission_ids[0],
					self::$permission_ids[1]
				),
				'Nicht Kategorisiert' => array(
					self::$permission_ids[2],
					self::$permission_ids[3]
				)
			),
			'TestOK2' => array(
				'TestUK2' => array(
					self::$permission_ids[4],
					self::$permission_ids[5]
				),
				'TestUK3' => array(
					self::$permission_ids[6],
					self::$permission_ids[7]
				)
			),
			'TestOK3' => array(
				'TestUK4' => array(
					self::$permission_ids[8]
				),
				'TestUK5' => array(
					self::$permission_ids[9]
				),
				'TestUK6' => array(
					self::$permission_ids[10]
				),
				'TestUK7' => array(
					self::$permission_ids[11]
				)
			),
			'TestOK4' => array(
				'Nicht Kategorisiert' => array(
					self::$permission_ids[12],
					self::$permission_ids[13]
				)
			),
			'Nicht Kategorisiert' => array(
				self::$permission_ids[14],
				self::$permission_ids[15]
			)
		);
		
		$this->assertEquals(Permission_Control::getValues(), $values);
	}
	
	public function test_delete_get_values() {
		Permission_Control::delete(self::$permission_ids[0]);
		Permission_Control::delete(self::$permission_ids[1]);
		Permission_Control::delete(self::$permission_ids[2]);
		Permission_Control::delete(self::$permission_ids[3]);
		Permission_Control::delete(self::$permission_ids[4]);
		Permission_Control::delete(self::$permission_ids[5]);
		Permission_Control::delete(self::$permission_ids[6]);
		Permission_Control::delete(self::$permission_ids[7]);
		Permission_Control::delete(self::$permission_ids[8]);
		Permission_Control::delete(self::$permission_ids[9]);
		Permission_Control::delete(self::$permission_ids[10]);
		Permission_Control::delete(self::$permission_ids[11]);
		Permission_Control::delete(self::$permission_ids[12]);
		Permission_Control::delete(self::$permission_ids[13]);
		Permission_Control::delete(self::$permission_ids[14]);
		Permission_Control::delete(self::$permission_ids[15]);
	}
}
