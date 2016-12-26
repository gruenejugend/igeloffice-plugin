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
    public function __construct($id) {
        $this->request = new Request($id);
    }

    function getArt() {
        return self::art();
    }

    function getArtSuffix($requested) {
        return ": " . $requested[Request_Util::DETAIL_DOMAIN_HOST] . " zu " . $requested[Request_Util::DETAIL_DOMAIN_TARGET];
    }

    function approve($id) {
        // TODO: Implement approve() method.
    }

    function reject($id) {
        // TODO: Implement reject() method.
    }

    function getObject() {
        // TODO: Implement getObject() method.
    }
}