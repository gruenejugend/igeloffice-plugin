<?php

/**
 * Description of Domain_Util
 *
 * @author KWM
 */
class Domain_Util {
	const DB = "manager";

    const TABLE_HOST = "hosts";
    const TABLE_HOST_C_ID = "id";
    const TABLE_HOST_C_HOST = "host";

    const TABLE_REDIRECTS = "redirects";
    const TABLE_REDIRECTS_C_ID = "id";
    const TABLE_REDIRECTS_C_HOST = "host_id";
    const TABLE_REDIRECTS_C_LOCATION = "location";
    const TABLE_REDIRECTS_C_TARGET = "target";
    const TABLE_REDIRECTS_C_MODE = "mode";
    const TABLE_REDIRECTS_C_MODE_E_PERM = "permanent";
    const TABLE_REDIRECTS_C_MODE_E_TEMP = "temporary";

    const TABLE_PROXY = "tlsProxy";
    const TABLE_PROXY_C_ID = "id";
    const TABLE_PROXY_C_TARGET = "proxyTarget";
    const TABLE_PROXY_C_ACTIVE = "active";
    const TABLE_PROXY_C_HOST = "host_id";
    const TABLE_PROXY_C_LOCATION = "location";

	const POST_TYPE = 'io_domain';
	
	const POST_ATTRIBUT_INFO_NONCE = "io_domain_info_nonce";
	const INFO_NONCE = "io_domain_info";
	const POST_ATTRIBUT_TARGET = "io_domain_target";
    const POST_ATTRIBUT_HOST = "io_domain_host";
    const POST_ATTRIBUT_VERWENDUNGSZWECK = "io_verwendungszweck";
    const POST_ATTRIBUT_LOCATION = "io_domain_location";

    const VERWENDUNGSZWECK = "io_verwendungszweck";
    const HOST_ID = "io_host_id";
    const HOST_SETTING_ID = "io_host_setting_id";

    const VZ_WORDPRESS = "wp";
    const VZ_REDIRECT = "redirect";
    const VZ_WEB = "web";

    const VZ_ARRAY = array(
        self::VZ_WORDPRESS              => "WordPress",
        self::VZ_REDIRECT               => "Weiterleitung",
        self::VZ_WEB                    => "Web"
    );

    const VZ_ADRESS_ARRAY = array(
        self::VZ_WORDPRESS              => "http://wordpress",
        self::VZ_WEB                    => "http://web",
        self::VZ_REDIRECT               => ""
    );
}
