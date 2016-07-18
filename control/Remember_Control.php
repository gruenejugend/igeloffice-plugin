<?php

/**
 * Description of Remember_Control
 *
 * @author KWM
 */
class Remember_Control {
    public static function remember() {
        $mails = self::get_remembers();

        $registerUrl = "https://account.gruene-jugend.de/wp-login.php?action=register";
        $profilUrl = "https://account.gruene-jugend.de/wp-admin/profile.php?rm=";
        foreach($mails AS $mail) {
			$message = "Hallo,<br><br>
Du hast Verantwortung in der GR&Uuml;NEN JUGEND &uuml;bernommen. Das ist toll!<br><br>
Damit du deine Aufgabe wahrnehmen kannst ist es notwendig, dass du dich im IGELoffice registrierst. Das IGELoffice ist unser Mitglieder-Service, in dem du verschiedene Services in Anspruch nehmen kannst, die du f&uuml;r deine Aufgabe brauchst. Ein Beispiel hierf&uuml;r ist das Mailinglisten-Management: Ohne eine Registrierung im IGELoffice wirst du nicht Teil der f&uuml;r dich wichtigen Mailinglisten.<br><br>
Unter " . $registerUrl . " kannst du dich im IGELoffice registrieren. Solltest du bereits im IGELoffice registriert sein, logge dich bitte ein und besuche diesen Link: " . $profilUrl . $mail . "<br><br>
Du wirst diese Mail nun t&auml;glich als Erinnerung bekommen. Die Erinnerung wird abgeschaltet, wenn du dich mit dieser Mail-Adresse im IGELoffice registrierst.<br><br>
Dein IGELoffice";
            wp_mail($mail, "Bitte registriere dich im IGELoffice!", $message);
        }
    }

    public static function register($user_id) {
        $mails = self::get_remembers();
        $user = get_userdata($user_id);
        if(in_array($user->user_email, $mails)) {
            User_Control::aktivieren($user_id);

            self::grant($user->user_email, $user->user_login);
        }
    }

    public static function unremember($wp_user) {
        $mail = sanitize_text_field($_GET['rm']);
		$mails = self::get_remembers();
		
		if(in_array($mail, $mails)) {
			$key = wp_generate_password(12, false, false);
			$save = array($key, $mail);
			update_user_meta($wp_user->ID, "io_remember_key", serialize($save));
			$profilUrl = "https://account.gruene-jugend.de/wp-admin/profile.php?rmf=" . $mail . "&rmk=" . $key;
			
			$message = "Hallo,<br><br>
Für deine Mail-Adresse wurde angegeben, dass Sie einem*einer Benutzer*in Namens " . $wp_user->user_login . " zugehörig ist.<br><br>
Diese Zuordnung muss aus Sicherheitsgründen bestätigt werden. Wenn dem also so ist, logge dich bitte mit diesem Zugang ein und klicke auf folgenden Link: " . $profilUrl . "<br><br>
Dein IGELoffice";
			
			wp_mail($mail, "Bitte bestätige deine Erinnerungsaufhebung!", $message);
			
			set_transient("remember_profil", true, 3);
		}
    }

    public static function unremember_final($wp_user) {
        $mail = sanitize_text_field($_GET['rmf']);
        $key = sanitize_text_field($_GET['rmk']);
		$mails = self::get_remembers();
		
		$value = unserialize(get_user_meta($wp_user->ID, "io_remember_key", true));
		if($value != "" && count($value) == 2 && in_array($mail, $mails) && $value[0] == $key && $value[1] == $mail) {
			delete_user_meta($wp_user->ID, "io_remember_key");
			
            self::grant($wp_user->user_email, $wp_user->user_login);
			
			set_transient("remember_profil_final", true, 3);
		}
    }
	
	public static function grant($mail, $login) {
		$query_group = get_posts(array(
			'post_type'         => Group_Util::POST_TYPE,
			'meta_query'        => array(
				'relation'          => 'AND',
				array(
					'key'               => 'io_group_remember',
					'compare'           => 'EXISTS'
				),
				array(
					'key'               => 'io_group_remember',
					'value'             => $mail,
					'compare'           => 'LIKE'
				)
			)
		));

		$query_permission = get_posts(array(
			'post_type'         => Permission_Util::POST_TYPE,
			'meta_query'        => array(
				'relation'          => 'AND',
				array(
					'key'               => 'io_permission_remember',
					'compare'           => 'EXISTS'
				),
				array(
					'key'               => 'io_permission_remember',
					'value'             => $mail,
					'compare'           => 'LIKE'
				)
			)
		));

		foreach($query_group AS $group) {
			$mails = unserialize(get_post_meta($group->ID, "io_group_remember", true));
			if(!in_array($mail, $mails)) {
				continue;
			}

			$key = array_search($mail, $mails);
			unset($mails[$key]);
			$mails = array_values($mails);
			update_post_meta($group->ID, "io_group_remember", serialize($mails));
			LDAP_Proxy::addUsersToGroup($login, $group->post_title);
		}

		foreach($query_permission AS $permission) {
			$mails = unserialize(get_post_meta($permission->ID, "io_permission_remember", true));
			if(!in_array($mail, $mails)) {
				continue;
			}

			$key = array_search($mail, $mails);
			unset($mails[$key]);
			$mails = array_values($mails);
			update_post_meta($permission->ID, "io_permission_remember", serialize($mails));
			LDAP_Proxy::addUserPermission($login, $permission->post_title);
		}
	}
	
	public static function msg_remember_profil_final() {
		$check = get_transient("remember_profil_final");
		
		if(!empty($check)) {
		?>
		
	<div class="updated">
		<p>Deine E-Mail wurde akzeptiert und deine Erinnerung entfernt. Die entsprechenden Gruppen-Mitgliedschaften und Berechtigungen wurden nun vergeben.</p>
	</div>	
		 
		<?php
		}
	}
	
	public static function msg_remember_profil() {
		$check = get_transient("remember_profil");
		
		if(!empty($check)) {
		?>
		
	<div class="updated">
		<p>Eine Best&auml;tigungsmail wurde versendet.</p>
	</div>	
		 
		<?php
		}
	}

    public static function get_remembers() {
        $query_group = get_posts(array(
            'post_type'         => Group_Util::POST_TYPE,
            'meta_query'        => array(
                array(
                    'key'               => 'io_group_remember',
                    'compare'           => 'EXISTS'
                )
            )
        ));

        $query_permission = get_posts(array(
            'post_type'         => Permission_Util::POST_TYPE,
            'meta_query'        => array(
                array(
                    'key'               => 'io_permission_remember',
                    'compare'           => 'EXISTS'
                )
            )
        ));

        $mails = array();
        foreach($query_group AS $group) {
            $mailsAttribute = unserialize(get_post_meta($group->ID, "io_group_remember", true));
            foreach($mailsAttribute AS $mail) {
                if(!in_array($mail, $mails)) {
                    array_push($mails, $mail);
                }
            }
        }

        foreach($query_permission AS $permission) {
            $mailsAttribute = unserialize(get_post_meta($permission->ID, "io_permission_remember", true));
            foreach($mailsAttribute AS $mail) {
                if(!in_array($mail, $mails)) {
                    array_push($mails, $mail);
                }
            }
        }

        return $mails;
    }
	
	public static function schedule() {
		if( !wp_next_scheduled("rememberSchedule")) {
			wp_schedule_event(time(), "daily", "rememberSchedule");
		}
	}
	
	public static function schedule_exec() {
		wp_mail("webmaster@gruene-jugend.de", "IO-Remember: Erinnerung verschickt!", "IO-Remember: Erinnerung verschickt!");
		self::remember();
	}
}