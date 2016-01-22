<?php

/**
 * Description of test_ldap_user
 *
 * @author KWM
 */
class test_ldap_user extends PHPUnit_Framework_TestCase {
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
		$this->asserFalse(self::$ldap->DNexists("cn=Test1,ou=users,dc=gruene-jugend,dc=de"));
		$this->asserFalse(self::$ldap->DNexists("cn=NRW,ou=users,dc=gruene-jugend,dc=de"));
		$this->asserFalse(self::$ldap->DNexists("cn=Testgruppe1,ou=users,dc=gruene-jugend,dc=de"));
		$this->asserFalse(self::$ldap->DNexists("cn=Test2,ou=users,dc=gruene-jugend,dc=de"));
	}
	
	/*
	 * Zusätzlicher Test: createLDAP
	 */
	public function test_aktivieren() {
		User_Control::aktivieren(self::$user_ids[0]);
		User_Control::aktivieren(self::$user_ids[1]);
		User_Control::aktivieren(self::$user_ids[2]);
		User_Control::aktivieren(self::$user_ids[3]);
		
		$this->asserTrue(self::$ldap->DNexists("cn=Test1,ou=users,dc=gruene-jugend,dc=de"));
		$this->asserTrue(self::$ldap->DNexists("cn=NRW,ou=users,dc=gruene-jugend,dc=de"));
		$this->asserTrue(self::$ldap->DNexists("cn=Testgruppe1,ou=users,dc=gruene-jugend,dc=de"));
		$this->asserTrue(self::$ldap->DNexists("cn=Test2,ou=users,dc=gruene-jugend,dc=de"));
		
		$this->assertTrue(get_user_meta(self::$user_ids[0], "io_user_aktiv",		true));
		$this->assertTrue(get_user_meta(self::$user_ids[1], "io_user_aktiv",		true));
		$this->assertTrue(get_user_meta(self::$user_ids[2], "io_user_aktiv",		true));
		$this->assertTrue(get_user_meta(self::$user_ids[3], "io_user_aktiv",		true));
		
		$this->assertTrue(self::$user[0]->aktiv);
		$this->assertTrue(self::$user[1]->aktiv);
		$this->assertTrue(self::$user[2]->aktiv);
		$this->assertTrue(self::$user[2]->aktiv);
	}
	
	public function test_add_permission() {
		User_Control::addPermission(self::$user_ids[0], self::$permission_ids[0]);
		User_Control::addPermission(self::$user_ids[0], self::$permission_ids[1]);
		User_Control::addPermission(self::$user_ids[0], self::$permission_ids[2]);
		User_Control::addPermission(self::$user_ids[1], self::$permission_ids[3]);
		
		foreach(self::$user[0]->permissions AS $permission) {
			$this->assertTrue($permission->name == self::$permission[0]->name || $permission->name == self::$permission[1]->name || $permission->name == self::$permission[2]->name);
		}
		$this->assertEquals(count(self::$user[0]->permissions), 3);
		
		$this->assertTrue(self::$user[1]->permissions[0]->name == self::$permission[3]->name);
	}
	
	public function test_del_permission() {
		User_Control::delPermission(self::$user_ids[0], self::$permission_ids[0]);
		User_Control::delPermission(self::$user_ids[1], self::$permission_ids[3]);
		
		foreach(self::$user[0]->permissions AS $permission) {
			$this->assertTrue($permission->name != self::$permission[0]->name && ($permission->name == self::$permission[1]->name || $permission->name == self::$permission[2]->name));
		}
		$this->assertEquals(count(self::$user[0]->permissions), 2);
		
		$this->assertEquals(self::$user[1]->permissions, array());
	}
	
	public function test_add_group() {
		User_Control::addToGroup(self::$user_ids[0], self::$group_ids[0]);
		User_Control::addToGroup(self::$user_ids[0], self::$group_ids[1]);
		User_Control::addToGroup(self::$user_ids[0], self::$group_ids[2]);
		User_Control::addToGroup(self::$user_ids[1], self::$group_ids[3]);
		
		foreach(self::$user[0]->groups AS $group) {
			$this->assertTrue($group->name == self::$group[0]->name || $group->name == self::$group[1]->name || $group->name == self::$group[2]->name);
		}
		$this->assertEquals(count(self::$user[0]->groups), 3);
		
		$this->assertTrue(self::$user[1]->groups[0]->name == self::$group[3]->name);
	}
	
	public function test_del_group() {
		User_Control::delPermission(self::$user_ids[0], self::$group_ids[0]);
		User_Control::delPermission(self::$user_ids[1], self::$group_ids[3]);
		
		foreach(self::$user[0]->groups AS $group) {
			$this->assertTrue($group->name != self::$group[0]->name && ($group->name == self::$group[1]->name || $group->name == self::$group[2]->name));
		}
		$this->assertEquals(count(self::$user[0]->groups), 2);
		
		$this->assertEquals(self::$user[1]->groups, array());
	}
	
	public function test_authentifizierung() {
		self::$ldap->setUserPassword(self::$user[0]->user_login, "Test123");
		self::$ldap->setUserPassword(self::$user[1]->user_login, "Test123");
		self::$ldap->setUserPassword(self::$user[2]->user_login, "Test123");
		self::$ldap->setUserPassword(self::$user[3]->user_login, "Test123");
		
		$this->sssertTrue(User_Control::authentifizierung(self::$user[0], self::$user[0]->user_login, "Test123")->user_login == self::$user[0]->user_login);
		$this->sssertTrue(User_Control::authentifizierung(self::$user[1], self::$user[1]->user_login, "Test123")->user_login == self::$user[1]->user_login);
		$this->sssertTrue(User_Control::authentifizierung(self::$user[2], self::$user[2]->user_login, "Test123")->user_login == self::$user[2]->user_login);
		$this->sssertTrue(User_Control::authentifizierung(self::$user[3], self::$user[3]->user_login, "Test123")->user_login == self::$user[3]->user_login);
	}
	
	public function test_delete() {
		User_Control::delete(self::$user_ids[0]);
		User_Control::delete(self::$user_ids[1]);
		User_Control::delete(self::$user_ids[2]);
		User_Control::delete(self::$user_ids[3]);
		
		$this->asserFalse(self::$ldap->DNexists("cn=Test1,ou=users,dc=gruene-jugend,dc=de"));
		$this->asserFalse(self::$ldap->DNexists("cn=NRW,ou=users,dc=gruene-jugend,dc=de"));
		$this->asserFalse(self::$ldap->DNexists("cn=Testgruppe1,ou=users,dc=gruene-jugend,dc=de"));
		$this->asserFalse(self::$ldap->DNexists("cn=Test2,ou=users,dc=gruene-jugend,dc=de"));
	}
	
	public static function tearDownAfterClass() {
		Group_Control::delete(self::$group_ids[0]);
		Group_Control::delete(self::$group_ids[1]);
		Group_Control::delete(self::$group_ids[2]);
		Group_Control::delete(self::$group_ids[3]);
		
		Permission_Control::delete(self::$permission_ids[0]);
		Permission_Control::delete(self::$permission_ids[1]);
		Permission_Control::delete(self::$permission_ids[2]);
		Permission_Control::delete(self::$permission_ids[3]);
	}
}
