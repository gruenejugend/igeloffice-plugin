<?php

/**
 * Description of Permission
 *
 * @author KWM
 */
class Permission {
	private $id;
	private $name;
	private $oberkategorie;
	private $unterkategorie;
	private $users;
	private $groups;
	
	public function __construct($id) {
		$this->id = $id;
		
		$post = get_post($id);
		$this->name = $post->post_title;
	}
	
	public function __get($name) {
		if($name == 'id') {
			return $this->id;
		} else if($name == 'name') {
			return $this->name;
		} else if($name == 'oberkategorie') {
			return get_post_meta($this->id, 'io_permission_ok', true);
		} else if($name == 'unterkategorie') {
			return get_post_meta($this->id, 'io_permission_uk', true);
		} else {
			$ldapConnector = ldapConnector::get();
			
			$berechtigte = $ldapConnector->getPermissionAttribute($this->name, "member");
			foreach($berechtigte AS $berechtigter) {
				$explode = explode(",", substr($berechtigter, 3));
				$ou = substr($explode[1], 3);
				if($ou == 'users') {
					array_push($this->users, new User(get_user_by("login", $explode[0])->ID));
				} else if($ou == 'groups') {
					array_push($this->groups, new Group(get_page_by_title($explode[0], OBJECT, 'io_group')->ID));
				}
			}
			
			if($name == 'users') {
				return $this->users;
			} else if ($name == 'groups') {
				return $this->groups;
			}
		}
	}
}
