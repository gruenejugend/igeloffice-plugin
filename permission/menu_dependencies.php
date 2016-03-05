<?php

/**
 * Description of User_Control
 *
 * @author KWM
 */
class menu_dependencies {
	public static function cloudMenu($conditions) {
		$conditions[] = array(
			'name'			=> 'CloudPermitted',
			'condition'		=> function() {
				$owncloud_model = new cloud_model(get_current_user_id());
				return $owncloud_model->isPermitted;
			}
		);
		
		return $conditions;
	}
	
	public static function mailMenu($conditions) {
		$conditions[] = array(
			'name'			=> 'MailPermitted',
			'condition'		=> function() {
				$mailStandard_model = new mailStandard_model(get_current_user_id());
				return $mailStandard_model->isMailPermitted || $mailStandard_model->isMailForwardPermitted;
			}
		);
		
		return $conditions;
	}
	
	public static function listMenu($conditions) {
		$conditions[] = array(
			'name'			=> 'ListPermitted',
			'condition'		=> function() {
				$mailinglisten_model = new mailinglisten_model(get_current_user_id());
				return $mailinglisten_model->isPermitted;
			}
		);
		
		return $conditions;
	}
	
	public static function diensteMenu($conditions) {
		$conditions[] = array(
			'name'			=> 'DienstPermitted',
			'condition'		=> function() {
				$mailinglisten_model = new mailinglisten_model(get_current_user_id());
				$mailStandard_model = new mailStandard_model(get_current_user_id());
				$owncloud_model = new cloud_model(get_current_user_id());
				return $mailinglisten_model->isPermitted || $mailStandard_model->isMailPermitted || $mailStandard_model->isMailForwardPermitted || $owncloud_model->isPermitted;
			}
		);
		
		return $conditions;
	}
}