<?php

/**
 * Description of Group_Control
 *
 * @author KWM
 */
class Group_Control {
	const LDAP_ESCAPE_IGNORE = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPRSTUVWXYZ ";
	
	public static function create($name, $oberkategorie = null, $unterkategorie = null) {
		$id = wp_insert_post(array(
			'post_title'		=> $name,
			'post_type'			=> Group_Util::POST_TYPE,
			'post_status'		=> 'publish'
		));
		
		self::createMeta($id, $name, $oberkategorie, $unterkategorie);
		
		return $id;
	}
	
	public static function createMeta($id, $name, $oberkategorie = null, $unterkategorie = null) {
		if($oberkategorie) {
			update_post_meta($id, Group_Util::OBERKATEGORIE, $oberkategorie);
		}
		
		if($unterkategorie) {
			update_post_meta($id, Group_Util::UNTERKATEGORIE, $unterkategorie);
		}
		
		$ldapConnector = ldapConnector::get();
		$ldapConnector->addGroup(ldap_escape($name, self::LDAP_ESCAPE_IGNORE));
	}
	
	public static function update($id, $key, $value) {
		if($key == 'oberkategorie') {
			if($value) {
				update_post_meta($id, Group_Util::OBERKATEGORIE, $value);
			} else {
				delete_post_meta($id, Group_Util::OBERKATEGORIE);
			}
		} else if($key == 'unterkategorie') {
			if($value) {
				update_post_meta($id, Group_Util::UNTERKATEGORIE, $value);
			} else {
				delete_post_meta($id, Group_Util::UNTERKATEGORIE);
			}
		}
	}
	
	public static function delete($id) {
		$ldapConnector = ldapConnector::get();
		$ldapConnector->delGroup((new Group($id))->ldapName);
		
		wp_delete_post($id);
	}
	
	public static function addOwner($id, $user_id) {
		$ldapConnector = ldapConnector::get();
		$ldapConnector->setGroupAttribute((new Group($id))->ldapName, 'owner', $ldapConnector->userDN((new User($user_id))->user_login));
	}
	
	public static function delOwner($id, $user_id) {
		$ldapConnector = ldapConnector::get();
		$ldapConnector->delGroupAttribute((new Group($id))->ldapName, 'owner', $ldapConnector->userDN((new User($user_id))->user_login));
	}
	
	public static function addGroup($id, $group_id) {
		$ldapConnector = ldapConnector::get();
		$ldapConnector->addGroupToGroup((new Group($group_id))->ldapName, (new Group($id))->ldapName);
	}
	
	public static function delGroup($id, $group_id) {
		$ldapConnector = ldapConnector::get();
		$ldapConnector->delGroupFromGroup((new Group($group_id))->ldapName, (new Group($id))->ldapName);
	}
	
	public static function addPermission($id, $permission_id) {
		$ldapConnector = ldapConnector::get();
		$ldapConnector->addGroupPermission((new Group($id))->ldapName, (new Permission($permission_id))->name);
	}
	
	public static function delPermission($id, $permission_id) {
		$ldapConnector = ldapConnector::get();
		$ldapConnector->delGroupPermission((new Group($id))->ldapName, (new Permission($permission_id))->name);
	}
	
	public static function standardZuweisung($user_id) {
		$user = new User($user_id);
		
		$land = null;
		$art = $user->art;
		if($art == User_Util::USER_ART_BASISGRUPPE) {
			$art = "Basisgruppe";
			$land = $user->landesverband;
		} else if($art == User_Util::USER_ART_LANDESVERBAND) {
			$art = "Landesverbände";
			$land = strtolower($user->user_login);
		}
		
		$args = array(
			'post_type'				=> Group_Util::POST_TYPE,
			'meta_query'			=> array(
				array(
					'key'					=> "io_group_standard",
					'value'					=> $art,
					'compare'				=> "LIKE"
				)
			)
		);
		if($land != null) {
			$args['meta_query'][] = array(
					'key'					=> "io_group_standard",
					'value'					=> $land,
					'compare'				=> "LIKE"
				);
			$args['meta_query']['relation'] = "AND";
		}
		
		$posts = get_posts($args);
		
		foreach($posts AS $post) {
			$group = new Group($post->ID);
			$standards = $group->standard;
			if( (($art == User_Util::USER_ART_USER || $art == User_Util::USER_ART_ORGANISATORISCH) && $standards[$art]) || 
				(($user->art == User_Util::USER_ART_BASISGRUPPE || $user->art == User_Util::USER_ART_LANDESVERBAND) && $standards[$art][$land])) {
				LDAP_Proxy::addUsersToGroup($user->user_login, $group->name);
			}
		}
	}
	
	public static function getValues($art_sensitiv = false) {
		global $wpdb;

		if($art_sensitiv && current_user_can('administrator')) {
			$art_sensitiv = false;
		}

		if($art_sensitiv) {
			$user = new User(get_current_user_id());
			$user_art = $user->art;
			$land = null;
			if ($user_art == User_Util::USER_ART_BASISGRUPPE) {
				$land = $user->landesverband;
			} else if ($user_art == User_Util::USER_ART_LANDESVERBAND) {
				$land = strtolower($user->user_login);
			}
		}

		$results = $wpdb->get_results("SELECT pm1.post_id AS 'id', pm1.meta_value AS 'mv1', pm2.meta_value AS 'mv2'
FROM $wpdb->postmeta pm1
INNER JOIN $wpdb->postmeta pm2
ON pm1.post_id = pm2.post_id
INNER JOIN $wpdb->posts p
ON p.ID = pm1.post_id
WHERE pm1.meta_key = '" . Group_Util::OBERKATEGORIE . "'
AND pm2.meta_key = '" . Group_Util::UNTERKATEGORIE . "'
AND p.post_status = 'publish'
AND p.post_type = '" . Group_Util::POST_TYPE. "'");
		
		$ids = array();
		$values = array();
		foreach($results AS $result) {
			if ($art_sensitiv && self::sensitivCheck($user, $user_art, $land, $result->id)) {
				continue;
			}
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
WHERE pm1.meta_key = '" . Group_Util::OBERKATEGORIE . "'
AND p.post_status = 'publish'
AND p.post_type = '" . Group_Util::POST_TYPE . "'
" . $start . implode(" AND pm1.post_id <> ", $ids) . "
");
		
		foreach($results AS $result) {
			if ($art_sensitiv && self::sensitivCheck($user, $user_art, $land, $result->id)) {
				continue;
			}
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
AND post_type = '" . Group_Util::POST_TYPE . "'
" . $start . implode(" AND ID <> ", $ids));
	
		foreach($results AS $result) {
			if ($art_sensitiv && self::sensitivCheck($user, $user_art, $land, $result->ID)) {
				continue;
			}
			$values['Nicht Kategorisiert'][] = $result->ID;
		}
		
		unset($results);
		
		return $values;
	}

	private static function sensitivCheck($user, $user_art, $land, $result) {
		$group = new Group($result);
		$gruppen_mitgliedschaften = $user->groups;
		$pruef = false;

		foreach($gruppen_mitgliedschaften AS $gruppen_mitgliedschaft) {
			if ($result == $gruppen_mitgliedschaft->ID) {
				$pruef = true;
				break;
			}
		}

		if($pruef) {
			return false;
		} else {
			if($user_art == User_Util::USER_ART_USER && isset($group->sichtbarkeit[User_Util::USER_ART_USER])) {
				return true;
			} else if($user_art == User_Util::USER_ART_ORGANISATORISCH && isset($group->sichtbarkeit[User_Util::USER_ART_ORGANISATORISCH])) {
				return true;
			} else if($user_art == User_Util::USER_ART_LANDESVERBAND && isset($group->sichtbarkeit["Landesverbände"][$land])) {
				return true;
			} else if($user_art == User_Util::USER_ART_BASISGRUPPE && isset($group->sichtbarkeit["Basisgruppen"][$land])) {
				return true;
			}
			return false;
		}
	}
}
