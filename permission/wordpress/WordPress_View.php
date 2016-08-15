<?php

/**
 * Description of WordPress_View
 *
 * @author KWM
 */
class WordPress_View
{
    public static function maskHandler()
    {
        $user = new User(get_current_user_id());
        if (User_Control::isPermitted(get_current_user_id(), WordPress_Control::getWordPressPermission()->id)) {
            $wordpress_create_submitted_css = " style='display: none;'";
            $wordpress_delete_submitted_css = " style='display: none;'";
            $wordpress_create_css = " style='display: none;'";
            $wordpress_create_fail_css = " style='display: none;'";
            $wordpress_edit_css = " style='display: none;'";
            $wordpress_delete_css = " style='display: none;'";
            $wordpress_delete_confirm_css = " style='display: none;'";

            $mitarbeiter_innen_id = get_user_meta($user->ID, WordPress_Util::ATTRIBUT_WORDPRESS_MITARBEITER_INNEN, true);
            $redakteur_innen_id = get_user_meta($user->ID, WordPress_Util::ATTRIBUT_WORDPRESS_REDAKTEUR_INNEN, true);
            $autor_innen_id = get_user_meta($user->ID, WordPress_Util::ATTRIBUT_WORDPRESS_AUTOR_INNEN, true);
            $abonnent_innen_id = get_user_meta($user->ID, WordPress_Util::ATTRIBUT_WORDPRESS_ABONNENT_INNEN, true);

            if (!empty($_POST[WordPress_Util::POST_ATTRIBUT_CREATE_SUBMIT])) {
                $wordpress_create_submitted_css = "";
                $wordpress_edit_css = "";
                WordPress_Control::createSite();
            } else if (!empty($_POST[WordPress_Util::POST_ATTRIBUT_DELETE_SUBMIT])) {
                $wordpress_delete_confirm_css = "";
            } else if (!empty($_POST[WordPress_Util::POST_ATTRIBUT_DELETE_SUBMIT_CONFIRM])) {
                $wordpress_delete_submitted_css = "";
                WordPress_Control::deleteSite();
            } else if (get_user_meta(get_current_user_id(), WordPress_Util::ATTRIBUT_WORDPRESS_SETTED, true)) {
                $wordpress_edit_css = "";
                $wordpress_delete_css = "";
            } else {
                $wordpress_create_css = "";
            }

            include 'wp-content/plugins/igeloffice/permission/wordpress/templates/wordpressCreate.php';
        }
    }
}