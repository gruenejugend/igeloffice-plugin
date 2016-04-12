<?php

/**
 * Description of Request_Control
 *
 * @author KWM
 */
interface Request_Strategy {
	static function art();
	
	function __construct($id);
	
	function getArt();
	function getArtSuffix($requested_id);
	
	function approve($id);
	function reject($id);
	
	function getObject();
}
