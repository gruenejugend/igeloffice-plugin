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
        $admID = Group_Control::create("WordPress Administration " . $this->request->steller_in, "WordPress", $this->request->steller_in);
        $redID = Group_Control::create("WordPress Redakteur " . $this->request->steller_in, "WordPress", $this->request->steller_in);
        $autID = Group_Control::create("WordPress Autor_in " . $this->request->steller_in, "WordPress", $this->request->steller_in);
        $mitID = Group_Control::create("WordPress Mitarbeiter_in " . $this->request->steller_in, "WordPress", $this->request->steller_in);
        $aboID = Group_Control::create("WordPress Abonnent_in " . $this->request->steller_in, "WordPress", $this->request->steller_in);

        $groups = array(
            Request_Util::DETAIL_WORDPRESS_GROUPS_ADMIN     => $admID,
            Request_Util::DETAIL_WORDPRESS_GROUPS_REDAKTEUR => $redID,
            Request_Util::DETAIL_WORDPRESS_GROUPS_AUTOR     => $autID,
            Request_Util::DETAIL_WORDPRESS_GROUPS_MITARBEIT => $mitID,
            Request_Util::DETAIL_WORDPRESS_GROUPS_ABO       => $aboID
        );

        update_post_meta($this->request->ID, Request_Util::DETAIL_WORDPRESS_GROUPS, maybe_serialize($groups));

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