<?php

/**
 * TODO: LDAP Anbindung
 * TODO: Berechtigungszuordnung
 * TODO: Gruppenzuordnung
 */

/**
 * Description of io_user
 *
 * @author KWM
 */
class io_user {
	
	
	
	
	
	
	
	
	
	
	/***********************************************************
	 ***********************  Formulare  ***********************
	 ***********************************************************/
	public static function register_form() {
		wp_enqueue_script('jqueryIO');
		wp_nonce_field('io_users', 'io_users_nonce');
		
		$userArtInput = esc_attr(wp_unslash((!empty($_POST['user_art'])) ? trim($_POST['user_art']) : ''));
		switch($userArtInput) {
			case 'User':
				$userArtValue[0] = " checked";
				$userArtValue[1] = "";
				$userArtValue[2] = "";
				break;
			case 'Landesverband':
				$userArtValue[0] = "";
				$userArtValue[1] = " checked";
				$userArtValue[2] = "";
				break;
			case 'Basisgruppe':
				$userArtValue[0] = "";
				$userArtValue[1] = "";
				$userArtValue[2] = " checked";
				break;
			default:
				$userArtValue[0] = " checked";
				$userArtValue[1] = "";
				$userArtValue[2] = "";
				break;
		}
		$first_name = (!empty($_POST['first_name'])) ? trim($_POST['first_name']) : '';
		$last_name = (!empty($_POST['last_name'])) ? trim($_POST['last_name']) : '';
		$name = (!empty($_POST['name'])) ? trim($_POST['name']) : '';
		$land = (!empty($_POST['land'])) ? trim($_POST['land']) : '';
		
		$landChecked[0]  = ($land == 'baden-wuerttemberg' ? ' checked' : '');
		$landChecked[1]  = ($land == 'bayern' ? ' checked' : '');
		$landChecked[2]  = ($land == 'berlin' ? ' checked' : '');
		$landChecked[3]  = ($land == 'brandenburg' ? ' checked' : '');
		$landChecked[4]  = ($land == 'bremen' ? ' checked' : '');
		$landChecked[5]  = ($land == 'hamburg' ? ' checked' : '');
		$landChecked[6]  = ($land == 'hessen' ? ' checked' : '');
		$landChecked[7]  = ($land == 'mecklemburg-vorpommern' ? ' checked' : '');
		$landChecked[8]  = ($land == 'niedersachsen' ? ' checked' : '');
		$landChecked[9]  = ($land == 'nordrhein-westfalen' ? ' checked' : '');
		$landChecked[10] = ($land == 'rheinland-pfalz' ? ' checked' : '');
		$landChecked[11] = ($land == 'saarland' ? ' checked' : '');
		$landChecked[12] = ($land == 'sachsen' ? ' checked' : '');
		$landChecked[13] = ($land == 'sachsen-anhalt' ? ' checked' : '');
		$landChecked[14] = ($land == 'schleswig-holstein' ? ' checked' : '');
		$landChecked[15] = ($land == 'thueringen' ? ' checked' : '');
?>

<p>
	<label for="user_art">Nutzungsart:<br>
		<label for="user_art_user">Normale*r Benutzer*in
			<input type="radio" name="user_art" id="user_art_user" class="input" value="user"<?php echo $userArtValue[0]; ?>>
		</label>
		<label for="user_art_landesverband">Landesverband
			<input type="radio" name="user_art" id="user_art_landesverband" class="input" value="landesverband"<?php echo $userArtValue[1]; ?>>
		</label>
		<label for="user_art_basisgruppe">Basisgruppe
			<input type="radio" name="user_art" id="user_art_basisgruppe" class="input" value="basisgruppe"<?php echo $userArtValue[2]; ?>>
		</label>
	</label>
</p>

<p id="first_name_box">
	<label for="first_name">Vorname:<br>
		<input type="text" name="first_name" id="first_name" class="input" value="<?php echo esc_attr(wp_unslash($first_name)); ?>" size="25">
	</label>
</p>

<p id="last_name_box">
	<label for="last_name">Nachname:<br>
		<input type="text" name="last_name" id="last_name" class="input" value="<?php echo esc_attr(wp_unslash($last_name)); ?>" size="25">
	</label>
</p>

<p id="name_box">
	<label for="name">Ort:<br>
		<input type="text" name="name" id="name" class="input" value="<?php echo esc_attr(wp_unslash($name)); ?>" size="25">
	</label>
</p>

<p id="land_box">
	<label for="land">Bundesland:<br>
		<select name="land" id="land">
			<option value="0">--- Bitte auswählen ---</option>
			<option<?php echo $landChecked[0];  ?> value="baden-wuerttemberg">Baden-Württemberg</option>
			<option<?php echo $landChecked[1];  ?> value="bayern">Bayern</option>
			<option<?php echo $landChecked[2];  ?> value="berlin">Berlin</option>
			<option<?php echo $landChecked[3];  ?> value="brandenburg">Brandenburg</option>
			<option<?php echo $landChecked[4];  ?> value="bremen">Bremen</option>
			<option<?php echo $landChecked[5];  ?> value="hamburg">Hamburg</option>
			<option<?php echo $landChecked[6];  ?> value="hessen">Hessen</option>
			<option<?php echo $landChecked[7];  ?> value="mecklemburg-vorpommern">Mecklemburg Vorpommern</option>
			<option<?php echo $landChecked[8];  ?> value="niedersachsen">Niedersachsen</option>
			<option<?php echo $landChecked[9];  ?> value="nordrhein-westfalen">Nordrhein-Westfalen</option>
			<option<?php echo $landChecked[10]; ?> value="rheinland-pfalz">Rheinland-Pfalz</option>
			<option<?php echo $landChecked[11]; ?> value="saarland">Saarland</option>
			<option<?php echo $landChecked[12]; ?> value="sachsen">Sachsen</option>
			<option<?php echo $landChecked[13]; ?> value="sachsen-anhalt">Sachsen-Anhalt</option>
			<option<?php echo $landChecked[14]; ?> value="schleswig-holstein">Schleswig-Holstein</option>
			<option<?php echo $landChecked[15]; ?> value="thueringen">Thüringen</option>
		</select>
	</label>
</p>

<p id="new_user_login">
	
</p>

<script type="text/javascript">
	document.addEventListener("DOMContentLoaded", function() { 
		$("#user_login").prop('readonly', 'true');
		$("#new_user_login").append($("label[for='user_login']"));
		$("label[for='user_login']").html($("label[for='user_login']").html().replace('Benutzername', 'Benutzer*innenname (wird generiert)'));
		
		var userLoginValue = "";
		var userLoginValueTmp = "";
		
		var userArtChange = function() {
			switch($("input[name='user_art']:checked").val()) {
				case 'user':
				default:
					$("#first_name_box").show();
					$("#last_name_box").show();
					$("#name_box").hide();
					$("#land_box").hide();
					break;
				case 'landesverband':
				case 'basisgruppe':
					$("#first_name_box").hide();
					$("#last_name_box").hide();
					$("#name_box").show();
					$("#land_box").show();
					break;
			}
			userNameKeyUp();
			$("#user_login").val(userLoginValue);
		};
		
		var userNameKeyUp = function() {
			switch($("input[name='user_art']:checked").val()) {
				case 'user':
				default:
					userLoginValue = $("#first_name").val() + "." + $("#last_name").val();
					break;
				case 'landesverband':
				case 'basisgruppe':
					userLoginValue = "GrueneJugend" + $("#name").val();
					break;
			}
			
			do {
				userLoginValueTmp = userLoginValue;
				userLoginValue = userLoginValue.replace("ä", "ae");
				userLoginValue = userLoginValue.replace("Ä", "Ae");
				userLoginValue = userLoginValue.replace("ö", "oe");
				userLoginValue = userLoginValue.replace("Ö", "Oe");
				userLoginValue = userLoginValue.replace("ü", "ue");
				userLoginValue = userLoginValue.replace("Ü", "Ue");
				userLoginValue = userLoginValue.replace("ß", "ss");
				userLoginValue = userLoginValue.replace(" ", "");
			} while(userLoginValueTmp !== userLoginValue);
			$("#user_login").val(userLoginValue);
		};
		
		userArtChange();
		
		$("input[name='user_art']:radio").change(function() {
			userArtChange();
		});
		
		$("#first_name").keyup(function() {
			userNameKeyUp();
		});
		
		$("#last_name").keyup(function() {
			userNameKeyUp();
		});
		
		$("#name").keyup(function() {
			userNameKeyUp();
		});
    });
	
	
</script>

<?php
	}
	
	/*
	 * TODO: KEINE ÄNDERUNGEN AN LANDESVERBAND UND ORT
	 */
	public static function new_user_form($user) {
		wp_enqueue_script('jqueryIO');
		wp_nonce_field('io_users', 'io_users_nonce');
		
		$userArtInput = esc_attr(wp_unslash((!empty($_POST['user_art'])) ? trim($_POST['user_art']) : ''));
		switch($userArtInput) {
			case 'User':
				$userArtValue[0] = " checked";
				$userArtValue[1] = "";
				$userArtValue[2] = "";
				break;
			case 'Landesverband':
				$userArtValue[0] = "";
				$userArtValue[1] = " checked";
				$userArtValue[2] = "";
				break;
			case 'Basisgruppe':
				$userArtValue[0] = "";
				$userArtValue[1] = "";
				$userArtValue[2] = " checked";
				break;
			default:
				$userArtValue[0] = " checked";
				$userArtValue[1] = "";
				$userArtValue[2] = "";
				break;
		}
		$name = (!empty($_POST['name'])) ? trim($_POST['name']) : '';
		$land = (!empty($_POST['land'])) ? trim($_POST['land']) : '';
		
		$landChecked[0]  = ($land == 'baden-wuerttemberg' ? ' checked' : '');
		$landChecked[1]  = ($land == 'bayern' ? ' checked' : '');
		$landChecked[2]  = ($land == 'berlin' ? ' checked' : '');
		$landChecked[3]  = ($land == 'brandenburg' ? ' checked' : '');
		$landChecked[4]  = ($land == 'bremen' ? ' checked' : '');
		$landChecked[5]  = ($land == 'hamburg' ? ' checked' : '');
		$landChecked[6]  = ($land == 'hessen' ? ' checked' : '');
		$landChecked[7]  = ($land == 'mecklemburg-vorpommern' ? ' checked' : '');
		$landChecked[8]  = ($land == 'niedersachsen' ? ' checked' : '');
		$landChecked[9]  = ($land == 'nordrhein-westfalen' ? ' checked' : '');
		$landChecked[10] = ($land == 'rheinland-pfalz' ? ' checked' : '');
		$landChecked[11] = ($land == 'saarland' ? ' checked' : '');
		$landChecked[12] = ($land == 'sachsen' ? ' checked' : '');
		$landChecked[13] = ($land == 'sachsen-anhalt' ? ' checked' : '');
		$landChecked[14] = ($land == 'schleswig-holstein' ? ' checked' : '');
		$landChecked[15] = ($land == 'thueringen' ? ' checked' : '');
		
?>

<table class="form-table">
	<tr class="form-field form-required">
		<th scope="row"><label for="user_art">Nutzungsart <span class="description">(erforderlich)</span></label></th>
		<td>
			<input type="radio" name="user_art" id="user_art_user" value="user"<?php echo $userArtValue[0]; ?>> Normale*r Benutzer*in<br>
			<input type="radio" name="user_art" id="user_art_landesverband" value="landesverband"<?php echo $userArtValue[1]; ?>> Landesverband<br>
			<input type="radio" name="user_art" id="user_art_basisgruppe" value="basisgruppe"<?php echo $userArtValue[2]; ?>> Basisgruppe
		</td>
	</tr>
	<tr class="form-field form-required">
		<th scope="row"><label for="name">Ort <span class="description">(erforderlich)</span></label></th>
		<td>
			<input type="text" name="name" id="name" class="input" value="<?php echo esc_attr(wp_unslash($name)); ?>" size="25">
		</td>
	</tr>
	<tr class="form-field form-required">
		<th scope="row"><label for="land">Bundesland <span class="description">(erforderlich)</span></label></th>
		<td>
			<select name="land" id="land">
				<option value="0">--- Bitte auswählen ---</option>
				<option<?php echo $landChecked[0];  ?> value="baden-wuerttemberg">Baden-Württemberg</option>
				<option<?php echo $landChecked[1];  ?> value="bayern">Bayern</option>
				<option<?php echo $landChecked[2];  ?> value="berlin">Berlin</option>
				<option<?php echo $landChecked[3];  ?> value="brandenburg">Brandenburg</option>
				<option<?php echo $landChecked[4];  ?> value="bremen">Bremen</option>
				<option<?php echo $landChecked[5];  ?> value="hamburg">Hamburg</option>
				<option<?php echo $landChecked[6];  ?> value="hessen">Hessen</option>
				<option<?php echo $landChecked[7];  ?> value="mecklemburg-vorpommern">Mecklemburg Vorpommern</option>
				<option<?php echo $landChecked[8];  ?> value="niedersachsen">Niedersachsen</option>
				<option<?php echo $landChecked[9];  ?> value="nordrhein-westfalen">Nordrhein-Westfalen</option>
				<option<?php echo $landChecked[10]; ?> value="rheinland-pfalz">Rheinland-Pfalz</option>
				<option<?php echo $landChecked[11]; ?> value="saarland">Saarland</option>
				<option<?php echo $landChecked[12]; ?> value="sachsen">Sachsen</option>
				<option<?php echo $landChecked[13]; ?> value="sachsen-anhalt">Sachsen-Anhalt</option>
				<option<?php echo $landChecked[14]; ?> value="schleswig-holstein">Schleswig-Holstein</option>
				<option<?php echo $landChecked[15]; ?> value="thueringen">Thüringen</option>
			</select>
		</td>
	</tr>
	<tr class="form-field form-required">
		<th scope="row"><label for="groups">Gruppenmitgliedschaften</label></th>
		<td>
			<select name="groups" id="groups" size="10" multiple>
	<?php

		$values = io_groups::getValues();
		foreach($values AS $key_1 => $value_1) {
			?>				<optgroup label="<?php echo get_option('io_grp_ok_' . $key_1); ?>">
		<?php
			if(is_array($value_1)) {
				foreach($value_1 AS $key_2 => $value_2) {
					?>					<optgroup label="<?php echo get_option('io_grp_uk_' . $key_2); ?>">
					<?php

						foreach($value_2 AS $key_3 => $value_3) {
							?>						<option value="<?php echo $key_3; ?>"><?php echo $value_3; ?></option><?php
						}

					?>					</optgroup>
					<?php
				}
			} else {
				foreach($value_1 AS $key_2 => $value_2) {
					?>					<option value="<?php echo $key_2; ?>"><?php echo $value_2; ?></option><?php
				}
			}
		?>
					</optgroup>
		<?php 

		}

	?>
			</select>
		</td>
	</tr>
</table>

<script type="text/javascript">
	document.addEventListener("DOMContentLoaded", function() { 
		$(".form-field:eq(4)").hide();
		$(".form-field:eq(3)").after($(".form-field:eq(0)"));
		$(".form-field:eq(0)").after($(".form-field:eq(8)"));
		$(".form-field:eq(3)").after($(".form-field:eq(9)"));
		$(".form-field:eq(4)").after($(".form-field:eq(10)"));
		$(".form-field:eq(2)").addClass("form-required");
		$(".form-field:eq(3)").addClass("form-required");
		$("#user_login").prop('readonly', 'true');
		$("label[for='user_login']").html($("label[for='user_login']").html().replace('(erforderlich)', '(wird generiert)'));
		$("label[for='first_name']").html($("label[for='first_name']").html().replace('Vorname ', 'Vorname <span class="description">(erforderlich)</span>'));
		$("label[for='last_name']").html($("label[for='last_name']").html().replace('Nachname ', 'Nachname <span class="description">(erforderlich)</span>'));
		
		var userLoginValue = "";
		var userLoginValueTmp = "";
		
		var userArtChange = function() {
			switch($("input[name='user_art']:checked").val()) {
				case 'user':
				default:
					$(".form-field:eq(2)").show();
					$(".form-field:eq(3)").show();
					$(".form-field:eq(4)").hide();
					$(".form-field:eq(5)").hide();
					break;
				case 'landesverband':
				case 'basisgruppe':
					$(".form-field:eq(2)").hide();
					$(".form-field:eq(3)").hide();
					$(".form-field:eq(4)").show();
					$(".form-field:eq(5)").show();
					break;
			}
			userNameKeyUp();
			$("#user_login").val(userLoginValue);
		};
		
		var userNameKeyUp = function() {
			switch($("input[name='user_art']:checked").val()) {
				case 'user':
				default:
					userLoginValue = $("#first_name").val() + "." + $("#last_name").val();
					break;
				case 'landesverband':
				case 'basisgruppe':
					userLoginValue = "GrueneJugend" + $("#name").val();
					break;
			}
			
			do {
				userLoginValueTmp = userLoginValue;
				userLoginValue = userLoginValue.replace("ä", "ae");
				userLoginValue = userLoginValue.replace("Ä", "Ae");
				userLoginValue = userLoginValue.replace("ö", "oe");
				userLoginValue = userLoginValue.replace("Ö", "Oe");
				userLoginValue = userLoginValue.replace("ü", "ue");
				userLoginValue = userLoginValue.replace("Ü", "Ue");
				userLoginValue = userLoginValue.replace("ß", "ss");
				userLoginValue = userLoginValue.replace(" ", "");
			} while(userLoginValueTmp !== userLoginValue);
			$("#user_login").val(userLoginValue);
		};
		
		userArtChange();
		
		$("input[name='user_art']:radio").change(function() {
			userArtChange();
		});
		
		$("#first_name").keyup(function() {
			userNameKeyUp();
		});
		
		$("#last_name").keyup(function() {
			userNameKeyUp();
		});
		
		$("#name").keyup(function() {
			userNameKeyUp();
		});
	});
</script>

<?php
	}
	
	public static function register_error($errors) {
		if(empty($_POST['user_art'])) {
			$errors->add('user_art_error', '<strong>FEHLER:</strong> Du musst eine Nutzungsart angeben!');
		} else if($_POST['user_art'] == "user" && (empty($_POST['first_name']) || empty($_POST['last_name']))) {
			if(empty($_POST['first_name'])) {
				$errors->add('first_name_error', '<strong>FEHLER:</strong> Du musst einen Vornamen angeben!');
			}
			
			if(empty($_POST['last_name'])) {
				$errors->add('last_name_error', '<strong>FEHLER:</strong> Du musst einen Nachnamen angeben!');
			}
		} else if($_POST['user_art'] != "user" && (empty($_POST['name']) || $_POST['land'] == 0)) {
			if(empty($_POST['name'])) {
				$errors->add('name_error', '<strong>FEHLER:</strong> Du musst einen Ortsnamen angeben!');
			}
			
			if(empty($_POST['land'])) {
				$errors->add('land_error', '<strong>FEHLER:</strong> Du musst ein Bundesland angeben!');
			}
		}
		
		return $errors;
	}
	
	public static function user_register($user_id) {
		if(!empty($_POST['user_art'])) {
			if( !isset($_POST['io_users_nonce']) || 
				!wp_verify_nonce($_POST['io_users_nonce'], 'io_users') || 
				defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
				return;
			}
			
			update_user_meta($user_id, "user_art", trim($_POST['user_art']));
			update_user_meta($user_id, "user_aktiv", 1);
			if($_POST['user_art'] == "user") {
				update_user_meta($user_id, "first_name", trim($_POST['first_name']));
				update_user_meta($user_id, "last_name", trim($_POST['last_name']));
			} else {
				update_user_meta($user_id, "ort", trim($_POST['name']));
				update_user_meta($user_id, "land", trim($_POST['land']));
			}
		}
	}
	
	/***********************************************************
	 ***********************  User List  ***********************
	 ***********************************************************/
	public static function user_column($columns) {
		$columns['user_art'] = 'Nutzungsart';
		$columns['user_aktiv'] = 'User Aktiv';
		unset($columns['name']);
		unset($columns['role']);
		unset($columns['posts']);
		return $columns;
	}
	
	public static function user_column_value($value, $column_name, $user_id) {
		if($column_name == 'user_aktiv') {
			switch(get_user_meta($user_id, 'user_aktiv', true)) {
				case 0:
					return 'Ja';
				case 1:
					return 'Nein';
			}
		} else if($column_name == 'user_art') {
			switch(get_user_meta($user_id, 'user_art', true)) {
				case 'basisgruppe':
					return 'Basisgruppe (' . get_user_meta($user_id, 'land', true) . ')';
				case 'landesverband':
					return 'Landesverband (' . get_user_meta($user_id, 'land', true) . ')';
				default:
					return 'User';
			}
		}
		return $value;
	}
	
	public static function user_order($query) {
		if($query->query_vars['orderby'] == "UserAktiv") {
			global $wpdb;
			$query->query_from .= " LEFT OUTER JOIN $wpdb->usermeta AS um ON $wpdb->users.ID = um.user_id ";
			$query->query_where .= " AND um.meta_key = 'user_aktiv' AND um.meta_value = 1 ";
			$query->query_orderby = " ORDER BY um.meta_value ".($query->query_vars["order"] == "ASC" ? "asc " : "desc ");
		} else if($query->query_vars['orderby'] == "Nutzungsart") {
			global $wpdb;
			$query->query_from .= " LEFT OUTER JOIN $wpdb->usermeta AS um ON $wpdb->users.ID = um.user_id ";
			$query->query_where .= " AND um.meta_key = 'user_art' ";
			$query->query_orderby = " ORDER BY um.meta_value ".($query->query_vars["order"] == "ASC" ? "asc " : "desc ");
		}
		return $query;
	}
	
	public static function user_options($actions, $user) {
		if(get_user_meta($user->ID, "user_aktiv", true) == 1) {
			$actions['Aktivieren'] = '<a href="user-edit.php?user_id=' . $user->ID . '&user_aktiv=true">Aktivieren</a>';
		}
		return $actions;
	}
	
	/***********************************************************
	 *************************  Menu  **************************
	 ***********************************************************/
	public static function user_menu() {
		if(is_admin()) {
			$users = get_users(array(
				'meta_key'		=> 'user_aktiv',
				'meta_value'	=> 1
			));
			
			if(count($users) > 0) {
				global $menu;
				foreach($menu AS $key => $value) {
					if($menu[$key][2] == "users.php") {
						$menu[$key][0] = $menu[$key][0] . ' <span class="update-plugins ' . count($users) . '"><span class="plugin-count">' . count($users) . '</span></span>';

						return;
					}
				}
			}
		}
	}
	
	/***********************************************************
	 *******************  User Aktivierung  ********************
	 ***********************************************************/
	public static function user_profile($user) {
		$user_id = $user->ID;
		
		if(is_admin() && isset($_GET['user_aktiv']) && $_GET['user_aktiv'] == true && get_user_meta($user_id, 'user_aktiv', true) != 0) {
			update_user_meta($user_id, "user_aktiv", 0);
			user_ldap_add($user_id);
		}
?>

<table class="form-table">
	<tr>
		<th><label for="user_aktiv">User aktiviert</label></th>
		<td><?php

		if(get_user_meta($user_id, "user_aktiv", true) == 1) {
			?><input type="checkbox" name="user_aktiv" id="user_aktiv" value="0"><?php
		} else {
			?><div id="user_aktiv">Aktiv</div><?php
		}

		?></td>
	</tr>
</table>

<?php
	}
	
	/*
	 * TODO: SPEICHERUNG GRUPPENMITGLIEDSCHAFT
	 */
	public static function user_profile_save($user_id) {
		if(isset($_POST["user_aktiv"]) && $_POST["user_aktiv"] == 0 && get_user_meta($user_id, 'user_aktiv', true) != 0) {
			update_user_meta($user_id, "user_aktiv", 0);
			user_ldap_add($user_id);
		}
	}
	
	public static function authentifizierung($user, $user_name) {
		$user_id = $user->ID;
		
		if(get_user_meta($user_id, "user_aktiv", true) == 1) {
			return new WP_Error("user_inaktiv", "<strong>FEHLER:</strong> Dein Account wurde noch nicht aktiviert!");
		}
		return $user;
	}
	
	public static function user_ldap_add($user_id) {
		$ldapConn = new ldapConnector();
		$ldapConn->addUser(get_post_meta($user_id, 'first_user', true), get_post_meta($post_id, 'last_user', true));
		$ldapConn->setUserAttribute(get_userdata($user_id)->display_name, "user_art", get_post_meta($post_id, 'user_art', true));
		
		if(get_user_meta($user_id, 'user_art', true) == 'Basisgruppe' || get_user_meta($user_id, 'user_art', true) == 'Landesverband') {
			$ldapConn->setUserAttribute(get_userdata($user_id)->display_name, "user_land", get_post_meta($post_id, 'land', true));
		}
		
		if(get_user_meta($user_id, 'user_art', true) == 'Basisgruppe') {
			$ldapConn->setUserAttribute(get_userdata($user_id)->display_name, "user_ort", get_post_meta($post_id, 'ort', true));
		}
	}
}
