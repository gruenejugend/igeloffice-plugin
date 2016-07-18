<?php

/**
 * Description of backend_news
 *
 * @author KWM
 */
class frontend_newsletter {
	public static function maskHandler() {
		if(!empty($_GET['newsletter_code'])) {
			$checkCode = self::checkCode();
			if($checkCode == "l") {
				echo "Du wurdest erfolgreich aus dem Verteiler ausgetragen.";
			} else if($checkCode == "e") {
				echo "Du wurdest erfolgreich auf dem Verteiler eingetragen.";
			} else if($checkCode == "a") {
				$pruef = true;
				if(!empty($_POST['newsletter_submit'])) {
					if( !isset($_POST['io_newsletter_nonce']) || 
						!wp_verify_nonce($_POST['io_newsletter_nonce'], 'io_newsletter') || 
						defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
						return;
					}
					
					if(self::changeSendMail()) {
						$pruef = false;
						echo "Es wurde eine Best&auml;tigungsmail an die neue Mail-Adresse versendet.";
					} else {
						echo "Die alte Mail-Adresse stimmt nicht mit der hinterlegten &uuml;berein.";
					}
				} 
				
				if($pruef) {
					include 'wp-content/plugins/igeloffice/templates/frontend/newsletterChange.php';
				}
			} else if($checkCode == "c") {
				echo "Deine Mail-Adresse wurde erfolgreich ge&auml;ndert.";
			} else {
				echo "Etwas ging schief. Vermutlich liegt deine Anfrage schon drei Tage zurück. Wenn dem so ist, stell bitte eine neue. Sonst stell bitte ein Ticket <a href='https://support.gruene-jugend.de' traget='_blank'>hier</a>.";
			}
			echo '<br><br>';
		} else {
			if(!empty($_POST['newsletter_submit'])) {
				if( !isset($_POST['io_newsletter_nonce']) || 
					!wp_verify_nonce($_POST['io_newsletter_nonce'], 'io_newsletter') || 
					defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
					return;
				}
				
				if(empty(get_transient("loesung"))) {
					echo "Du musst die Aufgabe innerhalb drei Minuten l&ouml;sen.";
				} else if(get_transient("loesung") != $_POST['newsletter_aufgabe']) {
					echo "Die eingegebene L&ouml;sung ist leider falsch. Bitte versuche es nochmal!";
					$vorlage = sanitize_text_field($_POST['newsletter_email']);
				} else {
					echo "Deine Eingabe war erfolgreich. Sollte die angegebene Mail-Adresse in unserem System wirklich existieren, schicken wir dieser nun eine E-Mail in der das weitere Verfahren beschrieben ist.";
					self::sendMail();
				}
				echo '<br><br>';
			}
			
			$aufgabe = self::setAufgabe();

			include 'wp-content/plugins/igeloffice/templates/frontend/newsletterExit.php';
		}
	}

	public static function checkCode()
	{
		$key = sanitize_text_field($_GET['newsletter_code']);
		$art = LDAP_Proxy::isSherpaKey($key);
		if ($art) {
			if ($art == "l") {
				LDAP_Proxy::setSherpaLoeschen($key);
			} else if ($art == "c") {
				LDAP_Proxy::setSherpaChangeFinal($key);
			} else if ($art == "e") {
				LDAP_Proxy::setSherpaEintragen($key);
			}
			return $art;
		}
		return false;
	}

	public static function changeSendMail()
	{
		$alt = sanitize_text_field($_POST['newsletter_email_alt']);
		$neu = sanitize_text_field($_POST['newsletter_email_neu']);

		$key = sanitize_text_field($_GET['newsletter_code']);

		$key = LDAP_Proxy::setSherpaChange($key, $alt, $neu);
		if ($key) {
			$subject = "Änderung deiner Mail-Adresse";

			$message = __('Hallo,') . "\r\n\r\n";
			$message .= __('Diese E-Mail-Adresse wurde soeben zum als neue E-Mail-Adresse für den Monatsigel-Verteiler der GRÜNEN JUGEND markiert.') . "\r\n\r\n";
			$message .= __('Bitte folge folgenden Link zum Eintragen auf die Liste:') . "\r\n\r\n";
			$message .= io_get_current_url() . "&newsletter_code=" . $key . "\r\n\r\n";
			$message .= __('Wenn du dich nicht zum Eintragen markiert hast, ignoriere diese Mail bitte.') . "\r\n\r\n";
			$message .= __('Liebe Grüße,') . "\r\n";
			$message .= __('Deine GRÜNE JUGEND');

			wp_mail($neu, $subject, $message, 'From: webmaster@gruene-jugend.de');

			return true;
		}
		return false;
	}
	
	public static function sendMail() {
		$mail = sanitize_text_field($_POST['newsletter_email']);
		$art = sanitize_text_field($_POST['newsletter_art']);
		if(LDAP_Proxy::isSherpaMember($mail)) {
			$key = LDAP_Proxy::setSherpaMemberCode($mail, $art);
			
			if($art == "l") {
				$subject = "Austragung vom Monatsigel-Verteiler";
				
				$message = __('Hallo,') . "\r\n\r\n";
				$message .= __('Diese E-Mail-Adresse wurde soeben zum Austragen aus dem Monatsigel-Verteiler der GRÜNEN JUGEND markiert.') . "\r\n\r\n";
				$message .= __('Um dich vom Verteiler auszutragen, folge bitte diesen Link:') . "\r\n\r\n";
				$message .= io_get_current_url() . "&newsletter_code=" . $key . "\r\n\r\n";
				$message .= __('Wenn du dich nicht zum Austragen markiert hast, ignoriere diese Mail bitte.') . "\r\n\r\n";
				$message .= __('Liebe Grüße,') . "\r\n";
				$message .= __('Deine GRÜNE JUGEND');
			} else if($art == "a") {
				$subject = "Änderung deiner Mail-Adresse";
				
				$message = __('Hallo,') . "\r\n\r\n";
				$message .= __('Diese E-Mail-Adresse wurde soeben zum Ändern markiert. Du möchtest diese Mail-Adresse durch eine andere ersetzen.') . "\r\n\r\n";
				$message .= __('Um deine Mail-Adresse zu ändern, folge bitte diesen Link:') . "\r\n\r\n";
				$message .= io_get_current_url() . "&newsletter_code=" . $key . "\r\n\r\n";
				$message .= __('Wenn du dich nicht zum Ändern markiert hast, ignoriere diese Mail bitte.') . "\r\n\r\n";
				$message .= __('Liebe Grüße,') . "\r\n";
				$message .= __('Deine GRÜNE JUGEND');
			} else if($art == "e") {
				$subject = "Eintragen zum Monatsigel-Verteiler";
				
				$message = __('Hallo,') . "\r\n\r\n";
				$message .= __('Diese E-Mail-Adresse wurde soeben zum Eintragen auf dem Monatsigel-Verteiler der GRÜNEN JUGEND markiert.') . "\r\n\r\n";
				$message .= __('Um deine Mail-Adresse einzutragen, folge bitte diesen Link:') . "\r\n\r\n";
				$message .= io_get_current_url() . "&newsletter_code=" . $key . "\r\n\r\n";
				$message .= __('Wenn du dich nicht zum Eintragen markiert hast, ignoriere diese Mail bitte.') . "\r\n\r\n";
				$message .= __('Liebe Grüße,') . "\r\n";
				$message .= __('Deine GRÜNE JUGEND');
			}

			wp_mail($mail, $subject, $message, 'From: webmaster@gruene-jugend.de');
		} else if($art == "e") {
			$subject = "Eintragen zum Monatsigel-Verteiler";
			$url = "";

			$message = __('Hallo,') . "\r\n\r\n";
			$message .= __('Diese E-Mail-Adresse wurde soeben zum Eintragen auf dem Monatsigel-Verteiler der GRÜNEN JUGEND markiert.') . "\r\n\r\n";
			$message .= __('Bitte folge folgenden Link zum Eintragen auf die Liste:') . "\r\n\r\n";
			$message .= $url . "\r\n\r\n";
			$message .= __('Liebe Grüße,') . "\r\n";
			$message .= __('Deine GRÜNE JUGEND');

			wp_mail($mail, $subject, $message, 'From: webmaster@gruene-jugend.de');
		}
	}

	private static function setAufgabe()
	{
		$operationZ = mt_rand(0, 3);
		if ($operationZ == 3) {
			do {
				$zahl1 = mt_rand(0, 10);
				$zahl2 = mt_rand(1, 10);
			} while ($zahl1 % $zahl2 != 0);
		} else {
			$zahl1 = mt_rand(0, 10);
			$zahl2 = mt_rand(0, 10);
		}

		$aufgabe = "";
		$loesung = 0;
		switch ($operationZ) {
			case 0:
				$aufgabe = $zahl1 . " plus " . $zahl2;
				$loesung = $zahl1 + $zahl2;
				break;
			case 1:
				$aufgabe = $zahl1 . " minus " . $zahl2;
				$loesung = $zahl1 - $zahl2;
				break;
			case 2:
				$aufgabe = $zahl1 . " mal " . $zahl2;
				$loesung = $zahl1 * $zahl2;
				break;
			case 3:
				$aufgabe = $zahl1 . " geteilt durch " . $zahl2;
				$loesung = $zahl1 / $zahl2;
				break;
		}

		set_transient("loesung", $loesung, 180);
		unset($loesung);

		return $aufgabe;
	}

	private static function maskExecution()
	{
		
	}
}
