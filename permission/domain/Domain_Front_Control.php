<?php

/**
 * Created by PhpStorm.
 * User: KWM
 * Date: 28.12.2016
 * Time: 23:35
 */
class Domain_Front_Control {
    const WORDPRESS_PERMISSION = "WordPress Init";
    const DOMAIN_PERMISSION = "Domain Init";

    public static function getDomainPermission() {
        return new Permission(get_page_by_title(self::DOMAIN_PERMISSION, OBJECT, Permission_Util::POST_TYPE)->ID);
    }

    public static function getWordPressPermission() {
        return new Permission(get_page_by_title(self::WORDPRESS_PERMISSION, OBJECT, Permission_Util::POST_TYPE)->ID);
    }
}