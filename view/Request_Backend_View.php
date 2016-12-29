<?php

/**
 * Description of Request_Backend_Viewr
 *
 * @author KWM
 */
class Request_Backend_View {
	public static function maskHandler($post_type, $post) {
		add_meta_box("io_request_info_mb", "Informationen", array("Request_Backend_View", "maskInfo"), Request_Util::POST_TYPE, "normal", "default");
		add_meta_box("io_request_action_mb", "Aktion", array("Request_Backend_View", "maskAction"), Request_Util::POST_TYPE, "normal", "default");

		$request = new Request($post->ID);
		if(current_user_can('administrator') && $request->status == "Gestellt") {
			add_meta_box("io_request_message_mb", "Antwort", array("Request_Backend_View", "maskMessage"), Request_Util::POST_TYPE, "normal", "default");
		}
	}
	
	public static function maskInfo($post) {
		$request = new Request($post->ID);
		$request_art = Request_Factory::getRequest($request->art, $post->ID);

		$user = new User($request->steller_in);

		?>

<table border="0" cellpadding="5" cellspacing="0" width="100%">
	<tr>
		<td width="40%">Berechtigungsart:</td>
		<td width="60%" colspan="2"><b><?php echo $request->art; ?></b></td>
	</tr>
	<tr>
		<td width="40%">Steller*in:</td>
		<td width="60%" colspan="2"><b><?php echo $user->user_login; ?></b></td>
	</tr>
	<tr>
		<td width="40%">F&uuml;r:</td>
		<td width="60%" colspan="2"><b><?php echo $request_art->getObject()->name; ?></b></td>
	</tr>
	<tr>
		<td width="40%">Status:</td>
		<td width="60%" colspan="2"><b><?php echo $request->status; ?></b></td>
	</tr>
	<?php

		$vars = get_post_meta($request->ID, Request_Util::DETAIL_WORDPRESS_GROUPS, true);
		if($vars) {
			$vars = maybe_unserialize($vars);
			?><tr>
				<td width="40%" rowspan="5">Gruppen:</td>
				<td width="20%">Admins:</td>
				<td width="40%"><input type="text" value="cn=<?php echo (new Group($vars[Request_Util::DETAIL_WORDPRESS_GROUPS_ADMIN]))->name. ',' . LDAP_GROUP_BASE; ?>" readonly></td>
			</tr>
			<tr>
				<td width="20%">Redakteur*innen:</td>
				<td width="40%"><input type="text" value="cn=<?php echo (new Group($vars[Request_Util::DETAIL_WORDPRESS_GROUPS_REDAKTEUR]))->name. ',' . LDAP_GROUP_BASE; ?>" readonly></td>
			</tr>
			<tr>
				<td width="20%">Autor*innen:</td>
				<td width="40%"><input type="text" value="cn=<?php echo (new Group($vars[Request_Util::DETAIL_WORDPRESS_GROUPS_AUTOR]))->name. ',' . LDAP_GROUP_BASE; ?>" readonly></td>
			</tr>
			<tr>
				<td width="20%">Mitarbeiter*in:</td>
				<td width="40%"><input type="text" value="cn=<?php echo (new Group($vars[Request_Util::DETAIL_WORDPRESS_GROUPS_MITARBEIT]))->name. ',' . LDAP_GROUP_BASE; ?>" readonly></td>
			</tr>
			<tr>
				<td width="20%">Abonnent:</td>
				<td width="40%"><input type="text" value="cn=<?php echo (new Group($vars[Request_Util::DETAIL_WORDPRESS_GROUPS_ABO]))->name. ',' . LDAP_GROUP_BASE; ?>" readonly></td>
			</tr>
			<?php
		}

	?>
</table>

<?php
	}
	
	public static function maskAction($post) {
		$request = new Request($post->ID);
		wp_nonce_field(Request_Util::ACTION_NONCE, Request_Util::POST_ATTRIBUT_ACTION_NONCE);
		
		if($request->status == "Gestellt") {
			?>

			<input type="radio" name="<?php echo Request_Util::POST_ATTRIBUT_STATUS; ?>"
				   value="annahme"> Antrag annehmen
<br><br><br>
			<input type="radio" name="<?php echo Request_Util::POST_ATTRIBUT_STATUS; ?>"
				   value="ablehnung"> Antrag ablehnen

<?php
		} else {
			?><b>Antrag wurde bereits <?php echo $request->status; ?>.</b><?php
		}
	}
	
	public static function maskMessage($post) {
		$request = new Request($post->ID);
		wp_nonce_field(Request_Util::MESSAGE_NONCE, Request_Util::POST_ATTRIBUT_MESSAGE_NONCE);
		
		?>
		
		Nachricht an den*die Antragssteller*in:<br>
		
		<textarea name="<?php echo Request_Util::ATTRIBUT_MESSAGE; ?>" cols="80" rows="10"></textarea>
		
		<?php
	}
	
	public static function maskSave($post_id) {
		echo $post_id."<br>";
		if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
			return;
		}

		if(get_post_type($post_id) != Request_Util::POST_TYPE) {
			return;
		}

		if (!isset($_POST[Request_Util::POST_ATTRIBUT_ACTION_NONCE]) ||
			!wp_verify_nonce($_POST[Request_Util::POST_ATTRIBUT_ACTION_NONCE], Request_Util::ACTION_NONCE) ||
			(empty($_POST[Request_Util::POST_ATTRIBUT_STATUS]) && empty($_POST[Request_Util::ATTRIBUT_MESSAGE]))
		) {
			return;
		}

		if (!isset($_POST[Request_Util::POST_ATTRIBUT_MESSAGE_NONCE]) ||
			!wp_verify_nonce($_POST[Request_Util::POST_ATTRIBUT_MESSAGE_NONCE], Request_Util::MESSAGE_NONCE) ||
			(empty($_POST[Request_Util::POST_ATTRIBUT_STATUS]) && empty($_POST[Request_Util::ATTRIBUT_MESSAGE]))
		) {
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
			if ($_POST[Request_Util::POST_ATTRIBUT_STATUS] == "annahme") {
				Request_Control::approve($post_id);
			} else if ($_POST[Request_Util::POST_ATTRIBUT_STATUS] == "ablehnung") {
				Request_Control::reject($post_id);
			}
		}

		if(current_user_can('administrator') && $request->status == "Gestellt" && $_POST[Request_Util::ATTRIBUT_MESSAGE] != null && $_POST[Request_Util::ATTRIBUT_MESSAGE] != "") {
			$user = new User($request->steller_in);

			if($request == "User-Aktivierung") {
				$subject = "Eine Nachricht zu deiner Registration im IGELoffice";
			} else {
				$subject = "Eine Nachricht zu deinem Antrag im IGELoffice";
			}

			wp_mail($user->user_email, $subject, $_POST[Request_Util::ATTRIBUT_MESSAGE], 'From: webmaster@gruene-jugend.de');
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
		return array_merge($columns, array(Request_Util::ATTRIBUT_STATUS => 'Status'));
	}
	
	public static function maskColumn($column, $post_id) {
		if($column == Request_Util::ATTRIBUT_STATUS) {
			echo (new Request($post_id))->status;
		}
	}
	
	public static function orderby($vars) {
		if($vars['post_type'] == Request_Util::POST_TYPE && $vars['orderby'] == 'Status') {
			$vars = array_merge($vars, array(
				'meta_key'	=> Request_Util::ATTRIBUT_STATUS,
				'orderby'	=> 'meta_value'
			));
		}
		return $vars;
	}
	
	public static function maskFiltering() {
		$screen = get_current_screen();
		if($screen->post_type == Request_Util::POST_TYPE) {
			$groups = Group_Control::getValues();
			$permissions = Permission_Control::getValues();
			$users = User_Control::getValues();
			
			?><table border="0" cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td>
			<?php
			$title = "Arten";
			$values = Request_Factory::getValues();
			$name = Request_Util::ATTRIBUT_ART;
			$selected = array();
			if(isset($_POST[Request_Util::ATTRIBUT_ART])) {
				$selected = $_POST[Request_Util::ATTRIBUT_ART];
			}
			include '../wp-content/plugins/igeloffice/templates/backend/filterSelect.php';
			?>
		</td>
		<td>
<?php		
			$title = "Status";
			$values = array("Angenommen", "Abgelehnt", "Gestellt");
			$name = Request_Util::ATTRIBUT_STATUS;
			$selected = array();
if (isset($_POST[Request_Util::POST_ATTRIBUT_STATUS])) {
	$selected = $_POST[Request_Util::POST_ATTRIBUT_STATUS];
			}
			include '../wp-content/plugins/igeloffice/templates/backend/filterSelect.php';
			?>
		</td>
		<td>
<?php		
			$title = "Gruppen";
			$values = self::prepareOptions($groups);
			$name = Request_Util::ATTRIBUT_REQUESTED_ID . "_groups";
			$selected = array();
			if(isset($_POST[Request_Util::ATTRIBUT_REQUESTED_ID . '_groups'])) {
				$selected = $_POST[Request_Util::ATTRIBUT_REQUESTED_ID . '_groups'];
			}
			include '../wp-content/plugins/igeloffice/templates/backend/filterSelect.php';
			?>
		</td>
		<td>
<?php		
			$title = "Berechtigungen";
			$values = self::prepareOptions($permissions);
			$name = Request_Util::ATTRIBUT_REQUESTED_ID . "_permissions";
			$selected = array();
			if(isset($_POST[Request_Util::ATTRIBUT_REQUESTED_ID . '_permissions'])) {
				$selected = $_POST[Request_Util::ATTRIBUT_REQUESTED_ID . '_permissions'];
			}
			include '../wp-content/plugins/igeloffice/templates/backend/filterSelect.php';
			?>
		</td>
		<td>
<?php		
			$title = "Steller*in";
			$values = self::prepareOptions($users, true);
			$name = Request_Util::ATTRIBUT_STELLER_IN;
			$selected = array();
			if(isset($_POST[Request_Util::ATTRIBUT_STELLER_IN])) {
				$selected = $_POST[Request_Util::ATTRIBUT_STELLER_IN];
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
				$_POST[Request_Util::ATTRIBUT_REQUESTED_ID] = array();

				foreach($_POST[Request_Util::ATTRIBUT_REQUESTED_ID . '_groups'] AS $group) {
					$_POST[Request_Util::ATTRIBUT_REQUESTED_ID][] = $group;
				}

				foreach($_POST[Request_Util::ATTRIBUT_REQUESTED_ID . '_permissions'] AS $permission) {
					$_POST[Request_Util::ATTRIBUT_REQUESTED_ID][] = $permission;
				}

				$names = array(Request_Util::ATTRIBUT_ART, Request_Util::ATTRIBUT_STATUS, Request_Util::ATTRIBUT_REQUESTED_ID, Request_Util::ATTRIBUT_STELLER_IN);
				return io_filter($query, $names, Request_Util::POST_TYPE);
			}
		}
		return null;
	}
	
	public static function leadingFilter($query) {
		if(function_exists(get_current_screen)) {
			$screen = get_current_screen();
			if(is_admin() && !current_user_can('administrator') && $screen->post_type == Request_Util::POST_TYPE) {
				$user = new User(get_current_user_id());

				$leadingGroups = $user->leading_groups;
				if(!empty($leadingGroups)) {
					foreach($leadingGroups AS $group) {
						$query->query_vars['meta_query'][] = array(
							'key'		=> Request_Util::ATTRIBUT_REQUESTED_ID,
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
