<?php

/**
 * Description of Domain
 *
 * @author KWM
 */
class Domain {
	private $id;
	private $autor_in;
	private $host;
	private $target; 
	private $alias;
	private $ssl;
	
	public function __construct($id) {
		$this->id = $id;
		$post = get_post($id);
		
		$this->autor_in = $post->post_author;
		$this->host = $post->post_title;
	}
	
	public function __get($name) {
		switch ($name) {
			case 'id':
				return $this->id;
			case 'autor_in':
				return $this->autor_in;
			case 'host':
				return $this->host;
			case 'target':
				return Domain_Control::prepareRouting(MySQL_Proxy::getDomain($this->host)[Domain_Util::TARGET]);
			case 'alias':
				return MySQL_Proxy::readDomain($this->host)[Domain_Util::ALIAS];
		}
	}
}
