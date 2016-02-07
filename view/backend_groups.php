<?php

/**
 * Description of backend_groups
 *
 * @author KWM
 */
class backend_groups {
	public static function maskHandler() {
		add_meta_box("io_groups_info_mb", "Informationen", array("backend_groups", "metaInfo"), Group_Control::POST_TYPE, "normal", "default");
		add_meta_box("io_groups_member_mb", "Mitgliedschaften", array("backend_groups", "metaMember"), Group_Control::POST_TYPE, "normal", "default");
		add_meta_box("io_groups_permission_mb", "Berechtigungen", array("backend_groups", "metaPermission"), Group_Control::POST_TYPE, "normal", "default");
	}
	
	public static function metaInfo($post) {
		wp_nonce_field('io_groups_info', 'io_groups_info_nonce');
		
		$oberkategorie_sel = "";
		$unterkategorie_sel = "";
		if(get_post_meta($post->ID, "io_group_aktiv", true) != "") {
			$group = new Group($post->ID);
			
			$oberkategorie_sel = $group->oberkategorie;
			$unterkategorie_sel = $group->unterkategorie;
		}
		$group = Group_Control::getValues();
		
		include '../wp-content/plugins/igeloffice/templates/backend/groupInfo.php';
	}
	
	public static function metaMember($post) {
		wp_nonce_field('io_groups_member', 'io_groups_member_nonce');
		
		$owner = array();
		$users = array();
		$groups = array();
		if(get_post_meta($post->ID, "io_group_aktiv", true) == 1) {
			$group = new Group($post->ID);
			
			$owner = io_get_ids($group->owner, true, true);
			$users = io_get_ids($group->users, true, true);
			$groups = io_get_ids($group->groups, true);
		}
		
		$post_id = $post->ID;
		
		include '../wp-content/plugins/igeloffice/templates/backend/groupMember.php';
	}
	
	public static function metaPermission($post) {
		wp_nonce_field('io_groups_permission', 'io_groups_permission_nonce');
		
		$permissions = array();
		if(get_post_meta($post->ID, "io_group_aktiv", true) != "") {
			$group = new Group($post->ID);
			
			$permissions = io_get_ids($group->permissions, true);
		}
		include '../wp-content/plugins/igeloffice/templates/backend/groupPermission.php';
	}
	
	public static function column($columns) {
		return array_merge($columns, array('io_group_ok' => 'Oberkategorie', 'io_group_uk' => 'Unterkategorie'));
	}
	
	public static function maskColumn($column, $post_id) {
		if($column == 'io_group_ok' || $column == 'io_group_uk') {
			echo get_post_meta($post_id, $column, true) != "" ? get_post_meta($post_id, $column, true) : "Nicht Kategorisiert";
		}
	}
	
	public static function orderby($vars) {
		if($vars['orderby'] == 'io_group_ok' || $vars['orderby'] == 'io_group_uk') {
			$vars = array_merge($vars, array(
				'meta_key'	=> $vars['orderby'],
				'orderby'	=> 'meta_value'
			));
		}
		return $vars;
	}
	
	public static function maskFiltering() {
		$screen = get_current_screen();
		if($screen->post_type == Group_Control::POST_TYPE) {
			$groups = Group_Control::getValues();
			
			$oberkategorien = array();
			$unterkategorien = array();
			foreach($groups AS $key_1 => $value_1) {
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
			$name = "io_group_ok";
			$selected = array();
			if(isset($_POST['io_group_ok'])) {
				$selected = $_POST['io_group_ok'];
			}
			include '../wp-content/plugins/igeloffice/templates/backend/filterSelect.php';
			?>
		</td>
		<td>
<?php			
			$title = "Unterkategorie";
			$values = $unterkategorien;
			$name = "io_group_uk";
			$selected = array();
			if(isset($_POST['io_group_uk'])) {
				$selected = $_POST['io_group_uk'];
			}
			include '../wp-content/plugins/igeloffice/templates/backend/filterSelect.php';
			?>		</td>
	</tr>
</table>
			<?php
		}
	}
	
	public static function filtering($query) {
		$names = array('io_group_ok', 'io_group_uk');
		return io_filter($query, $names, Group_Control::POST_TYPE);
	}
	
	public static function maskSave($post_id) {
		if(is_admin()) {
			if( !isset($_POST['io_groups_info_nonce']) || 
				!wp_verify_nonce($_POST['io_groups_info_nonce'], 'io_groups_info') || 
				defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
				return;
			}
			
			if( !isset($_POST['io_groups_member_nonce']) || 
				!wp_verify_nonce($_POST['io_groups_member_nonce'], 'io_groups_member') || 
				defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
				return;
			}
			
			if( !isset($_POST['io_groups_permission_nonce']) || 
				!wp_verify_nonce($_POST['io_groups_permission_nonce'], 'io_groups_permission') || 
				defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
				return;
			}
			
			if(get_post_meta($post_id, "io_group_aktiv", true) != 1) {
				update_post_meta($post_id, "io_group_aktiv", 1);
				Group_Control::createMeta($post_id, get_post($post_id)->post_title);
			}
			
			$group = new Group($post_id);
			
			io_save_kategorie($post_id, $group, "group");
			
			io_add_del($_POST['owner'], $group->owner, $post_id, "Group_Control", "Owner");
			io_add_del($_POST['users'], $group->users, $post_id, "User_Control", "ToGroup", true);
			io_add_del($_POST['groups'], $group->group, $post_id, "Group_Control", "Group");
			io_add_del($_POST['permissions'], $group->permissions, $post_id, "Group_Control", "Permission");
		}
	}
	
	public static function maskDelete($post_id) {
		if(is_admin() && get_post_meta($post_id, "io_group_aktiv", true) != "") {
			Group_Control::delete($post_id);
		}
	}
}
