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
			'publicly_queryable'	=> false,
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
			'publicly_queryable'	=> false,
			'show_in_nav_menus'		=> false,
			'supports'				=> array("title")
		);
		
		register_post_type(Permission_Control::POST_TYPE, $args);
	}
	
	function io_register_posttype() {
		io_register_group();
		io_register_permission();
	}
	
	add_action('init', 'io_register_posttype');
	
	function io_login_redirect($redirect_to, $request, $user) {
		global $user;
		if(isset($user->roles) && is_array($user->roles)) {
			if (in_array('administrator', $user->roles)) {
				return $redirect_to;
			} else {
				return home_url();
			}
		} else {
			return $redirect_to;
		}
	}
	add_filter('login_redirect',								'io_login_redirect', 10, 3 );

	function io_in_wp($name, $user = true, $type = null) {
		if(!$user) {
			if(get_page_by_title($name, OBJECT, $type)) {
				return true;
			}
		} else {
			if(get_user_by("login", $name)) {
				return true;
			}
		}
		return false;
	}
	
	function io_toLoginMsg($message) {
		if(!empty($_GET['password']) && $_GET['password'] == 1) {
			$message = '<p class="message">Dein Passwort wurde ge&auml;ndert. Bitte logge dich neu ein.</p><br />';
		}
		return $message;
	}
			
	function io_mailErrorMsg($errors, $update, $user) {
		if(str_replace("@gruene-jugend.de", "", $user->user_email) != $user->user_email && $user->user_email != get_user_meta($user->ID, 'io_user_email_alt', true) && $update) {
			$errors->add('gj_mail', '<b>FEHLER:</b> Eine nachtr&auml;gliche Speicherung einer GR&Uuml;NE-JUGEND-Mail-Adresse ist hier aus Sicherheitsgr&uuml;nden nicht möglich. Wende dich bitte an webmaster@gruene-jugend.de.');
		}
		return $errors;
	}