<?php

/**
 * Description of mailStandard_Control
 *
 * @author KWM
 */
class cloud_view {
	public static function maskHandler() {
		$owncloud_model = new cloud_model(get_current_user_id());
		
		include 'wp-content/plugins/igeloffice/permission/cloud/templates/cloud.php';
	}
}