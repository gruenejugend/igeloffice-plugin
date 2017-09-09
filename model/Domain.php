<?php

/**
 * Model-Klasse zur Objekt-Erstellung von Domains.
 *
 * Diese Klasse arbeitet mit magischen Methoden. Entsprechend verfuegbare Variablen koennen der Klasse __get entnommen
 * werden. Entsprechende Informationen werden in Echtzeit von WordPress abgerufen.
 *
 * @author KWM
 */
class Domain {
	private $id;
    private $post;
	
	public function __construct($id) {
		$this->id = $id;
		$this->post = get_post($id);
    }

	public function __get($name) {
		switch ($name) {
			case 'id':
				return $this->id;
			case 'autor_in':
				return $this->post->post_author;
            case 'title':
                return $this->post->post_title;
            case 'hostID':
                return get_post_meta($this->id, Domain_Util::HOST_ID, true);
            case 'hostSettingsID':
                return get_post_meta($this->id, Domain_Util::HOST_SETTING_ID, true);
            case 'zweck':
                return get_post_meta($this->id, Domain_Util::VERWENDUNGSZWECK, true);
			case 'host':
				return MySQL_Proxy::getHostByID($this->__get("hostID"));
			case 'target':
			    if(Domain_Control::isNotVM($this->__get("zweck"))) {
                    return MySQL_Proxy::getRedirectByID($this->__get("hostSettingsID"))[Domain_Util::TABLE_REDIRECTS_C_TARGET];
                } else {
                    return MySQL_Proxy::getProxyByID($this->__get("hostSettingsID"))[Domain_Util::TABLE_PROXY_C_TARGET];
                }
            case 'location':
                if(Domain_Control::isNotVM($this->__get("zweck"))) {
                    return MySQL_Proxy::getRedirectByID($this->__get("hostSettingsID"))[Domain_Util::TABLE_REDIRECTS_C_LOCATION];
                } else {
                    return MySQL_Proxy::getProxyByID($this->__get("hostSettingsID"))[Domain_Util::TABLE_PROXY_C_LOCATION];
                }
		}
	}
}
