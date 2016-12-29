<?php

/**
 * Created by PhpStorm.
 * User: KWM
 * Date: 26.12.2016
 * Time: 22:31
 */
class Request_Domain implements Request_Strategy {
    static function art() {
        return "Domain";
    }

    private $request;
    public $name;
    public function __construct($id) {
        $this->request = new Request($id);
        $this->name = $this->request->meta[Request_Util::DETAIL_DOMAIN_HOST] . " zu " . $this->request->meta[Request_Util::DETAIL_DOMAIN_TARGET];
    }

    function getArt() {
        return self::art();
    }

    function getArtSuffix($requested) {
        return ": " . $requested[Request_Util::DETAIL_DOMAIN_HOST] . " zu " . $requested[Request_Util::DETAIL_DOMAIN_TARGET];
    }

    function approve() {
        if(MySQL_Proxy::checkHostExists($this->request->meta[Request_Util::DETAIL_DOMAIN_HOST])) {
            $hostID = MySQL_Proxy::getIDByHost($this->request->meta[Request_Util::DETAIL_DOMAIN_HOST]);
            $posts = get_posts(array(
                'post_type' => Domain_Util::POST_TYPE,
                'meta_query' => array(
                    array(
                        'key' => Domain_Util::HOST_ID,
                        'value' => $hostID
                    )
                ),
                'author' => $this->request->steller_in
            ));

            if (isset($posts[0]->ID)) {
                Domain_Control::update($posts[0]->ID, $this->request->meta[Request_Util::DETAIL_DOMAIN_HOST], $this->request->meta[Request_Util::DETAIL_DOMAIN_ZWECK], $this->request->meta[Request_Util::DETAIL_DOMAIN_TARGET], $this->request->meta[Request_Util::DETAIL_DOMAIN_LOCATION]);
            } else {
                Domain_Control::create($this->name, $this->request->steller_in, $this->request->meta[Request_Util::DETAIL_DOMAIN_HOST], $this->request->meta[Request_Util::DETAIL_DOMAIN_ZWECK], $this->request->meta[Request_Util::DETAIL_DOMAIN_TARGET], $this->request->meta[Request_Util::DETAIL_DOMAIN_LOCATION]);
            }
        } else {
            Domain_Control::create($this->name, $this->request->steller_in, $this->request->meta[Request_Util::DETAIL_DOMAIN_HOST], $this->request->meta[Request_Util::DETAIL_DOMAIN_ZWECK], $this->request->meta[Request_Util::DETAIL_DOMAIN_TARGET], $this->request->meta[Request_Util::DETAIL_DOMAIN_LOCATION]);
        }
    }

    function reject() {
        return;
    }

    function getObject() {
        return $this;
    }
}