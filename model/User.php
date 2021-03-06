<?php

/**
 * Model-Klasse zur Objekt-Erstellung von User
 *
 * Diese Klasse arbeitet mit magischen Methoden. Entsprechend verfuegbare Variablen koennen der Klasse __get entnommen
 * werden. Entsprechende Informationen werden in Echtzeit von WordPress abgerufen.
 *
 * @author KWM
 */
class User {
	private $art;
	private $aktiv;
	private $landesverband;
	private $permissions = array();
	private $groups = array();
	private $leading_groups = array();
	private $wp_user;
	private $ID;
	private $first_name;
	private $last_name;
	private $user_login;
	private $user_email;
	private $user_url;
	
	public function __construct($id) {
		$this->wp_user = get_user_by('ID', $id);
		$this->ID = $id;
	}
	
	public function __get($name) {
		if($name == 'art') {
			return get_user_meta($this->ID, User_Util::ATTRIBUT_ART, true);
		} else if($name == 'aktiv') {
			return get_user_meta($this->ID, User_Util::ATTRIBUT_AKTIV, true);
		} else if($name == 'landesverband') {
			return get_user_meta($this->ID, User_Util::ATTRIBUT_LANDESVERBAND, true);
		} else if($name == 'permissions') {
			$ldapConnector = ldapConnector::get();
			$this->permissions = array();
			$permissions = $ldapConnector->getUserPermissions($this->wp_user->user_login);
			if(count($permissions) > 0) {
				foreach($permissions AS $permission) {
					array_push($this->permissions, new Permission(get_page_by_title($permission, OBJECT, Permission_Util::POST_TYPE)->ID));
				}
				return $this->permissions;
			}
			return array();
		} else if($name == 'groups') {
			$ldapConnector = ldapConnector::get();
			$this->groups = array();
			$groups = $ldapConnector->getUserGroups($this->wp_user->user_login);
			if(count($groups) > 0) {
				foreach($groups AS $group) {
					array_push($this->groups, new Group(get_page_by_title($this->ldapNameUnescape($group), OBJECT, 'io_group')->ID));
				}
				return $this->groups;
			}
			return array();
		} else if($name == "leading_groups") {
			$ldapConnector = ldapConnector::get();
			$this->leading_groups = array();
			$groups = $ldapConnector->getGroupsOfLeader($this->wp_user->user_login);
			if($groups && count($groups) > 0) {
				foreach($groups AS $group) {
				    if(get_page_by_title($this->ldapNameUnescape($group), OBJECT, 'io_group') != null) {
                        array_push($this->leading_groups, new Group(get_page_by_title($this->ldapNameUnescape($group), OBJECT, 'io_group')->ID));
                    }
				}
				return $this->leading_groups;
			}
			return array();
		} else if($name == "first_name") {
			return $this->wp_user->first_name;
		} else if($name == "last_name") {
			return $this->wp_user->last_name;
		} else if($name == "user_login") {
			return $this->wp_user->user_login;
		} else if($name == "user_email") {
			return $this->wp_user->user_email;
		} else if($name == "user_url") {
			return $this->wp_user->user_url;
		} else if($name == "ID") {
			return $this->ID;
		}
		return null;
	}

    /*
     * Ausgabe des jeweiligen User-Namen in den entsprechenden LDAP-Namen (Umwandlung von Umlauten z. B.)
     */
	private function ldapNameUnescape($value) {
	    return preg_replace("/\\\([0-9A-Fa-f]{2})/e", "''.chr(hexdec('\\1')).''", $value);
    	}
}
