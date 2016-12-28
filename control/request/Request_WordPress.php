<?php

/**
 * Created by PhpStorm.
 * User: KWM
 * Date: 28.12.2016
 * Time: 09:18
 */
class Request_WordPress implements Request_Strategy {

    static function art() {
        return "WordPress";
    }

    private $request;
    public $name;
    public function __construct($id) {
        $this->request = new Request($id);
        $this->name = "WordPress".$this->getArtSuffix($this->request->meta);
    }

    function getArt() {
        return self::art();
    }

    function getArtSuffix($requested) {
        return " für ".$this->request->steller_in;
    }

    function approve() {
        //Gruppen erstellen + Gruppenleiter
        //Gruppen-DNs müssen aber auch angezeigt werden, damit nach Genehmigung direkt kopiert werden kann
    }

    function reject() {
        return;
    }

    function getObject() {
        return $this;
    }
}