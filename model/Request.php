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
				return get_post_meta($this->id, Request_Util::ATTRIBUT_ART, true);
			case 'steller_in':
				return get_post_meta($this->id, Request_Util::ATTRIBUT_STELLER_IN, true);
			case 'status':
				return get_post_meta($this->id, Request_Util::ATTRIBUT_STATUS, true);
            case 'meta':
                return maybe_unserialize(get_post_meta($id, Request_Util::ATTRIBUT_META, true));
            default:
				return get_post_meta($this->id, $name, true);
		}
	}
}
