<?php

/**
 * Description of Domain_Control
 *
 * @author KWM
 */
class Domain_Control {
	public static final function create($name, $author, $host, $zweck, $target, $location = "/") {
        $id = wp_insert_post(array(
            'post_title'		=> $name,
            'post_type'			=> Domain_Util::POST_TYPE,
            'post_status'		=> 'publish',
            'post_author'       => $author
        ));

        self::createMeta($id, $host, $zweck, $target, $location);

        return $id;
    }

    public static final function isNotVM($zweck) {
        return $zweck == Domain_Util::VZ_REDIRECT;
    }

    private static final function parseLocation($location) {
        $location = preg_replace('/[^A-Za-z0-9\-]/', '', $location);

        if(substr($location, 0, 1) != "/") {
            $location = "/".$location;
        }

        if(substr($location, -1, 1) != "/") {
            $location .= "/";
        }

        return $location;
    }

    public static final function createMeta($id, $host, $zweck, $target, $location = "/") {
        update_post_meta($id, Domain_Util::VERWENDUNGSZWECK, $zweck);

        $location = self::parseLocation($location);

        $hostID = MySQL_Proxy::createHost($host);
        update_post_meta($id, Domain_Util::HOST_ID, $hostID);
        if(self::isNotVM($zweck)) {
            $settingID = MySQL_Proxy::createRedirect($hostID, $target, $location);
        } else {
            $settingID = MySQL_Proxy::createProxy($hostID, $target, $location);
        }
        update_post_meta($id, Domain_Util::HOST_SETTING_ID, $settingID);
    }

    public static final function update($id, $host, $zweck, $target, $location = "/") {
        $domain = new Domain($id);

        $location = self::parseLocation($location);

        if($host != $domain->host) {
            MySQL_Proxy::updateHost($domain->hostID, $host);
        }

        if($domain->zweck != $zweck && self::isNotVM($domain->zweck) != self::isNotVM($zweck)) {
            if(self::isNotVM($domain->zweck)) {
                MySQL_Proxy::deleteRedirect($domain->hostSettingsID);
                $settingsID = MySQL_Proxy::createProxy($domain->hostID, $target, $location);
            } else {
                MySQL_Proxy::deleteProxy($domain->hostSettingsID);
                $settingsID = MySQL_Proxy::createRedirect($domain->hostID, $target, $location);
            }
            update_post_meta($id, Domain_Util::HOST_SETTING_ID, $settingsID);
        } else {
            if(self::isNotVM($zweck)) {
                MySQL_Proxy::updateRedirect($domain->hostSettingsID, $target, $location);
            } else {
                MySQL_Proxy::updateProxy($domain->hostSettingsID, $target, $location);
            }
        }
        update_post_meta($id, Domain_Util::VERWENDUNGSZWECK, $zweck);
    }

    public static final function delete($id) {
        $domain = new Domain($id);

        if(self::isNotVM($domain->zweck)) {
            MySQL_Proxy::deleteRedirect($domain->hostSettingsID);
        } else {
            MySQL_Proxy::deleteProxy($domain->hostSettingsID);
        }
        MySQL_Proxy::deleteHost($domain->hostID);
    }
}