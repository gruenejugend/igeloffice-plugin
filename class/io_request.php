<?php

/**
 * Klasse zur Regelung von Anfragen für Berechtigungen oder Gruppenmitgliedschaften
 *
 * @author KayWilhelm
 */
class io_request extends io_postlist {
	private $id;
	private $name;
	private $art;
	private $user;
	private $active;
	private $group;
	private $permission;
	private $status;
	
	public function __construct($post) {
		$this->id = $post->ID;
		$this->name				= get_the_title($this->id);
		$this->art				= get_post_meta($this->id, self::$PREFIX . '_art', true);
		$this->user				= get_post_meta($this->id, self::$PREFIX . '_user', true);
		$this->active			= get_post_meta($this->id, self::$PREFIX . '_aktiv', true);
		if($this->art == 'p') {
			$this->permission	= get_post_meta($this->id, self::$PREFIX . '_permission', true);
		}
		
		if($this->art == 'g') {
			$this->group		= get_post_meta($this->id, self::$PREFIX . '_group', true);
		}
		
		$this->status			= get_post_meta($this->id, self::$PREFIX . '_status', true);
	}
	
	public function __get($name) {
		switch($name) {
			case 'name':
				return $this->name;
			case 'art':
				return $this->art;
			case 'user':
				return $this->user;
			case 'active':
				return $this->active;
			case 'group':
				return $this->group;
			case 'permission':
				return $this->permission;
			case 'status':
				return $this->status;
		}
	}
	
	public function activate() {
		$this->active			= update_post_meta($this->id, self::$PREFIX . '_aktiv', 1);
	}
	
	/*****************************************************
	 ********************** Masken ***********************
	 *****************************************************/
	public static function metabox() {
		add_meta_box("io_requests_mb", "Anfrage", array("io_requests", "updBackendForm"), self::$POST_TYPE, "normal", "default");
		add_meta_box("io_requests_buttpn_mb", "Bearbeitung", array("io_requests", "updBackendFormButton"), self::$POST_TYPE, "side", "default");
	}
	
	public static function menu() {
		remove_submenu_page('edit.php?post_type=io_requests', 'post-new.php?post_type=io_requests');
		
		if(isset($_GET['post_type']) && $_GET['post_type'] == self::$POST_TYPE) {
			echo '<style type="text/css">
    .add-new-h2 { display:none; }
    </style>';
		}
		
		if(is_admin()) {
			$posts = get_posts(array(
				'post_type'		=> self::$POST_TYPE,
				'meta_key'		=> self::$PREFIX . '_status',
				'meta_value'	=> 'Eingereicht'
			));
			
			if(count($posts) > 0) {
				global $menu;
				foreach($menu AS $key => $value) {
					if($menu[$key][2] == "edit.php?post_type=io_requests") {
						$menu[$key][0] = $menu[$key][0] . ' <span class="update-plugins ' . count($posts) . '"><span class="plugin-count">' . count($posts) . '</span></span>';

						return;
					}
				}
			}
		}
	}
	
	public static function updBackendForm($post) {
		$io_request = new io_request($post);
		
		?>
<table border="0" cellpadding="5" cellspacing="0" width="100%">
	<tr>
		<td width="30%" valign="top">Art der Anfrage:</td>
		<td width="70%" valign="top"><?php
			if($io_request->art == 'p') {
				?>Berechtigung<?php
			} else if($io_request->art == 'g') {
				?>Gruppe<?php
			}
		?></td>
	</tr>
	<tr>
		<td width="30%" valign="top">Benutzer*in*:</td>
		<td width="70%" valign="top"><?php
			echo get_userdata($this->user)->first_name . ' ' . get_userdata($this->user)->last_name;
		?></td>
	</tr>
	<tr>
		<td width="30%" valign="top">Status:</td>
		<td width="70%" valign="top"><?php
			echo $this->status;
		?></td>
	</tr>
	<tr>
		<td width="30%" valign="top"><?php 
			if($this->art == 'p') {
				?>Berechtigung<?php
			} else if($this->art == 'g') {
				?>Gruppe<?php
			}
		?>:</td>
	</tr>
	<tr>
		<td width="70%" valign="top"><?php
			if($this->art == 'p') {
				echo $io_request->permission;
			} else if($this->art == 'g') {
				echo $io_request->group;
			}
		?></td>
	</tr>
</table>
		<?php
	}
	
	public static function updBackendFormButton($post) {
		$io_request = new io_request($post);
		
		if(isset($_GET['action']) && 
				!(!isset($_POST['io_requests_nonce']) || 
				!wp_verify_nonce($_POST['io_requests_nonce'], 'io_requests') || 
				defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)) {
			if($_GET['action'] == '1') {
				update_post_meta($post->ID, self::$PREFIX . '_status', 'Angenommen');
				if($io_request->art == 'p') {
					self::grant($io_request->user, $io_request->art, $io_request->permission);
				} else if($io_request->art == 'g') {
					self::grant($io_request->user, $io_request->art, $io_request->group);
				}
			} else {
				update_post_meta($post->ID, self::$PREFIX . '_status', 'Abgelehnt');
			}
		}
		
		wp_nonce_field('io_requests', 'io_requests_nonce');
		
		if($io_request->status == "Eingereicht") {
		?>

<a  href="" style="padding: 7px; background: red; font-family: Arial, Helvetica, sans-serif; font-weight: bold; color: white; border: 1px solid darkred; border-radius: 10px;">Annehmen</a><br>
<a  href="" style="padding: 7px; background: red; font-family: Arial, Helvetica, sans-serif; font-weight: bold; color: white; border: 1px solid darkred; border-radius: 10px;">Ablehnen</a><br>

		<?php
		} else {
			?>Bereits bearbeitet.<?php
		}
	}
	
	/*****************************************************
	 ************** Database & LDAP Change ***************
	 *****************************************************/
	public static function grant($user, $art, $permission) {
		
	}
	
	public static function addRequest($user, $art, $object, $active = true) {
		$id = wp_insert_post(array(
			'post_type'		=> self::$POST_TYPE,
			'post_title'	=> ($art == 'p') ? 'Berechtigung - ' : 'Gruppe - ' . ($art == 'p') ? (new io_permission($object)).name : (new io_groups($object)).name
		));
		
		update_post_meta($id, self::$PREFIX . '_user', $user);
		update_post_meta($id, self::$PREFIX . '_active', ($active == true) ? 1 : 0);
		update_post_meta($id, self::$PREFIX . '_art', ($art == 'p') ? 'p' : 'g');
		update_post_meta($id, self::$PREFIX . ($art == 'p') ? '_permiission' : '_group', $object);
		update_post_meta($id, self::$PREFIX . '_status', 'Eingereicht');
	}
	
	/*****************************************************
	 ****************** Hilfsfunktionen ******************
	 *****************************************************/
	public static function getValues() {
		
	}
	
	/*****************************************************
	 ***************** Register Posttype *****************
	 *****************************************************/
	/**
	 * Post-Type Variable für Gruppen
	 * 
	 * @var String Post-Type Variable für Gruppen 
	 */
	public static $POST_TYPE = "io_requests";
	
	/**
	 * Daten von Gruppen 
	 * 
	 * @var Array String => String 
	 */
	public static $CRITERIAS = array(
		'requests_art'			=> 'Anfrageart',
		'requerts_user'			=> 'Anfragesteller*in*',
		'reuqests_status'		=> 'Bearbeitungsstatus'
	);
	
	/**
	 * Post-Type Prefix
	 * 
	 * @var String Prefix
	 */
	public static $PREFIX = "requests";

	/**
	 * Register Posttype Groups
	 */
	public static function register() {
		parent::register_pt(self::$POST_TYPE, "Anfrage", "Anfragen", "io_request", "anfragen");
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
