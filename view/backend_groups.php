<?php

/**
 * Description of backend_groups
 *
 * @author KWM
 */
class backend_groups {
	public static function maskHandler($post_type, $post) {
		$user = new User(get_current_user_id());
		
		$pruef = false;
		foreach($user->leading_groups AS $group) {
			if($group->name == $post->post_title) {
				$pruef = true;
				break;
			}
		}
		
		if(current_user_can('administrator')) {
			add_meta_box("io_groups_info_mb", "Informationen", array("backend_groups", "metaInfo"), Group_Util::POST_TYPE, "normal", "default");
			add_meta_box("io_groups_member_mb", "Mitgliedschaften", array("backend_groups", "metaMember"), Group_Util::POST_TYPE, "normal", "default");
			add_meta_box("io_groups_permission_mb", "Berechtigungen", array("backend_groups", "metaPermission"), Group_Util::POST_TYPE, "normal", "default");
			add_meta_box("io_groups_sichtbarkeit_mb", "Sichtbarkeit für Anträge", array("backend_groups", "metaSichtbarkeit"), Group_Util::POST_TYPE, "normal", "default");
			if (Remember_Util::REMEMBER_SCHALTER) {
				add_meta_box("io_groups_remember_mb", "Erinnerungen", array("backend_groups", "metaRemember"), Group_Util::POST_TYPE, "normal", "default");
			}
		} else if($pruef) {
			add_meta_box("io_groups_member_mb", "Mitgliedschaften", array("backend_groups", "metaLeaderMember"), Group_Util::POST_TYPE, "normal", "default");
		}
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
	
	public static function metaLeaderMember($post) {
		wp_nonce_field('io_groups_leader_member', 'io_groups_leader_member_nonce');
		
		$users = array();
		if(get_post_meta($post->ID, "io_group_aktiv", true) == 1) {
			$group = new Group($post->ID);
			
			$users = io_get_ids($group->users, true, true);
		}
		
		$post_id = $post->ID;
		
		include '../wp-content/plugins/igeloffice/templates/backend/groupLeadingMember.php';
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

	public static function metaSichtbarkeit($post) {
		wp_nonce_field('io_groups_sichtbarkeit', 'io_groups_sichtbarkeit_nonce');

		$group = new Group($post->ID);

		$sichtbarkeit = $group->sichtbarkeit;

		include '../wp-content/plugins/igeloffice/templates/backend/groupSichtbarkeit.php';
	}

	public static function metaRemember($post)
	{
		wp_nonce_field('io_groups_remember', 'io_groups_remember_nonce');

		$group = new Group($post->ID);

		$remember = $group->remember;

		include '../wp-content/plugins/igeloffice/templates/backend/groupRemember.php';
	}
	
	public static function column($columns) {
		return array_merge($columns, array(Group_Util::OBERKATEGORIE => 'Oberkategorie', Group_Util::UNTERKATEGORIE => 'Unterkategorie'));
	}
	
	public static function maskColumn($column, $post_id) {
		if($column == Group_Util::OBERKATEGORIE || $column == Group_Util::UNTERKATEGORIE) {
			echo get_post_meta($post_id, $column, true) != "" ? get_post_meta($post_id, $column, true) : "Nicht Kategorisiert";
		}
	}
	
	public static function orderby($vars) {
		if($vars['post_type'] == Group_Util::POST_TYPE && ($vars['orderby'] == 'Oberkategorie' || $vars['orderby'] == 'Unterkategorie')) {
			$vars = array_merge($vars, array(
				'meta_key'	=> $vars['orderby'] == 'Oberkategorie' ? Group_Util::OBERKATEGORIE : Group_Util::UNTERKATEGORIE,
				'orderby'	=> 'meta_value'
			));
		}
		return $vars;
	}
	
	public static function maskFiltering() {
		$screen = get_current_screen();
		if($screen->post_type == Group_Util::POST_TYPE) {
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
			$name = Group_Util::OBERKATEGORIE;
			$selected = array();
			if(isset($_POST[Group_Util::OBERKATEGORIE])) {
				$selected = $_POST[Group_Util::OBERKATEGORIE];
			}
			include '../wp-content/plugins/igeloffice/templates/backend/filterSelect.php';
			?>
		</td>
		<td>
<?php			
			$title = "Unterkategorie";
			$values = $unterkategorien;
			$name = Group_Util::UNTERKATEGORIE;
			$selected = array();
			if(isset($_POST[Group_Util::UNTERKATEGORIE])) {
				$selected = $_POST[Group_Util::UNTERKATEGORIE];
			}
			include '../wp-content/plugins/igeloffice/templates/backend/filterSelect.php';
			?>		</td>
	</tr>
</table>
			<?php
		}
	}
	
	public static function filtering($query) {
		$names = array(Group_Util::OBERKATEGORIE, Group_Util::UNTERKATEGORIE);
		return io_filter($query, $names, Group_Util::POST_TYPE);
	}
	
	public static function leadingFilter($query) {
		if(function_exists(get_current_screen)) {
			$screen = get_current_screen();
			if(is_admin() && !current_user_can('administrator') && $screen->post_type == Group_Util::POST_TYPE) {
				$user = new User(get_current_user_id());

				$leadingGroups = $user->leading_groups;
				$leadingGroupsID = array();
				if(!empty($leadingGroups)) {
					foreach($leadingGroups AS $group) {
						$leadingGroupsID[] = $group->id;
					}

					$query->query_vars['post__in'] = $leadingGroupsID;
				}
			}
		}
		return $query;
	}
	
	public static function maskSave($post_id) {
		if(current_user_can('administrator')) {
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

			if( !isset($_POST['io_groups_sichtbarkeit_nonce']) ||
				!wp_verify_nonce($_POST['io_groups_sichtbarkeit_nonce'], 'io_groups_sichtbarkeit') ||
				defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
				return;
			}

			if (Remember_Util::REMEMBER_SCHALTER && (!isset($_POST['io_groups_remember_nonce']) ||
					!wp_verify_nonce($_POST['io_groups_remember_nonce'], 'io_groups_remember') ||
					defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
			) {
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
			io_add_del($_POST['groups'], $group->groups, $post_id, "Group_Control", "Group");
			io_add_del($_POST['permissions'], $group->permissions, $post_id, "Group_Control", "Permission");

			if(isset($_POST['sichtbarkeit'])) {
				$user_arten_save = self::userArtenChange(User_Util::USER_ARTEN, $_POST['sichtbarkeit']);
				update_post_meta($post_id, "io_group_sichtbarkeit", serialize($user_arten_save));
			}

			if (Remember_Util::REMEMBER_SCHALTER && isset($_POST['remember']) && $_POST['remember'] != "") {
				$remembers = explode(", ", $_POST['remember']);
				$remembers_save = $remembers;
				foreach ($remembers AS $key => $remember) {
					if (!filter_var($remember, FILTER_VALIDATE_EMAIL)) {
						set_transient("remember_failed_address", $remember, 3);
						unset($remembers_save[$key]);
						continue;
					} elseif (get_user_by_email($remember) != false) {
						set_transient("remember_failed_user", get_user_by_email($remember), 3);
						unset($remembers_save[$key]);
						continue;
					}
					$remembers_save[$key] = sanitize_text_field($remember);
				}
				update_post_meta($post_id, "io_group_remember", serialize($remembers_save));
			}
		} else {
			if( !isset($_POST['io_groups_leader_member_nonce']) || 
				!wp_verify_nonce($_POST['io_groups_leader_member_nonce'], 'io_groups_leader_member') || 
				defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
				return;
			}
			
			$user = new User(get_current_user_id());
			$pruef = false;
			foreach($user->leading_groups AS $group) {
				if($group->id == $post_id) {
					$pruef = true;
					break;
				}
			}
			
			if($pruef) {
				/*
				 * Gruppenmitglieder entfernen
				 */
				$group = new Group($post_id);
				
				$fehler = false;
				if(!empty($group->users)) {
					$_POST['users'] = $_POST['users'] == null ? array() : $_POST['users'];
					if(!empty(array_diff(io_get_ids($_POST['users']), $group->users))) {
						$fehler = true;
					}
				} elseif(empty($group->users) && !empty($_POST['users'])) {
					$fehler = true;
				}
				
				if(!$fehler) {
					io_add_del($_POST['users'], $group->users, $post_id, "User_Control", "ToGroup", true);
				}
				
				/*
				 * Gruppenmitglieder mit E-Mail hinzufügen
				 */
				$added_user = array();
				$fail = array();
				if(!empty($_POST['new_mails'])) {
					$new_mails = explode(", ", $_POST['new_mails']);
					foreach($new_mails AS $mail) {
						$mail_to_add = sanitize_text_field($mail);
						
						$user = get_user_by("email", $mail_to_add);
						if($user) {
							User_Control::addToGroup($user->ID, $post_id);
							$added_user[] = $user->user_login;
						} else {
							$fail[] = $mail_to_add;
						}
					}
				}
				
				/*
				 * Gruppenmitglieder mit Namen hinzufügen
				 */
				if(!empty($_POST['new_names'])) {
					$new_names = explode(", ", $_POST['new_names']);
					foreach($new_names AS $name) {
						$name_to_add = sanitize_text_field($name);
						
						$user = get_user_by("user_login", $name_to_add);
						if($user) {
							User_Control::addToGroup($user->ID, $post_id);
							$added_user[] = $user->user_login;
						} else {
							$fail[] = $name_to_add;
						}
					}
				}
				
				set_transient("added_user", $added_user, 1);
				set_transient("failed_user", $fail, 1);
			}
		}
	}

	public function userArtenChange($user_arten, $post) {
		$user_arten_save = $user_arten;

		function save_userArt($user_arten, $check, $key, $key_2 = null) {
			if($key_2 == null) {
				if(!in_array($key, $check)) {
					unset($user_arten[$key]);
				}
			} else {
				if(!in_array($key . "_" . $key_2, $check)) {
					unset($user_arten[$key][$key_2]);
				}

				if(count($user_arten[$key]) == 0) {
					unset($user_arten[$key]);
				}
			}
			return $user_arten;
		}

		foreach ($user_arten AS $key => $user_art) {
			if (is_array($user_art)) {
				foreach ($user_art AS $key_2 => $user_art_2) {
					$user_arten_save = save_userArt($user_arten_save, $post, $key, $key_2);
				}
			} else {
				$user_arten_save = save_userArt($user_arten_save, $post, $key);
			}
		}

		return $user_arten_save;
	}

	public static function maskDelete($post_id)
	{
		if (current_user_can('administrator') && get_post_meta($post_id, "io_group_aktiv", true) != "") {
			Group_Control::delete($post_id);
		}
	}
					
	public function userAddedLeaderUserMsg() {
		$added_user = get_transient("added_user");
		
		if(!empty($added_user)) {
		?>

		<div class="updated notice">
			<p>Folgende User wurden hinzugef&uuml;gt:</p>
			<b><ul>
				<?php

				foreach($added_user AS $add) {
					?>						<li>&bull; &nbsp; <?php echo $add; ?></li>
<?php
				}

				?>
				</ul></b>
		</div>

		<?php
		}
	}

	public function userFailedLeaderUserMsg() {
		$fail = get_transient("failed_user");
		
		if(!empty($fail)) {
		?>

		<div class="error notice">
			<p>Folgende User wurden nicht gefunden:</p>
			<b><ul>
				<?php

				foreach($fail AS $failed) {
					?>						<li>&bull; &nbsp; <?php echo $failed; ?></li>
<?php
				}

				?>
			</ul></b>
		</div>

		<?php
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
