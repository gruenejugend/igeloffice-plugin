<?php

/**
 * Description of test_group
 *
 * @author KWM
 */
class test_group extends PHPUnit_Framework_TestCase {
	public static $group_ids = array();

	public static function setUp() {
		
	}
	
	public function test_control_create() {
		self::$group_ids[0] = Group_Control::create("Test5", "TestOben1", "TestUnten1");
		self::$group_ids[1] = Group_Control::create("Test6", "TestOben1", "TestUnten2");
		self::$group_ids[2] = Group_Control::create("Test7", "TestOben2", "TestUnten3");
		self::$group_ids[3] = Group_Control::create("Test8", "TestOben3");
		
		/*
		 * Test Group 1
		 */
		$post = get_post(array(
			'post_type'			=> 'io_group',
			'ID'				=> self::$group_ids[0]
		));
		
		$this->assertNotNull($post);
		$this->assertEquals($post->post_title,											"Test5");
		$this->assertEquals(get_post_meta(self::$group_ids[0], 'io_group_ok', true),	"TestOben1");
		$this->assertEquals(get_post_meta(self::$group_ids[0], 'io_group_uk', true),	"TestUnten1");
		
		/*
		 * Test Group 2
		 */
		$post = get_post(array(
			'post_type'			=> 'io_group',
			'ID'				=> self::$group_ids[1]
		));
		
		$this->assertNotNull($post);
		$this->assertEquals($post->post_title,											"Test6");
		$this->assertEquals(get_post_meta(self::$group_ids[1], 'io_group_ok', true),	"TestOben1");
		$this->assertEquals(get_post_meta(self::$group_ids[1], 'io_group_uk', true),	"TestUnten2");
		
		/*
		 * Test Group 3
		 */
		$post = get_post(array(
			'post_type'			=> 'io_group',
			'ID'				=> self::$group_ids[2]
		));
		
		$this->assertNotNull($post);
		$this->assertEquals($post->post_title,											"Test7");
		$this->assertEquals(get_post_meta(self::$group_ids[2], 'io_group_ok', true),	"TestOben2");
		$this->assertEquals(get_post_meta(self::$group_ids[2], 'io_group_uk', true),	"TestUnten3");
		
		/*
		 * Test Group 4
		 */
		$post = get_post(array(
			'post_type'			=> 'io_group',
			'ID'				=> self::$group_ids[3]
		));
		
		$this->assertNotNull($post);
		$this->assertEquals($post->post_title,											"Test8");
		$this->assertEquals(get_post_meta(self::$group_ids[3], 'io_group_ok', true),	"TestOben3");
		$this->assertNull(get_post_meta(self::$group_ids[3], 'io_group_uk', true));
	}
	
	public function test_control_update() {
		Group_Control::update(self::$group_ids[0], "oberkategorie",		"TestOben2");
		Group_Control::update(self::$group_ids[0], "unterkategorie",	"TestUnten3");
		$this->assertEquals(get_post_meta(self::$group_ids[0], 'io_group_ok', true),	"TestOben2");
		$this->assertEquals(get_post_meta(self::$group_ids[0], 'io_group_uk', true),	"TestUnten3");
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
		$this->assertEquals($group[0]->name,			"Test6");
		$this->assertEquals($group[0]->oberkategorie,	"TestOben1");
		$this->assertEquals($group[0]->unterkategorie,	"TestUnten2");
		
		/*
		 * Test Group 3
		 */
		$this->assertEquals($group[0]->name,			"Test7");
		$this->assertEquals($group[0]->oberkategorie,	"TestOben2");
		$this->assertEquals($group[0]->unterkategorie,	"TestUnten3");
		
		/*
		 * Test Group 4
		 */
		$this->assertEquals($group[0]->name,			"Test8");
		$this->assertEquals($group[0]->oberkategorie,	"TestOben3");
		$this->assertNull($group[0]->unterkategorie);
	}
	
	public function test_control_delete() {
		Group_Control::delete(self::$group_ids[0]);
		Group_Control::delete(self::$group_ids[1]);
		Group_Control::delete(self::$group_ids[2]);
		Group_Control::delete(self::$group_ids[3]);
		
		/*
		 * Test Group 1
		 */
		$post = get_post(array(
			'post_type'			=> 'io_group',
			'ID'				=> self::$group_ids[0]
		));
		$this->assertNull($post);
		
		/*
		 * Test Group 2
		 */
		$post = get_post(array(
			'post_type'			=> 'io_group',
			'ID'				=> self::$group_ids[1]
		));
		$this->assertNull($post);
		
		/*
		 * Test Group 3
		 */
		$post = get_post(array(
			'post_type'			=> 'io_group',
			'ID'				=> self::$group_ids[2]
		));
		$this->assertNull($post);
		
		/*
		 * Test Group 4
		 */
		$post = get_post(array(
			'post_type'			=> 'io_group',
			'ID'				=> self::$group_ids[3]
		));
		$this->assertNull($post);
	}
}
