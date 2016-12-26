<?php

/**
 * Description of Request_Group
 *
 * @author KWM
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

	public function getArtSuffix($requested) {
		return " bei " . get_post($requested[Request_Util::DETAIL_REQUESTED_ID])->post_title;
	}
	
	public function approve($id) {
		User_Control::addToGroup($this->request->steller_in, $this->request->{Request_Util::DETAIL_REQUESTED_ID});
	}

	public function reject($id) {
		return;
	}
	
	public function getObject() {
		return new Group($this->request->requested_id);
	}
}
