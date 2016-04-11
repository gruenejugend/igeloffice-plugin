<?php

/**
 * Description of Request
 *
 * @author KWM
 */
class Request {
	private $id;
	private $name;
	
	public function __construct($id) {
		$this->id = $id;
		$this->name = get_post($id)->post_title;
	}
	
	public function __get($name) {
		switch($name) {
			case 'ID':
				return $this->id;
			case 'name':
				return $this->name;
			case 'art':
				return get_post_meta($this->id, "io_request_art", true);
			case 'steller_in':
				return get_post_meta($this->id, "io_request_steller_in", true);
			case 'status':
				return get_post_meta($this->id, "io_request_status", true);
			case 'requested_id':
				return get_post_meta($this->id, "io_request_requested_id", true);
		}
	}
}
