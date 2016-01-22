<?php

	function io_register_group() {
		$labels = array(
			'name'					=> "Gruppen",
			'singular_name'			=> "Gruppe",
			'name_admin_bar'		=> "Neue Gruppe",
			'add_new'				=> "Neue Gruppen",
			'add_new_item'			=> "Neue Gruppe",
			'edit_item'				=> "Gruppe bearbeiten",
			'new_item'				=> "Neue Gruppe",
			'view_item'				=> "Gruppe anzeigen",
			'search_items'			=> "Gruppe suchen",
			'not_found'				=> "Keine Gruppe gefunden",
			'not_found_in_trash'	=> "Keine Gruppe im Papierkorb gefunden"
		);
		
		$args = array(
			'labels'				=> $labels,
			'public'				=> true,
			'show_in_nav_menus'		=> false,
			'supports'				=> array("title")
		);
		
		register_post_type(Group_Control::POST_TYPE, $args);
	}
	
	function io_register_permission() {
		$labels = array(
			'name'					=> "Berechtigungen",
			'singular_name'			=> "Berechtigumg",
			'name_admin_bar'		=> "Neue Berechtigung",
			'add_new'				=> "Neue Berechtigungen",
			'add_new_item'			=> "Neue Berechtigung",
			'edit_item'				=> "Berechtigung bearbeiten",
			'new_item'				=> "Neue Berechtigung",
			'view_item'				=> "Berechtigung anzeigen",
			'search_items'			=> "Berechtigung suchen",
			'not_found'				=> "Keine Berechtigung gefunden",
			'not_found_in_trash'	=> "Keine Berechtigung im Papierkorb gefunden"
		);
		
		$args = array(
			'labels'				=> $labels,
			'public'				=> true,
			'show_in_nav_menus'		=> false,
			'supports'				=> array("title")
		);
		
		register_post_type(Group_Control::POST_TYPE, $args);
	}
	
	function io_register_posttype() {
		io_register_group();
		io_register_permission();
	}
	
	add_action('init', 'io_register_posttype');