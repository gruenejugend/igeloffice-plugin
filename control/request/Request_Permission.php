<?php

/**
 * Description of Request_Permission
 *
 * @author deb139e
 */
class Request_Permission implements Request_Strategy {
	public static function art() {
		return 'Berechtigungs-Antrag';
	}
	
	private $request;
	
	public function __construct($id) {
		$this->request = new Request($id);
	}
	
	public function getArt() {
		return self::art();
	}

	public function getArtSuffix($request_id) {
		return " zu " . get_post($request_id)->post_title;
	}
	
	public function approve($id) {
		User_Control::addPermission($this->request->steller_in, $this->request->requested_id);
	}

	public function reject($id) {
		return;
	}
}
