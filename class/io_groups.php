<?php

/**
 * Weitere, noch nicht implementierte Features:
 * //TODO: Mitgliedschaftsbeantragung übers Frontend (und Zulassung) (TEST)
 * //TODO: Gruppenverwaltung übers Frontend (Mitglieder hinzufügen, entfernen, Leiter*innen verändern, Verteiler ändern, Listen hinzufügen) (TEST)
 * //TODO: Maximale Anzahl von Gruppenmitgliedern
 * //TODO: Listenerstellung, bei Eintrag
 */

/**
 * Klasse zur Verwaltung von Gruppen im IGELoffice
 *
 * @author KayWilhelm
 */
class io_groups extends io_postlist {
	private $name;
	private $oberkategorie;
	private $unterkategorie;
	private $leiter_innen;
	private $leiter_innenID;
	private $verteiler;
	private $listen;
	private $mitgliedschaften;
	private $mitgliedschaftenID;
	private $gruppen;
	private $gruppenID;
	private $berechtigungen;
	private $berechtigungenID;
	
	private $ldapConn;
	
	public function __construct($post, $load = false) {
		$id = $post->ID;
		$this->name				= get_the_title($id);
		
		$ldapConn = ldapConnector::get();
		
		if(get_post_meta($id, self::$PREFIX . '_active', true) == true) {
			$this->oberkategorie	= $ldapConn->getGroupAttribute($this->name, self::$PREFIX . 'Oberkategorie')[0];
			$this->unterkategorie	= $ldapConn->getGroupAttribute($this->name, self::$PREFIX . 'Unterkategorie')[0];
			$this->leiter_innen		= $ldapConn->getGroupAttribute($this->name, 'owner');
			$this->verteiler		= $ldapConn->getGroupAttribute($this->name, self::$PREFIX . 'Verteiler');
			$this->listen			= $ldapConn->getGroupAttribute($this->name, self::$PREFIX . 'Listen');
			$this->mitgliedschaften	= $ldapConn->getAllGroupMembers($this->name);
			$this->gruppen			= $ldapConn->getAllGroupGroups($this->name);
			$this->berechtigungen	= $ldapConn->getGroupPermissions($this->name);
			
			$this->leiter_innenID = array();
			if($load == true) {
				if(count($this->leiter_innen) > 0) {
					foreach($this->leiter_innen AS $leiter_in) {
						$users = get_user_by("login", $leiter_in);

						$user_id = (isset($users) ? $users->ID : false);

						if ($user_id) {
							$this->leiter_innenID[$user_id] = $user_id;
						}
					}
				}
				
				$this->mitgliedschaftenID = array();
				if(count($this->mitgliedschaften) > 0) {
					foreach($this->mitgliedschaften AS $mitglied) {
						$users = get_user_by("login", $mitglied);

						$user_id = (isset($users) ? $users->ID : false);

						if ($user_id) {
							$this->mitgliedschaftenID[$user_id] = $user_id;
						}
					}
				}

				$this->gruppenID = array();
				if(count($this->gruppen) > 0) {
					foreach ($this->gruppen AS $gruppe) {
						$gruppe = get_page_by_title($gruppe, OBJECT, self::$POST_TYPE);

						$gruppe_id = (isset($gruppe) ? $gruppe->ID : false);

						if($gruppe_id) {
							$this->gruppenID[$gruppe_id] = $gruppe_id;
						}
					}
				}

				$this->berechtigungenID = array();
				if(count($this->berechtigungen) > 0) {
					foreach($this->berechtigungen AS $berechtigung) {
						$berechtigung = get_page_by_title($berechtigung, OBJECT, io_permission::$POST_TYPE);

						$berechtigung_id = (isset($berechtigung) ? $berechtigung->ID : false);

						if ($berechtigung_id) {
							$this->berechtigungenID[$berechtigung_id] = $berechtigung_id;
						}
					}
				}
			}
		}
	}
	
	public function __get($name) {
		switch($name) {
			case 'name':
				return $this->name;
			case 'oberkategorie':
				return $this->oberkategorie;
			case 'unterkategorie':
				return $this->unterkategorie;
			case 'leiter_innen':
				return $this->leiter_innen;
			case 'leiter_innenID':
				return $this->leiter_innenID;
			case 'verteiler':
				return $this->verteiler;
			case 'listen':
				return $this->listen;
			case 'mitgliedschaften':
				return $this->mitgliedschaften;
			case 'mitgliedschaftenID':
				return $this->mitgliedschaftenID;
			case 'berechtigungen':
				return $this->berechtigungen;
		};
	}
	
	/*****************************************************
	 ********************** Masken ***********************
	 *****************************************************/
	public static function metabox() {
		add_meta_box("io_groups_mb", "Gruppe", array("io_groups", "updBackendForm"), self::$POST_TYPE, "normal", "default");
	}
	
	public static function updBackendForm($post) {
		$io_groups = new io_groups($post, true);
		
		$form = new io_form(array(
			'form'		=> false,
			'prefix'	=> self::$PREFIX,
			'table'		=> true,
			'submit'	=> 'pt'
		));
		
		wp_nonce_field('io_groups', 'io_groups_nonce');
			
		$users = get_users(array(
			'meta_key'		=> 'user_aktiv',
			'meta_value'	=> 0
		));
		
		$values = array();
		foreach($users AS $user) {
			$user_art = get_user_meta($user->ID, "user_art", true);
			if($user_art != null && $user_art != "") {
				$values[$user->ID] = $user->first_name . ' ' . $user->last_name;
				if($values[$user->ID] == ' ') {
					$values[$user->ID] = $user->user_login;
				}
			}
		}
		
		$form->td_select(array(
			'beschreibung'	=> 'Leiter*in:',
			'name'			=> 'leiter_innenID',
			'values'		=> $values,
			'multiple'		=> true,
			'selected'		=> $io_groups->leiter_innenID,
			'size'			=> 10,
			'first'			=> true
		));

		$form->td_text(array(
			'beschreibung'			=> 'Oberkategorie Text:',
			'name'					=> 'oberkategorieTXT',
			'size'					=> 30
		));
		
		$form->td_select(array(
			'beschreibung'			=> '-- oder --<br>Oberkategorie Auswahl:',
			'name'					=> 'oberkategorieSEL',
			'values'				=> self::getOberkategorie(),
			'selected'				=> $io_groups->oberkategorie,
			'erste'					=> true
		));
		
		$form->td_text(array(
			'beschreibung'			=> 'Unterkategorie Text:',
			'name'					=> 'unterkategorieTXT',
			'size'					=> 30
		));

		$form->td_select(array(
			'beschreibung'			=> '-- oder --<br>Unterkategorie Auswahl:',
			'name'					=> 'unterkategorieSEL',
			'values'				=> self::getUnterkategorie(),
			'selected'				=> $io_groups->unterkategorie,
			'opt_group'				=> true,
			'erste'					=> true
		));
		
		$form->td_text(array(
			'beschreibung'			=> 'Gruppen Verteiler:',
			'name'					=> 'verteiler',
			'size'					=> 30,
			'value'					=> $io_groups->verteiler,
			'checking'				=> 'Mail'
		));
		
		$form->td_textarea(array(
			'anzeige'				=> 'neben',
			'beschreibung'			=> 'Mailinglisten der Gruppe:',
			'name'					=> 'listen',
			'cols'					=> 40,
			'rows'					=> 5,
			'value'					=> $io_groups->listen
		));
		
		$form->td_select(array(
			'beschreibung'	=> 'Mitglieder:',
			'name'			=> 'mitgliedschaftenID',
			'values'		=> $values,
			'multiple'		=> true,
			'selected'		=> $io_groups->mitgliedschaftenID,
			'size'			=> 10
		));
		
		$form->td_select(array(
			'beschreibung'	=> 'Gruppen, die Mitglied sind:',
			'name'			=> 'gruppenID',
			'values'		=> io_groups::getValues(),
			'opt_group'		=> true,
			'multiple'		=> true,
			'selected'		=> $io_groups->gruppenID,
			'size'			=> 10
		));
		
		$form->td_select(array(
			'beschreibung'	=> 'Berechtigungen:',
			'name'			=> 'berechtigungenID',
			'values'		=> io_permission::getValues(),
			'opt_group'		=> true,
			'multiple'		=> true,
			'selected'		=> $io_groups->berechtigungenID,
			'size'			=> 10
		));
		
		io_form::jsHead();
		io_form::jsScript();
		io_postlist::wpHide();
		
		unset($form);
	}
	
	public static function edit() {
		if(!isset($_GET['group'])) {
			?>
			
			<h1>Gruppen-Bearbeitung</h1>
			
			Als Gruppen-Leiter*in* bist du hier in der Lage, deine Gruppen zu bearbeiten. Dabei kannst du einzelne Mitglieder deiner Gruppe entfernen oder neue hinzufügen.<hr>

			<table border="0" cellpadding="5" cellspacing="0" width="100%">
				<thead>
					<tr>
						<th><b>Name</b></th>
						<th><b>Oberkategorie</b></th>
						<th><b>Unterkategorie</b></th>
						<th><b>Aktion</b></th>
					</tr>
				</thead>
			<?php
				foreach(self::getLeaderGroups() AS $group) {
					$io_group = new io_groups($group);
					
					?>
				<tr>
					<td><b><?php echo $io_group->name; ?></b></td>
					<td><?php echo $io_group->oberkategorie; ?></td>
					<td><?php echo $io_group->unterkategorie; ?></td>
					<td>
					<a  href="<?php echo io_get_current_url(); ?>&group=<?php echo $group; ?>" style="padding: 7px; background: red; font-family: Arial, Helvetica, sans-serif; font-weight: bold; color: white; border: 1px solid darkred; border-radius: 10px;">Bearbeiten</a></td>
				</tr>
					<?php
				}
			?>
			</table>
			<?php
		} else if(isset($_GET['group']) && in_array((new io_groups(get_post(array('ID' => sanitize_text_field($_GET['group'])))))->name, self::getLeaderGroups())) {
			$io_group = new io_groups(get_post(array('ID' => sanitize_text_field($_GET['group']))));
			
			if(isset($_POST['io_group_submit'])) {
				$ldapConn = ldapConnector::get();
				foreach($io_group->mitgliedschaftenID AS $user_id) {
					if(isset($_POST['io_group_mit_' . $user_id]) && $_POST['io_group_mit_' . $user_id] == true && !in_array($user_id, $io_group->leiter_innenID)) {
						$ldapConn->delUserFromGroup(get_userdata($user_id)->user_lgoin, $io_group->name);
					}
				}
				
				foreach($io_group->gruppenID AS $group_id) {
					if(isset($_POST['io_group_grp_' . $group_id]) && $_POST['io_group_grp_' . $group_id] == true) {
						$ldapConn->delGroupFromGroup(get_post(array('ID' => $group_id))->post_title, $io_group->name);
					}
				}
				
				$newUsers = explode("\n\r", sanitize_text_field($_POST['io_group_add_mit']));
				foreach($newUsers AS $user) {
					if(get_user_by('login', $user)) {
						$ldapConn->addUsersToGroup(array($user), $io_group->name);
					} else {
						?>			<b>Der*die* Benutzer*in* <?php echo $user; ?> ist im IGELoffice nicht registriert.</b><hr>
<?php
					}
				}
				
				$newGroups = explode("\n\r", sanitize_text_field($_POST['io_group_grp_mit']));
				foreach($newGroups AS $group) {
					if(get_post(array('post_title' => $group)) != null) {
						$ldapConn->addGroupToGroup($group, $io_group->name);
					} else {
						?>			<b>Die Gruppe <?php echo $group; ?> ist nicht existent.</b><hr>
<?php
					}
				}
				
				$io_group = new io_groups(get_post(array('ID' => sanitize_text_field($_GET['group']))));
				?>			<b>Bearbeitung abgeschlossen.</b>
<?php
			}
			
			?>
			<form action="<?php echo io_get_current_url(); ?>" method="post">
			
				<h1>Gruppenbearbeitung</h1>
				<h2><?php echo $io_group->name; ?></h2>
				<b>Oberkategorie:</b> <?php echo $io_group->oberkategorie; ?><br>
				<b>Unterkategorie:</b> <?php echo $io_group->unterkategorie; ?><br>
				<b>Leiter*innen*:</b><br>
				<?php 
					foreach($io_group->leiter_innen AS $leiter_in) {
						?>				<?php echo $leiter_in; ?><br>
<?php
					}
				?><br>
				<b>Verteiler:</b><br>
				<?php 
					foreach($io_group->verteiler AS $verteiler) {
						?>				<?php echo $verteiler; ?><br>
<?php
					}
				?><br>
				<b>Listen:</b><br>
				<?php 
					foreach($io_group->listen AS $listen) {
						?>				<?php echo $listen; ?><br>
<?php
					}
				?><br>
				
				<b>Mitglieder:</b>
				<table border="0" cellpadding="5" cellspacing="0" width="100%">
					<thead>
						<tr>
							<th width="10%"><b>Entfernen</b></th>
							<th width="90%"><b>Name</b></th>
						</tr>
					</thead>
					<?php
						foreach($io_group->mitgliedschaftenID AS $user_id) {
							?>					<tr>
							<td><?php
							if(!in_array($user_id, $io_group->leiter_innenID)) {
								?><input type="checkbox" name="io_group_mit_<?php echo $user_id ?>" value="true"><?php
							} ?></td>
							<td><?php echo get_userdata($user_id)->user_login; ?></td>
						<td></td>
					</tr>
<?php
						}
					?>
				</table><br>
				
				<b>Mitglieder hinzufügen (Je Zeile ein Mitglied):</b><br>
				<textarea name="io_group_add_mit" cols="20" rows="5"></textarea><br>
				
				<b>Gruppen als Mitglied:</b>
				<table border="0" cellpadding="5" cellspacing="0" width="100%">
					<thead>
						<tr>
							<th width="10%"><b>Entfernen</b></th>
							<th width="90%"><b>Name</b></th>
						</tr>
					</thead>
					<?php
						foreach($io_group->gruppenID AS $group_id) {
							?>					<tr>
							<td><input type="checkbox" name="io_group_grp_<?php echo $group_id ?>" value="true"></td>
							<td><?php echo get_post(array('ID' => $group_id))->post_title; ?></td>
						<td></td>
					</tr>
<?php
						}
					?>
				</table><br>
				
				<b>Gruppen hinzufügen (Je Zeile eine):</b><br>
				<textarea name="io_group_add_grp" cols="20" rows="5"></textarea><br><br>
				
				<input type="submit" name="io_group_submit" value="Abschicken">
			</form>	
			<?php
		}
	}


	/*****************************************************
	 ************** Database & LDAP Change ***************
	 *****************************************************/
	public static function save($post_id) {
		if(get_post_type($post_id) == self::$POST_TYPE) {
			if( !isset($_POST['io_groups_nonce']) || 
				!wp_verify_nonce($_POST['io_groups_nonce'], 'io_groups') || 
				defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
				return;
			}
			
			$ldapConn = ldapConnector::get();
			if(get_post_meta($post_id, self::$PREFIX . '_active', true) == null) {
				$ldapConn->addGroup(get_the_title($post_id));
				update_post_meta($post_id, self::$PREFIX . '_active', true);
			}
			
			function checkKat($art, $name) {
				$artK = ($art == "Oberkategorie" ? "ok" : "uk");
				if(!get_option('io_grp_' . $artK . '_' . utf8_encode($name), false)) {
					add_option('io_grp_' . $artK . '_' . utf8_encode($name), $name);
				}
			}
			
			$io_groups = new io_groups(get_post($post_id), true);
			
			$isOberkategorie = false;
			if($_POST[self::$PREFIX . '_oberkategorieTXT'] == "" && $_POST[self::$PREFIX . '_oberkategorieSEL'] == -1) {
				$ldapConn->setGroupAttribute(get_the_title($post_id), self::$PREFIX . 'Oberkategorie', utf8_encode("Nicht Kategorisiert"), "replace", $io_groups->oberkategorie);
				checkKat("Oberkategorie", "Nicht Kategorisiert");
			} elseif($_POST[self::$PREFIX . '_oberkategorieTXT'] != "") {
				$ldapConn->setGroupAttribute(get_the_title($post_id), self::$PREFIX . 'Oberkategorie', utf8_encode(sanitize_text_field($_POST[self::$PREFIX . '_oberkategorieTXT'])), "replace", $io_groups->oberkategorie);
				checkKat("Oberkategorie", sanitize_text_field($_POST[self::$PREFIX . '_oberkategorieTXT']));
				$isOberkategorie = true;
			} elseif($_POST[self::$PREFIX . '_oberkategorieTXT'] == "" && $_POST[self::$PREFIX . '_oberkategorieSEL'] != -1 && get_option('io_grp_ok_' . utf8_encode(sanitize_text_field($_POST[self::$PREFIX . '_oberkategorieSEL'])), false)) {
				$ldapConn->setGroupAttribute(get_the_title($post_id), self::$PREFIX . 'Oberkategorie', sanitize_text_field($_POST[self::$PREFIX . '_oberkategorieSEL']), "replace", $io_groups->oberkategorie);
				$isOberkategorie = true;
			} elseif(!get_option('io_grp_ok_' . utf8_encode(sanitize_text_field($_POST[self::$PREFIX . '_oberkategorieSEL'])), false)) {
				//TODO FEHLER
			}
			
			if($_POST[self::$PREFIX . '_unterkategorieTXT'] != "") {
				$ldapConn->setGroupAttribute(get_the_title($post_id), self::$PREFIX . 'Unterkategorie', utf8_encode(sanitize_text_field($_POST[self::$PREFIX . '_unterkategorieTXT'])), "replace", $io_groups->unterkategorie);
				checkKat("Unterkategorie", sanitize_text_field($_POST[self::$PREFIX . '_unterkategorieTXT']));
			} elseif($_POST[self::$PREFIX . '_unterkategorieTXT'] == "" && $_POST[self::$PREFIX . '_unterkategorieSEL'] != -1 && get_option('io_grp_uk_' . utf8_encode(sanitize_text_field($_POST[self::$PREFIX . '_unterkategorieSEL'])), false)) {
				$ldapConn->setGroupAttribute(get_the_title($post_id), self::$PREFIX . 'Unterkategorie', sanitize_text_field($_POST[self::$PREFIX . '_unterkategorieSEL']), "replace", $io_groups->unterkategorie);
			} elseif(!get_option('io_grp_uk_' . utf8_encode(sanitize_text_field($_POST[self::$PREFIX . '_unterkategorieSEL'])), false)) {
				//TODO FEHLER
			}
			
			if(count($_POST[self::$PREFIX . '_leiter_innenID']) > 0) {
				$neueLeiterInnen = array_diff($_POST[self::$PREFIX . '_leiter_innenID'], $io_groups->leiter_innenID);
				foreach($neueLeiterInnen AS $leiter_in) {
					if(get_userdata($leiter_in)->user_login != "") {
						$ldapConn->setGroupAttribute(get_the_title($post_id), 'owner', get_userdata($leiter_in)->user_login);
					}
				}
			}
			
			if(count($io_groups->leiter_innenID) > 0) {
				$alteLeiterInnen = array_diff($io_groups->leiter_innenID, $_POST[self::$PREFIX . '_leiter_innenID']);
				foreach($alteLeiterInnen AS $leiter_in) {
					if(get_userdata($leiter_in)->user_login != "") {
						$ldapConn->delGroupAttribute(get_the_title($post_id), 'owner', get_userdata($leiter_in)->user_login);
					}
				}
			}
			
			if(!filter_var($_POST[self::$PREFIX . '_verteiler'], FILTER_VALIDATE_EMAIL)) {
				add_action('admin_notices', array('io_groups', 'errorMessageVerteiler'));
			} else {
				$ldapConn->setGroupAttribute(get_the_title($post_id), self::$PREFIX . 'Verteiler', sanitize_text_field($_POST[self::$PREFIX . '_verteiler']));
			}
			
			$listen = explode("\n\r", $_POST[self::$PREFIX . '_listen']);
			foreach($listen AS $liste) {
				if(!filter_var($liste, FILTER_VALIDATE_EMAIL)) {
					add_action('admin_notices', array('io_groups', 'errorMessageListe'));
				} else {
					$ldapConn->setGroupAttribute(get_the_title($post_id), self::$PREFIX . 'Listen', sanitize_text_field($liste));
				}
			}
			
			if(count($_POST[self::$PREFIX . '_leiter_innenID']) > 0) {
				foreach($_POST[self::$PREFIX . '_leiter_innenID'] AS $key => $leiter_in) {
					if(!(isset($_POST[self::$PREFIX . '_mitgliedschaftenID'][$key]) && $_POST[self::$PREFIX . '_mitgliedschaftenID'][$key] != $leiter_in)) {
						$_POST[self::$PREFIX . '_mitgliedschaftenID'][$key] = $leiter_in;
					}
				}
			}
			
			if(count($_POST[self::$PREFIX . '_mitgliedschaftenID']) > 0) {
				$neueMitgliederID = array_diff($_POST[self::$PREFIX . '_mitgliedschaftenID'], $io_groups->mitgliedschaftenID);
				$neueMitglieder = array();
				foreach($neueMitgliederID AS $mitglied) {
					$neueMitglieder[] = get_userdata($mitglied)->user_login;
				}
				$ldapConn->addUsersToGroup($neueMitglieder, get_the_title($post_id));
			}
			
			if(count($io_groups->mitgliedschaftenID) > 0) {
				$alteMitglieder = array_diff($io_groups->mitgliedschaftenID, $_POST[self::$PREFIX . '_mitgliedschaftenID']);
				foreach($alteMitglieder AS $mitglied) {
					$ldapConn->delUserFromGroup(get_userdata($mitglied)->user_login, get_the_title($post_id));
				}
			}
			
			if(count($_POST[self::$PREFIX . '_gruppenID']) > 0) {
				$neueGruppen = array_diff($_POST[self::$PREFIX . '_gruppenID'], $io_groups->gruppenID);
				foreach($neueGruppen AS $gruppe) {
					$ldapConn->addGroupToGroup(get_the_title($gruppe), get_the_title($post_id));
				}
			}
			
			if(count($io_groups->gruppenID) > 0) {
				$alteGruppen = array_diff($io_groups->gruppenID, $_POST[self::$PREFIX . '_gruppenID']);
				foreach($alteGruppen AS $gruppe) {
					$ldapConn->delGroupFromGroup(get_the_title($gruppe), get_the_title($post_id));
				}
			}
			
			if(count($_POST[self::$PREFIX . '_berechtigungenID']) > 0) {
				$neueBerechtigung = array_diff($_POST[self::$PREFIX . '_berechtigungenID'], $io_groups->berechtigungenID);
				foreach($neueBerechtigung AS $berechtigung) {
					$ldapConn->addGroupPermission(get_the_title($post_id), get_the_title($berechtigung));
				}
			}
			
			if(count($io_groups->berechtigungenID) > 0) {
				$alteBerechtigung = array_diff($io_groups->berechtigungenID, $_POST[self::$PREFIX . '_berechtigungenID']);
				foreach($alteBerechtigung AS $berechtigung) {
					$ldapConn->delGroupPermission(get_the_title($post_id), get_the_title($berechtigung));
				}
			}
		}
	}
	
	public static function errorMessageVerteiler() {
		?>
		
	<div class="error">
		<p>Fehler: Die eingebe Verteiler-Adresse ist ungültig.</p>
	</div>	
		 
		<?php
		
		remove_action('admin_notices', 'errorMessageVerteiler');
	}
	
	public static function errorMessageListen() {
		?>
		
	<div class="error">
		<p>Fehler: Eine der eingegebenen Listen war ungültig.</p>
	</div>	
		 
		<?php
		
		remove_action('admin_notices', 'errorMessageListen');
	}
	
	public static function delete($post_id) {
		if(get_post_type($post_id) == self::$POST_TYPE) {
			//TODO: VALIDIERUNG? SIND NOCH MENSCHEN MITGLIED? SIND NOCH BERECHTIGUNGEN ZUGEORDNET?
			$ldapConn = ldapConnector::get();
			$ldapConn->delGroup(get_the_title($post_id));
		}
	}
	
	/*****************************************************
	 ****************** Hilfsfunktionen ******************
	 *****************************************************/
	public static function getValues() {
		$posts = get_posts(array(
			'post_type'			=> self::$POST_TYPE,
			'order_by'			=> 'title',
			'posts_per_page'	=> -1
		));
		
		$return = array();
		foreach($posts AS $post) {
			$groups = new io_groups($post);
			if($groups->unterkategorie != "") {
				$return[$groups->oberkategorie][$groups->unterkategorie][$post->ID] = $post->post_title;
			} else {
				$return[$groups->oberkategorie][$post->ID] = $post->post_title;
			}
		}
		
		return $return;
	}
	
	public static function getOberkategorie() {
		$posts = get_posts(array(
			'post_type'			=> self::$POST_TYPE,
			'order_by'			=> 'title',
			'posts_per_page'	=> -1
		));
		
		$return = array();
		foreach($posts AS $post) {
			$oberkategorie = new io_groups($post);
			
			if(!in_array($oberkategorie->oberkategorie, $return)) {
				$return[$oberkategorie->oberkategorie] = get_option("io_grp_ok_" . $oberkategorie->oberkategorie);
			}
		}

		return $return;
	}
	
	public static function getUnterkategorie() {
		$posts = get_posts(array(
			'post_type'		=> self::$POST_TYPE,
			'order_by'		=> 'title'
		));
		
		$return = array();
		foreach($posts AS $post) {
			$unterkategorie = new io_groups($post);
			
			if(!in_array($unterkategorie->unterkategorie, $return)) {
				$return[$unterkategorie->oberkategorie][$unterkategorie->unterkategorie] = get_option("io_grp_uk_" . $unterkategorie->unterkategorie, 'Fehler - Bitte an Webmaster wenden!');
			}
		}

		return $return;
	}
	
	public static function getLeaderGroups() {
		$ldapConn = ldapConnector::get();
		$groups = $ldapConn->getUserGroups(get_current_user()->user_login);
		
		$leaderOf = array();
		foreach($groups AS $group) {
			$group = explode(',', substr($group, 3))[0];
			$leaders = $ldapConn->getGroupAttribute($group, 'owner');
			
			foreach($leaders AS $leader) {
				if($leader == get_current_user()->user_login) {
					array_push($leaderOf, $group);
					break;
				}
			}
		}
		return $leaderOf;
	}
	
	/*****************************************************
	 ***************** Register Posttype *****************
	 *****************************************************/
	/**
	 * Post-Type Variable für Gruppen
	 * 
	 * @var String Post-Type Variable für Gruppen 
	 */
	public static $POST_TYPE = "io_groups";
	
	/**
	 * Daten von Gruppen 
	 * 
	 * @var Array String => String 
	 */
	public static $CRITERIAS = array(
		'groups_leader'			=> 'Gruppen Leiter*in',
		'groups_intern_lists'	=> 'Gruppen Verteiler',
		'groups_public_lists'	=> 'Gruppen Mailinglisten'			
	);
	
	/**
	 * Post-Type Prefix
	 * 
	 * @var String Prefix
	 */
	public static $PREFIX = "groups";

	/**
	 * Register Posttype Groups
	 */
	public static function register() {
		parent::register_pt(self::$POST_TYPE, "Gruppe", "Gruppen", "io_groups", "gruppen");
	}
	
	/**
	 * Angabe, welche Spalten angezeigt werden sollen
	 * 
	 * @param Array Default-Spalten
	 * @return Array Anzuzeigender Spalten
	 */
	public static function postlists($defaults) {
		return parent::postlists($defaults, self::$CRITERIAS);
	}
	
	/**
	 * Anzeige von Spalteninhalt von neuer Spalten
	 * 
	 * @param String $column_name
	 * @param int $post_id
	 */
	public static function postlist_column($column_name, $post_id) {
		parent::postlist_column(self::$POST_TYPE, $column_name, $post_id, self::$CRITERIAS);
	}
	
	/**
	 * Angabe der sortierbaren Spalten
	 * 
	 * @param Array $sort_columns Sortable Columns
	 */
	public static function postlist_sorting($sort_columns) {
		return parent::postlist_sorting($sort_columns, self::$CRITERIAS);
	}
	
	/**
	 * Sortierung der zu sortierenden Spalten
	 * 
	 * @param wp_query $vars Sortiervariablen
	 * @return wp_query Sortiervariablen
	 */
	public static function postlist_orderby($vars) {
		return parent::postlist_orderby(self::$POST_TYPE, $vars, self::$CRITERIAS);
	}
	
	/**
	 * Anzeige Filter-Formular
	 */
	public static function postlist_filtering() {
		parent::postlist_filtering(self::$POST_TYPE, self::$PREFIX, self::$CRITERIAS);
	}
	
	/**
	 * Filterung
	 * 
	 * @param wp_query $query WP_Query
	 * @return wp_query Neuer WP_Query
	 */
	public static function postlist_filtering_sort($query) {
		return parent::postlist_filtering_sort($query, self::$POST_TYPE, self::$CRITERIAS);
	}
}
