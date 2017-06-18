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
    const ATTRIBUT_ADRESSE = "io_user_adresse";
    const ATTRIBUT_IGEL = "io_user_igel";
    //NEU
    const ATTRIBUT_VERANTWORTLICHE_PERSON = "io_user_verantwortliche_r";
    const ATTRIBUT_VERANTWORTLICHE_HANDY = "io_user_verantwortliche_r_handy";
    const ATTRIBUT_VERANTWORTLICHE_MAIL = "io_user_verantwortliche_r_mail";
    const ATTRIBUT_LIEFERADRESSE_ORT = "io_user_liefer_ort";
    const ATTRIBUT_LIEFERADRESSE_ZUSATZ = "io_user_liefer_zusatz";
    const ATTRIBUT_LIEFERADRESSE_STRASSE = "io_user_liefer_strasse";
    const ATTRIBUT_LIEFERADRESSE_PLZ = "io_user_liefer_plz";
    const ATTRIBUT_LIEFERADRESSE_STADT = "io_user_liefer_stadt";
	
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
    const POST_ATTRIBUT_ADRESSE = "adresse";
    const POST_ATTRIBUT_IGEL = "igel";
    const POST_ATTRIBUT_IGEL_AGGRI = "aggri";
    const POST_ATTRIBUT_IGEL_OEKI = "oeki";
    //NEU
    const POST_ATTRIBUT_VERANTWORTLICHE_PERSON = "person";
    const POST_ATTRIBUT_VERANTWORTLICHE_HANDY = "person_handy";
    const POST_ATTRIBUT_VERANTWORTLICHE_MAIL = "person_mail";
    const POST_ATTRIBUT_LIEFERADRESSE_ORT = "liefer_ort";
    const POST_ATTRIBUT_LIEFERADRESSE_ZUSATZ = "liefer_zusatz";
    const POST_ATTRIBUT_LIEFERADRESSE_STRASSE = "liefer_strasse";
    const POST_ATTRIBUT_LIEFERADRESSE_PLZ = "liefer_plz";
    const POST_ATTRIBUT_LIEFERADRESSE_STADT = "liefer_stadt";

    const POST_ATTRIBUT_FRONTEND_SUBMIT = "frontendSubmit";
	
	const USERS_NONCE = 'io_users';
    const USERS_NONCE_CONTACT = 'io_users_contact';
}
