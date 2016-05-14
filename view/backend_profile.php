<?php

/**
 * Description of backend_profile
 *
 * @author KWM
 */
class backend_profile {
	public static function maskHandler($wp_user) {
		wp_nonce_field('io_users', 'io_users_nonce');
		
		if($_GET['user_aktiv'] == "true" && current_user_can('administrator')) {
			User_Control::aktivieren($wp_user->ID);
			
			set_transient("user_aktiv", true, 3);
		}
		
		$user = new User($wp_user->ID);
		
		$permissions = Permission_Control::getValues();
		$groups = Group_Control::getValues();
		
		$permissions_values = io_get_ids($user->permissions, true);
		$groups_values = io_get_ids($user->groups, true);
		
		include '../wp-content/plugins/igeloffice/templates/backend/profile.php';
	}
	
	public static function maskExecution($user_id, $old_user) {
		if(str_replace("@gruene-jugend.de", "", get_userdata($user_id)->user_email) != get_userdata($user_id)->user_email && get_userdata($user_id)->user_email != $old_user->user_email) {
			wp_update_user(array(
				'ID'			=> $user_id,
				'user_email'	=> $old_user->user_email
			));
		} elseif(get_userdata($user_id)->user_email != $old_user->user_email) {
			$ldapConnector = ldapConnector::get();
			if(	($ldapConnector->getUserAttribute(get_userdata($user_id)->user_login, "mail")[0] == $ldapConnector->getUserAttribute(get_userdata($user_id)->user_login, "mailAlternateAddress")[0] && $ldapConnector->getUserAttribute(get_userdata($user_id)->user_login, "mailAlternateAddress")[0] == $old_user->user_email) ||
				($ldapConnector->getUserAttribute(get_userdata($user_id)->user_login, "mailAlternateAddress")[0] == "" && $ldapConnector->getUserAttribute(get_userdata($user_id)->user_login, "mail")[0] == $old_user->user_email)) {
				$ldapConnector->setUserAttribute(get_userdata($user_id)->user_login, "mail", get_userdata($user_id)->user_email, "replace", $old_user->user_email);
				$ldapConnector->setUserAttribute(get_userdata($user_id)->user_login, "mailAlternateAddress", get_userdata($user_id)->user_email, "replace", $old_user->user_email);
			} elseif($ldapConnector->getUserAttribute(get_userdata($user_id)->user_login, "mail")[0] != $ldapConnector->getUserAttribute(get_userdata($user_id)->user_login, "mailAlternateAddress")[0][0] && $ldapConnector->getUserAttribute(get_userdata($user_id)->user_login, "mailAlternateAddress")[0] == $old_user->user_email) {
				$ldapConnector->setUserAttribute(get_userdata($user_id)->user_login, "mailAlternateAddress", get_userdata($user_id)->user_email, "replace", $old_user->user_email);
			}
			update_user_meta($user_id, "io_user_email_alt", get_userdata($user_id)->user_email);
		}
		
		if(current_user_can('administrator')) {
			if( !isset($_POST['io_users_nonce']) || 
				!wp_verify_nonce($_POST['io_users_nonce'], 'io_users') || 
				defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
				return;
			}
			
			$user = new User($user_id);
			if(isset($_POST['user_aktiv']) && $_POST['user_aktiv'] == 'true' && $user->aktiv == 0) {
				User_Control::aktivieren($user_id);
				
				Request_Control::approve(get_post(array(
					'post_type'			=> Request_Control::POST_TYPE,
					'meta_query'		=> array(
						'relation'			=> 'AND',
						array(
							'key'				=> 'io_request_art',
							'value'				=> Request_User::art(),
							'compare'			=> '='
						),
						array(
							'key'				=> 'io_request_steller_in',
							'value'				=> $user_id,
							'compare'			=> '='
						),
						array(
							'key'				=> 'io_request_status',
							'value'				=> 'Gestellt',
							'compare'			=> '='
						)
					)
				))->ID);
			}
			
			io_add_del($_POST['permissions'], $user->permissions, $user_id, "User_Control", "Permission");
			io_add_del($_POST['groups'], $user->groups, $user_id, "User_Control", "ToGroup");
		} else {
			if( !isset($_POST['io_users_nonce']) || 
				!wp_verify_nonce($_POST['io_users_nonce'], 'io_users') || 
				defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
				return;
			}
			
			$user = new User($user_id);
			
			$_POST['permissions'] = $_POST['permissions'] == null ? array() : $_POST['permissions'];
			$user_permissions = $user->permissions == null ? array() : $user->permissions;
			
			$_POST['permissions'] = io_get_ids($_POST['permissions']);
			$user_permissions = io_get_ids($user_permissions, true);
			
			$to_del_permission = array_diff($user_permissions, $_POST['permissions']);
			$to_add_permission = array_diff($_POST['permissions'], $user_permissions);
			
			if(count($to_del_permission) > 0) {
				set_transient("permission_fail", true, 3);
			}
			
			if(count($to_add_permission) > 0) {
				foreach($to_add_permission AS $permission) {
					Request_Control::create($user_id, "Permission", $permission);
				}
				set_transient("permission_start", true, 3);
			}
			
			$_POST['groups'] = $_POST['groups'] == null ? array() : $_POST['groups'];
			$user_groups = $user->groups == null ? array() : $user->groups;
			
			$_POST['groups'] = io_get_ids($_POST['groups']);
			$user_groups = io_get_ids($user_groups, true);

			$to_del_group = array_diff($user_groups, $_POST['groups']);
			$to_add_group = array_diff($_POST['groups'], $user_groups);

			if(count($to_del_group) > 0) {
				set_transient("group_fail", true, 3);
			}
			
			if(count($to_add_group) > 0) {
				foreach($to_add_group AS $group) {
					Request_Control::create($user_id, "Group", $group);
				}
				set_transient("group_start", true, 3);
			}
		}
		
		if(LDAP_Proxy::isLDAPUser($user->user_login) && isset($_POST['pass1-text']) && $_POST['pass1-text'] != "") {
			LDAP_Proxy::changePW($user, sanitize_text_field($_POST['pass1-text']));
			if($user->user_login == get_userdata(get_current_user_id())->user_login) {
				function toLogin(){
					remove_action('wp_logout', 'toLogin');
					wp_redirect(wp_login_url() . '?password=1');
					exit();
				}
				add_action('wp_logout','toLogin');
				
				wp_logout();
			}
		}
	}
	
	public static function column($columns) {
		return array_merge($columns, array("io_user_art" => "Art", "io_user_aktiv" => "Aktiv"));
	}
	
	public static function maskColumn($value, $column, $user_id) {
		if($column == "io_user_art" || $column == "io_user_aktiv") {
			return get_user_meta($user_id, $column, true);
		}
		return $value;
	}
	
	public static function orderby($query) {
		global $wpdb;
		
		if(!is_admin()) {
			return;
		}
		
		if($query->query_vars['orderby'] == "Aktiv") {
			$query->query_from .= " LEFT OUTER JOIN $wpdb->usermeta AS um ON $wpdb->users.ID = um.user_id ";
			$query->query_where .= " AND um.meta_key = 'io_user_aktiv' AND um.meta_value = ".($query->query_vars["order"] == "ASC" ? "0 " : "1 ");
			$query->query_orderby = " ORDER BY um.meta_value ".($query->query_vars["order"] == "ASC" ? "asc " : "desc ");
		} else if($query->query_vars['orderby'] == "Art") {
			$query->query_from .= " LEFT OUTER JOIN $wpdb->usermeta AS um ON $wpdb->users.ID = um.user_id ";
			$query->query_where .= " AND um.meta_key = 'io_user_art' ";
			$query->query_orderby = " ORDER BY um.meta_value ".($query->query_vars["order"] == "ASC" ? "asc " : "desc ");
		}
		return $query;
	}
	
	public static function row($actions, $user) {
		if(get_user_meta($user->ID, "io_user_aktiv", true) == 0) {
			$hinweis = "";
			if(str_replace("@gruene-jugend.de", "", $user->user_email) != $user->user_email) {
				$hinweis = " <b>>>ACHTUNG! GJ-Adresse!<<</b>";
			}
			$actions['Aktivieren'] = '<a href="user-edit.php?user_id=' . $user->ID . '&user_aktiv=true">Aktivieren' . $hinweis . '</a>';
		}
		return $actions;
	}
	
	public static function menu() {
		if(current_user_can('administrator')) {
			$users = get_users(array(
				'meta_key'		=> 'io_user_aktiv',
				'meta_value'	=> 0
			));
			
			if(count($users) > 0) {
				global $menu;
				foreach($menu AS $key => $value) {
					if($menu[$key][2] == "users.php") {
						$menu[$key][0] = $menu[$key][0] . ' <span class="update-plugins ' . count($users) . '"><span class="plugin-count">' . count($users) . '</span></span>';
						break;
					}
				}
			}
		}
		return;
	}
	
	/*
	 * Messages
	 */
	public static function userActive() {
		$check = get_transient("user_active");
		
		if(!empty($check)) {
		?>
		
	<div class="updated">
		<p>User erfolgreich aktiviert.</p>
	</div>	
		 
		<?php
		}
	}
	
	public static function msg_request_group_start() {
		$check = get_transient("group_start");
		
		if(!empty($check)) {
		?>
		
	<div class="updated">
		<p>Antrag zur Gruppen-Mitgliedschaft wurde gestellt.</p>
	</div>	
		 
		<?php
		}
	}
	
	public static function msg_request_group_fail() {
		$check = get_transient("group_fail");
		
		if(!empty($check)) {
		?>
		
	<div class="error">
		<p>Du kannst keinen Antrag stellen, aus einer Gruppe auszutreten. Bitte melde dich beim Webmaster oder bei der Gruppen-Leitung.</p>
	</div>	
		 
		<?php
		}
	}
	
	public static function msg_request_permission_start() {
		$check = get_transient("permission_start");
		
		if(!empty($check)) {
		?>
		
	<div class="updated">
		<p>Antrag zur Berechtigung wurde gestellt.</p>
	</div>	
		 
		<?php
		}
	}
	
	public static function msg_request_permission_fail() {
		$check = get_transient("permission_fail");
		
		if(!empty($check)) {
		?>
		
	<div class="error">
		<p>Du kannst keinen Antrag stellen, eine Berechtigung zu verlieren. Bitte melde dich beim Webmaster.</p>
	</div>	
		 
		<?php
		}
	}
}
