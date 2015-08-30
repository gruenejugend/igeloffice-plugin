<?php

/**
 * Weitere, noch nicht implementierte Features:
 * - Mitgliedschaftsbeantragung übers Frontend (und Zulassung)
 * - Gruppenverwaltung übers Frontend (Mitglieder hinzufügen, entfernen, Leiter*innen verändern, Verteiler ändern, Listen hinzufügen)
 * - Listenerstellung, bei Eintrag
 * - Gruppe Mitglied einer anderen Gruppe werden
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
	private $ldapConn;
	
	public function __construct($post) {
		$id = $post->ID;
		$this->name				= get_the_title($id);
		
		$ldapConn = ldapConnector::get();
		
		if(get_post_meta($id, self::$PREFIX . '_active', true) == true) {
			$this->oberkategorie	= $ldapConn->getGroupAttribute($this->name, self::$PREFIX . '_oberkategorie');
			$this->unterkategorie	= $ldapConn->getGroupAttribute($this->name, self::$PREFIX . '_unterkategorie');
			$this->leiter_innen		= $ldapConn->getGroupAttribute($this->name, self::$PREFIX . '_leiter_in');
			$this->verteiler		= $ldapConn->getGroupAttribute($this->name, self::$PREFIX . '_verteiler');
			$this->listen			= $ldapConn->getGroupAttribute($this->name, self::$PREFIX . '_listen');
			$this->mitgliedschaften	= $ldapConn->getGroupAttribute($this->name, self::$PREFIX . '_mitgliedschaft');
			
			$this->leiter_innenID = array();
			foreach($this->leiter_innen AS $leiter_in) {
				$users = get_users(array(
					'meta_key'		=> 'display_name',
					'meta_value'	=> $leiter_in
				));
				
				$user = (isset($users[0]) ? $user[0] : false);
				$user_id = ($user ? $user->ID : false);
				
				array_push($this->leiter_innenID, $user_id);
			}
			
			$this->mitgliedschaftenID = array();
			foreach($this->mitgliedschaften AS $mitglied) {
				$users = get_users(array(
					'meta_key'		=> 'display_name',
					'meta_value'	=> $mitglied
				));
				
				$user = (isset($users[0]) ? $user[0] : false);
				$user_id = ($user ? $user->ID : false);
				
				array_push($this->mitgliedschaftenID, $user_id);
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
		};
	}
	
	/*****************************************************
	 ********************** Masken ***********************
	 *****************************************************/
	public static function metabox() {
		add_meta_box("io_groups_mb", "Gruppe", array("io_groups", "updBackendForm"), self::$POST_TYPE, "normal", "default");
	}
	
	public static function updBackendForm($post) {
		$io_groups = new io_groups($post);
		
		$form = new io_form(array(
			'form'		=> false,
			'prefix'	=> self::$PREFIX,
			'table'		=> true,
			'submit'	=> 'pt'
		));
		
		wp_nonce_field('io_groups', 'io_groups_nonce');
		
		?>		<tr>
			<td>Leiter*in:</td>
			<td><?php
				
				wp_dropdown_users(array(
					'orderby'		=> 'display_name',
					'multi'			=> true,
					'selected'		=> $io_groups->leiter_innenID,
					'name'			=> self::$PREFIX . '_leiter_innenID'
				));
				
				?></td>
		</tr>
<?php

		$form->td_text(array(
			'beschreibung'			=> 'Oberkategorie Text:',
			'name'					=> self::$PREFIX . '_oberkategorieTXT',
			'size'					=> 30
		));
		
		$form->td_select(array(
			'beschreibung'			=> '-- oder --<br>Oberkategorie Auswahl:',
			'name'					=> self::$PREFIX . '_oberkategorieSEL',
			'values'				=> self::getOberkategorie(),
			'selected'				=> $io_groups->oberkategorie,
			'first'					=> true
		));
		
		$form->td_text(array(
			'beschreibung'			=> 'Unterkategorie Text:',
			'name'					=> self::$PREFIX . '_unterkategorieTXT',
			'size'					=> 30
		));

		$form->td_select(array(
			'beschreibung'			=> '-- oder --<br>Unterkategorie Auswahl:',
			'name'					=> self::$PREFIX . '_unterkategorieSEL',
			'values'				=> self::getUnterkategorie(),
			'selected'				=> $io_groups->unterkategorie,
			'optgroup'				=> true,
			'first'					=> true
		));
		
		$form->td_text(array(
			'beschreibung'			=> 'Gruppen Verteiler:',
			'name'					=> self::$PREFIX . '_verteiler',
			'size'					=> 30,
			'value'					=> $io_groups->verteiler,
			'checking'				=> 'Mail'
		));
		
		$form->td_textarea(array(
			'anzeige'				=> 'neben',
			'beschreibung'			=> 'Mailinglisten der Gruppe:',
			'name'					=> self::$PREFIX . '_listen',
			'cols'					=> 40,
			'rows'					=> 5,
			'value'					=> $io_groups->listen
		));
		
		?>		<tr>
			<td>Mitglieder:</td>
			<td><?php
				
				wp_dropdown_users(array(
					'orderby'		=> 'display_name',
					'multi'			=> true,
					'selected'		=> $io_groups->mitgliedschaftenID,
					'name'			=> self::$PREFIX . '_mitgliedschaftenID'
				));
				
				?></td>
		</tr>
<?php
		
		io_form::jsHead();
		io_form::jsScript();
		io_postlist::wpHide();
		
		unset($form);
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
			
			$isOberkategorie = false;
			if($_POST[self::$PREFIX . '_oberkategorieTXT'] == "" && $_POST[self::$PREFIX . '_oberkategorieSEL'] == 0) {
				$ldapConn->setGroupAttribute(get_the_title($post_id), self::$PREFIX . '_oberkategorie', utf8_encode("Nicht Kategorisiert"));
				checkKat("Oberkategorie", "Nicht Kategorisiert");
				$ldapConn->setGroupAttribute(get_the_title($post_id), self::$PREFIX . '_unterkategorie', utf8_encode(""));
				checkKat("Unterkategorie", "");
			} elseif($_POST[self::$PREFIX . '_oberkategorieTXT'] != "") {
				$ldapConn->setGroupAttribute(get_the_title($post_id), self::$PREFIX . '_oberkategorie', utf8_encode(sanitize_text_field($_POST[self::$PREFIX . '_oberkategorieTXT'])));
				checkKat("Oberkategorie", sanitize_text_field($_POST[self::$PREFIX . '_oberkategorieTXT']));
				$isOberkategorie = true;
			} elseif($_POST[self::$PREFIX . '_oberkategorieTXT'] == "" && $_POST[self::$PREFIX . '_oberkategorieSEL'] != 0 && get_option('io_grp_' . utf8_encode(sanitize_text_field($_POST[self::$PREFIX . '_oberkategorieSEL'])), false)) {
				$ldapConn->setGroupAttribute(get_the_title($post_id), self::$PREFIX . '_oberkategorie', sanitize_text_field($_POST[self::$PREFIX . '_oberkategorieSEL']));
				$isOberkategorie = true;
			} elseif(!get_option('io_grp_' . utf8_encode(sanitize_text_field($_POST[self::$PREFIX . '_oberkategorieSEL'])), false)) {
				//FEHLER
			}
			
			if($isOberkategorie && $_POST[self::$PREFIX . '_unterkategorieTXT'] == "" && $_POST[self::$PREFIX . '_unterkategorieSEL'] == 0) {
				$ldapConn->setGroupAttribute(get_the_title($post_id), self::$PREFIX . '_unterkategorie', utf8_encode(""));
			} elseif($_POST[self::$PREFIX . '_unterkategorieTXT'] != "") {
				$ldapConn->setGroupAttribute(get_the_title($post_id), self::$PREFIX . '_unterkategorie', utf8_encode(sanitize_text_field($_POST[self::$PREFIX . '_unterkategorieTXT'])));
				checkKat("Unterkategorie", sanitize_text_field($_POST[self::$PREFIX . '_unterkategorieTXT']));
			} elseif($_POST[self::$PREFIX . '_unterkategorieTXT'] == "" && $_POST[self::$PREFIX . '_unterkategorieSEL'] != 0 && get_option('io_grp_' . utf8_encode(sanitize_text_field($_POST[self::$PREFIX . '_unterkategorieSEL'])), false)) {
				$ldapConn->setGroupAttribute(get_the_title($post_id), self::$PREFIX . '_unterkategorie', sanitize_text_field($_POST[self::$PREFIX . '_unterkategorieSEL']));
			} elseif(!get_option('io_grp_' . utf8_encode(sanitize_text_field($_POST[self::$PREFIX . '_unterkategorieSEL'])), false)) {
				//FEHLER
			}
			
			//TODO: PRÜFUNG NAME RICHTIG?
			foreach($_POST[self::$PREFIX . '_leiter_innenID'] AS $leiter_in) {
				$ldapConn->setGroupAttribute(get_the_title($post_id), self::$PREFIX . '_leiter_in', get_userdata($leiter_in)->display_name);
			}
			
			//TODO: PRÜFUNG NAME RICHTIG?
			if(!filter_var($_POST[self::$PREFIX . '_verteiler'], FILTER_VALIDATE_EMAIL)) {
				add_action('admin_notices', array('io_groups', 'errorMessageVerteiler'));
			} else {
				$ldapConn->setGroupAttribute(get_the_title($post_id), self::$PREFIX . '_verteiler', sanitize_text_field($_POST[self::$PREFIX . '_verteiler']));
			}
			
			//TODO: PRÜFUNG NAME RICHTIG?
			$listen = explode("\n\r", $_POST[self::$PREFIX . '_listen']);
			foreach($listen AS $liste) {
				if(!filter_var($liste, FILTER_VALIDATE_EMAIL)) {
					add_action('admin_notices', array('io_groups', 'errorMessageListe'));
				} else {
					$ldapConn->setGroupAttribute(get_the_title($post_id), self::$PREFIX . '_listen', sanitize_text_field($liste));
				}
			}
			
			foreach($_POST[self::$PREFIX . '_leiter_innenID'] AS $key => $leiter_in) {
				if(!(isset($_POST[self::$PREFIX . '_mitgliedschaftenID'][$key]) && $_POST[self::$PREFIX . '_mitgliedschaftenID'][$key] != $leiter_in)) {
					$_POST[self::$PREFIX . '_mitgliedschaftenID'][$key] = $leiter_in;
				}
			}
			
			//TODO: PRÜFUNG NAME RICHTIG?
			foreach($_POST[self::$PREFIX . '_mitgliedschaftenID'] AS $mitglied) {
				$ldapConn->addUserToGroup(get_the_title($post_id), get_userdata($mitglied)->display_name);
			}
		}
	}
	
	public static function errorMessageVerteiler() {
		?>
		
	<div class="error"
		<p>Fehler: Die eingebe Verteiler-Adresse ist ungültig.</p>
	</div>	
		 
		<?php
		
		remove_action('admin_notices', 'errorMessageVerteiler');
	}
	
	public static function errorMessageListen() {
		?>
		
	<div class="error"
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
			'post_type'		=> self::$POST_TYPE,
			'order_by'		=> 'title'
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
			'post_type'		=> self::$POST_TYPE,
			'order_by'		=> 'title'
		));
		
		$return = array();
		foreach($posts AS $post) {
			$oberkategorie = new io_groups($post);
			
			if(!in_array($oberkategorie->oberkategorie, $return)) {
				$return[$oberkategorie->oberkategorie] = get_option("io_grp_ok_" . $oberkategorie->oberkategorie);
			}
		}
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
				$return[$unterkategorie->oberkategorie][$unterkategorie->unterkategorie] = get_option("io_grp_uk_" . $unterkategorie->oberkategorie);
			}
		}
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
		parent::register_pt(self::$POST_TYPE, "Gruppe", "Gruppen", "Gruppen", "gruppen");
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
