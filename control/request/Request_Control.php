<?php

/**
 * Description of Request_Control
 *
 * @author KWM
 */
class Request_Control {
	const POST_TYPE = 'io_request';
	
	public static function create($user_id, $art, $requested_id = null) {
		$request = Request_Factory::getRequest($art, null);
		
		$id = wp_insert_post(array(
			'post_title'		=> $request->getArt() . " " . get_userdata($user_id)->user_login . $request->getArtSuffix($requested_id),
			'post_type'			=> self::POST_TYPE,
			'post_status'		=> 'publish'
		));
		
		self::createMeta($id, $request, 
				sanitize_text_field($user_id), 
				sanitize_text_field($requested_id));
		
		return $id;
	}
	
	public static function createMeta($id, $request, $user_id, $requested_id) {
		update_post_meta($id, "io_request_art",					$request->getArt());
		update_post_meta($id, "io_request_steller_in",			$user_id);
		if($requested_id) {
			update_post_meta($id, "io_request_requested_id",	$requested_id);
		}
		update_post_meta($id, "io_request_status",				"Gestellt");
	}
	
	public static function count() {
		print_r(get_posts(array(
			'post_type'				=> self::POST_TYPE,
			'posts_per_page'		=> -1,
			'meta_query'			=> array(
				array(
					'key' => 'io_request_status',
					'value' => "Gestellt"
				)
			))));
		
		return count(get_posts(array(
			'post_type'				=> self::POST_TYPE,
			'posts_per_page'		=> -1,
			'meta_query'			=> array(
				array(
					'key' => 'io_request_status',
					'value' => "Gestellt"
				)
			)
		)));
	}
	
	public static function approve($id) {
		Request_Factory::getRequest(get_post_meta($id, "io_request_art", true), $id)->approve($id);
		update_post_meta($id, "io_request_status",				"Angenommen");
	}
	
	public static function reject($id) {
		Request_Factory::getRequest(get_post_meta($id, "io_request_art", true), $id)->reject($id);
		update_post_meta($id, "io_request_status",				"Abgelehnt");
	}
}
