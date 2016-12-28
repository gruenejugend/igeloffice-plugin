<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Request_Util
 *
 * @author KWM
 */
class Request_Util
{
	const POST_TYPE = 'io_request';

	const ATTRIBUT_ART = "io_request_art";
	const ATTRIBUT_STELLER_IN = "io_request_steller_in";
	const ATTRIBUT_STATUS = "io_request_status";
	const ATTRIBUT_REQUESTED_ID = "io_request_requested_id";
	const ATTRIBUT_MESSAGE = "io_request_message";
    const ATTRIBUT_META = "io_request_meta";

	const POST_ATTRIBUT_ACTION_NONCE = "io_request_action_nonce";
	const POST_ATTRIBUT_MESSAGE_NONCE = "io_request_message_nonce";
	const POST_ATTRIBUT_STATUS = "io_request_status";

	const ACTION_NONCE = "io_request_action";
	const MESSAGE_NONCE = "io_request_message";

    /*
     * Requested Details
     */
    const DETAIL_REQUESTED_ID = "requested_id";
    const DETAIL_DOMAIN_HOST = "host";
    const DETAIL_DOMAIN_TARGET = "target";
    const DETAIL_DOMAIN_ZWECK = "zweck";
    const DETAIL_DOMAIN_LOCATION = "location";

    /*
     * WordPress Details
     */
    const DETAIL_WORDPRESS_DOMAIN = "domain";
    const DETAIL_WORDPRESS_GROUPS = "io_request_gruppen";
    const DETAIL_WORDPRESS_GROUPS_ADMIN = "admin";
    const DETAIL_WORDPRESS_GROUPS_REDAKTEUR = "redakteur";
    const DETAIL_WORDPRESS_GROUPS_AUTOR = "autor";
    const DETAIL_WORDPRESS_GROUPS_MITARBEIT = "mitarbeiter";
    const DETAIL_WORDPRESS_GROUPS_ABO = "abonnent";
}
