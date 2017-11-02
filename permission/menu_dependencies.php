<?php

/**
 * Description of User_Control
 *
 * @author KWM
 */
class menu_dependencies {
	public static function cloudMenu($conditions) {
		$conditions[] = array(
			'id'			=> 'CloudPermitted',
			'name'			=> 'CloudPermitted',
			'condition'		=> function() {
				$owncloud_model = new cloud_model(get_current_user_id());
				return get_current_user_id() != 0 && $owncloud_model->isPermitted;
			}
		);
		
		return $conditions;
	}
	
	public static function mailMenu($conditions) {
		$conditions[] = array(
			'id'			=> 'MailPermitted',
			'name'			=> 'MailPermitted',
			'condition'		=> function() {
				$mailStandard_model = new mailStandard_model(get_current_user_id());
				return get_current_user_id() != 0 && ($mailStandard_model->isMailPermitted || $mailStandard_model->isMailForwardPermitted);
			}
		);
		
		return $conditions;
	}
	
	public static function listMenu($conditions) {
		$conditions[] = array(
			'id'			=> 'ListPermitted',
			'name'			=> 'ListPermitted',
			'condition'		=> function() {
				$mailinglisten_model = new mailinglisten_model(get_current_user_id());
				return get_current_user_id() != 0 && $mailinglisten_model->isPermitted;
			}
		);
		
		return $conditions;
	}
	
	public static function diensteMenu($conditions) {
		$conditions[] = array(
			'id'			=> 'DienstPermitted',
			'name'			=> 'DienstPermitted',
			'condition'		=> function() {
				$mailinglisten_model = new mailinglisten_model(get_current_user_id());
				$mailStandard_model = new mailStandard_model(get_current_user_id());
				$owncloud_model = new cloud_model(get_current_user_id());
				return get_current_user_id() != 0 && ($mailinglisten_model->isPermitted || $mailStandard_model->isMailPermitted || $mailStandard_model->isMailForwardPermitted || $owncloud_model->isPermitted);
			}
		);
		
		return $conditions;
	}

    public static function domainMenu($conditions) {
        $conditions[] = array(
			'id'			=> 'DomainPermitted',
            'name'			=> 'DomainPermitted',
            'condition'		=> function() {
                $model = new Domain_Front_Model(get_current_user_id());
                return get_current_user_id() != 0 && ($model->isDomainPermitted);
            }
        );

        return $conditions;
    }
}