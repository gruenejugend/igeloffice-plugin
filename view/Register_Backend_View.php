<?php
/**
 * Description of Register_Backend_View
 *
 * @author KWM
 */
class Register_Backend_View {
	public static function maskHandler() {
		wp_nonce_field('io_users', 'io_users_nonce');
		
		$userArtInput = esc_attr(wp_unslash((!empty($_POST[User_Util::POST_ATTRIBUT_ART])) ? sanitize_text_field($_POST[User_Util::POST_ATTRIBUT_ART]) : ''));
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
		$first_name = (!empty($_POST[User_Util::POST_ATTRIBUT_FIRST_NAME])) ? sanitize_text_field($_POST[User_Util::POST_ATTRIBUT_FIRST_NAME]) : '';
		$last_name = (!empty($_POST[User_Util::POST_ATTRIBUT_LAST_NAME])) ? sanitize_text_field($_POST[User_Util::POST_ATTRIBUT_LAST_NAME]) : '';
		$orga_name = (!empty($_POST[User_Util::POST_ATTRIBUT_ORGA_NAME])) ? sanitize_text_field($_POST[User_Util::POST_ATTRIBUT_ORGA_NAME]) : '';
		$name = (!empty($_POST['name'])) ? sanitize_text_field($_POST['name']) : '';
		$land = (!empty($_POST[User_Util::POST_ATTRIBUT_LAND])) ? sanitize_text_field($_POST[User_Util::POST_ATTRIBUT_LAND]) : '';
		
		$landChecked[0]  = ($land == 'baden-wuerttemberg' ? ' checked' : '');
		$landChecked[1]  = ($land == 'bayern' ? ' checked' : '');
		$landChecked[2]  = ($land == 'berlin' ? ' checked' : '');
		$landChecked[3]  = ($land == 'brandenburg' ? ' checked' : '');
		$landChecked[4]  = ($land == 'bremen' ? ' checked' : '');
		$landChecked[5]  = ($land == 'hamburg' ? ' checked' : '');
		$landChecked[6]  = ($land == 'hessen' ? ' checked' : '');
		$landChecked[7]  = ($land == 'mecklenburg-vorpommern' ? ' checked' : '');
		$landChecked[8]  = ($land == 'niedersachsen' ? ' checked' : '');
		$landChecked[9]  = ($land == 'nordrhein-westfalen' ? ' checked' : '');
		$landChecked[10] = ($land == 'rheinland-pfalz' ? ' checked' : '');
		$landChecked[11] = ($land == 'saarland' ? ' checked' : '');
		$landChecked[12] = ($land == 'sachsen' ? ' checked' : '');
		$landChecked[13] = ($land == 'sachsen-anhalt' ? ' checked' : '');
		$landChecked[14] = ($land == 'schleswig-holstein' ? ' checked' : '');
		$landChecked[15] = ($land == 'thueringen' ? ' checked' : '');
		
		$groups = Group_Control::getValues();
		$group_values = array();
		if(isset($_POST['groups']) && count($_POST['groups']) > 0) {
			foreach($_POST['groups'] AS $group) {
				$group_values[sanitize_text_field($group)] = sanitize_text_field($group);
			}
		}
		
		$permissions = Permission_Control::getValues();
		$permission_values = array();
		if(isset($_POST['permissions']) && count($_POST['permissions']) > 0) {
			foreach($_POST['permissions'] AS $permission) {
				$permission_values[sanitize_text_field($permission)] = sanitize_text_field($permission);
			}
		}
		
		include '../wp-content/plugins/igeloffice/templates/backend/newUser.php';
	}
	
	public static function maskExecution($user_id) {
		if(current_user_can('administrator')) {
			if( !isset($_POST['io_users_nonce']) || 
				!wp_verify_nonce($_POST['io_users_nonce'], 'io_users') || 
				defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
				return;
			}
			
			User_Control::aktivieren($user_id);

			foreach($_POST['groups'] AS $group) {
				$group = sanitize_text_field($group);
				User_Control::addToGroup($user_id, $group);
			}

			foreach($_POST['permissions'] AS $permission) {
				$permission = sanitize_text_field($permission);
				User_Control::addPermission($user_id, $permission);
			}
		}
	}
	
	public static function registerMsg($errors, $redirect_to) {
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
}
