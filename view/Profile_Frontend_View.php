<?php

/**
 * Created by PhpStorm.
 * User: KWM
 * Date: 18.06.2017
 * Time: 11:46
 */
class Profile_Frontend_View
{
    public static function maskHandler($atts) {
        $user = new User(get_current_user_id());

        if($user->art == User_Util::USER_ART_BASISGRUPPE) {
            $atts = shortcode_atts(array(
                'update' => 'social'
            ), $atts, 'profilUpdate');

            if (isset($_POST[User_Util::POST_ATTRIBUT_FRONTEND_SUBMIT])) {
                self::maskExecution($atts);
            }

            $form = true;
            include 'wp-content/plugins/igeloffice/templates/frontend/profileForm.php';
            wp_nonce_field(User_Util::USERS_NONCE, User_Util::POST_ATTRIBUT_USERS_NONCE);

            switch ($atts['update']) {
                case 'social':
                    include 'wp-content/plugins/igeloffice/templates/frontend/profileSocial.php';
                    break;
                case 'karte':
                    include 'wp-content/plugins/igeloffice/templates/frontend/profileBasisgruppenkarte.php';
                    break;
                case 'verantwortlich':
                    include 'wp-content/plugins/igeloffice/templates/frontend/profileVerantwortliche.php';
                    break;
                case 'lieferadresse':
                    include 'wp-content/plugins/igeloffice/templates/frontend/profileLieferadresse.php';
                    break;
            }

            $form = false;
            include 'wp-content/plugins/igeloffice/templates/frontend/profileForm.php';
        }
    }

    public static function maskExecution($atts) {
        if( !isset($_POST[User_Util::POST_ATTRIBUT_USERS_NONCE]) ||
            !wp_verify_nonce($_POST[User_Util::POST_ATTRIBUT_USERS_NONCE], User_Util::USERS_NONCE) ||
            defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        switch ($atts['update']) {
            case 'social':
                self::saveSocial();
                break;
            case 'karte':
                self::saveBasisgruppenkarte();
                break;
            case 'verantwortlich':
                self::saveVerantwortliche();
                break;
            case 'lieferadresse':
                self::saveLieferadresse();
                break;
        }
    }

    private static function saveSocial() {
        update_usermeta(get_current_user_id(), User_Util::ATTRIBUT_FACEBOOK,		sanitize_text_field($_POST[User_Util::POST_ATTRIBUT_FACEBOOK]));
        update_usermeta(get_current_user_id(), User_Util::ATTRIBUT_TWITTER,		    sanitize_text_field($_POST[User_Util::POST_ATTRIBUT_TWITTER]));
        update_usermeta(get_current_user_id(), User_Util::ATTRIBUT_INSTAGRAM,	    sanitize_text_field($_POST[User_Util::POST_ATTRIBUT_INSTAGRAM]));
    }

    private static function saveBasisgruppenkarte() {
        update_usermeta(get_current_user_id(), User_Util::ATTRIBUT_GRADE,		    sanitize_text_field($_POST[User_Util::POST_ATTRIBUT_GRADE]));
        update_usermeta(get_current_user_id(), User_Util::ATTRIBUT_BESCHREIBUNG,	sanitize_text_field($_POST[User_Util::POST_ATTRIBUT_BESCHREIBUNG]));
        update_usermeta(get_current_user_id(), User_Util::ATTRIBUT_ADRESSE,		    sanitize_text_field($_POST[User_Util::POST_ATTRIBUT_ADRESSE]));
        update_usermeta(get_current_user_id(), User_Util::ATTRIBUT_IGEL,			sanitize_text_field($_POST[User_Util::POST_ATTRIBUT_IGEL]));
    }

    private static function saveVerantwortliche() {
        update_usermeta(get_current_user_id(), User_Util::ATTRIBUT_VERANTWORTLICHE_PERSON,		sanitize_text_field($_POST[User_Util::POST_ATTRIBUT_VERANTWORTLICHE_PERSON]));
        update_usermeta(get_current_user_id(), User_Util::ATTRIBUT_VERANTWORTLICHE_MAIL,		sanitize_text_field($_POST[User_Util::POST_ATTRIBUT_VERANTWORTLICHE_MAIL]));
        update_usermeta(get_current_user_id(), User_Util::ATTRIBUT_VERANTWORTLICHE_HANDY,		sanitize_text_field($_POST[User_Util::POST_ATTRIBUT_VERANTWORTLICHE_HANDY]));
    }

    private static function saveLieferadresse() {
        update_usermeta(get_current_user_id(), User_Util::ATTRIBUT_LIEFERADRESSE_ORT,			sanitize_text_field($_POST[User_Util::POST_ATTRIBUT_LIEFERADRESSE_ORT]));
        update_usermeta(get_current_user_id(), User_Util::ATTRIBUT_LIEFERADRESSE_ZUSATZ,		sanitize_text_field($_POST[User_Util::POST_ATTRIBUT_LIEFERADRESSE_ZUSATZ]));
        update_usermeta(get_current_user_id(), User_Util::ATTRIBUT_LIEFERADRESSE_STRASSE,		sanitize_text_field($_POST[User_Util::POST_ATTRIBUT_LIEFERADRESSE_STRASSE]));
        update_usermeta(get_current_user_id(), User_Util::ATTRIBUT_LIEFERADRESSE_PLZ,			sanitize_text_field($_POST[User_Util::POST_ATTRIBUT_LIEFERADRESSE_PLZ]));
        update_usermeta(get_current_user_id(), User_Util::ATTRIBUT_LIEFERADRESSE_STADT,			sanitize_text_field($_POST[User_Util::POST_ATTRIBUT_LIEFERADRESSE_STADT]));
    }

    public static function menu($conditions) {
        $conditions[] = array(
            'name'			=> 'Basisgruppe',
            'condition'		=> function() {
                $user = new User(get_current_user_id());
                return ($user->art == User_Util::USER_ART_BASISGRUPPE);
            }
        );

        return $conditions;
    }
}