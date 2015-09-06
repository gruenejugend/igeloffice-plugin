<?php

/**
 *
 * @author KWM
 */
interface ldapInterface {
	/*
	 * User Initials
	 */
	//Füge User hinzu
	function addUser($firstname, $surname, $mail);
	//Füge technischen User hinzu
	function addTechnicalUser($name);
	//Lösche User
	function delUser($user);
	
	/*
	 * User Attribute
	 */
	//Bekomme ein bestimmtes User Attribut
	function getUserAttribute($user, $attribute);
	//Setze ein bestimmtes Attribut eines bestimmten Users auf einen gewissen Value
	function setUserAttribute($user, $attribute, $value);
	//Gebe alle Gruppenzugehörigkeiten zurück
	function getUserGroups($user);
	
	/*
	 * User Permissions
	 */
	//Füge eine Berechtigung einem User hinzu
	function addUserPermission($user, $permission);
	//Lösche eine Berechtigung eines Users
	function delUserPermission($user, $permission);
	//Bekomme alle Berechtigungen eines Users, ohne Gruppen
	function getUserPermissions($user);
	//Bekomme alle Berechtigungen eines Users, inkl. Gruppen
	function getUserAllPermissions($user);
	
	/*
	 * Gruppen Initials
	 */
	//Erstelle eine Gruppe
	function addGroup($group);
	//Lösche eine Gruppe
	function delGroup($group);
	//Ersetze alle Gruppenmitglieder
	function addUsersToGroup($user, $group);
	//Lösche User aus einer Gruppe
	function delUserToGroup($user, $group);
	//Füge Gruppe einer Gruppe hinzu
	function addGroupToGroup($groupToAdd, $group);
	//Lösche Gruppe aus einer Gruppe
	function delGroupToGroup($groupToDel, $group);
	
	/*
	 * Gruppen Attribute
	 */
	//Bekomme Gruppen Attribut
	function getGroupAttribute($group, $attribute);
	//Setze Gruppen Attribut
	function setGroupAttribute($group, $attribute, $value);
	
	/*
	 * Group Permissions
	 */
	//Füge Gruppe eine Berechtigung hinzu
	function addGroupPermission($group, $permission);
	//Lösche Berechtigung einer Gruppe
	function delGroupPermission($group, $permission);
	
	/*
	 * Permission Initials
	 */
	//Füge Berechtigung hinzu
	function addPermission($permission);
	//Lösche Berechtigung
	function delPermission($permission);
	
	/*
	 * Sonstiges
	 */
	//Abfrage, ob User berechtigt ist
	function isQualified($user, $permission);
}
