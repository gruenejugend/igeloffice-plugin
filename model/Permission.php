<?php

/**
 * Model-Klasse zur Objekt-Erstellung von Berechtigungen
 *
 * Diese Klasse arbeitet mit magischen Methoden. Entsprechend verfuegbare Variablen koennen der Klasse __get entnommen
 * werden. Entsprechende Informationen werden in Echtzeit von WordPress abgerufen.
 *
 * @author KWM
 */
class Permission {
	private $id;
	private $name;
	private $oberkategorie;
	private $unterkategorie;
	private $users = array();
	private $groups = array();
	
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
			return get_post_meta($this->id, Permission_Util::OBERKATEGORIE, true);
		} else if($name == 'unterkategorie') {
			return get_post_meta($this->id, Permission_Util::UNTERKATEGORIE, true);
		} else if ($name == 'remember') {
			return unserialize(get_post_meta($this->id, "io_permission_remember", true));
		} else {
			$ldapConnector = ldapConnector::get();
			
			$berechtigte = $ldapConnector->getPermissionAttribute($this->name, "member");
			$this->users = array();
			$this->groups = array();
			foreach($berechtigte AS $berechtigter) {
				$explode = explode(",", substr($berechtigter, 3));
				$ou = substr($explode[1], 3);
				if($ou == 'users') {
					if(get_user_by("login", $explode[0])->ID) {
						array_push($this->users, new User(get_user_by("login", $explode[0])->ID));
					}
				} else if($ou == 'groups') {
					if(get_page_by_title($explode[0], OBJECT, 'io_group')->ID) {
						array_push($this->groups, new Group(get_page_by_title($explode[0], OBJECT, 'io_group')->ID));
					}
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
