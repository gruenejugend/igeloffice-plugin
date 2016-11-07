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
			'show_ui'				=> true,
			'supports'				=> array("title"),
			'capabilities'			=> array(
				'edit_post'				=> 'edit_group', 
				'read_post'				=> 'read_group', 
				'edit_posts'			=> 'edit_groups', 
				'edit_others_posts'		=> 'edit_others_groups'
			)
		);
		
		register_post_type(Group_Util::POST_TYPE, $args);
	}
	
	function groupLeaderCap() {
		$role = get_role('subscriber');
		$role->add_cap('read_group'); 
		$role->add_cap('edit_group'); 
		$role->add_cap('edit_groups'); 
		$role->add_cap('edit_others_groups'); 
		
		$role = get_role('administrator');
		$role->add_cap('read_group'); 
		$role->add_cap('edit_group'); 
		$role->add_cap('edit_groups'); 
		$role->add_cap('edit_others_groups'); 
	}
	add_action('admin_init', 'groupLeaderCap');
	
	function io_register_permission() {
		$labels = array(
			'name'					=> "Berechtigungen",
			'singular_name'			=> "Berechtigung",
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
		
		register_post_type(Permission_Util::POST_TYPE, $args);
	}
	
	function io_register_request() {
		$labels = array(
			'name'					=> "Anträge",
			'singular_name'			=> "Antrag",
			'name_admin_bar'		=> "Neuer Antrag",
			'add_new'				=> "Neue Anträge",
			'add_new_item'			=> "Neuer Antrag",
			'edit_item'				=> "Antrag bearbeiten",
			'new_item'				=> "Neuer Antrag",
			'view_item'				=> "Antrag anzeigen",
			'search_items'			=> "Antrag suchen",
			'not_found'				=> "Kein Antrag gefunden",
			'not_found_in_trash'	=> "Kein Antrag im Papierkorb gefunden"
		);
		
		$args = array(
			'labels'				=> $labels,
			'public'				=> true,
			'publicly_queryable'	=> false,
			'show_in_nav_menus'		=> false,
			'supports'				=> array("title"),
			'capabilities'			=> array(
				'edit_post'				=> 'edit_request', 
				'read_post'				=> 'read_request', 
				'edit_posts'			=> 'edit_requests', 
				'edit_others_posts'		=> 'edit_others_requests'
			)
		);
		
		register_post_type(Request_Util::POST_TYPE, $args);
	}
	
	function requestLeaderCap() {
		$role = get_role('subscriber');
		$role->add_cap('read_request'); 
		$role->add_cap('edit_request'); 
		$role->add_cap('edit_requests'); 
		$role->add_cap('edit_others_requests'); 
		
		$role = get_role('administrator');
		$role->add_cap('read_request'); 
		$role->add_cap('edit_request'); 
		$role->add_cap('edit_requests'); 
		$role->add_cap('edit_others_requests'); 
	}
	add_action('admin_init', 'requestLeaderCap');
	
	function io_register_domain() {
		$labels = array(
			'name'					=> "Domains",
			'singular_name'			=> "Domain",
			'name_admin_bar'		=> "Neue Domain",
			'add_new'				=> "Neue Domains",
			'add_new_item'			=> "Neue Domain",
			'edit_item'				=> "Domain bearbeiten",
			'new_item'				=> "Neue Domain",
			'view_item'				=> "Domain anzeigen",
			'search_items'			=> "Domain suchen",
			'not_found'				=> "Keine Domain gefunden",
			'not_found_in_trash'	=> "Keine Domain im Papierkorb gefunden"
		);
		
		$args = array(
			'labels'				=> $labels,
			'public'				=> true,
			'publicly_queryable'	=> false,
			'show_in_nav_menus'		=> false,
			'supports'				=> array("title"),
			'capabilities'			=> array(
				'create_posts'			=> false
			)
		);
		
		register_post_type(Domain_Util::POST_TYPE, $args);
	}
	
	function io_register_posttype() {
		io_register_group();
		io_register_permission();
		io_register_request();
		io_register_domain();
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
	
	function io_umlaute($input) {
		$suche =	array("ä",		"ö",	"ü",	"ß",	"Ä",	"Ö",	"Ü",	"é",	"á",	"ó");
		$ersetzen =	array("ae",		"oe",	"ue",	"ss",	"Ae",	"Oe",	"Ue",	"e",	"a",	"o");
		
		return str_replace($suche, $ersetzen, $input);
	}

function io_mail($old_mail)
{
	return "webmaster@gruene-jugend.de";
}

add_filter('wp_mail_from', 'io_mail');