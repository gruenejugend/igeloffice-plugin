<?php

/**
 * Created by PhpStorm.
 * User: KWM
 * Date: 30.12.2016
 * Time: 14:28
 */
class Request_Cloud implements Request_Strategy {
    static function art() {
        return "Cloud";
    }

    private $request;
    public $name;
    public function __construct($id) {
        $this->request = new Request($id);
        $this->name = "Cloud".$this->getArtSuffix($this->request->meta);
    }

    function getArt() {
        return self::art();
    }

    function getArtSuffix($requested) {
        return "";
    }

    function approve() {
        $groupID = Group_Control::create("Cloud " . (new User($this->request->steller_in))->user_login, "Cloud", "User");
        User_Control::addToGroup($this->request->steller_in, $groupID);
        Group_Control::addOwner($groupID, $this->request->steller_in);
        Group_Control::addPermission($groupID, cloud_control::getPermission()->id);
    }

    function reject() {
        return;
    }

    function getObject() {
        return $this;
    }
}