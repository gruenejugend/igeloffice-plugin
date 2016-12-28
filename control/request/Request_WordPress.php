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
        $user_login = (new User($this->request->steller_in))->user_login;

        $admID = Group_Control::create("WordPress Administration " . $user_login, "WordPress", $user_login);
        $redID = Group_Control::create("WordPress Redakteur " . $user_login, "WordPress", $user_login);
        $autID = Group_Control::create("WordPress Autor_in " . $user_login, "WordPress", $user_login);
        $mitID = Group_Control::create("WordPress Mitarbeiter_in " . $user_login, "WordPress", $user_login);
        $aboID = Group_Control::create("WordPress Abonnent_in " . $user_login, "WordPress", $user_login);

        $groups = array(
            Request_Util::DETAIL_WORDPRESS_GROUPS_ADMIN     => $admID,
            Request_Util::DETAIL_WORDPRESS_GROUPS_REDAKTEUR => $redID,
            Request_Util::DETAIL_WORDPRESS_GROUPS_AUTOR     => $autID,
            Request_Util::DETAIL_WORDPRESS_GROUPS_MITARBEIT => $mitID,
            Request_Util::DETAIL_WORDPRESS_GROUPS_ABO       => $aboID
        );

        update_post_meta($this->request->ID, Request_Util::DETAIL_WORDPRESS_GROUPS, maybe_serialize($groups));

        User_Control::addToGroup($this->request->steller_in, $admID);
        Group_Control::addOwner($admID, $this->request->steller_in);
        User_Control::addToGroup($this->request->steller_in, $redID);
        Group_Control::addOwner($redID, $this->request->steller_in);
        User_Control::addToGroup($this->request->steller_in, $autID);
        Group_Control::addOwner($autID, $this->request->steller_in);
        User_Control::addToGroup($this->request->steller_in, $mitID);
        Group_Control::addOwner($mitID, $this->request->steller_in);
        User_Control::addToGroup($this->request->steller_in, $aboID);
        Group_Control::addOwner($aboID, $this->request->steller_in);
    }

    function reject() {
        return;
    }

    function getObject() {
        return $this;
    }
}