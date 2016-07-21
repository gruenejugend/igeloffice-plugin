<?php

/**
 * Description of backend_groups
 *
 * @author KWM
 */
class backend_permission {
	public static function maskHandler() {
		add_meta_box("io_permissions_info_mb", "Informationen", array("backend_permission", "metaInfo"), Permission_Util::POST_TYPE, "normal", "default");
		add_meta_box("io_permissions_member_mb", "Mitgliedschaften", array("backend_permission", "metaMember"), Permission_Util::POST_TYPE, "normal", "default");
		if (Remember_Util::REMEMBER_SCHALTER) {
			add_meta_box("io_permissions_remember_mb", "Erinnerungen", array("backend_permission", "metaRemember"), Permission_Util::POST_TYPE, "normal", "default");
		}
	}
	
	public static function metaInfo($post) {
		wp_nonce_field('io_permissions_info', 'io_permissions_info_nonce');
		
		$oberkategorie_sel = "";
		$unterkategorie_sel = "";
		if(get_post_meta($post->ID, "io_permission_aktiv", true) != "") {
			$permission = new Permission($post->ID);
			
			$oberkategorie_sel = $permission->oberkategorie;
			$unterkategorie_sel = $permission->unterkategorie;
		}
		$group = Permission_Control::getValues();
		
		include '../wp-content/plugins/igeloffice/templates/backend/groupInfo.php';
	}
	
	public static function metaMember($post) {
		wp_nonce_field('io_permissions_member', 'io_permissions_member_nonce');
		
		$users = array();
		$groups = array();
		if(get_post_meta($post->ID, "io_permission_aktiv", true) != "") {
			$permission = new Permission($post->ID);
			
			$users = io_get_ids($permission->users, true, true);
			$groups = io_get_ids($permission->groups, true);
		}
		
		include '../wp-content/plugins/igeloffice/templates/backend/permissionMember.php';
	}

	public static function metaRemember($post)
	{
		wp_nonce_field('io_permissions_remember', 'io_permissions_remember_nonce');

		$permission = new Permission($post->ID);

		$remember = $permission->remember;

		include '../wp-content/plugins/igeloffice/templates/backend/groupRemember.php';
	}
	
	public static function column($columns) {
		return array_merge($columns, array(Permission_Util::OBERKATEGORIE => 'Oberkategorie', Permission_Util::UNTERKATEGORIE => 'Unterkategorie'));
	}
	
	public static function maskColumn($column, $post_id) {
		if($column == Permission_Util::OBERKATEGORIE || $column == Permission_Util::UNTERKATEGORIE) {
			echo get_post_meta($post_id, $column, true) != "" ? get_post_meta($post_id, $column, true) : "Nicht Kategorisiert";
		}
	}
	
	public static function orderby($vars) {
		if($vars['post_type'] == Permission_Util::POST_TYPE && ($vars['orderby'] == 'Oberkategorie' || $vars['orderby'] == 'Unterkategorie')) {
			$vars = array_merge($vars, array(
				'meta_key'	=> $vars['orderby'] == 'Oberkategorie' ? Permission_Util::OBERKATEGORIE : Permission_Util::UNTERKATEGORIE,
				'orderby'	=> 'meta_value'
			));
		}
		return $vars;
	}
	
	public static function maskDelete($post_id) {
		if(current_user_can('administrator') && get_post_meta($post_id, "io_permission_aktiv", true) != "") {
			Permission_Control::delete($post_id);
		}
	}
	
	public static function maskFiltering() {
		$screen = get_current_screen();
		if($screen->post_type == Permission_Util::POST_TYPE) {
			$permissions = Permission_Control::getValues();
			
			$oberkategorien = array();
			$unterkategorien = array();
			foreach($permissions AS $key_1 => $value_1) {
				$oberkategorien[$key_1] = $key_1;
				foreach($value_1 AS $key_2 => $value_2) {
					$unterkategorien[$key_1][$key_2] = $key_2;
				}
			}
			
			?><table border="0" cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td>
			<?php
			$title = "Oberkategorie";
			$values = $oberkategorien;
			$name = Permission_Util::OBERKATEGORIE;
			$selected = array();
			if(isset($_POST[Permission_Util::OBERKATEGORIE])) {
				$selected = $_POST[Permission_Util::OBERKATEGORIE];
			}
			include '../wp-content/plugins/igeloffice/templates/backend/filterSelect.php';
			?>
		</td>
		<td>
<?php			
			$title = "Unterkategorie";
			$values = $unterkategorien;
			$name = Permission_Util::UNTERKATEGORIE;
			$selected = array();
			if(isset($_POST[Permission_Util::UNTERKATEGORIE])) {
				$selected = $_POST[Permission_Util::UNTERKATEGORIE];
			}
			include '../wp-content/plugins/igeloffice/templates/backend/filterSelect.php';
			?>		</td>
	</tr>
</table>
			<?php
		}
	}
	
	public static function filtering($query) {
		$names = array(Permission_Util::OBERKATEGORIE, Permission_Util::UNTERKATEGORIE);
		return io_filter($query, $names, Permission_Util::POST_TYPE);
	}
	
	public static function maskSave($post_id) {
		if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
			return;
		}
		
		if(current_user_can('administrator')) {
			if( !isset($_POST['io_permissions_info_nonce']) || 
				!wp_verify_nonce($_POST['io_permissions_info_nonce'], 'io_permissions_info')) {
				return;
			}
			
			if( !isset($_POST['io_permissions_member_nonce']) || 
				!wp_verify_nonce($_POST['io_permissions_member_nonce'], 'io_permissions_member')) {
				return;
			}

			if (Remember_Util::REMEMBER_SCHALTER && (!isset($_POST['io_permissions_remember_nonce']) ||
					!wp_verify_nonce($_POST['io_permissions_remember_nonce'], 'io_permissions_remember'))
			) {
				return;
			}
			
			if(get_post_meta($post_id, "io_permission_aktiv", true) != 1) {
				update_post_meta($post_id, "io_permission_aktiv", 1);
				Permission_Control::createMeta($post_id, get_post($post_id)->post_title);
			}
			
			$permission = new Permission($post_id);
			
			io_save_kategorie($post_id, $permission, "permission");
			
			io_add_del($_POST['users'], $permission->users, $post_id, "User_Control", "Permission", true);
			io_add_del($_POST['groups'], $permission->groups, $post_id, "Group_Control", "Permission", true);

			if (Remember_Util::REMEMBER_SCHALTER && isset($_POST['remember']) && $_POST['remember'] != "") {
				$remembers = explode(", ", $_POST['remember']);
				$remembers_save = $remembers;
				$failed_adress = array();
				$failes_user = array();
				foreach ($remembers AS $key => $remember) {
					if (!filter_var($remember, FILTER_VALIDATE_EMAIL)) {
						$failed_adress[] = $remember;
						unset($remembers_save[$key]);
						continue;
					} elseif (get_user_by_email($remember) != false) {
						$failes_user[] = get_user_by_email($remember);
						unset($remembers_save[$key]);
						continue;
					}
					$remembers_save[$key] = sanitize_text_field($remember);
				}

				if (count($failed_adress) != 0) {
					set_transient("remember_failed_address", $failed_adress, 3);
				}

				if (count($failes_user) != 0) {
					set_transient("remember_failed_user", $failes_user, 3);
				}

				update_post_meta($post_id, "io_permission_remember", serialize($remembers_save));
			}
		}
	}

	public function rememberUserMsg()
	{
		$address = get_transient("remember_failed_address");
		$users = get_transient("remember_failed_user");

		if (!empty($address)) {
			?>

			<div class="updated error">
				<p>Folgende Mail-Adressen sind ung&uuml;ltig:</p>
				<b>
					<ul>
						<?php

						foreach ($address AS $address_one) {
							?>
							<li>&bull; &nbsp; <?php echo $address_one; ?></li>
							<?php
						}

						?>
					</ul>
				</b>
			</div>

			<?php
			delete_transient("remember_failed_address");
		}

		if (!empty($users)) {
			?>

			<div class="updated notice">
				<p>Folgende User sind bereits vorhanden:</p>
				<b>
					<ul>
						<?php

						foreach ($users AS $user) {
							?>
							<li>&bull; &nbsp; <?php echo $user->user_email; ?> -> <?php echo $user->user_login; ?></li>
							<?php
						}

						?>
					</ul>
				</b>
				<p>Bitte f&uuml;ge sie als User hinzu.</p>
			</div>

			<?php
			delete_transient("remember_failed_user");
		}
	}
}
