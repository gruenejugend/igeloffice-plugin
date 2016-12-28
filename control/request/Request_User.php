<?php

/**
 * Description of Request_User
 *
 * @author KWM
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

	public function getArtSuffix($requested) {
		return '';
	}
	
	public function approve() {
		$request = new Request($this->request->ID);
		$user = new User($request->steller_in);
		
		if($user->aktiv == 0) {
			User_Control::aktivieren($this->request->steller_in);
		}
	}

	public function reject() {
		$request = new Request($this->request->ID);
		$user = new User($request->steller_in);
		
		if($user->aktiv == 0) {
			User_Control::delete($this->request->steller_in);
		}
	}
	
	public function getObject() {
		return new User($this->request->steller_in);
	}
}
