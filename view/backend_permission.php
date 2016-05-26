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
		if(current_user_can('administrator')) {
			if( !isset($_POST['io_permissions_info_nonce']) || 
				!wp_verify_nonce($_POST['io_permissions_info_nonce'], 'io_permissions_info') || 
				defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
				return;
			}
			
			if( !isset($_POST['io_permissions_member_nonce']) || 
				!wp_verify_nonce($_POST['io_permissions_member_nonce'], 'io_permissions_member') || 
				defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
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
		}
	}
}
