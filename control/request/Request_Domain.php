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
        Domain_Control::create($this->name, $this->request->steller_in, $this->request->meta[Request_Util::DETAIL_DOMAIN_HOST], $this->request->meta[Request_Util::DETAIL_DOMAIN_ZWECK], $this->request->meta[Request_Util::DETAIL_DOMAIN_TARGET], $this->request->meta[Request_Util::DETAIL_DOMAIN_LOCATION]);
    }

    function reject() {
        return;
    }

    function getObject() {
        return $this;
    }
}