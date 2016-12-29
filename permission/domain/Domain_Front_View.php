<?php

/**
 * Created by PhpStorm.
 * User: KWM
 * Date: 28.12.2016
 * Time: 23:35
 */
class Domain_Front_View {
    private static $user;

    const NONCE = "io_domain_nonce_tt";
    const POST_NONCE = "io_domain_nonce";
    const POST_SUBMIT = "io_domain_submit";
    const POST_DOMAIN = "io_domain_domain_";
    const POST_ZWECK = "io_domain_zweck_";
    const POST_ZWECKE = array(
        Domain_Util::VZ_WORDPRESS       => "WordPress",
        Domain_Util::VZ_REDIRECT        => "Weiterleitung"
    );
    const POST_REDIRECT = "io_domain_redirect_";

    const TD_DOMAIN = "td_domain_domain_";
    const TD_ZWECK = "td_domain_zweck_";
    const TD_TARGET = "td_domain_target_";
    const DIV_TARGET_ZIEL = "weiterleitung_";
    const DIV_TARGET_LINK = "link_";

    public static function maskHandler() {
        self::$user = new Domain_Front_Model(get_current_user_id());

        if(!self::$user->isDomainPermitted) {
            return;
        }

        if(isset($_POST[self::POST_SUBMIT])) {
            self::maskExecution();
        }
        include 'wp-content/plugins/igeloffice/permission/domain/templates/domain.php';
    }

    private static function setRequests($id, $model) {
        if($_POST[self::POST_ZWECK.$id] == Domain_Util::VZ_WORDPRESS && $model->isWordPressPermitted) {
            Request_Control::create(get_current_user_id(), Request_WordPress::art(), array(
                Request_Util::DETAIL_WORDPRESS_DOMAIN       => sanitize_text_field($_POST[Domain_Front_View::POST_DOMAIN.$id])
            ));
        } elseif($_POST[self::POST_ZWECK.$id] == Domain_Util::VZ_REDIRECT && $model->isDomainPermitted) {
            Request_Control::create(get_current_user_id(), Request_Domain::art(), array(
                Request_Util::DETAIL_DOMAIN_HOST            => sanitize_text_field($_POST[Domain_Front_View::POST_DOMAIN.$id]),
                Request_Util::DETAIL_DOMAIN_TARGET          => sanitize_text_field($_POST[Domain_Front_View::POST_REDIRECT.$id]),
                Request_Util::DETAIL_DOMAIN_LOCATION        => "/",
                Request_Util::DETAIL_DOMAIN_ZWECK           => Domain_Util::VZ_REDIRECT
            ));
        }
    }

    public static function checkChange($id) {
        $domain = new Domain($id);
        return $_POST[self::POST_ZWECK.$id] != $domain->zweck || ($domain->zweck == Domain_Util::VZ_REDIRECT && $_POST[self::POST_REDIRECT.$id] != $domain->target);
    }

    public static function maskExecution() {
        if( !isset($_POST[self::POST_NONCE]) ||
            !wp_verify_nonce($_POST[self::POST_NONCE], self::NONCE)) {
            return;
        }

        $model = new Domain_Front_Model(get_current_user_id());

        //Eigene Domains
        if(!MySQL_Proxy::checkHostExists($model->eigeneDomain) && $_POST[self::POST_ZWECK."0"] != "0") {
            self::setRequests(0, $model);
        }

        //Existierende Domains
        foreach($model->domains AS $domain) {
            if(self::checkChange($domain->id)) {
                self::setRequests($domain->id, $model);
            }
        }

        echo "<h1>&Auml;nderungen wurden beantragt.</h1>";
    }
}