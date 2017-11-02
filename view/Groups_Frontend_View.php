<?php

/**
 * Created by PhpStorm.
 * User: KWM
 * Date: 11.06.2017
 * Time: 00:52
 */
class Groups_Frontend_View
{
    public static function maskHandler() {
        //Submits
        self::maskExec();

        //Mask
        self::maskView();
    }

    private static function maskView() {
        if(isset($_GET["gruppe"]) && self::isPermitted()) {
            $group = new Group(intval(sanitize_text_field($_GET["gruppe"])));

            include 'wp-content/plugins/igeloffice/templates/frontend/gruppeAnsicht.php';
        } else {
            $user = new User(get_current_user_id());
            $groups = $user->leading_groups;

            include 'wp-content/plugins/igeloffice/templates/frontend/gruppeUebersicht.php';
        }
    }

    private static function maskExec() {
        $group = new Group(intval(sanitize_text_field($_GET["gruppe"])));
        if (isset($_POST[Group_Util::POST_ATTRIBUT_FRONTEND_NEU_SUBMIT]) && self::isPermitted()) {
            if( !isset($_POST[Group_Util::POST_ATTRIBUT_MEMBER_NONCE]) ||
                !wp_verify_nonce($_POST[Group_Util::POST_ATTRIBUT_MEMBER_NONCE], Group_Util::MEMBER_NONCE)) {
                return;
            }
            self::neuExec($group);
        } elseif (isset($_POST[Group_Util::POST_ATTRIBUT_FRONTEND_REQUEST_SUBMIT]) && self::isPermitted()) {
            if( !isset($_POST[Group_Util::POST_ATTRIBUT_INFO_NONCE]) ||
                !wp_verify_nonce($_POST[Group_Util::POST_ATTRIBUT_INFO_NONCE], Group_Util::INFO_NONCE)) {
                return;
            }
            self::requestExec($group);
        } elseif (isset($_POST[Group_Util::POST_ATTRIBUT_FRONTEND_USER_SUBMIT]) && self::isPermitted()) {
            if( !isset($_POST[Group_Util::POST_ATTRIBUT_STANDARD_NONCE]) ||
                !wp_verify_nonce($_POST[Group_Util::POST_ATTRIBUT_STANDARD_NONCE], Group_Util::STANDARD_NONCE)) {
                return;
            }
            self::userExec($group);
        }
    }

    private static function isPermitted() {
        if(isset($_POST[Group_Util::POST_ATTRIBUT_FRONTEND_GROUP])) {
            $group_id = sanitize_text_field($_POST[Group_Util::POST_ATTRIBUT_FRONTEND_GROUP]);
        } else {
            $group_id = sanitize_text_field($_GET["gruppe"]);
        }

        $group = new Group($group_id);
        $user = new User(get_current_user_id());

        $pruef = false;
        foreach($group->owner AS $owner) {
            if($owner->user_login == $user->user_login) {
                $pruef = true;
                break;
            }
        }

        return $pruef;
    }

    private static function neuExec($group) {
        $names = explode("\r\n", $_POST[Group_Util::POST_ATTRIBUT_FRONTEND_USER_NEU]);
        $mails = explode("\r\n", $_POST[Group_Util::POST_ATTRIBUT_FRONTEND_MAIL_NEU]);

        if(count($names) == 1 && $names[0] == "") {
            $mails = array();
        }

        if(count($mails) == 1 && $mails[0] == "") {
            $mails = array();
        }

        $mails_fail = array();

        foreach ($names AS $name) {
            $userPre = get_user_by("login", sanitize_text_field($name));
            if($userPre && !self::inGroup($userPre, $group)) {
                $user = new User($userPre->ID);
                LDAP_Proxy::addUsersToGroup($userPre->user_login, $group->name);
                self::mailUser($userPre, $group);
                echo $user->user_login . " erfolgreich zur Gruppe hinzugef&uuml;gt.<br>";
            } elseif(!$userPre) {
                echo $name . " ist bis jetzt noch nicht im IGELoffice registriert. Gebe unten eine E-Mail-Adresse ein und richte damit eine Erinnerung zur Registrierung ein.<br>";
            } elseif(self::inGroup($userPre, $group)) {
                echo $name . " ist bereits in der Gruppe.<br>";
            }
        }

        foreach ($mails AS $mail) {
            if(filter_var($mail, FILTER_VALIDATE_EMAIL)) {
                $userPre = get_user_by("email", sanitize_text_field($mail));
                if ($userPre && !self::inGroup($userPre, $group)) {
                    $user = new User($userPre->ID);
                    LDAP_Proxy::addUsersToGroup($userPre->user_login, $group->name);
                    self::mailUser($userPre, $group);
                    echo $user->user_login . " erfolgreich zur Gruppe hinzugef&uuml;gt.<br>";
                } elseif(self::inGroup($userPre, $group)) {
                    echo $name . " ist bereits in der Gruppe.<br>";
                } else {
                    $mails_fail[] = $mail;
                }
            } else {
                $mails_fail[] = $mail;
            }
        }

        foreach ($mails_fail AS $key => $mail) {
            $remembers_save = unserialize(get_post_meta($group->id, "io_group_remember", true));
            if(filter_var($mail, FILTER_VALIDATE_EMAIL) && !in_array($mail, $remembers_save)) {
                $remembers_save[] = $mail;
                unset($mails_fail[$key]);
                echo $mail . " wird an die Registrierung erinnert werden.<br>";
            }
            update_post_meta($group->id, "io_group_remember", serialize($remembers_save));
        }

        foreach ($mails_fail AS $mail) {
            echo $mail . " ist eine ung&uuml;ltige Mail-Adresse.<br>";
        }
    }

    private static function inGroup($user, $group) {
        $users = $group->users;
        foreach($users AS $user_var) {
            if($user_var->user_login == $user->user_login) {
                return true;
            }
        }
        return false;
    }

    private static function mailUser($user, $group) {
        wp_mail($user->user_email, "IGELoffice: Du wurdest zur Gruppe " . $group->name . " hinzugefügt",
            "Hallo " . $user->first_name . "\r\n\r\n".
            "Du wurdest von der Gruppenleitung der Gruppe " . $group->name . " zur Gruppe hinzugefügt. Damit erhältst du einige neue Berechtigungen.\r\n\r\n".
            "Wenn du glaubst, dass es sich dabei um ein Versehen handelt oder du ein anderes anliegen hast, wende dich an webmaster@gruene-jugend.de.\r\n\r\n".
            "Viele Grüße,\r\nDein IGELoffice");
    }

    public static function getGroupRequests($group) {
        $posts = get_posts(array(
            'post_type'                 => Request_Util::POST_TYPE,
            'meta_query'                => array(
                array(
                    'key'                       => Request_Util::ATTRIBUT_ART,
                    'value'                     => Request_Group::art()
                ),
                array(
                    'key'                       => Request_Util::ATTRIBUT_STATUS,
                    'value'                     => 'Gestellt'
                )
            ),
            'posts_per_page'            => -1
        ));

        $requests = array();
        foreach ($posts AS $post) {
            $request = new Request($post->ID);
            if($request->meta[Request_Util::DETAIL_REQUESTED_ID] == $group->id) {
                $requests[] = $request;
            }
        }
        return $requests;
    }
    
    private static function requestExec($group) {
        $requests = self::getGroupRequests($group);
        
        if(count($requests) > 0) {
            foreach ($requests AS $request) {
                if(isset($_POST[Group_Util::POST_ATTRIBUT_FRONTEND_REQUEST_STATUS.$request->ID]) &&
                    $_POST[Group_Util::POST_ATTRIBUT_FRONTEND_REQUEST_STATUS.$request->ID] == Group_Util::POST_ATTRIBUT_FRONTEND_REQUEST_STATUS_A_A) {
                    Request_Control::approve($request->ID);
                } elseif(isset($_POST[Group_Util::POST_ATTRIBUT_FRONTEND_REQUEST_STATUS.$request->ID]) &&
                    $_POST[Group_Util::POST_ATTRIBUT_FRONTEND_REQUEST_STATUS.$request->ID] == Group_Util::POST_ATTRIBUT_FRONTEND_REQUEST_STATUS_A_R) {
                    Request_Control::reject($request->ID);
                }
            }
        }

        echo 'Mitgliedschaftsantr&auml;ge behandelt.<br>';
    }
    
    private static function userExec($group) {
        foreach ($group->users AS $user) {
            if(isset($_POST[Group_Util::POST_ATTRIBUT_FRONTEND_USER.$user->ID]) && $_POST[Group_Util::POST_ATTRIBUT_FRONTEND_USER.$user->ID] == 1) {
                User_Control::delToGroup($user->ID, $group->id);
                echo $user->user_login . " wurde aus der Gruppe entfernt.<br>";
            }
        }
    }

    public static function menu() {
        $conditions[] = array(
            'id'			=> 'Gruppen',
            'name'			=> 'Gruppen',
            'condition'		=> function() {
                if(get_current_user_id() != 0) {
                    $user = new User(get_current_user_id());
                    return count($user->leading_groups) > 0;
                }
                return false;
            }
        );

        return $conditions;
    }
}