<?php

/**
 * Description of Register_Frontend_View
 *
 * @author KWM
 */
class Register_Frontend_View {
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
		$landChecked[7]  = ($land == 'mecklenburg-vorpommern' ? ' checked' : '');
		$landChecked[8]  = ($land == 'niedersachsen' ? ' checked' : '');
		$landChecked[9]  = ($land == 'nordrhein-westfalen' ? ' checked' : '');
		$landChecked[10] = ($land == 'rheinland-pfalz' ? ' checked' : '');
		$landChecked[11] = ($land == 'saarland' ? ' checked' : '');
		$landChecked[12] = ($land == 'sachsen' ? ' checked' : '');
		$landChecked[13] = ($land == 'sachsen-anhalt' ? ' checked' : '');
		$landChecked[14] = ($land == 'schleswig-holstein' ? ' checked' : '');
		$landChecked[15] = ($land == 'thueringen' ? ' checked' : '');
		
		if($_GET['art'] == 'User') {
			include 'wp-content/plugins/igeloffice/templates/frontend/userRegistration.php';
		} else if($_GET['art'] == 'Landesverband') {
			include 'wp-content/plugins/igeloffice/templates/frontend/landRegistration.php';
		} else if($_GET['art'] == 'Basisgruppe') {
			include 'wp-content/plugins/igeloffice/templates/frontend/basisgruppeRegistration.php';
		} else if($_GET['art'] == 'Orgauser') {
			include 'wp-content/plugins/igeloffice/templates/frontend/orgauserRegistration.php';
		} else {
			include 'wp-content/plugins/igeloffice/templates/frontend/registration.php';
		}
	}
	
	public static function errorHandler($errors) {
		if(empty($_POST[User_Util::POST_ATTRIBUT_ART])) {
			$errors->add('user_art_error', '<strong>FEHLER:</strong> Du musst eine Nutzungsart angeben!');
		}
		
		if(empty($_POST['user_email'])) {
			$errors->add('user_email_error', '<strong>FEHLER:</strong> Du musst eine E-Mail-Adresse angeben!');
		}
		
		if((!isset($_POST['erweitert']) || (isset($_POST['erweitert']) && $_POST['erweitert'] != 1)) && str_replace("@gruene-jugend.de", "", $_POST['user_email']) != $_POST['user_email']) {
			$errors->add('user_email_gj_error', '<strong>FEHLER:</strong> Du darfst keine GR&Uuml;NE JUGEND E-Mail-Adresse hier angeben!');
		}
		
		if($_POST[User_Util::POST_ATTRIBUT_ART] == User_Util::USER_ART_USER && (empty($_POST['first_name']) || empty($_POST['last_name']))) {
			if(empty($_POST['first_name'])) {
				$errors->add('first_name_error', '<strong>FEHLER:</strong> Du musst einen Vornamen angeben!');
			}
			
			if(empty($_POST['last_name'])) {
				$errors->add('last_name_error', '<strong>FEHLER:</strong> Du musst einen Nachnamen angeben!');
			}
		}
		
		if($_POST[User_Util::POST_ATTRIBUT_ART] == User_Util::USER_ART_BASISGRUPPE && (empty($_POST['name']) || $_POST['land'] == '0')) {
			if(empty($_POST['name'])) {
				$errors->add('name_error', '<strong>FEHLER:</strong> Du musst einen Ortsnamen angeben!');
			}
			
			if(empty($_POST['land'])) {
				$errors->add('land_error', '<strong>FEHLER:</strong> Du musst ein Bundesland angeben!');
			}
		}
		
		if($_POST[User_Util::POST_ATTRIBUT_ART] == User_Util::USER_ART_LANDESVERBAND && $_POST['land'] == '0') {
			$errors->add('land_error', '<strong>FEHLER:</strong> Du musst ein Bundesland angeben!');
		}
		
		if($_POST[User_Util::POST_ATTRIBUT_ART] == User_Util::USER_ART_ORGANISATORISCH && empty($_POST['orga_name'])) {
			$errors->add('orga_error', '<strong>FEHLER:</strong> Du musst einen Namen angeben!');
		}
		
		return $errors;
	}
	
	public static function loginLabel() {
		include 'wp-content/plugins/igeloffice/templates/frontend/js/loginlabel.php';
	}
}
