<?php

/**
 * Description of Group
 *
 * @author KWM
 */
class Group {
	private $id;
	private $name;
	private $oberkategorie;
	private $unterkategorie;
	private $owner = array();
	private $users = array();
	private $groups = array();
	private $permissions = array();
	
	public function __construct($id) {
		$this->id = $id;
		
		$post = get_post(array('ID' => $id));
		$this->name = $post->post_title;
	}
	
	public function __get($name) {
		if($name == 'id') {
			return $this->id;
		} else if($name == 'name') {
			return $this->name; 
		} else if($name == 'oberkategorie') {
			return get_post_meta($this->id, 'io_group_ok', true);
		} else if($name == 'unterkategorie') {
			return get_post_meta($this->id, 'io_group_uk', true);
		} else  {
			$ldapConnector = ldapConnector::get();
			if($name == 'owner') {
				$owners = $ldapConnector->getGroupAttribute($this->name, "owner");
				foreach($owners AS $owner) {
					array_push($this->owner, new User(get_user_by('login', explode(",ou", substr($owner, 3))[0])->ID));
				}
				return $this->owner;
			} else if($name == 'users') {
				$members = $ldapConnector->getAllGroupMembers($this->name);
				foreach($members AS $member) {
					array_push($this->users, new User(get_user_by("login", $member)->ID));
				}
				return $this->users;
			} else if($name == 'groups') {
				$groups = $ldapConnector->getAllGroupGroups($this->name);
				foreach($groups AS $group) {
					array_push($this->groups, new Group(get_page_by_title($group, OBJECT, Group_Control::POST_TYPE)->ID));
				}
				return $this->groups;
			} else if($name == 'permissions') {
				$permissions = $ldapConnector->getGroupPermissions($this->name);
				foreach($permissions AS $permission) {
					array_push($this->permissions, new Permission(get_page_by_title($permission, OBJECT, Permission_Control::POST_TYPE)->ID));
				}
				return $this->permissions;
			}
		}
	}
}
