<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of owncloud_control
 *
 * @author KWM
 */
class cloud_control {
	const CLOUD_PERMISSION = 'Cloud';
	
	public static function getPermission() {
		return new Permission(get_page_by_title(self::CLOUD_PERMISSION, OBJECT, Permission_Util::POST_TYPE)->ID);
	}
}
