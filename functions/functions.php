<?php

	function io_case_change($value) {
		$search_accents = array('à','á','â','ã','ä', 'ç', 'è','é','ê','ë', 'ì','í','î','ï', 'ñ', 'ò','ó','ô','õ','ö', 'ù','ú','û','ü', 'ý','ÿ', 'À','Á','Â','Ã','Ä', 'Ç', 'È','É','Ê','Ë', 'Ì','Í','Î','Ï', 'Ñ', 'Ò','Ó','Ô','Õ','Ö', 'Ù','Ú','Û','Ü', 'Ý');
		$replace_accents = array('a','a','a','a','ae', 'c', 'e','e','e','e', 'i','i','i','i', 'n', 'o','o','o','o','oe', 'u','u','u','u', 'y','y', 'A','A','A','A','Ae', 'C', 'E','E','E','E', 'I','I','I','I', 'N', 'O','O','O','O','Oe', 'U','U','U','Ue', 'Y');

		return str_replace($search_accents, $replace_accents, $value);
	}
	
	function io_case_mail_change($value) {
		$search = array(' ');
		$replace = array('-');
		
		return strtolower(str_replace($search, $replace, io_case_change($value)));
	}
	
	function io_sanitize_user($user, $raw_user, $strict = false) {
		$raw_user = preg_replace( '@<(script|style)[^>]*?>.*?</\\1>@si', '', $raw_user );
		$raw_user = preg_replace('/[\r\n\t ]+/', ' ', $raw_user);
		$raw_user = trim($raw_user);
		$raw_user = strip_tags($raw_user);
        $raw_user = preg_replace( '/&.+?;/', '', $raw_user );
        $raw_user = preg_replace( '|\s+|', ' ', $raw_user );
		
		return $raw_user;
	}
	
?>
