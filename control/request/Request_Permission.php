<?php

/**
 * Description of Request_Permission
 *
 * @author KWM
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

	public function getArtSuffix($requested) {
		return " zu " . get_post($requested[Request_Util::DETAIL_REQUESTED_ID])->post_title;
	}
	
	public function approve($id) {
		User_Control::addPermission($this->request->steller_in, $this->request->{Request_Util::DETAIL_REQUESTED_ID});
	}

	public function reject($id) {
		return;
	}
	
	public function getObject() {
		return new Permission($this->request->requested_id);
	}
}
