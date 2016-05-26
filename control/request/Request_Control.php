<?php

/**
 * Description of Request_Control
 *
 * @author KWM
 */
class Request_Control {	
	public static function create($user_id, $art, $requested_id = null) {
		$request = Request_Factory::getRequest($art, null);
		
		$checkExist = self::checkExist($user_id, $request, $requested_id);
		if($checkExist) {
			return $checkExist;
		}
		
		$id = wp_insert_post(array(
			'post_title'		=> self::createTitle($request, $user_id, $requested_id),
			'post_type'			=> Request_Util::POST_TYPE,
			'post_status'		=> 'publish'
		));
		
		self::createMeta($id, $request, 
				sanitize_text_field($user_id), 
				sanitize_text_field($requested_id));
		
		return $id;
	}
	
	public static function createMeta($id, $request, $user_id, $requested_id) {
		update_post_meta($id, Request_Util::ATTRIBUT_ART,				$request->getArt());
		update_post_meta($id, Request_Util::ATTRIBUT_STELLER_IN,		$user_id);
		if($requested_id) {
			update_post_meta($id, Request_Util::ATTRIBUT_REQUESTED_ID,	$requested_id);
		}
		update_post_meta($id, Request_Util::ATTRIBUT_STATUS,			"Gestellt");
	}
	
	private static function createTitle($request, $user_id, $requested_id) {
		return $request->getArt() . " " . get_userdata($user_id)->user_login . $request->getArtSuffix($requested_id);
	}
	
	private static function checkExist($user_id, $request, $requested_id = null) {
		$args = array(
			'post_type'			=> Request_Util::POST_TYPE,
			'meta_query'		=> array(
				'relation'			=> 'AND',
				array(
					'key'				=> Request_Util::ATTRIBUT_ART,
					'value'				=> $request->getArt()
				),
				array(
					'key'				=> Request_Util::ATTRIBUT_STELLER_IN,
					'value'				=> $user_id
				),
				array(
					'key'				=> Request_Util::ATTRIBUT_STATUS,
					'value'				=> 'Gestellt'
				)
			)
		);
		
		if($requested_id) {
			$args['meta_query'][3] = array(
				'key'				=> Request_Util::ATTRIBUT_REQUESTED_ID,
				'value'				=> $requested_id
			);
		}
		
		$posts = get_posts($args);
		foreach($posts AS $post) {
			if($post->post_title == self::createTitle($request, $user_id, $requested_id)) {
				return $post->ID;
			}
		}
		
		return false;
	}
	
	public static function count() {
		return count(get_posts(array(
			'post_type'				=> Request_Util::POST_TYPE,
			'posts_per_page'		=> -1,
			'meta_query'			=> array(
				array(
					'key' => Request_Util::ATTRIBUT_STATUS,
					'value' => "Gestellt"
				)
			)
		)));
	}
	
	public static function approve($id) {
		Request_Factory::getRequest(get_post_meta($id, Request_Util::ATTRIBUT_ART, true), $id)->approve($id);
		update_post_meta($id, Request_Util::ATTRIBUT_STATUS,				"Angenommen");
	}
	
	public static function reject($id) {
		Request_Factory::getRequest(get_post_meta($id, Request_Util::ATTRIBUT_ART, true), $id)->reject($id);
		update_post_meta($id, Request_Util::ATTRIBUT_STATUS,				"Abgelehnt");
	}
}
