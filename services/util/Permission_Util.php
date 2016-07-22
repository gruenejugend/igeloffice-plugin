<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Permission_Util
 *
 * @author KWM
 */
class Permission_Util {
	const POST_TYPE = 'io_permission';
	
	const OBERKATEGORIE = 'io_permission_ok';
	const UNTERKATEGORIE = 'io_permission_uk';
	
	const POST_ATTRIBUT_INFO_NONCE = 'io_permissions_info_nonce';
	const POST_ATTRIBUT_MEMBER_NONCE = 'io_permissions_member_nonce';
	const POST_ATTRIBUT_REMEMBER_NONCE = 'io_permissions_remember_nonce';
	const POST_ATTRIBUT_USERS = 'users';
	const POST_ATTRIBUT_GROUPS = 'groups';
	const POST_ATTRIBUT_REMEMBER = 'remember';
	
	const INFO_NONCE = 'io_permissions_info';
	const MEMBER_NONCE = 'io_permissions_member';
	const REMEMBER_NONCE = 'io_permissions_remember';
}
