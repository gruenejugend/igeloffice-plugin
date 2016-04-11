<?php

/**
 * Description of Request_Group
 *
 * @author deb139e
 */
class Request_Group implements Request_Strategy {
	public static function art() {
		return 'Gruppen-Mitgliedschaft';
	}
	
	private $request;
	
	public function __construct($id) {
		$this->request = new Request($id);
	}

	public function getArt() {
		return self::art();
	}

	public function getArtSuffix($request_id) {
		return " bei " . get_post($request_id)->post_title;
	}
	
	public function approve($id) {
		User_Control::addToGroup($this->request->steller_in, $this->request->requested_id);
	}

	public function reject($id) {
		return;
	}
}
