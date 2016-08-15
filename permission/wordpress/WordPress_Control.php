<?php

/**
 * Description of WordPress_Control
 *
 * @author KWM
 */
class WordPress_Control
{
    public static function createSite()
    {
        if (User_Control::isPermitted(get_current_user_id(), WordPress_Control::getWordPressPermission()->id)) {
            $user = new User(get_current_user_id());
            $domain = Domain_Control::prepareDomain($user->user_login);

            $id = file("https://wordpress/wp-create-site.php?domain=" . $domain . "&title=GRÜNE JUGEND " . $user->user_login);
            if (is_numeric($id)) {
                $mitarbeiter_innen_id = self::createGroup(WordPress_Util::GROUP_WORDPRESS . " " . $domain . " " . WordPress_Util::GROUP_ROLE_MITARBEITER_INNEN, $domain, $user->user_login);
                $redakteur_innen_id = self::createGroup((WordPress_Util::GROUP_WORDPRESS . " " . $domain . " " . WordPress_Util::GROUP_ROLE_REDAKTEUR_INNEN, $domain, $user->user_login);
                $autor_innen_id = self::createGroup((WordPress_Util::GROUP_WORDPRESS . " " . $domain . " " . WordPress_Util::GROUP_ROLE_AUTOR_INNEN, $domain, $user->user_login);
                $abonnent_innen_id = self::createGroup((WordPress_Util::GROUP_WORDPRESS . " " . $domain . " " . WordPress_Util::GROUP_ROLE_ABONNENT_INNEN, $domain, $user->user_login);

                /*
                 * WIE SOLLEN BITTE ADMINS ERKANNT WERDEN???
                 */

                update_user_meta($user->ID, WordPress_Util::ATTRIBUT_WORDPRESS_MITARBEITER_INNEN, $mitarbeiter_innen_id);
                update_user_meta($user->ID, WordPress_Util::ATTRIBUT_WORDPRESS_REDAKTEUR_INNEN, $redakteur_innen_id);
                update_user_meta($user->ID, WordPress_Util::ATTRIBUT_WORDPRESS_AUTOR_INNEN, $autor_innen_id);
                update_user_meta($user->ID, WordPress_Util::ATTRIBUT_WORDPRESS_ABONNENT_INNEN, $abonnent_innen_id);
                update_user_meta($user->ID, WordPress_Util::ATTRIBUT_WORDPRESS_SETTED, 1);
            }


            /*
             * Gruppen in Datenbank schreiben
             */
            /*
             * Array
(
    [Enabled] => 1
    [CachePW] =>
    [URI] => ...
    [Filter] => (cn=%s)
    [NameAttr] => cn
    [SecName] =>
    [UidAttr] => cn
    [MailAttr] => mail
    [WebAttr] =>
    [Groups] => Array
        (
            [administrator] => cn=Sozialkampagne,ou=groups,dc=gruene-jugend,dc=de
            [editor] =>
            [author] =>
            [contributor] =>
            [subscriber] =>
        )

    [Debug] =>
    [GroupAttr] => cn
    [GroupFilter] =>
    [DefaultRole] =>
    [GroupEnable] => 1
    [GroupOverUser] =>
    [Version] => 1
    [GroupSeparator] =>
    [StartTLS] =>
)
             */
        }
    }

    public static function getWordPressPermission()
    {
        return new Permission(get_page_by_title(WordPress_Util::WORDPRESS_PERMISSION, OBJECT, Permission_Util::POST_TYPE)->ID);
    }

    private static function createGroup($name, $domain, $user_login)
    {
        $id = wp_insert_post(array(
            'post_title' => $name,
            'post_type' => Group_Util::POST_TYPE,
            'post_status' => 'publish'
        ));

        update_post_meta($id, Group_Util::OBERKATEGORIE, "WordPress-Seite");
        update_post_meta($id, Group_Util::UNTERKATEGORIE, $domain);

        $new_user_arten = User_Util::USER_ARTEN;
        unset($new_user_arten[User_Util::USER_ART_USER]);
        $user_arten_save = self::userArtenChange(User_Util::USER_ARTEN, $new_user_arten);
        update_post_meta($id, "io_group_standard", serialize($user_arten_save));
        LDAP_Proxy::addGroup($name, $user_login);

        return $id;
    }

    public static function deleteSite()
    {

    }

    private static function deleteGroup($group)
    {
        LDAP_Proxy::delGroup($group->name);
        wp_delete_post($group->id);
    }
}