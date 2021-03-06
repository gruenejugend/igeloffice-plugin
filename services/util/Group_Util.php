<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Group_Util
 *
 * @author KWM
 */
class Group_Util {
	const POST_TYPE = 'io_group';
	
	const OBERKATEGORIE = 'io_group_ok';
	const UNTERKATEGORIE = 'io_group_uk';
	
	const STANDARD_ZUWEISUNG_SCHALTER = true;
	
	const POST_ATTRIBUT_INFO_NONCE = 'io_groups_info_nonce';
	const INFO_NONCE = 'io_groups_info';
	const POST_ATTRIBUT_MEMBER_NONCE = 'io_groups_member_nonce';
	const MEMBER_NONCE = 'io_groups_member';
	const POST_ATTRIBUT_PERMISSION_NONCE = 'io_groups_permission_nonce';
	const PERMISSION_NONCE = 'io_groups_permission';
	const POST_ATTRIBUT_SICHTBARKEIT_NONCE = 'io_groups_sichtbarkeit_nonce';
	const SICHTBARKEIT_NONCE = 'io_groups_sichtbarkeit';
	const POST_ATTRIBUT_STANDARD_NONCE = 'io_groups_standard_nonce';
	const STANDARD_NONCE = 'io_groups_standard';
	const POST_ATTRIBUT_REMEMBER_NONCE = 'io_groups_remember_nonce';
	const REMEMBER_NONCE = 'io_groups_remember';
	const POST_ATTRIBUT_QUOTA_NONCE = 'io_groups_quota_nonce';
	const QUOTA_NONCE = 'io_groups_quota';
	const POST_ATTRIBUT_LEADER_MEMBER_NONCE = 'io_groups_leader_member_nonce';
	const LEADER_MEMBER_NONCE = 'io_groups_leader_member';
	
	const POST_ATTRIBUT_OWNER = 'owner';
	const POST_ATTRIBUT_USERS = 'users';
	const POST_ATTRIBUT_GROUPS = 'groups';
	const POST_ATTRIBUT_PERMISSIONS = 'permissions';
	const POST_ATTRIBUT_SICHTBARKEIT = 'sichtbarkeit';
	const POST_ATTRIBUT_SIZE = "size";
	const POST_ATTRIBUT_REMEMBER = 'remember';
	const POST_ATTRIBUT_STANDARD = 'standard';
	const POST_ATTRIBUT_QUOTA_SIZE = 'quotaSize';
	const POST_ATTRIBUT_QUOTA_TYPE = 'quotaType';
	const POST_ATTRIBUT_NEW_NAMES = 'new_names';
	const POST_ATTRIBUT_NEW_MAILS = 'new_mails';

	const POST_ATTRIBUT_QUOTA_B = 'b';
	const POST_ATTRIBUT_QUOTA_KB = 'kb';
	const POST_ATTRIBUT_QUOTA_MB = 'mb';
	const POST_ATTRIBUT_QUOTA_GB = 'gb';

	const POST_ATTRIBUT_FRONTEND_USER_NEU = "io_groups_neu_name";
	const POST_ATTRIBUT_FRONTEND_MAIL_NEU = "io_groups_neu_mail";
	const POST_ATTRIBUT_FRONTEND_NEU_SUBMIT = "io_groups_neu_submit";
	const POST_ATTRIBUT_FRONTEND_REQUEST_SUBMIT = "io_groups_request_submit";
	const POST_ATTRIBUT_FRONTEND_REQUEST_STATUS = "io_groups_request_status_";
	const POST_ATTRIBUT_FRONTEND_REQUEST_STATUS_A_U = "unbearbeitet";
	const POST_ATTRIBUT_FRONTEND_REQUEST_STATUS_A_A = "annehmen";
	const POST_ATTRIBUT_FRONTEND_REQUEST_STATUS_A_R = "ablehnen";
	const POST_ATTRIBUT_FRONTEND_USER_SUBMIT = "io_groups_user_submit";
	const POST_ATTRIBUT_FRONTEND_USER = "io_groups_user_";
	const POST_ATTRIBUT_FRONTEND_GROUP = "io_groups_group";
}
