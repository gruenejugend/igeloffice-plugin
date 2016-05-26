<?php

/**
 * Description of Request_Factory
 *
 * @author KWM
 */
class Request_Factory {
	public static function getRequest($art, $id = null) {
		switch($art) {
			case 'Group':
			case Request_Group::art():
				return new Request_Group($id);
			case 'Permission':
			case Request_Permission::art():
				return new Request_Permission($id);
			case 'User':
			case Request_User::art():
				return new Request_User($id);
		}
	}
	
	public static function getValues() {
		return array(
			'Group'				=> Request_Group::art(),
			'Permission'		=> Request_Permission::art(),
			'User'				=> Request_User::art()
		);
	}
}
