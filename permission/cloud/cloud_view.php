<?php

/**
 * Description of mailStandard_Control
 *
 * @author KWM
 */
class cloud_view {
    const POST_SUBMIT = "io_cloud_submit";
    const POST_NONCE = "io_cloud_nonce";

    const NONCE = "nonce";

	public static function maskHandler() {
		$owncloud_model = new cloud_model(get_current_user_id());

        if(!$owncloud_model->isPermitted) {
            return;
        }

        self::maskCloud();

        if($owncloud_model->isSpacePermitted) {
            self::maskSpace();
        }
	}

	public static function maskCloud() {
        include 'wp-content/plugins/igeloffice/permission/cloud/templates/cloud.php';
    }

    public static function maskSpace() {
        if(isset($_POST[self::POST_SUBMIT])) {
            self::maskSpaceExecution();
        }

        $model = new cloud_model(get_current_user_id());

        include 'wp-content/plugins/igeloffice/permission/cloud/templates/space.php';
    }

	public static function maskSpaceExecution() {
        if( !isset($_POST[self::POST_NONCE]) ||
            !wp_verify_nonce($_POST[self::POST_NONCE], self::NONCE)) {
            return;
        }

        $model = new cloud_model(get_current_user_id());

        Request_Control::create(get_current_user_id(), Request_Cloud::art());

	    echo "<h1>Cloud-Space beantragt.</h1>";
    }
}