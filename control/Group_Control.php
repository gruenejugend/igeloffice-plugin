<?php

/**
 * Description of Group_Control
 *
 * @author KWM
 */
class Group_Control {
	const POST_TYPE = 'io_group';
	
	public static function create($name, $oberkategorie = null, $unterkategorie = null) {
		$id = wp_insert_post(array(
			'post_title'		=> $name,
			'post_type'			=> self::POST_TYPE,
			'post_status'		=> 'publish'
		));
		
		if($oberkategorie) {
			update_post_meta($id, 'io_group_ok', $oberkategorie);
		}
		
		if($unterkategorie) {
			update_post_meta($id, 'io_group_uk', $unterkategorie);
		}
		
		$ldapConnector = ldapConnector::get();
		$ldapConnector->addGroup($name);
		
		return $id;
	}
	
	public static function update($id, $key, $value) {
		if($key == 'oberkategorie') {
			if($value) {
				update_post_meta($id, 'io_group_ok', $value);
			} else {
				delete_post_meta($id, 'io_group_ok');
			}
		} else if($key == 'unterkategorie') {
			if($value) {
				update_post_meta($id, 'io_group_uk', $value);
			} else {
				delete_post_meta($id, 'io_group_uk');
			}
		}
	}
	
	public static function delete($id) {
		$ldapConnector = ldapConnector::get();
		$ldapConnector->delGroup((new Group($id))->name);
		
		wp_delete_post($id);
	}
	
	public static function addOwner($id, $user_id) {
		$ldapConnector = ldapConnector::get();
		$ldapConnector->setGroupAttribute((new Group($id))->name, 'owner',  $ldapConnector->userDN((new User($user_id))->user_login));
	}
	
	public static function delOwner($id, $user_id) {
		$ldapConnector = ldapConnector::get();
		$ldapConnector->delGroupAttribute((new Group($id))->name, 'owner',  $ldapConnector->userDN((new User($user_id))->user_login));
	}
	
	public static function addGroup($id, $group_id) {
		$ldapConnector = ldapConnector::get();
		$ldapConnector->addGroupToGroup((new Group($id))->name, (new Group($group_id))->name);
	}
	
	public static function delGroup($id, $group_id) {
		$ldapConnector = ldapConnector::get();
		$ldapConnector->delGroupFromGroup((new Group($id))->name, (new Group($group_id))->name);
	}
	
	public static function addPermission($id, $permission_id) {
		$ldapConnector = ldapConnector::get();
		$ldapConnector->addGroupPermission((new Group($id))->name, (new Permission($permission_id))->name);
	}
	
	public static function delPermission($id, $permission_id) {
		$ldapConnector = ldapConnector::get();
		$ldapConnector->delGroupPermission((new Group($id))->name, (new Permission($permission_id))->name);
	}
}
