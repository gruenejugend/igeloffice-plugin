<?php

/**
 * Description of Request_Factory
 *
 * @author deb139e
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
}
