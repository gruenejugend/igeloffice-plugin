<?php

/**
 * Klasse zur Verwaltung von Berechtigungen im IGELoffice
 *
 * @author KayWilhelm
 */
class io_permission extends io_postlist {
	private $name;
	private $system;
	private $kategorie;
	private $berechtigte;
	private $berechtigteID;
	
	public function __construct($post) {
		$id = $post->ID;
		$this->name				= get_tht_title($id);
		
		$ldapConn = ldapConnector::get();
		
		if(get_post_meta($id, self::$PREFIX . '_active', true) == true) {
			$this->system			= $ldapConn->getPermissionAttribute(get_the_title($id), "permission_system");
			$this->berechtigte		= $ldapConn->getPermissionAttribute(get_the_title($id), "permission_berechtigte");
			$this->kategorie		= $ldapConn->getPermissionAttribute(get_the_title($id), "permission_kategorie");
			
			$this->berechtigteID = array();
			foreach($this->berechtigte AS $berechtigte) {
				$users = get_users(array(
					'meta_key'		=> 'display_name',
					'meta_value'	=> $berechtigte
				));
				
				$user = (isset($users[0]) ? $user[0] : false);
				$user_id = ($user ? $user->ID : false);
				
				array_push($this->berechtigteID, $user_id);
			}
		}
	}
	
	public function __get($name) {
		switch($name) {
			case 'name':
				return $this->name;
			case 'system':
				return $this->system;
			case 'berechtigte':
				return $this->berechtigte;
			case 'berechtigteID':
				return $this->berechtigteID;
		};
	}
	
	/*****************************************************
	 ********************** Masken ***********************
	 *****************************************************/
	public static function metabox() {
		add_meta_box("io_permission_mb", "Berechtigung", array("io_permission", "updBackendForm"), self::$POST_TYPE, "normal", "default");
	}
	
	public static function updBackendForm($post) {
		$io_permission = new io_permission($post);
		
		$form = new io_form(array(
			'form'		=> false,
			'prefix'	=> self::$PREFIX,
			'table'		=> true,
			'submit'	=> 'pt'
		));
		
		wp_nonce_field('io_permission', 'io_permission_nonce');
		
		$form->td_text(array(
			'beschreibung'	=> 'System:',
			'name'			=> self::$PREFIX . '_system',
			'size'			=> 20,
			'value'			=> $this->system
		));
		
		$form->td_text(array(
			'beschreibung'	=> 'Kategorie Text:',
			'name'			=> self::$PREFIX . '_kategorieTXT',
			'size'			=> 20
		));
		
		$form->td_select(array(
			'beschreibung'			=> '-- oder --<br>Kategorie Auswahl:',
			'name'					=> self::$PREFIX . '_KategorieSEL',
			'values'				=> self::getKategorie(),
			'selected'				=> $io_permission->Kategorie,
			'optgroup'				=> true,
			'checking'				=> 'Selection',
			'first'					=> true
		));
		
		//SELECT
		
		?>		<tr>
			<td>Berechtigte Mitglieder:</td>
			<td><?php
				
				wp_dropdown_users(array(
					'orderby'		=> 'display_name',
					'multi'			=> true,
					'selected'		=> $io_permission->berechtigteID,
					'name'			=> self::$PREFIX . '_berechtigteID'
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
			if( !isset($_POST['io_permission_nonce']) || 
				!wp_verify_nonce($_POST['io_permission_nonce'], 'io_permission') || 
				defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
				return;
			}
		
			$ldapConn = ldapConnector::get();
			if(get_post_meta($post_id, self::$PREFIX . '_active', true) == null) {
				$ldapConn->addPermission(get_the_title($post_id));
				update_post_meta($post_id, self::$PREFIX . '_active', true);
			}

			$ldapConn->setPermissionAttribute(get_the_title($post_id), self::$PREFIX . '_system', sanitize_text_field($_POST[self::$PREFIX . '_system']));
			
			function checkKat($name) {
				add_option('io_per_' . $artK . '_' . utf8_encode($name), $name);
			}

			if($_POST[self::$PREFIX . '_KategorieTXT'] == "" && $_POST[self::$PREFIX . '_KategorieSEL'] == 0) {
				$ldapConn->setGroupAttribute(get_the_title($post_id), self::$PREFIX . '_Kategorie', utf8_encode("Nicht Kategorisiert"));
				checkKat("Nicht Kategorisiert");
			} elseif($_POST[self::$PREFIX . '_KategorieTXT'] != "") {
				$ldapConn->setGroupAttribute(get_the_title($post_id), self::$PREFIX . '_Kategorie', utf8_encode(sanitize_text_field($_POST[self::$PREFIX . '_KategorieTXT'])));
				checkKat("Kategorie", sanitize_text_field($_POST[self::$PREFIX . '_KategorieTXT']));
			} elseif($_POST[self::$PREFIX . '_KategorieTXT'] == "" && $_POST[self::$PREFIX . '_KategorieSEL'] != 0 && get_option('io_per_' . utf8_encode(sanitize_text_field($_POST[self::$PREFIX . '_KategorieSEL'])), false)) {
				$ldapConn->setGroupAttribute(get_the_title($post_id), self::$PREFIX . '_Kategorie', sanitize_text_field($_POST[self::$PREFIX . '_KategorieSEL']));
			} elseif(!get_option('io_per_' . utf8_encode(sanitize_text_field($_POST[self::$PREFIX . '_KategorieSEL'])), false)) {
				//FEHLER
			}
			
			foreach($_POST[self::$PREFIX . '_berechtigteID'] AS $berechtigte) {
				//TODO: USER ADD PERMISSION, ABER WIE?
				//FESTSTELLUNG: NEUE BERECHTIGUNG? ODER ZU LÖSCHENDE BERECHTIGUNGEN?
				$ldapConn->addUserPermission(get_the_title($post_id), get_userdata($berechtigte)->user_login);
			}
		}
	}
	
	public static function delete($post_id) {
		if(get_post_type($post_id) == self::$POST_TYPE) {
			//TODO: VALIDIERUNG? SIND NOCH MENSCHEN MITGLIED? SIND NOCH BERECHTIGUNGEN ZUGEORDNET?
			$ldapConn = ldapConnector::get();
			$ldapConn->delPermission(get_the_title($post_id));
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
			$return[$post->ID] = $post->post_title;
		}
		
		return $return;
	}
	
	public static function getKategorie() {
		$posts = get_posts(array(
			'post_type'		=> self::$POST_TYPE,
			'order_by'		=> 'title'
		));
		
		$return = array();
		foreach($posts AS $post) {
			$kategorie = new io_permission($post);
			
			if(!in_array($kategorie->kategorie, $return)) {
				$return[$kategorie->kategorie] = get_option("io_per_k_" . $kategorie->kategorie);
			}
		}
	}
	
	
	/*****************************************************
	 ***************** Register Posttype *****************
	 *****************************************************/
	/**
	 * Post-Type Variable für Berechtigungen
	 * 
	 * @var String Post-Type Variable für Berechtigungen 
	 */
	public static $POST_TYPE = "io_permissions";
	
	/**
	 * Daten von Berechtigungen
	 * 
	 * @var Array String => String 
	 */
	public static $CRITERIAS = array(
		'permissions_system'			=> 'System',			
	);
	
	/**
	 * Post-Type Prefix
	 * 
	 * @var String Prefix
	 */
	public static $PREFIX = "permission";

	/**
	 * Register Posttype Permissions
	 */
	public static function register() {
		parent::register_pt(self::$POST_TYPE, "Berechtigung", "Berechtigungen", "Berechtigungen", "berechtigung");
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