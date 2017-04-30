<?php

/**
 * Description of User_UTIL
 *
 * @author KWM
 */
class User_Util {
	const USER_ART_USER = "user";
	const USER_ART_BASISGRUPPE = "basisgruppe";
	const USER_ART_LANDESVERBAND = "landesverband";
	const USER_ART_ORGANISATORISCH = "organisatorisch";

	const USER_ARTEN = array(
		self::USER_ART_USER				=> "User",
		"Basisgruppen"					=> array(
			"baden-wuerttemberg"			=> "Baden-Württemberg",
			"bayern"						=> "Bayern",
			"berlin"						=> "Berlin",
			"brandenburg"					=> "Brandenburg",
			"bremen"						=> "Bremen",
			"hamburg"						=> "Hamburg",
			"hessen"						=> "Hessen",
			"mecklenburg-vorpommern"		=> "Mecklenburg-Vorpommern",
			"niedersachsen"					=> "Niedersachsen",
			"nordrhein-westfalen"			=> "Nordrhein-Westfalen",
			"rheinland-pfalz"				=> "Rheinland-Pfalz",
			"saarland"						=> "Saarland",
			"sachsen"						=> "Sachsen",
			"sachsen-anhalt"				=> "Sachsen-Anhalt",
			"schleswig-holstein"			=> "Schleswig-Holstein",
			"thueringen"					=> "Thüringen"
		),
		"Landesverbände"				=> array(
			"baden-wuerttemberg"			=> "Baden-Württemberg",
			"bayern"						=> "Bayern",
			"berlin"						=> "Berlin",
			"brandenburg"					=> "Brandenburg",
			"bremen"						=> "Bremen",
			"hamburg"						=> "Hamburg",
			"hessen"						=> "Hessen",
			"mecklenburg-vorpommern"		=> "Mecklenburg-Vorpommern",
			"niedersachsen"					=> "Niedersachsen",
			"nordrhein-westfalen"			=> "Nordrhein-Westfalen",
			"rheinland-pfalz"				=> "Rheinland-Pfalz",
			"saarland"						=> "Saarland",
			"sachsen"						=> "Sachsen",
			"sachsen-anhalt"				=> "Sachsen-Anhalt",
			"schleswig-holstein"			=> "Schleswig-Holstein",
			"thueringen"					=> "Thüringen"
		),
		self::USER_ART_ORGANISATORISCH	=> "Organisatorisch"
	);
	
	const ATTRIBUT_ART = "io_user_art";
	const ATTRIBUT_AKTIV = "io_user_aktiv";
	const ATTRIBUT_LANDESVERBAND = "io_user_lv";
    const ATTRIBUT_FACEBOOK = "io_user_facebook";
    const ATTRIBUT_TWITTER = "io_user_twitter";
    const ATTRIBUT_INSTAGRAM = "io_user_instagram";
    const ATTRIBUT_GRADE = "io_user_grade";
    const ATTRIBUT_BESCHREIBUNG = "io_user_beschreibung";
	
	const POST_ATTRIBUT_ART = "user_art";
	const POST_ATTRIBUT_EMAIL = "user_email";
	const POST_ATTRIBUT_FIRST_NAME = "first_name";
	const POST_ATTRIBUT_LAST_NAME = "last_name";
	const POST_ATTRIBUT_ORGA_NAME = "orga_name";
	const POST_ATTRIBUT_NAME = "name";
	const POST_ATTRIBUT_LAND = "land";
	const POST_ATTRIBUT_LANDESVERBAND = "landesverband";
	const POST_ATTRIBUT_USERS_NONCE = 'io_users_nonce';
    const POST_ATTRIBUT_USERS_CONTACT_NONCE = 'io_users_contect_nonce';
	const POST_ATTRIBUT_AKTIV = "user_aktiv";
	const POST_ATTRIBUT_PERMISSIONS = "permissions";
	const POST_ATTRIBUT_GROUPS = "groups";
	const POST_ATTRIBUT_ERWEITERT = "erweitert";
    const POST_ATTRIBUT_FACEBOOK = "facebook";
    const POST_ATTRIBUT_TWITTER = "twitter";
    const POST_ATTRIBUT_INSTAGRAM = "instagram";
    const POST_ATTRIBUT_GRADE = "grade";
    const POST_ATTRIBUT_BESCHREIBUNG = "beschreibung";
	
	const USERS_NONCE = 'io_users';
    const USERS_NONCE_CONTACT = 'io_users_contact';
}
