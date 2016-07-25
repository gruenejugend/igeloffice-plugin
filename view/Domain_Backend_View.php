<?php

/**
 * Description of Domain_Backend_View
 *
 * @author KWM
 */
class Domain_Backend_View {
	public static function maskHandler() {
		if(current_user_can('administrator')) {
			add_meta_box("io_domain_info_mb", "Informationen", array("Domain_Backend_View", "metaInfo"), Domain_Util::POST_TYPE, "normal", "default");
		}
	}
	
	public static function metaInfo($post) {
		wp_nonce_field(Domain_Util::INFO_NONCE, Domain_Util::POST_ATTRIBUT_INFO_NONCE);
		$domain = new Domain($post->ID);
		
		include '../wp-content/plugins/igeloffice/templates/backend/domainInfo.php';
	}
	
	public static function column($columns) {
		return array_merge($columns, array(Domain_Util::ATTRIBUT_TARGET => 'Ziel'));
	}
	
	public static function maskColumn($column, $post_id) {
		if($column == Domain_Util::ATTRIBUT_TARGET) {
			echo (new Domain($post_id))->target;
		}
	}
	
	public static function maskSave($post_id) {
		if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
			return;
		}
		
		if(current_user_can('administrator')) {
			if( !isset($_POST[Domain_Util::POST_ATTRIBUT_INFO_NONCE]) || 
				!wp_verify_nonce($_POST[Domain_Util::POST_ATTRIBUT_INFO_NONCE], Domain_Util::INFO_NONCE)) {
				return;
			}
			
			$alias = !empty($_POST[Domain_Util::POST_ATTRIBUT_ALIAS]) ? 1 : 0;
			Domain_Control::update(get_post($post_id)->post_title, sanitize_text_field($_POST[Domain_Util::POST_ATTRIBUT_TARGET]), $alias);
		}
	}
}
