<?php

/**
 * Description of backend_profile
 *
 * @author KWM
 */
class backend_profile {
	public static function maskHandler($wp_user) {
		wp_nonce_field('io_users', 'io_users_nonce');
		
		if($_GET['user_aktiv'] == "true" && is_admin()) {
			User_Control::aktivieren($wp_user->ID);
			
			add_action('admin_notices', array('backend_profile', 'userActive'));
		}
		
		$user = new User($wp_user->ID);
		
		$permissions = Permission_Control::getValues();
		$groups = Group_Control::getValues();
		
		$permissions_values = io_get_ids($user->permissions, true);
		$groups_values = io_get_ids($user->groups, true);
		
		include '../wp-content/plugins/igeloffice/templates/backend/profile.php';
	}
	
	public static function userActive() {
		?>
		
	<div class="updated">
		<p>User erfolgreich aktiviert.</p>
	</div>	
		 
		<?php

		remove_action('admin_notices', 'errorMessageVerteiler');
	}
	
	public static function maskExecution($user_id, $old_user) {
		if(str_replace("@gruene-jugend.de", "", get_userdata($user_id)->user_email) != get_userdata($user_id)->user_email && get_userdata($user_id)->user_email != $old_user->user_email) {
			echo 'Test1';
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
		
		if(is_admin()) {
			if( !isset($_POST['io_users_nonce']) || 
				!wp_verify_nonce($_POST['io_users_nonce'], 'io_users') || 
				defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
				return;
			}
			
			$user = new User($user_id);
			if(isset($_POST['user_aktiv']) && $_POST['user_aktiv'] == 'true' && $user->aktiv == 0) {
				User_Control::aktivieren($user_id);
			}
			
			io_add_del($_POST['permissions'], $user->permissions, $user_id, "User_Control", "Permission");
			io_add_del($_POST['groups'], $user->groups, $user_id, "User_Control", "ToGroup");
		}
		
		if(LDAP_Proxy::isLDAPUser($user->user_login) && isset($_POST['pass1-text']) && $_POST['pass1-text'] != "") {
			LDAP_Proxy::changePW($user->user_login, sanitize_text_field($_POST['pass1-text']));
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
		if($query->query_vars['orderby'] == "io_user_aktiv") {
			global $wpdb;
			$query->query_from .= " LEFT OUTER JOIN $wpdb->usermeta AS um ON $wpdb->users.ID = um.user_id ";
			$query->query_where .= " AND um.meta_key = 'io_user_aktiv' AND um.meta_value = 1 ";
			$query->query_orderby = " ORDER BY um.meta_value ".($query->query_vars["order"] == "ASC" ? "asc " : "desc ");
		} else if($query->query_vars['orderby'] == "io_user_art") {
			global $wpdb;
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
		if(is_admin()) {
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
}
