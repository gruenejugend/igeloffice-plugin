<?php

/**
 * //TODO: Berechtigungszuordnung (TESTEN!!!)
 * //TODO: Gruppenzuordnung (TESTEN!!!)
 * //TODO: Technischer User (TESTEN!!!)
 * //TODO: Koordinaten auf Karte (TESTEN!!!)
 * //TODO: Webseite wieder mit rein (TESTEN!!!)
 */

/**
 * Description of io_user
 *
 * @author KWM
 */
class io_user {
	private $login;
	private $art;
	private $aktiv;
	private $first_name;
	private $last_name;
	private $orga_name;
	private $landesverband;
	private $ort;
	private $groups;
	private $groupsID;
	private $permissions;
	private $permissionsID;
	
	public function __construct($user, $load = false) {
		$id						= $user->ID;
		$this->login			= $user->user_login;
		$this->art				= get_user_meta($id, "user_art", true);
		$this->aktiv			= get_user_meta($id, "user_aktiv", true);
		$this->first_name		= get_user_meta($id, "first_name", true);
		$this->last_name		= get_user_meta($id, "last_name", true);
		$this->orga_name		= get_user_meta($id, "orga_name", true);
		$this->landesverband	= get_user_meta($id, "landesverband", true);
		$this->ort				= get_user_meta($id, "ort", true);
		
		if($load == true) {
			$ldapConn = ldapConnector::get();
			$this->groups			= $ldapConn->getUserGroups($this->login);
			$this->groupsID			= array();
			foreach($this->groups AS $groups) {
				$groups = explode(",", substr($groups, 3))[0];
				$post = get_post(array('post_title' => $groups));
				
				$post_id = (isset($post)) ? $post->ID : false;
				
				if($post_id) {
					$this->groupsID[$post_id] = $post_id;
				}
			}
			
			$this->permissions		= $ldapConn->getUserPermissions($this->login);
			$this->permissionsID	= array();
			foreach($this->permissions AS $permissions) {
				$permissions = explode(",", substr($permissions, 3))[0];
				$post = get_post(array('post_title' => $permissions));
				
				$post_id = (isset($post)) ? $post->ID : false;
				
				if($post_id) {
					$this->permissionsID[$post_id] = $post_id;
				}
			}
		}
	}
	
	public function __get($name) {
		switch($name) {
			case 'login':
				return $this->login;
			case 'art':
				return $this->art;
			case 'aktiv':
				return $this->aktiv;
			case 'first_name':
				return $this->first_name;
			case 'last_name':
				return $this->last_name;
			case 'orga_name':
				return $this->orga_name;
			case 'landesverband':
				return $this->landesverband;
			case 'ort':
				return $this->ort;
			case 'groups':
				return $this->groups;
			case 'groupsID':
				return $this->groupsID;
			case 'permissions':
				return $this->permissions;
			case 'permissionsID':
				return $this->permissionsID;
		}
	}
	
	/***********************************************************
	 ***********************  Formulare  ***********************
	 ***********************************************************/
	//TODO: Auch hier Gruppen- und Berechtigungsanfragen
	public static function register_form() {
		wp_enqueue_script('jqueryIO');
		wp_nonce_field('io_users', 'io_users_nonce');
		
		$userArtInput = esc_attr(wp_unslash((!empty($_POST['user_art'])) ? sanitize_text_field($_POST['user_art']) : ''));
		switch($userArtInput) {
			case 'User':
				$userArtValue[0] = " checked";
				$userArtValue[1] = "";
				$userArtValue[2] = "";
				$userArtValue[3] = "";
				break;
			case 'Landesverband':
				$userArtValue[0] = "";
				$userArtValue[1] = " checked";
				$userArtValue[2] = "";
				$userArtValue[3] = "";
				break;
			case 'Basisgruppe':
				$userArtValue[0] = "";
				$userArtValue[1] = "";
				$userArtValue[2] = " checked";
				$userArtValue[3] = "";
				break;
			case 'Organisatorisch':
				$userArtValue[0] = "";
				$userArtValue[1] = "";
				$userArtValue[2] = "";
				$userArtValue[3] = " checked";
				break;
			default:
				$userArtValue[0] = " checked";
				$userArtValue[1] = "";
				$userArtValue[2] = "";
				$userArtValue[3] = "";
				break;
		}
		$first_name = (!empty($_POST['first_name'])) ? sanitize_text_field($_POST['first_name']) : '';
		$last_name = (!empty($_POST['last_name'])) ? sanitize_text_field($_POST['last_name']) : '';
		$orga_name = (!empty($_POST['orga_name'])) ? sanitize_text_field($_POST['orga_name']) : '';
		$name = (!empty($_POST['name'])) ? sanitize_text_field($_POST['name']) : '';
		$land = (!empty($_POST['land'])) ? sanitize_text_field($_POST['land']) : '';
		
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
		<label for="user_art_basisgruppe">Organisatorische*r Benutzer*in
			<input type="radio" name="user_art" id="user_art_organisatorisch" class="input" value="organisatorisch"<?php echo $userArtValue[3]; ?>>
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

<p id="orga_name_box">
	<label for="last_name">Name:<br>
		<input type="text" name="orga_name" id="orga_name" class="input" value="<?php echo esc_attr(wp_unslash($orga_name)); ?>" size="25">
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
					$("#orga_name_box").hide();
					$("#name_box").hide();
					$("#land_box").hide();
					break;
				case 'landesverband':
					$("#first_name_box").hide();
					$("#last_name_box").hide();
					$("#orga_name_box").hide();
					$("#name_box").hide();
					$("#land_box").show();
					break;
				case 'basisgruppe':
					$("#first_name_box").hide();
					$("#last_name_box").hide();
					$("#orga_name_box").hide();
					$("#name_box").show();
					$("#land_box").show();
					break;
				case 'organisatorisch':
					$("#first_name_box").hide();
					$("#last_name_box").hide();
					$("#orga_name_box").show();
					$("#name_box").hide();
					$("#land_box").hide();
					break
			}
			userNameKeyUp();
			$("#user_login").val(userLoginValue);
		};
		
		var userNameKeyUp = function() {
			switch($("#land").val()) {
				case 'baden-wuerttemberg':
					landKurz = 'BW';
					break;
				case 'bayern':
					landKurz = 'BY';
					break;
				case 'berlin':
					landKurz = 'BE';
					break;
				case 'brandenburg':
					landKurz = 'BB';
					break;
				case 'bremen':
					landKurz = 'HB';
					break;
				case 'hamburg':
					landKurz = 'HH';
					break;
				case 'hessen':
					landKurz = 'HE';
					break;
				case 'mecklemburg-vorpommern':
					landKurz = 'MV';
					break;
				case 'niedersachsen':
					landKurz = 'NI';
					break;
				case 'nordrhein-westfalen':
					landKurz = 'NW';
					break;
				case 'rheinland-pfalz':
					landKurz = 'RP';
					break;
				case 'schleswig-holstein':
					landKurz = 'SH';
					break;
				case 'saarland':
					landKurz = 'SL';
					break;
				case 'sachsen':
					landKurz = 'SN';
					break;
				case 'sachsen-anhalt':
					landKurz = 'ST';
					break;
				case 'thueringen':
					landKurz = 'TH';
					break;
				default:
					landKurz = '';
					break;
			}
			
			switch($("input[name='user_art']:checked").val()) {
				case 'user':
				default:
					userLoginValue = $("#first_name").val() + " " + $("#last_name").val();
					break;
				case 'landesverband':
					userLoginValue = landKurz;
					break;
				case 'basisgruppe':
					userLoginValue = $("#name").val();
					break;
				case 'organisatorisch':
					userLoginValue = $("#orga_name").val();
					break;
			}
			
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
		
		$("#orga_name").keyup(function() {
			userNameKeyUp();
		});
		
		$("#name").keyup(function() {
			userNameKeyUp();
		});
		
		$("#land").change(function() {
			userNameKeyUp();
		});
    });
	
	
</script>

<?php
	}
	
	/*
	 * TODO: KEINE Ã„NDERUNGEN AN LANDESVERBAND UND ORT
	 */
	public static function new_user_form($user) {
		wp_enqueue_script('jqueryIO');
		wp_nonce_field('io_users', 'io_users_nonce');
		
		$userArtInput = esc_attr(wp_unslash((!empty($_POST['user_art'])) ? sanitize_text_field($_POST['user_art']) : ''));
		switch($userArtInput) {
			case 'User':
				$userArtValue[0] = " checked";
				$userArtValue[1] = "";
				$userArtValue[2] = "";
				$userArtValue[3] = "";
				break;
			case 'Landesverband':
				$userArtValue[0] = "";
				$userArtValue[1] = " checked";
				$userArtValue[2] = "";
				$userArtValue[3] = "";
				break;
			case 'Basisgruppe':
				$userArtValue[0] = "";
				$userArtValue[1] = "";
				$userArtValue[2] = " checked";
				$userArtValue[3] = "";
				break;
			case 'Organisatorisch':
				$userArtValue[0] = "";
				$userArtValue[1] = "";
				$userArtValue[2] = "";
				$userArtValue[3] = " checked";
				break;
			default:
				$userArtValue[0] = " checked";
				$userArtValue[1] = "";
				$userArtValue[2] = "";
				$userArtValue[3] = "";
				break;
		}
		$orga_name = (!empty($_POST['orga_name'])) ? sanitize_text_field($_POST['orga_name']) : '';
		$name = (!empty($_POST['name'])) ? sanitize_text_field($_POST['name']) : '';
		$geo = (!empty($_POST['geo'])) ? sanitize_text_field($_POST['geo']) : '';
		$land = (!empty($_POST['land'])) ? sanitize_text_field($_POST['land']) : '';
		
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
		
		
		if(has_action('admin_notices', 'io_request_notice')) {
			remove_action('admin_notices', 'io_request_notice');
		}
?>

<table class="form-table">
	<tr class="form-field form-required">
		<th scope="row"><label for="user_art">Nutzungsart <span class="description">(erforderlich)</span></label></th>
		<td>
			<input type="radio" name="user_art" id="user_art_user" value="user"<?php echo $userArtValue[0]; ?>> Normale*r Benutzer*in<br>
			<input type="radio" name="user_art" id="user_art_landesverband" value="landesverband"<?php echo $userArtValue[1]; ?>> Landesverband<br>
			<input type="radio" name="user_art" id="user_art_basisgruppe" value="basisgruppe"<?php echo $userArtValue[2]; ?>> Basisgruppe<br>
			<input type="radio" name="user_art" id="user_art_organisatorisch" value="organisatorisch"<?php echo $userArtValue[3]; ?>> Organisatorische*r Benutzer*in
		</td>
	</tr>
	<tr class="form-field form-required">
		<th scope="row"><label for="name">Ort <span class="description">(erforderlich)</span></label></th>
		<td>
			<input type="text" name="name" id="name" class="input" value="<?php echo esc_attr(wp_unslash($name)); ?>" size="25">
		</td>
	</tr>
	<tr class="form-field form-required">
		<th scope="row"><label for="name">Name <span class="description">(erforderlich)</span></label></th>
		<td>
			<input type="text" name="orga_name" id="orga_name" class="input" value="<?php echo esc_attr(wp_unslash($orga_name)); ?>" size="25">
		</td>
	</tr>
	<tr class="form-field form-required">
		<th scope="row"><label for="land">Bundesland <span class="description">(erforderlich)</span></label></th>
		<td>
			<select name="land" id="land">
				<option value="0">--- Bitte auswählen ---</option>
				<option<?php echo $landChecked[0];  ?> value="baden-wuerttemberg">Baden-W&uuml;rttemberg</option>
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
				<option<?php echo $landChecked[15]; ?> value="thueringen">Th&uuml;ringen</option>
			</select>
		</td>
	</tr>
	<tr class="form-field">
		<th scope="row"><label for="name">Ort Geo-Koordinaten <span class="description">(erforderlich, bei Google Maps ermittelbar)</span></label></th>
		<td>
			<input type="text" name="geo" id="geo" class="input" value="<?php echo esc_attr(wp_unslash($geo)); ?>" size="25">
		</td>
	</tr>
	<tr class="form-field">
		<th scope="row"><label for="groups">Gruppenmitgliedschaften</label></th>
		<td>
			<select name="groups[]" id="groups" size="10" multiple>
		<?php

			if(count(io_groups::getValues()) > 0) {
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
			}
			
			?>
			</select>
		</td>
	</tr>
	<tr class="form-field">
		<th scope="row"><label for="permissions">Berechtigungen</label></th>
		<td>
			<select name="permissions[]" id="permissions" size="10" multiple>
		<?php

			if(count(io_permission::getValues()) > 0) {
				$values = io_permission::getValues();
				foreach($values AS $key_1 => $value_1) {
					?>				<optgroup label="<?php echo $key_1; ?>">
				<?php
					foreach($value_1 AS $key_2 => $value_2) {
						?>					<option value="<?php echo $key_2; ?>"><?php echo $value_2; ?></option>
	<?php
					}
				?>
							</optgroup>
				<?php 

				}
			}

		?>
			</select>
		</td>
	</tr>
</table>

<script type="text/javascript">
	document.addEventListener("DOMContentLoaded", function() {
		$(".form-field:eq(0)").before($(".form-field:eq(1)"));
		$(".form-field:eq(1)").before($(".form-field:eq(8)"));
		$(".form-field:eq(2)").before($(".form-field:eq(3)"));
		$(".form-field:eq(3)").before($(".form-field:eq(4)"));
		$(".form-field:eq(4)").before($(".form-field:eq(9)"));
		$(".form-field:eq(5)").before($(".form-field:eq(10)"));
		$(".form-field:eq(6)").before($(".form-field:eq(11)"));
		$(".form-field:eq(8)").before($(".form-field:eq(12)"));
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
					$(".form-field:eq(6)").hide();
					$(".form-field:eq(8)").hide();
					break;
				case 'landesverband':
					$(".form-field:eq(2)").hide();
					$(".form-field:eq(3)").hide();
					$(".form-field:eq(4)").hide();
					$(".form-field:eq(5)").hide();
					$(".form-field:eq(6)").show();
					$(".form-field:eq(8)").show();
					break
				case 'basisgruppe':
					$(".form-field:eq(2)").hide();
					$(".form-field:eq(3)").hide();
					$(".form-field:eq(4)").show();
					$(".form-field:eq(5)").hide();
					$(".form-field:eq(6)").show();
					$(".form-field:eq(8)").show();
					break;
				case 'organisatorisch':
					$(".form-field:eq(2)").hide();
					$(".form-field:eq(3)").hide();
					$(".form-field:eq(4)").hide();
					$(".form-field:eq(5)").show();
					$(".form-field:eq(6)").hide();
					$(".form-field:eq(8)").hide();
					break;
			}
			userNameKeyUp();
			$("#user_login").val(userLoginValue);
		};
		
		var userNameKeyUp = function() {
			switch($("#land").val()) {
				case 'baden-wuerttemberg':
					landKurz = 'BW';
					break;
				case 'bayern':
					landKurz = 'BY';
					break;
				case 'berlin':
					landKurz = 'BE';
					break;
				case 'brandenburg':
					landKurz = 'BB';
					break;
				case 'bremen':
					landKurz = 'HB';
					break;
				case 'hamburg':
					landKurz = 'HH';
					break;
				case 'hessen':
					landKurz = 'HE';
					break;
				case 'mecklemburg-vorpommern':
					landKurz = 'MV';
					break;
				case 'niedersachsen':
					landKurz = 'NI';
					break;
				case 'nordrhein-westfalen':
					landKurz = 'NW';
					break;
				case 'rheinland-pfalz':
					landKurz = 'RP';
					break;
				case 'schleswig-holstein':
					landKurz = 'SH';
					break;
				case 'saarland':
					landKurz = 'SL';
					break;
				case 'sachsen':
					landKurz = 'SN';
					break;
				case 'sachsen-anhalt':
					landKurz = 'ST';
					break;
				case 'thueringen':
					landKurz = 'TH';
					break;
				default:
					landKurz = '';
					break;
			}
			
			switch($("input[name='user_art']:checked").val()) {
				case 'user':
				default:
					userLoginValue = $("#first_name").val() + " " + $("#last_name").val();
					break;
				case 'landesverband':
					userLoginValue = landKurz;
					break;
				case 'basisgruppe':
					userLoginValue = $("#name").val();
					break;
				case 'organisatorisch':
					userLoginValue = $("#orga_name").val();
					break;
			}
			
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
		
		$("#orga_name").keyup(function() {
			userNameKeyUp();
		});
		
		$("#name").keyup(function() {
			userNameKeyUp();
		});
		
		$("#land").change(function() {
			userNameKeyUp();
		});
	});
</script>

<?php
	}
	
	public static function register_error($errors) {
		/*
		 * Workaround
		$ldapConn = ldapConnector::get();
		if(	$ldapConn->isLDAPUser(sanitize_text_field($_POST['user_login'])) && 
			$ldapConn->getUserAttribute(sanitize_text_field($_POST['user_login']), 'mail') != $_POST['user_email'] &&
			$ldapConn->getUserAttribute(sanitize_text_field($_POST['user_login']), 'mailAlternateAddress') != $_POST['user_email']) {
			$errors->add('user_ldap_error', '<strong>FEHLER:</strong> Du bist bereits in unserem System registriert, allerdings stimmt die E-Mail-Adress nicht!');
		}
		*/
		if(empty($_POST['user_art'])) {
			$errors->add('user_art_error', '<strong>FEHLER:</strong> Du musst eine Nutzungsart angeben!');
		} else if($_POST['user_art'] == "user" && (empty($_POST['first_name']) || empty($_POST['last_name']))) {
			if(empty($_POST['first_name'])) {
				$errors->add('first_name_error', '<strong>FEHLER:</strong> Du musst einen Vornamen angeben!');
			}
			
			if(empty($_POST['last_name'])) {
				$errors->add('last_name_error', '<strong>FEHLER:</strong> Du musst einen Nachnamen angeben!');
			}
		} else if(($_POST['user_art'] == "landesverband" || $_POST['user_art'] == "basisgruppe") && (empty($_POST['name']) || $_POST['land'] == 0)) {
			if(empty($_POST['name'])) {
				$errors->add('name_error', '<strong>FEHLER:</strong> Du musst einen Ortsnamen angeben!');
			}
			
			if(empty($_POST['land'])) {
				$errors->add('land_error', '<strong>FEHLER:</strong> Du musst ein Bundesland angeben!');
			}
		} else if($_POST['user_art'] == "organisatorisch" && empty($_POST['orga_name'])) {
			$errors->add('orga_error', '<strong>FEHLER:</strong> Du musst einen Namen angeben!');
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
			
			update_user_meta($user_id, "user_art", sanitize_text_field($_POST['user_art']));
			
			/*
			 * Problem: Wenn niemand eingeloggt ist, ist niemand berechtigt zu schauen, ob der*die neu registrierte Benutzer*in berechtigt ist nachzuschauen,
			 * ob er*sie bereits im LDAP vorhanden ist.
			 * 
			 * Lösung des Problems suchen!
			 * 
			 * Workaround: Alle sind nicht aktiv und alle müssen aktiviert werden. Bei Aktivierung wird geprüft, ob User da ist
			 */
//			$ldapConn = ldapConnector::get();
//			if($ldapConn->isLDAPUser(get_userdata($user_id)->user_login) && (
//					$ldapConn->getUserAttribute(get_userdata($user_id)->user_login, 'mail') == get_userdata($user_id)->user_email ||
//					$ldapConn->getUserAttribute(get_userdata($user_id)->user_login, 'mailAlternateAddress') == get_userdata($user_id)->user_email)
//			) {
//				update_user_meta($user_id, "user_aktiv", 0);
//				self::user_ldap_add($user_id);
//			} else {
//				update_user_meta($user_id, "user_aktiv", 1);
//			}
			
			//Workaround Lösung Start
			update_user_meta($user_id, "user_aktiv", 1);
			//Workaround Lösung Ende
			
			if($_POST['user_art'] == "user") {
				update_user_meta($user_id, "first_name", sanitize_text_field($_POST['first_name']));
				update_user_meta($user_id, "last_name", sanitize_text_field($_POST['last_name']));
			} elseif($_POST['user_art'] == "landesverband") {
				update_user_meta($user_id, "land", sanitize_text_field($_POST['land']));
				update_user_meta($user_id, "geo", sanitize_text_field($_POST['geo']));
			} elseif($_POST['user_art'] == "basisgruppe") {
				update_user_meta($user_id, "ort", sanitize_text_field($_POST['name']));
				update_user_meta($user_id, "land", sanitize_text_field($_POST['land']));
				update_user_meta($user_id, "geo", sanitize_text_field($_POST['geo']));
			} elseif($_POST['user_art'] == "organisatorisch") {
				update_user_meta($user_id, "orga_name", sanitize_text_field($_POST['orga_name']));
			}
		}
	}
	
	function user_register_msg($errors, $redirect_to) {
		if(isset( $errors->errors['registered'])) {
			$needle = __('Registrierung vollständig. Bitte schau in dein E-Mail-Postfach.');
			foreach( $errors->errors['registered'] as $index => $msg ) {
				if( $msg === $needle ) {
					$errors->errors['registered'][$index] = 'Registrierung vollständig. Bitte warte auf deine Aktivierung. Du wirst via Mail benachrichtigt.';
				}
			}
		}

		return $errors;
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
				case 'organisatorisch':
					return 'Organisatorisch';
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
		wp_enqueue_script('jqueryIO');
		wp_nonce_field('io_users', 'io_users_nonce');
		
		$user = new io_user($user, true);
		
?>
<table class="form-table">
	<tr class="form-field">
		<th scope="row"><label for="groups">Gruppenmitgliedschaften</label></th>
		<td>
			<select name="groups[]" id="groups" size="10" multiple>
		<?php

			if(count(io_groups::getValues()) > 0) {
				$values = io_groups::getValues();
				foreach($values AS $key_1 => $value_1) {
					?>				<optgroup label="<?php echo get_option('io_grp_ok_' . $key_1); ?>">
				<?php
					if(is_array($value_1)) {
						foreach($value_1 AS $key_2 => $value_2) {
							?>					<optgroup label="&nbsp;&nbsp;&nbsp;&nbsp;<?php echo get_option('io_grp_uk_' . $key_2); ?>">
							<?php

								foreach($value_2 AS $key_3 => $value_3) {
									?>						<option value="<?php echo $key_3; ?>"<?php echo isset($user->groupsID[$key_3]) ? " selected" : ""; ?>>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $value_3; ?></option><?php
								}

							?>					</optgroup>
							<?php
						}
					} else {
						foreach($value_1 AS $key_2 => $value_2) {
							?>					<option value="<?php echo $key_2; ?>"<?php echo isset($user->groupsID[$key_2]) ? " selected" : ""; ?>><?php echo $value_2; ?></option><?php
						}
					}
					
			?>
				</optgroup>
		<?php
		
				}
			}
			
			?>
			</select>
		</td>
	</tr>
	<tr class="form-field">
		<th scope="row"><label for="permissions">Berechtigungen</label></th>
		<td>
			<select name="permissions[]" id="permissions" size="10" multiple>
		<?php

			if(count(io_permission::getValues()) > 0) {
				$values = io_permission::getValues();
				foreach($values AS $key_1 => $value_1) {
					?>				<optgroup label="<?php echo $key_1; ?>">
				<?php
					foreach($value_1 AS $key_2 => $value_2) {
						?>					<option value="<?php echo $key_2; ?>"><?php echo $value_2; ?></option>
	<?php
					}
				?>
							</optgroup>
				<?php 

				}
			}

		?>
			</select>
		</td>
	</tr>
<?php
		if(is_admin()) {
			$user_id = $user->ID;

			if(is_admin() && isset($_GET['user_aktiv']) && $_GET['user_aktiv'] == true && get_user_meta($user_id, 'user_aktiv', true) != 0) {
				update_user_meta($user_id, "user_aktiv", 0);
				self::user_ldap_add($user_id);
			}
?>
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
<?php
		}
?>
</table>

<script type="text/javascript">
	document.addEventListener("DOMContentLoaded", function() { 
		$("#first_name").prop('readonly', 'true');
		$("#last_name").prop('readonly', 'true');
		$(".user-nickname-wrap").hide();
		$(".user-display-name-wrap").hide();
		$(".user-url-wrap").hide();
		$(".user-description-wrap").hide();
	});
</script>

<?php
	}
	
	public static function user_profile_save($user_id) {
		if(isset($_POST["user_aktiv"]) && $_POST["user_aktiv"] == 0 && get_user_meta($user_id, 'user_aktiv', true) != 0 && is_admin()) {
			if( !isset($_POST['io_users_nonce']) || 
				!wp_verify_nonce($_POST['io_users_nonce'], 'io_users') || 
				defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
				return;
			}
			
			update_user_meta($user_id, "user_aktiv", 0);
			self::user_ldap_add($user_id);
		}
		
		$ldapConn = ldapConnector::get();
		$io_user = new io_user(get_userdata($user_id));
		
		$neueGruppen = array_diff($_POST['groups'], $io_user->groupsID);
		$alteGruppen = array_diff($io_user->groupsID, $_POST['groups']);
		
		$neuePermissions = array_diff($_POST['permissions'], $io_user->permissionsID);
		$altePermissions = array_diff($io_user->permissionsID, $_POST['permissions']);
		
		foreach($alteGruppen AS $gruppe) {
			$ldapConn->delUserToGroup(array($io_user->login), get_the_title($gruppe));
		}
		
		foreach($altePermissions AS $permission) {
			$ldapConn->delUserPermission(array($io_user->login), get_the_title($permission));
		}

		if(is_admin()) {
			foreach($neueGruppen AS $gruppe) {
				$ldapConn->addUsersToGroup(array($io_user->login), get_the_title($gruppe));
			}

			foreach($neuePermissions AS $permission) {
				$ldapConn->addUserPermission(array($io_user->login), get_the_title($permission));
			}
		} else {
			foreach($neueGruppen AS $gruppe) {
				io_request::addRequest($io_user->login, "g", get_the_title($gruppe));
			}
			
			foreach($neuePermissions AS $permission) {
				io_request::addRequest($io_user->login, "p", get_the_title($permission));
			}
			
			function io_request_notice() {
    ?>
    <div class="updated">
        <p>Es wurde ein Antrag für die von dir beantragten Berechtigungen und/oder Mitgliedschaftsanfragen der Gruppe gestellt. Dieser wird so bald wie möglich bearbeitet.</p>
    </div>
<?php
			}
			add_action('admin_notices', 'io_request_notice');
		}
	}
	
	public static function authentifizierung($user, $user_name, $password) {
		if($user_name == '' || $password == '') {
			return;
		}
		
		if(!($user instanceof WP_User)) {
			$user = get_user_by('login', $user_name);
			if(!($user instanceof WP_User)) {
				return new WP_Error('user_not_extists', 'Du bist anscheinend noch nicht im IGELoffice registriert.');
			}
		}

		$user_id = $user->ID;
		
		if(get_user_meta($user_id, "user_aktiv", true) == 1) {
			return new WP_Error("user_inaktiv", "<strong>FEHLER:</strong> Dein Account wurde noch nicht aktiviert!");
		}

		$ldapConn = ldapConnector::get(false);

		if($ldapConn->bind($user_name, $password)) {
			//save password hash in database and key in cookie
			require_once IGELOFFICE_PATH.'class/php-encryption/Crypto.php';
			$key = Crypto::createNewRandomKey();
			$hash = base64_encode(Crypto::encrypt($password, $key));
			setcookie(hash('sha256', $user_name), base64_encode($key));
			unset($key, $password);
			update_user_meta($user_id, '_ldap_pass', $hash);
		}
		elseif($user_name != null) {
			return new WP_Error('ldap_login_failed', 'Deine Zugangsdaten sind nicht korrekt.');
		}
		
		remove_action('authenticate', 'wp_authenticate_username_password', 20);

		return $user;
	}
	
	public static function user_ldap_add($user_id) {
		$ldapConn = ldapConnector::get();
		$user_data = get_userdata($user_id);
		
		if(	
			(get_user_meta($user_id, 'user_art', true) == 'user' && !$ldapConn->isLDAPUser($user_data->first_name . ' ' . $user_data->last_name) && is_wp_error($ldapConn->addUser($user_data->first_name, $user_data->last_name, $user_data->user_email))) ||
			((get_user_meta($user_id, 'user_art', true) == 'basisgruppe' || get_user_meta($user_id, 'user_art', true) == 'landesverband') && !$ldapConn->isLDAPUser($user_data->user_login) && is_wp_error($ldapConn->addOrgaUser($user_data->user_login, $user_data->user_email)))
		) {
			return false;
		}
		$ldapConn->setUserAttribute(str_replace(".", " ", get_userdata($user_id)->user_login), "employeeType", get_user_meta($user_id, 'user_art', true));
		
		if(get_user_meta($user_id, 'user_art', true) == 'Basisgruppe' || get_user_meta($user_id, 'user_art', true) == 'Landesverband') {
			$ldapConn->setUserAttribute(str_replace(".", " ", get_userdata($user_id)->user_login), "st", get_usermeta($user_id, 'land', true));
		}
		
		if(get_user_meta($user_id, 'user_art', true) == 'Basisgruppe') {
			$ldapConn->setUserAttribute(str_replace(".", " ", get_userdata($user_id)->user_login), "l", get_user_meta($user_id, 'ort', true));
		}

		//send passwort reset link to user. modified version of receive_password from wp-login.php

		global $wpdb, $wp_hasher;

		$errors = new WP_Error();

		/**
		 * Fires before errors are returned from a password reset request.
		 *
		 * @since 2.1.0
		 */
		do_action( 'lostpassword_post' );

		// Redefining user_login ensures we return the right case in the email.
		$user_login = $user_data->user_login;
		$user_email = $user_data->user_email;

		/**
		 * Fires before a new password is retrieved.
		 *
		 * @since 1.5.0
		 * @deprecated 1.5.1 Misspelled. Use 'retrieve_password' hook instead.
		 *
		 * @param string $user_login The user login name.
		 */
		do_action( 'retreive_password', $user_login );

		/**
		 * Fires before a new password is retrieved.
		 *
		 * @since 1.5.1
		 *
		 * @param string $user_login The user login name.
		 */
		do_action( 'retrieve_password', $user_login );

		/**
		 * Filter whether to allow a password to be reset.
		 *
		 * @since 2.7.0
		 *
		 * @param bool true           Whether to allow the password to be reset. Default true.
		 * @param int  $user_data->ID The ID of the user attempting to reset a password.
		 */
		$allow = apply_filters( 'allow_password_reset', true, $user_data->ID );

		if ( ! $allow ) {
			return new WP_Error( 'no_password_reset', __('Password reset is not allowed for this user') );
		} elseif ( is_wp_error( $allow ) ) {
			return $allow;
		}

		// Generate something random for a password reset key.
		$key = wp_generate_password( 20, false );

		/**
		 * Fires when a password reset key is generated.
		 *
		 * @since 2.5.0
		 *
		 * @param string $user_login The username for the user.
		 * @param string $key        The generated password reset key.
		 */
		do_action( 'retrieve_password_key', $user_login, $key );

		// Now insert the key, hashed, into the DB.
		if ( empty( $wp_hasher ) ) {
			require_once ABSPATH . WPINC . '/class-phpass.php';
			$wp_hasher = new PasswordHash( 8, true );
		}
		$hashed = time() . ':' . $wp_hasher->HashPassword( $key );
		$wpdb->update( $wpdb->users, array( 'user_activation_key' => $hashed ), array( 'user_login' => $user_login ) );

		$message = __('Someone requested that the password be reset for the following account:') . "\r\n\r\n";
		$message .= network_home_url( '/' ) . "\r\n\r\n";
		$message .= sprintf(__('Username: %s'), $user_login) . "\r\n\r\n";
		$message .= __('If this was a mistake, just ignore this email and nothing will happen.') . "\r\n\r\n";
		$message .= __('To reset your password, visit the following address:') . "\r\n\r\n";
		$message .= '<' . network_site_url("wp-login.php?action=rp&key=$key&login=" . rawurlencode($user_login), 'login') . ">\r\n";

		if ( is_multisite() )
			$blogname = $GLOBALS['current_site']->site_name;
		else
			/*
			 * The blogname option is escaped with esc_html on the way into the database
			 * in sanitize_option we want to reverse this for the plain text arena of emails.
			 */
			$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

		$title = sprintf( __('[%s] Password Reset'), $blogname );

		/**
		 * Filter the subject of the password reset email.
		 *
		 * @since 2.8.0
		 *
		 * @param string $title Default email title.
		 */
		$title = apply_filters( 'retrieve_password_title', $title );

		/**
		 * Filter the message body of the password reset mail.
		 *
		 * @since 2.8.0
		 * @since 4.1.0 Added `$user_login` and `$user_data` parameters.
		 *
		 * @param string  $message    Default mail message.
		 * @param string  $key        The activation key.
		 * @param string  $user_login The username for the user.
		 * @param WP_User $user_data  WP_User object.
		 */
		$message = apply_filters( 'retrieve_password_message', $message, $key, $user_login, $user_data );

		if ( $message && !wp_mail( $user_email, wp_specialchars_decode( $title ), $message ) )
			wp_die( __('The e-mail could not be sent.') . "<br />\n" . __('Possible reason: your host may have disabled the mail() function.') );

		return true;


	}

	public static function password_reset($user, $password) {
		$ldapConn = ldapConnector::get();
		$ldapConn->setUserPassword($user->user_login, $password);
	}
}
