<?php

/**
 * Description of test_ldap_user
 *
 * @author KWM
 */
class test_ldap_permission extends PHPUnit_Framework_TestCase {
	public static $permission_ids = array();
	public static $group_ids = array();
	public static $user_ids = array();
	public static $ldap;

	public static function setUpBeforeClass() {
		self::$user_ids[0] = User_Control::createUser("Test1", "Tester1", "test1@test1.de");
		self::$user_ids[1] = User_Control::createLandesverband("NRW", "test2@test2.de");
		self::$user_ids[2] = User_Control::createBasisgruppe("Testgruppe1", "NRW", "test3@test3.de");
		self::$user_ids[3] = User_Control::createOrgauser("Test2", "test4@test4.de");
		
		self::$group_ids[0] = Group_Control::create("Test5", "TestOben1", "TestUnten1");
		self::$group_ids[1] = Group_Control::create("Test6", "TestOben1", "TestUnten2");
		self::$group_ids[2] = Group_Control::create("Test7", "TestOben2", "TestUnten3");
		self::$group_ids[3] = Group_Control::create("Test8", "TestOben3");
		
		self::$permission_ids[0] = Permission_Control::create("Test1", "TestOben1", "TestUnten1");
		self::$permission_ids[1] = Permission_Control::create("Test2", "TestOben1", "TestUnten2");
		self::$permission_ids[2] = Permission_Control::create("Test3", "TestOben2", "TestUnten3");
		self::$permission_ids[3] = Permission_Control::create("Test4", "TestOben3");
		
		self::$ldap = ldapConnector::get();
	}
	
	public function test_create() {
		$this->assertTrue(self::$ldap->DNexists("cn=Test1,ou=permissions,dc=gruene-jugend,dc=de"));
		$this->assertTrue(self::$ldap->DNexists("cn=Test2,ou=permissions,dc=gruene-jugend,dc=de"));
		$this->assertTrue(self::$ldap->DNexists("cn=Test3,ou=permissions,dc=gruene-jugend,dc=de"));
		$this->assertTrue(self::$ldap->DNexists("cn=Test4,ou=permissions,dc=gruene-jugend,dc=de"));
	}
	
	public function test_permission() {
		User_Control::addPermission(self::$user_ids[0], self::$permission_ids[0]);
		User_Control::addPermission(self::$user_ids[1], self::$permission_ids[0]);
		User_Control::addPermission(self::$user_ids[2], self::$permission_ids[0]);
		User_Control::addPermission(self::$user_ids[3], self::$permission_ids[0]);
		
		Group_Control::addPermission(self::$group_ids[0], self::$permission_ids[1]);
		Group_Control::addPermission(self::$group_ids[1], self::$permission_ids[1]);
		Group_Control::addPermission(self::$group_ids[2], self::$permission_ids[1]);
		Group_Control::addPermission(self::$group_ids[3], self::$permission_ids[1]);
		
		User_Control::addPermission(self::$user_ids[0], self::$permission_ids[2]);
		User_Control::addPermission(self::$user_ids[1], self::$permission_ids[2]);
		User_Control::addPermission(self::$user_ids[2], self::$permission_ids[2]);
		User_Control::addPermission(self::$user_ids[3], self::$permission_ids[2]);
		
		Group_Control::addPermission(self::$group_ids[0], self::$permission_ids[2]);
		Group_Control::addPermission(self::$group_ids[1], self::$permission_ids[2]);
		Group_Control::addPermission(self::$group_ids[2], self::$permission_ids[2]);
		Group_Control::addPermission(self::$group_ids[3], self::$permission_ids[2]);
		
		$permission[0] = new Permission(self::$permission_ids[0]);
		$permission[1] = new Permission(self::$permission_ids[1]);
		$permission[2] = new Permission(self::$permission_ids[2]);
		
		$this->assertEquals(count($permission[0]->users), 4);
		$this->assertEquals(count($permission[0]->groups), 0);
		$this->assertEquals(count($permission[1]->users), 0);
		$this->assertEquals(count($permission[1]->groups), 4);
		$this->assertEquals(count($permission[2]->users), 4);
		$this->assertEquals(count($permission[2]->groups), 4);
		
		$users = $permission[0]->users;
		foreach($users AS $user) {
			$this->assertTrue($user->ID == self::$user_ids[0] ||
					$user->ID == self::$user_ids[1] ||
					$user->ID == self::$user_ids[2] ||
					$user->ID == self::$user_ids[3]
			);
		}
		
		$groups = $permission[1]->groups;
		foreach($groups AS $group) {
			$this->assertTrue($group->id == self::$group_ids[0] ||
					$group->id == self::$group_ids[1] ||
					$group->id == self::$group_ids[2] ||
					$group->id == self::$group_ids[3]
			);
		}
		
		$groups = $permission[2]->groups;
		foreach($groups AS $group) {
			$this->assertTrue($group->id == self::$group_ids[0] ||
					$group->id == self::$group_ids[1] ||
					$group->id == self::$group_ids[2] ||
					$group->id == self::$group_ids[3]
			);
		}
		
		$users = $permission[2]->users;
		foreach($users AS $user) {
			$this->assertTrue($user->ID == self::$user_ids[0] ||
					$user->ID == self::$user_ids[1] ||
					$user->ID == self::$user_ids[2] ||
					$user->ID == self::$user_ids[3]
			);
		}
	}
	
	public function test_delete() {
		Permission_Control::delete(self::$permission_ids[0]);
		Permission_Control::delete(self::$permission_ids[1]);
		Permission_Control::delete(self::$permission_ids[2]);
		Permission_Control::delete(self::$permission_ids[3]);
		
		$this->assertFalse(self::$ldap->DNexists("cn=Test1,ou=permissions,dc=gruene-jugend,dc=de"));
		$this->assertFalse(self::$ldap->DNexists("cn=Test2,ou=permissions,dc=gruene-jugend,dc=de"));
		$this->assertFalse(self::$ldap->DNexists("cn=Test3,ou=permissions,dc=gruene-jugend,dc=de"));
		$this->assertFalse(self::$ldap->DNexists("cn=Test4,ou=permissions,dc=gruene-jugend,dc=de"));
	}
	
	public static function tearDownAfterClass() {
		Group_Control::delete(self::$group_ids[0]);
		Group_Control::delete(self::$group_ids[1]);
		Group_Control::delete(self::$group_ids[2]);
		Group_Control::delete(self::$group_ids[3]);
		User_Control::delete(self::$user_ids[0]);
		User_Control::delete(self::$user_ids[1]);
		User_Control::delete(self::$user_ids[2]);
		User_Control::delete(self::$user_ids[3]);
	}
}
