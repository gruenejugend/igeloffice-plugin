<?php

/**
 * Description of Request_User
 *
 * @author deb139e
 */
class Request_User implements Request_Strategy {
	public static function art() {
		return 'User-Aktivierung';
	}
	
	private $request;
	
	public function __construct($id) {
		$this->request = new Request($id);
	}
	
	public function getArt() {
		return self::art();
	}

	public function getArtSuffix($request_id) {
		return '';
	}
	
	public function approve($id) {
		$request = new Request($id);
		$user = new User($request->steller_in);
		
		if($user->aktiv == 0) {
			User_Control::aktivieren($this->request->steller_in);
		}
	}

	public function reject($id) {
		$request = new Request($id);
		$user = new User($request->steller_in);
		
		if($user->aktiv == 0) {
			User_Control::delete($this->request->steller_in);
		}
	}
}
