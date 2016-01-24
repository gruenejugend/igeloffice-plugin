<?php

	require_once 'E:\xampp\htdocs\dev\wp-content\plugins\igeloffice/control/ldap.php';
	require_once 'E:\xampp\htdocs\dev\wp-content\plugins\igeloffice/control/ldapConnector.php';

/**
 * Description of test_all
 *
 * @author KWM
 */
class test_all extends PHPUnit_Framework_TestSuite {
	public static function suite() {
		$suite = new test_all();
		
		$ldapConn = ldapConnector::get(false);
		if(!$ldapConn->bind("LDAPTest", "LDAPTest")) {
			echo 'Fehler';
		} else {
//			$suite->addTestFile('test/phpunit/tests/normal/test_group.php');
//			$suite->addTestFile('test/phpunit/tests/normal/test_permission.php');
//			$suite->addTestFile('test/phpunit/tests/normal/test_user.php');
//		
//			$suite->addTestFile('test/phpunit/tests/ldap/test_ldap_group.php');
//			$suite->addTestFile('test/phpunit/tests/ldap/test_ldap_permission.php');
//			$suite->addTestFile('test/phpunit/tests/ldap/test_ldap_proxy.php');
			$suite->addTestFile('test/phpunit/tests/ldap/test_ldap_user.php');
		}
		
		return $suite;
	}
}
