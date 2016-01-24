<?php

/**
 * Description of test_ldap_user
 *
 * @author KWM
 */
class test_ldap_group extends PHPUnit_Framework_TestCase {
	public static $user_ids = array();
	public static $user = array();
	public static $group_ids = array();
	public static $group = array();
	public static $permission_ids = array();
	public static $permission = array();
	public static $ldap;

	public static function setUpBeforeClass() {
		self::$user_ids[0] = User_Control::createUser("Test1", "Tester1", "test1@test1.de");
		self::$user_ids[1] = User_Control::createLandesverband("NRW", "test2@test2.de");
		self::$user_ids[2] = User_Control::createBasisgruppe("Testgruppe1", "NRW", "test3@test3.de");
		self::$user_ids[3] = User_Control::createOrgauser("Test2", "test4@test4.de");
		
		self::$user[0] = new User(self::$user_ids[0]);
		self::$user[1] = new User(self::$user_ids[1]);
		self::$user[2] = new User(self::$user_ids[2]);
		self::$user[3] = new User(self::$user_ids[3]);
		
		self::$permission_ids[0] = Permission_Control::create("Test1", "TestOben1", "TestUnten1");
		self::$permission_ids[1] = Permission_Control::create("Test2", "TestOben1", "TestUnten2");
		self::$permission_ids[2] = Permission_Control::create("Test3", "TestOben2", "TestUnten3");
		self::$permission_ids[3] = Permission_Control::create("Test4", "TestOben3");
		
		self::$permission[0] = new Permission(self::$permission_ids[0]);
		self::$permission[1] = new Permission(self::$permission_ids[1]);
		self::$permission[2] = new Permission(self::$permission_ids[2]);
		self::$permission[3] = new Permission(self::$permission_ids[3]);
		
		self::$group_ids[0] = Group_Control::create("Test5", "TestOben1", "TestUnten1");
		self::$group_ids[1] = Group_Control::create("Test6", "TestOben1", "TestUnten2");
		self::$group_ids[2] = Group_Control::create("Test7", "TestOben2", "TestUnten3");
		self::$group_ids[3] = Group_Control::create("Test8", "TestOben3");
		
		self::$group[0] = new Group(self::$group_ids[0]);
		self::$group[1] = new Group(self::$group_ids[1]);
		self::$group[2] = new Group(self::$group_ids[2]);
		self::$group[3] = new Group(self::$group_ids[3]);
		
		self::$ldap = ldapConnector::get();
	}
	
	public function test_create() {
		$this->assertTrue(self::$ldap->DNexists("cn=Test5,ou=groups,dc=gruene-jugend,dc=de"));
		$this->assertTrue(self::$ldap->DNexists("cn=Test6,ou=groups,dc=gruene-jugend,dc=de"));
		$this->assertTrue(self::$ldap->DNexists("cn=Test7,ou=groups,dc=gruene-jugend,dc=de"));
		$this->assertTrue(self::$ldap->DNexists("cn=Test8,ou=groups,dc=gruene-jugend,dc=de"));
	}
	
	public function test_addOwner() {
		Group_Control::addOwner(self::$group_ids[0], self::$user_ids[0]);
		Group_Control::addOwner(self::$group_ids[0], self::$user_ids[1]);
		Group_Control::addOwner(self::$group_ids[1], self::$user_ids[2]);
		Group_Control::addOwner(self::$group_ids[2], self::$user_ids[3]);
		
		$owners = self::$group[0]->owner;
		foreach($owners AS $owner) {
			$this->assertTrue($owner->user_login == self::$user[0]->user_login || $owner->user_login == self::$user[1]->user_login);
		}
		$this->assertEquals(count(self::$group[0]->owner), 2);
		
		$this->assertTrue(self::$group[1]->owner[0]->user_login == self::$user[2]->user_login);
		$this->assertEquals(count(self::$group[1]->owner), 1);
		$this->assertTrue(self::$group[2]->owner[0]->user_login == self::$user[3]->user_login);
		$this->assertEquals(count(self::$group[2]->owner), 1);
	}
	
	public function test_delOwner() {
		Group_Control::delOwner(self::$group_ids[0], self::$user_ids[0]);
		Group_Control::delOwner(self::$group_ids[1], self::$user_ids[2]);
		Group_Control::delOwner(self::$group_ids[2], self::$user_ids[3]);
		
		$this->assertTrue(self::$group[0]->owner[0]->user_login != self::$user[0]->user_login && self::$group[0]->owner[0]->user_login == self::$user[1]->user_login);
		$this->assertEquals(count(self::$group[0]->owner), 1);
		$this->assertFalse(self::$group[1]->owner[0]->user_login == self::$user[2]->user_login);
		$this->assertEquals(count(self::$group[1]->owner), 0);
		$this->assertFalse(self::$group[2]->owner[0]->user_login == self::$user[3]->user_login);
		$this->assertEquals(count(self::$group[2]->owner), 0);
	}
	
	/*
	 * Bei addGroup muss darauf geachtet werden, dass eine Gruppe nicht bei sich selbst Mitglied wird
	 * Auch nicht über mehrere Gruppen hinweg. In diesem Beispiel darf 0 nicht mehr Mitglied bei 1, 2 oder 3 werden
	 */
	public function test_addToGroup() {
		Group_Control::addGroup(self::$group_ids[0], self::$group_ids[1]);
		Group_Control::addGroup(self::$group_ids[0], self::$group_ids[2]);
		Group_Control::addGroup(self::$group_ids[0], self::$group_ids[3]);
		
		$groups = self::$group[0]->groups;
		foreach($groups AS $group) {
			$this->assertTrue($group->name == self::$group[1]->name || $group->name == self::$group[2]->name || $group->name == self::$group[3]->name);
		}
		$this->assertEquals(count($groups), 3);
	}
	
	public function test_delToGroup() {
		Group_Control::delGroup(self::$group_ids[0], self::$group_ids[2]);
		Group_Control::delGroup(self::$group_ids[0], self::$group_ids[3]);
		
		$this->assertTrue(self::$group[0]->groups[0]->name == self::$group[1]->name && self::$group[0]->groups[0]->name != self::$group[2]->name && self::$group[0]->groups[0]->name != self::$group[3]->name);
		$this->assertEquals(count(self::$group[0]->groups), 1);
	}
	
	public function test_addPermission() {
		Group_Control::addPermission(self::$group_ids[0], self::$permission_ids[0]);
		Group_Control::addPermission(self::$group_ids[0], self::$permission_ids[1]);
		Group_Control::addPermission(self::$group_ids[0], self::$permission_ids[2]);
		Group_Control::addPermission(self::$group_ids[1], self::$permission_ids[3]);
		
		$permissions = self::$group[0]->permissions;
		foreach($permissions AS $permission) {
			$this->assertTrue($permission->name == self::$permission[0]->name || $permission->name == self::$permission[1]->name || $permission->name == self::$permission[2]->name);
		}
		$this->assertEquals(count($permissions), 3);
		$this->assertTrue(self::$group[1]->permissions[0]->name == self::$permission[3]->name);
		$this->assertEquals(count(self::$group[1]->permissions), 1);
	}
	
	public function test_delPermission() {
		Group_Control::delPermission(self::$group_ids[0], self::$permission_ids[0]);
		Group_Control::delPermission(self::$group_ids[1], self::$permission_ids[3]);
		
		foreach(self::$group[0]->permissions AS $permission) {
			$this->assertTrue($permission->name != self::$permission[0]->name && ($permission->name == self::$permission[1]->name || $permission->name == self::$permission[2]->name));
		}
		$this->assertEquals(count(self::$group[0]->permissions), 2);
		$this->assertTrue(self::$group[1]->permissions == array());
	}
	
	public function test_delete() {
		Group_Control::delete(self::$group_ids[0]);
		Group_Control::delete(self::$group_ids[1]);
		Group_Control::delete(self::$group_ids[2]);
		Group_Control::delete(self::$group_ids[3]);
		
		$this->assertFalse(self::$ldap->DNexists("cn=Test5,ou=groups,dc=gruene-jugend,dc=de"));
		$this->assertFalse(self::$ldap->DNexists("cn=Test6,ou=groups,dc=gruene-jugend,dc=de"));
		$this->assertFalse(self::$ldap->DNexists("cn=Test7,ou=groups,dc=gruene-jugend,dc=de"));
		$this->assertFalse(self::$ldap->DNexists("cn=Test8,ou=groups,dc=gruene-jugend,dc=de"));
	}
	
	public static function tearDownAfterClass() {
		User_Control::delete(self::$user_ids[0]);
		User_Control::delete(self::$user_ids[1]);
		User_Control::delete(self::$user_ids[2]);
		User_Control::delete(self::$user_ids[3]);
		
		Permission_Control::delete(self::$permission_ids[0]);
		Permission_Control::delete(self::$permission_ids[1]);
		Permission_Control::delete(self::$permission_ids[2]);
		Permission_Control::delete(self::$permission_ids[3]);
	}
}
