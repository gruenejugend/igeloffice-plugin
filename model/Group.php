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
		
		$post = get_post($id);
		$this->name = $post->post_title;
	}
	
	private function ldapNameFunc($name) {
		return ldap_escape($name, Group_Control::LDAP_ESCAPE_IGNORE);
	}
	
	public function __get($name) {
		if($name == 'id') {
			return $this->id;
		} else if($name == 'name') {
			return $this->name; 
		} else if($name == 'ldapName') {
			return $this->ldapNameFunc($this->name); 
		} else if($name == 'oberkategorie') {
			return get_post_meta($this->id, 'io_group_ok', true);
		} else if($name == 'unterkategorie') {
			return get_post_meta($this->id, 'io_group_uk', true);
		} else  {
			$ldapConnector = ldapConnector::get();
			if($name == 'owner') {
				$owners = $ldapConnector->getGroupAttribute($this->ldapNameFunc($this->name), "owner");
				unset($owners['count']);
				if(count($owners) > 0) {
					$this->owner = array();
					foreach($owners AS $owner) {
						if(io_in_wp(explode(",ou", substr($owner, 3))[0])) {
							array_push($this->owner, new User(get_user_by('login', explode(",ou", substr($owner, 3))[0])->ID));
						}
					}
					return $this->owner;
				}
				return array();
			} else if($name == 'users') {
				$members = $ldapConnector->getAllGroupMembers($this->ldapNameFunc($this->name));
				unset($members['count']);
				if(count($members) > 0) {
					$this->users = array();
					foreach($members AS $member) {
						if(io_in_wp($member)) {
							array_push($this->users, new User(get_user_by("login", $member)->ID));
						}
					}
					return $this->users;
				}
				return array();
			} else if($name == 'groups') {
				$groups = $ldapConnector->getAllGroupGroups($this->ldapNameFunc($this->name));
				unset($groups['count']);
				if(count($groups) > 0) {
					$this->groups = array();
					foreach($groups AS $group) {
						if(io_in_wp($group, false, Group_Util::POST_TYPE)) {
							array_push($this->groups, new Group(get_page_by_title($group, OBJECT, Group_Util::POST_TYPE)->ID));
						}
					}
					return $this->groups;
				}
				return array();
			} else if($name == 'permissions') {
				$permissions = $ldapConnector->getGroupPermissions($this->ldapNameFunc($this->name));
				unset($permissions['count']);
				if(count($permissions) > 0) {
					$this->permissions = array();
					foreach($permissions AS $permission) {
						if(io_in_wp($permission, false, Permission_Util::POST_TYPE)) {
							array_push($this->permissions, new Permission(get_page_by_title($permission, OBJECT, Permission_Util::POST_TYPE)->ID));
						}
					}
					return $this->permissions;
				}
				return array();
			}
		}
	}
}
