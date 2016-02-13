<?php

/**
 * Description of mailinglisten_control
 *
 * @author KWM
 */
class mailinglisten_control {
	const MAILINGLISTEN_PERMISSION = 'ListenAbo';
	
	public static function getPermission() {
		return new Permission(get_page_by_title(self::MAILINGLISTEN_PERMISSION, OBJECT, Permission_Control::POST_TYPE)->ID);
	}
}
