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
		
		self::createMeta($id, $name, $oberkategorie, $unterkategorie);
		
		return $id;
	}
	
	public static function createMeta($id, $name, $oberkategorie = null, $unterkategorie = null) {
		if($oberkategorie) {
			update_post_meta($id, 'io_permission_ok', $oberkategorie);
		}
		
		if($unterkategorie) {
			update_post_meta($id, 'io_permission_uk', $unterkategorie);
		}
		
		$ldapConnector = ldapConnector::get();
		$ldapConnector->addPermission($name);
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
	
	public static function getValues() {
		global $wpdb;
		
		$results = $wpdb->get_results("SELECT pm1.post_id AS 'id', pm1.meta_value AS 'mv1', pm2.meta_value AS 'mv2'
FROM $wpdb->postmeta pm1
INNER JOIN $wpdb->postmeta pm2
ON pm1.post_id = pm2.post_id
INNER JOIN $wpdb->posts p
ON p.ID = pm1.post_id
WHERE pm1.meta_key = 'io_permission_ok'
AND pm2.meta_key = 'io_permission_uk'
AND p.post_status = 'publish'
AND p.post_type = '" . self::POST_TYPE. "'");
		
		$ids = array();
		$values = array();
		foreach($results AS $result) {
			$values[$result->mv1][$result->mv2][] = $result->id;
			$ids[] = $result->id;
		}
		
		unset($results);
		
		$start = "";
		if(count($ids) > 0) {
			$start = "AND pm1.post_id <> ";
		}
		
		$results = $wpdb->get_results("SELECT pm1.post_id AS 'id', pm1.meta_value AS 'mv1'
FROM $wpdb->postmeta pm1
INNER JOIN $wpdb->posts p
ON p.ID = pm1.post_id
WHERE pm1.meta_key = 'io_permission_ok'
AND p.post_status = 'publish'
AND p.post_type = '" . self::POST_TYPE . "'
" . $start . implode(" AND pm1.post_id <> ", $ids) . "
");
		
		foreach($results AS $result) {
			$values[$result->mv1]['Nicht Kategorisiert'][] = $result->id;
			$ids[] = $result->id;
		}
		
		unset($results);
		
		$start = "";
		if(count($ids) > 0) {
			$start = "AND ID <> ";
		}
		
		$results = $wpdb->get_results("SELECT ID
FROM $wpdb->posts
WHERE post_status = 'publish'
AND post_type = '" . self::POST_TYPE . "'
" . $start . implode(" AND ID <> ", $ids));
	
		foreach($results AS $result) {
			$values['Nicht Kategorisiert'][] = $result->ID;
		}
		
		unset($results);
		
		return $values;
	}
}
