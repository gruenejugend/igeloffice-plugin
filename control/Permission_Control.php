<?php

/**
 * Description of Permission_Control
 *
 * @author KWM
 */
class Permission_Control {
	const POST_TYPE = 'io_permission';
	
	public static function create($name, $oberkategorie = null, $unterkategorie = null) {
		$id = wp_insert_post(array(
			'post_title'	=> $name,
			'post_type'		=> self::POST_TYPE,
			'post_status'	=> 'publish'
		));
		
		if($oberkategorie) {
			update_post_meta($id, 'io_permission_ok', $oberkategorie);
		}
		
		if($unterkategorie) {
			update_post_meta($id, 'io_permission_uk', $unterkategorie);
		}
		
		$ldapConnector = ldapConnector::get();
		$ldapConnector->addPermission($name);
		
		return $id;
	}
	
	public static function update($id, $key, $value = null) {
		if($key == 'oberkategorie') {
			if($value) {
				update_post_meta($id, 'io_permission_ok', $value);
			} else {
				delete_post_meta($id, 'io_permission_ok');
			}
		} else if($key == 'unterkategorie') {
			if($value) {
				update_post_meta($id, 'io_permission_uk', $value);
			} else {
				delete_post_meta($id, 'io_permission_uk');
			}
		}
	}
	
	public static function delete($id) {
		$ldapConnector = ldapConnector::get();
		$ldapConnector->delPermission((new Permission($id))->name);
		
		wp_delete_post($id);
	}
}
