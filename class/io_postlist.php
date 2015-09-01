<?php

/**
 * Standardklasse für die Post-Anzeige eigener Posttypes in WordPress
 *
 * @author KWM
 */
class io_postlist {
	/**
	 * Angabe, welche Spalten angezeigt werden sollen.
	 * 
	 * @param array $defaults Standardspalten
	 * @param array $arr Neue Spalten
	 * @return array Anzuzeigende Spalten
	 */
	public static function postlists($defaults, $arr) {
		if(is_admin()) {
			foreach($arr AS $key => $value) {
				if(is_array($value)) {
					$defaults[$key] = $arr[$key]['key'];
				} else {
					$defaults[$key] = $arr[$key];
				}
			}

			unset($defaults['date']);
			$defaults['date'] = __('Date');

			return $defaults;
		}
	}
	
	/**
	 * Anzeige von Spalteninhalt neuer Spalten
	 * 
	 * @param string $post_type Posttype Name
	 * @param string $column_name Spaltenname
	 * @param int $post_id Aktuelle Post-ID
	 * @param string/array $meta Angabe des anzuzeigenden Spalteninhalts
	 */
	public static function postlist_column($post_type, $column_name, $post_id, $meta) {
		if(is_admin() && isset($meta[$column_name])) {
			$value = ucfirst(get_post_meta($post_id, $column_name, true));
			if(is_array($meta[$column_name])) {
				if($meta[$column_name]['value'] == 'date') {
					$show = om_tstodate($value);
				} else if($meta[$column_name]['value'] == 'post_title') {
					$show = get_the_title($post_id);
				} else if($meta[$column_name]['value'] == 'doubletostr') {
					$show = doubletostr($value, true, true);
				}
				
				if($meta[$column_name]['value'] == 'doubletostrwp') {
					$show = doubletostr($value, true);
				}
			} else {
				$show = $value;
			}
			
			$value = str_replace(" ", "%20", $value);
			echo ("<a href=" . $_SERVER['PHP_SELF'] . "?post_type=" . $post_type . "&" . $column_name . "[0]=" . $value . ">" . $show . "</a>");
		}
	}
	
	/**
	 * Angabe, welche Spalten sortiert werden können
	 * 
	 * @param array $sort_columns Sortierbare Spalten
	 * @param array $add_columns Zu sortierende Spalten
	 * @return array Sortierbare Spalten
	 */
	public static function postlist_sorting($sort_columns, $add_columns) {
		if(is_admin()) {
			foreach($add_columns AS $key => $value) {
				$sort_columns[$key] = $key;
			}
		}
			
		return $sort_columns;
	}
	
	/**
	 * Sortierung der sortierbaren Spalten
	 * 
	 * @param string $post_type Posttype
	 * @param wp_query $vars Query-Variablen
	 * @param array $meta Sortierbare Spalten
	 * @return wp_query Query-Variablen 
	 */
	public static function postlist_orderby($post_type, $vars, $meta) {
		if(is_admin()) {
			if(isset($vars['post_type']) && isset($vars['orderby']) && isset($meta[$vars['orderby']]) && $vars['post_type'] == $post_type) {
				$vars = array_merge($vars, array(
					'meta_key'	=> $vars['orderby'],
					'orderby'	=> 'meta_value_num'
				));
			}
			
			return $vars;
		}
		return $vars;
	}
	
	/**
	 * Anzeige Filter-Formular
	 * 
	 * @global wpdb $wpdb WordPress-Datenbankanbindung
	 * @param string $post_type Posttype
	 * @param string $form_prefix Prefix der Formularnamen
	 * @param array $meta Filterbare Informationen
	 */
	public static function postlist_filtering($post_type, $form_prefix, $meta) {
		if(is_admin()) {
			$screen = get_current_screen();
			if($screen->post_type == $post_type) {
				global $wpdb;
				
				$form = new io_form(array(
					'action'		=> "",
					'form'			=> false,
					'table'			=> false,
					'prefix'		=> $form_prefix
				));

				foreach($meta AS $value => $meta) {
					if(is_array($meta)) {
						$show = $meta['value'];
					}
					
					$results = $wpdb->get_results("SELECT pm.meta_value FROM " . $wpdb->postmeta . " pm INNER JOIN " . $wpdb->posts . " p ON p.ID = pm.post_id WHERE p.post_type = '" . $post_type . "' AND p.post_status = 'publish' AND pm.meta_key = '" . $value . "' GROUP BY pm.meta_value");
					
					$values[0] = "Alle";
					foreach($results AS $meta_data) {
						if(isset($show)) {
							if($show == "post_title") {
								$values[$meta_data->meta_value] = get_the_title($meta_data->meta_value);
							} elseif($show == "date") {
								$values[$meta_data->meta_value] = om_tstodate($meta_data->meta_value, true);
							}
						} else {
							$values[$meta_data->meta_value] = $meta_data->meta_value;
						}
					}
					
					if(isset($_GET[$value])) {
						foreach($_GET[$value] AS $getValue) {
							$selected['value'][$getValue] = 1;
						}
					} else {
						$selected = null;
					}

					$form->select(array(
						'name'			=> str_replace($form_prefix . "_", "", $value),
						'values'		=> $values,
						'selected'		=> $selected,
						'multiple'		=> true,
						'size'			=> 5
					));
					
					unset($values);
					unset($show);
				}
				
				unset($form);
			}
		}
	}
	
	/**
	 * Filterung
	 * 
	 * @param wp_query $query WP_Query
	 * @param string $post_type Posttype
	 * @param array $meta Filterbare Informationen
	 * @return wp_query Neuer WP_Query
	 */
	public static function postlist_filtering_sort($query, $post_type, $meta) {
		if(is_admin()) {
			$screen = get_current_screen();
			if($screen->post_type == $post_type && $screen->id == "edit-" . $post_type) {
				$query->query_vars['meta_query'] = array();
				
				foreach($meta AS $value => $meta) {
					if(isset($_GET[$value]) && is_array($_GET[$value])) {
						foreach($_GET[$value] AS $key => $value_temp) {
							$values[$key] = sanitize_text_field($value_temp);
						}
						
						$query->query_vars['meta_query'] = array_merge($query->query_vars['meta_query'], array(array(
							'key'		=> $value,
							'value'		=> $values,
							'compare'	=> 'IN'
						)));
					} elseif(isset($_GET[$value]) && $_GET[$value] != 0) {
						$query->query_vars['meta_query'] = array_merge($query->query_vars['meta_query'], array(array(
							'key'		=> $value,
							'value'		=> sanitize_text_field($_GET[$value])
						)));
					}
				}
			}
		}
		
		return $query;
	}
	
	/**
	 * Anzeige Line-Options
	 * 
	 * @param array $actions Optionen
	 * @param post $post Aktueller Post
	 * @param string $post_type Posttype
	 * @return array neue Actions
	 */
	public static function postlist_options($actions, $post, $post_type) {
		if($post->post_type == $post_type) {
			unset($actions['edit']);
			unset($actions['inline hide-if-no-js']);
			unset($actions['view']);
		}
		
		return $actions;
	}
	
	/**
	 * Register Posttype
	 * 
	 * @param string $post_type Posttype
	 * @param string $singular Posttype Beschreibung Singular
	 * @param string $plural Posttype Beschreibung Plural
	 * @param string $class_name Betreffende Klasse
	 * @param string $slug Linkslug
	 */
	public static function register_pt($post_type, $singular, $plural, $class_name, $slug) {
		if(is_admin()) {
			if(post_type_exists($post_type) == false) {
				register_post_type($post_type, array(
						'labels'		=> array(
							'name'					=> __($plural, 'OpenMeeting'),
							'singular_name'			=> __($singular, 'OpenMeeting'),
							'add_new'				=> __('Neue ' . $plural, 'OpenMeeting'),
							'add_new_item'			=> __('Neue ' . $singular, 'OpenMeeting'),
							'edit_item'				=> __($singular . ' bearbeiten', 'OpenMeeting'),
							'new_item'				=> __('Neue ' . $singular, 'OpenMeeting'),
							'seacht_items'			=> __($singular . ' Suchen', 'OpenMeeting'),
							'not_found'				=> __('Keine ' . $singular . ' gefunden.', 'OpenMeeting'),
							'not_found_in_trash'	=> __('Neue ' . $singular, 'OpenMeeting')
						),
						'public'				=> true,
						'supports'				=> array('title', 'editor'),
						'capability_type'		=> 'post',
						'rewrite'				=> array("slug" => $slug),
						'menu_position'			=> 25,
						'register_meta_box_cb'	=> array($class_name, 'metabox'),
						'has_archive'			=> true
					)
				);
			}
		}
	}
	
	/**
	 * Verstecken unwesentlicher Felder
	 */
	public static function wpHide() {
		echo ('		<script>
			$(".misc-pub-section").hide();
			$("#preview-action").hide();
			$("#save-action").hide();
			$("#minor-publishing-actions").html("Speichern ist nur möglich, wenn alle Daten richtig eingegeben wurden.");
			$(".submitdelete").hide();
			$("#publish").val("Speichern");
		</script>
');
	}
}
