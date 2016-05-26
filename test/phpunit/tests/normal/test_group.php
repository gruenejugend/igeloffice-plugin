<?php

/**
 * Description of test_group
 *
 * @author KWM
 */
class test_group extends PHPUnit_Framework_TestCase {
	public static $group_ids = array();
	
	public function test_control_create() {
		self::$group_ids[0] = Group_Control::create("Test5", "TestOben1", "TestUnten1");
		self::$group_ids[1] = Group_Control::create("Test6", "TestOben1", "TestUnten2");
		self::$group_ids[2] = Group_Control::create("Test7", "TestOben2", "TestUnten3");
		self::$group_ids[3] = Group_Control::create("Test8", "TestOben3");
		
		/*
		 * Test Group 1
		 */
		$post = get_post(self::$group_ids[0]);
		
		$this->assertNotNull($post);
		$this->assertEquals($post->post_title,														"Test5");
		$this->assertEquals(get_post_meta(self::$group_ids[0], Group_Util::OBERKATEGORIE, true),	"TestOben1");
		$this->assertEquals(get_post_meta(self::$group_ids[0], Group_Util::UNTERKATEGORIE, true),	"TestUnten1");
		
		/*
		 * Test Group 2
		 */
		$post = get_post(self::$group_ids[1]);
		
		$this->assertNotNull($post);
		$this->assertEquals($post->post_title,														"Test6");
		$this->assertEquals(get_post_meta(self::$group_ids[1], Group_Util::OBERKATEGORIE, true),	"TestOben1");
		$this->assertEquals(get_post_meta(self::$group_ids[1], Group_Util::UNTERKATEGORIE, true),	"TestUnten2");
		
		/*
		 * Test Group 3
		 */
		$post = get_post(self::$group_ids[2]);
		
		$this->assertNotNull($post);
		$this->assertEquals($post->post_title,														"Test7");
		$this->assertEquals(get_post_meta(self::$group_ids[2], Group_Util::OBERKATEGORIE, true),	"TestOben2");
		$this->assertEquals(get_post_meta(self::$group_ids[2], Group_Util::UNTERKATEGORIE, true),	"TestUnten3");
		
		/*
		 * Test Group 4
		 */
		$post = get_post(self::$group_ids[3]);
		
		$this->assertNotNull($post);
		$this->assertEquals($post->post_title,														"Test8");
		$this->assertEquals(get_post_meta(self::$group_ids[3], Group_Util::OBERKATEGORIE, true),	"TestOben3");
		$this->assertEquals('', get_post_meta(self::$group_ids[3], Group_Util::UNTERKATEGORIE, true));
	}
	
	public function test_control_update() {
		Group_Control::update(self::$group_ids[0], "oberkategorie",									"TestOben2");
		Group_Control::update(self::$group_ids[0], "unterkategorie",								"TestUnten3");
		$this->assertEquals(get_post_meta(self::$group_ids[0], Group_Util::OBERKATEGORIE, true),	"TestOben2");
		$this->assertEquals(get_post_meta(self::$group_ids[0], Group_Util::UNTERKATEGORIE, true),	"TestUnten3");
	}
	
	public function test_model() {
		$group[0] = new Group(self::$group_ids[0]);
		$group[1] = new Group(self::$group_ids[1]);
		$group[2] = new Group(self::$group_ids[2]);
		$group[3] = new Group(self::$group_ids[3]);
		
		/*
		 * Test Group 1
		 */
		$this->assertEquals($group[0]->name,			"Test5");
		$this->assertEquals($group[0]->oberkategorie,	"TestOben2");
		$this->assertEquals($group[0]->unterkategorie,	"TestUnten3");
		
		/*
		 * Test Group 2
		 */
		$this->assertEquals($group[1]->name,			"Test6");
		$this->assertEquals($group[1]->oberkategorie,	"TestOben1");
		$this->assertEquals($group[1]->unterkategorie,	"TestUnten2");
		
		/*
		 * Test Group 3
		 */
		$this->assertEquals($group[2]->name,			"Test7");
		$this->assertEquals($group[2]->oberkategorie,	"TestOben2");
		$this->assertEquals($group[2]->unterkategorie,	"TestUnten3");
		
		/*
		 * Test Group 4
		 */
		$this->assertEquals($group[3]->name,			"Test8");
		$this->assertEquals($group[3]->oberkategorie,	"TestOben3");
		$this->assertEquals('', $group[3]->unterkategorie);
	}
	
	public function test_control_delete() {
		Group_Control::delete(self::$group_ids[0]);
		Group_Control::delete(self::$group_ids[1]);
		Group_Control::delete(self::$group_ids[2]);
		Group_Control::delete(self::$group_ids[3]);
		
		/*
		 * Test Group 1
		 */
		$post = get_post(self::$group_ids[0]);
		$this->assertNull($post);
		
		/*
		 * Test Group 2
		 */
		$post = get_post(self::$group_ids[1]);
		$this->assertNull($post);
		
		/*
		 * Test Group 3
		 */
		$post = get_post(self::$group_ids[2]);
		$this->assertNull($post);
		
		/*
		 * Test Group 4
		 */
		$post = get_post(self::$group_ids[3]);
		$this->assertNull($post);
	}
	
	public function test_get_values() {
		self::$group_ids[0] = Group_Control::create("AATest01", "TestOK1", "TestUK1");
		self::$group_ids[1] = Group_Control::create("AATest02", "TestOK1", "TestUK1");
		self::$group_ids[2] = Group_Control::create("AATest03", "TestOK1");
		self::$group_ids[3] = Group_Control::create("AATest04", "TestOK1");
		self::$group_ids[4] = Group_Control::create("AATest05", "TestOK2", "TestUK2");
		self::$group_ids[5] = Group_Control::create("AATest06", "TestOK2", "TestUK2");
		self::$group_ids[6] = Group_Control::create("AATest07", "TestOK2", "TestUK3");
		self::$group_ids[7] = Group_Control::create("AATest08", "TestOK2", "TestUK3");
		self::$group_ids[8] = Group_Control::create("AATest09", "TestOK3", "TestUK4");
		self::$group_ids[9] = Group_Control::create("AATest10", "TestOK3", "TestUK5");
		self::$group_ids[10] = Group_Control::create("AATest11", "TestOK3", "TestUK6");
		self::$group_ids[11] = Group_Control::create("AATest12", "TestOK3", "TestUK7");
		self::$group_ids[12] = Group_Control::create("AATest13", "TestOK4");
		self::$group_ids[13] = Group_Control::create("AATest14", "TestOK4");
		self::$group_ids[14] = Group_Control::create("AATest15");
		self::$group_ids[15] = Group_Control::create("AATest16");
		
		$values = array(
			//Oberkategorie Ebene
				//Unterkategorie Ebene
					//Gruppenebene
			'TestOK1' => array(
				'TestUK1' => array(
					self::$group_ids[0],
					self::$group_ids[1]
				),
				'Nicht Kategorisiert' => array(
					self::$group_ids[2],
					self::$group_ids[3]
				)
			),
			'TestOK2' => array(
				'TestUK2' => array(
					self::$group_ids[4],
					self::$group_ids[5]
				),
				'TestUK3' => array(
					self::$group_ids[6],
					self::$group_ids[7]
				)
			),
			'TestOK3' => array(
				'TestUK4' => array(
					self::$group_ids[8]
				),
				'TestUK5' => array(
					self::$group_ids[9]
				),
				'TestUK6' => array(
					self::$group_ids[10]
				),
				'TestUK7' => array(
					self::$group_ids[11]
				)
			),
			'TestOK4' => array(
				'Nicht Kategorisiert' => array(
					self::$group_ids[12],
					self::$group_ids[13]
				)
			),
			'Nicht Kategorisiert' => array(
				self::$group_ids[14],
				self::$group_ids[15]
			)
		);
		
		$this->assertEquals(Group_Control::getValues(), $values);
	}
	
	public function test_delete_get_values() {
		Group_Control::delete(self::$group_ids[0]);
		Group_Control::delete(self::$group_ids[1]);
		Group_Control::delete(self::$group_ids[2]);
		Group_Control::delete(self::$group_ids[3]);
		Group_Control::delete(self::$group_ids[4]);
		Group_Control::delete(self::$group_ids[5]);
		Group_Control::delete(self::$group_ids[6]);
		Group_Control::delete(self::$group_ids[7]);
		Group_Control::delete(self::$group_ids[8]);
		Group_Control::delete(self::$group_ids[9]);
		Group_Control::delete(self::$group_ids[10]);
		Group_Control::delete(self::$group_ids[11]);
		Group_Control::delete(self::$group_ids[12]);
		Group_Control::delete(self::$group_ids[13]);
		Group_Control::delete(self::$group_ids[14]);
		Group_Control::delete(self::$group_ids[15]);
	}
}
