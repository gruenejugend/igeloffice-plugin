<?php

/**
 * Description of backend_requestr
 *
 * @author KWM
 */
class backend_request {
	public static function maskHandler() {
		add_meta_box("io_request_info_mb", "Informationen", array("backend_request", "maskInfo"), Request_Control::POST_TYPE, "normal", "default");
		add_meta_box("io_request_action_mb", "Aktion", array("backend_request", "maskAction"), Request_Control::POST_TYPE, "normal", "default");
	}
	
	public static function maskInfo($post) {
		$request = new Request($post->ID);
		$request_art = Request_Factory::getRequest($request->art, $post->ID);
		
		?>

<table border="0" cellpadding="5" cellspacing="0" width="100%">
	<tr>
		<td width="40%">Berechtigungsart:</td>
		<td width="60%"><b><?php echo $request->art; ?></b></td>
	</tr>
	<tr>
		<td width="40%">Steller*in:</td>
		<td width="60%"><b><?php echo $request->steller_in; ?></b></td>
	</tr>
	<tr>
		<td width="40%">F&uuml;r:</td>
		<td width="60%"><b><?php echo $request_art->getObject()->name; ?></b></td>
	</tr>
	<tr>
		<td width="40%">Status:</td>
		<td width="60%"><b><?php echo $request->status; ?></b></td>
	</tr>
</table>

<?php
	}
	
	public static function maskAction($post) {
		$request = new Request($post->ID);
		wp_nonce_field('io_request_action', 'io_request_action_nonce');
		
		if($request->status == "Gestellt") {
			?>

<input type="radio" name="io_request_status" value="annahme"> Antrag annehmen
<br><br><br>
<input type="radio" name="io_request_status" value="ablehnung"> Antrag ablehnen

<?php
		} else {
			?><b>Antrag wurde bereits <?php echo $request->status; ?>.</b><?php
		}
	}
	
	public static function maskSave($post_id) {
		if( !isset($_POST['io_request_action_nonce']) || 
			!wp_verify_nonce($_POST['io_request_action_nonce'], 'io_request_action') || 
			defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ||
			empty($_POST['io_request_status'])) {
			return;
		}
		
		$request = new Request($post_id);
		$pruef = false;
		if(current_user_can('administrator')) {
			$pruef = true;
		} else if($request->art == Request_Group::art()) {
			$user = new User(get_current_user_id());
			foreach($user->leading_groups AS $group) {
				if($group->id == $request->requested_id) {
					$pruef = true;
					break;
				}
			}
		}
		
		if($pruef) {
			if($_POST['io_request_status'] == "annahme") {
				Request_Control::approve($post_id);
			} else if($_POST['io_request_status'] == "ablehnung") {
				Request_Control::reject($post_id);
			}
		}
	}
	
	public static function menu() {
		if(current_user_can('administrator')) {
			if(Request_Control::count() > 0) {
				global $menu;
				foreach($menu AS $key => $value) {
					if($menu[$key][2] == "edit.php?post_type=io_request") {
						$menu[$key][0] = $menu[$key][0] . ' <span class="update-plugins ' . Request_Control::count() . '"><span class="plugin-count">' . Request_Control::count() . '</span></span>';
						break;
					}
				}
			}
		}
		return;
	}
	
	public static function column($columns) {
		return array_merge($columns, array('io_request_status' => 'Status'));
	}
	
	public static function maskColumn($column, $post_id) {
		if($column == 'io_request_status') {
			echo (new Request($post_id))->status;
		}
	}
	
	public static function orderby($vars) {
		if($vars['post_type'] == Request_Control::POST_TYPE && $vars['orderby'] == 'Status') {
			$vars = array_merge($vars, array(
				'meta_key'	=> 'io_request_status',
				'orderby'	=> 'meta_value'
			));
		}
		return $vars;
	}
	
	public static function maskFiltering() {
		$screen = get_current_screen();
		if($screen->post_type == Request_Control::POST_TYPE) {
			$groups = Group_Control::getValues();
			$permissions = Permission_Control::getValues();
			$users = User_Control::getValues();
			
			?><table border="0" cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td>
			<?php
			$title = "Arten";
			$values = Request_Factory::getValues();
			$name = "io_request_art";
			$selected = array();
			if(isset($_POST['io_request_art'])) {
				$selected = $_POST['io_request_art'];
			}
			include '../wp-content/plugins/igeloffice/templates/backend/filterSelect.php';
			?>
		</td>
		<td>
<?php		
			$title = "Status";
			$values = array("Angenommen", "Abgelehnt", "Gestellt");
			$name = "io_request_status";
			$selected = array();
			if(isset($_POST['io_request_status'])) {
				$selected = $_POST['io_request_status'];
			}
			include '../wp-content/plugins/igeloffice/templates/backend/filterSelect.php';
			?>
		</td>
		<td>
<?php		
			$title = "Gruppen";
			$values = self::prepareOptions($groups);
			$name = "io_request_requested_id_groups";
			$selected = array();
			if(isset($_POST['io_request_requested_id_groups'])) {
				$selected = $_POST['io_request_requested_id_groups'];
			}
			include '../wp-content/plugins/igeloffice/templates/backend/filterSelect.php';
			?>
		</td>
		<td>
<?php		
			$title = "Berechtigungen";
			$values = self::prepareOptions($permissions);
			$name = "io_request_requested_id_permissions";
			$selected = array();
			if(isset($_POST['io_request_requested_id_permissions'])) {
				$selected = $_POST['io_request_requested_id_permissions'];
			}
			include '../wp-content/plugins/igeloffice/templates/backend/filterSelect.php';
			?>
		</td>
		<td>
<?php		
			$title = "Steller*in";
			$values = self::prepareOptions($users, true);
			$name = "io_request_steller_in";
			$selected = array();
			if(isset($_POST['io_request_steller_in'])) {
				$selected = $_POST['io_request_steller_in'];
			}
			include '../wp-content/plugins/igeloffice/templates/backend/filterSelect.php';
			?>
		</td>
	</tr>
</table>
<?php
		}
	}
	
	private static function prepareOptions($values, $user = false) {
		$new_values = array();
		if(!empty($values)) {
			foreach($values AS $key1 => $value1) {
				foreach($value1 AS $key2 => $value2) {
					if(!is_array($value2)) {
						$new_values[$key1][$user ? get_userdata($value2)->user_login : get_post($value2)->post_title] = $key2;
					} else {
						foreach($value2 AS $key3 => $value3) {
							$new_values[$key1][$key2][$user ? get_userdata($value3)->user_login : get_post($value3)->post_title] = $key3;
						}
					}
				}
			}
		}
		return $new_values;
	}
	
	public static function filtering($query) {
		if(current_user_can('administrator') && function_exists(get_current_screen)) {
			$screen = get_current_screen();
			if($screen->post_type == $posttype && $screen->id == "edit-" . $posttype && isset($_POST['filter_action'])) {
				$_POST['io_request_requested_id'] = array();

				foreach($_POST['io_request_requested_id_groups'] AS $group) {
					$_POST['io_request_requested_id'][] = $group;
				}

				foreach($_POST['io_request_requested_id_permissions'] AS $permission) {
					$_POST['io_request_requested_id'][] = $permission;
				}

				$names = array('io_request_art', 'io_request_status', 'io_request_requested_id', 'io_request_steller_in');
				return io_filter($query, $names, Request_Control::POST_TYPE);
			}
		}
		return null;
	}
	
	public static function leadingFilter($query) {
		if(function_exists(get_current_screen)) {
			$screen = get_current_screen();
			if(is_admin() && !current_user_can('administrator') && $screen->post_type == Request_Control::POST_TYPE) {
				$user = new User(get_current_user_id());

				$leadingGroups = $user->leading_groups;
				if(!empty($leadingGroups)) {
					foreach($leadingGroups AS $group) {
						$query->query_vars['meta_query'][] = array(
							'key'		=> 'io_request_requested_id',
							'value'		=> $group->id,
							'compare'	=> '='
						);
					}
					$query->query_vars['meta_query']['relation'] = 'OR';
				}
			}
		}
		return $query;
	}
}
