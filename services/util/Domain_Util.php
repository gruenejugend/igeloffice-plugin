<?php

/**
 * Description of Domain_Util
 *
 * @author KWM
 */
class Domain_Util {
	const DB = "manager";

	const HOST = 'hostname';
	const TARGET = 'proxyTarget';
	const ALIAS = 'wwwAlias';
	const SSL = 'active';
	const DOMAINTABLE = 'sslProxy';
	
	const POST_TYPE = 'io_domain';
	
	const POST_ATTRIBUT_INFO_NONCE = "io_domain_info_nonce";
	const INFO_NONCE = "io_domain_info";
	const POST_ATTRIBUT_TARGET = "io_domain_target";
    const POST_ATTRIBUT_HOST = "io_domain_host";
    const POST_ATTRIBUT_VERWENDUNGSZWECK = "io_verwendungszweck";

    const VERWENDUNGSZWECK = "io_verwendungszweck";

    const VZ_WORDPRESS = "wp";
    const VZ_REDIRECT = "redirect";
    const VZ_WEB = "web";

    const VZ_ARRAY = array(
        self::VZ_WORDPRESS              => "WordPress",
        self::VZ_REDIRECT               => "Weiterleitung",
        self::VZ_WEB                    => "Web"
    );
}
