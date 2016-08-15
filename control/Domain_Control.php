<?php

/**
 * Description of Domain_Control
 *
 * @author KWM
 */
class Domain_Control {
	public static function create($domain, $routing, $alias, $besitzer_in) {
		$id = wp_insert_post(array(
			'post_title'		=> $domain,
			'post_type'			=> Domain_Util::POST_TYPE,
			'post_status'		=> 'draft',
			'post_author'		=> $besitzer_in
		));
		
		self::createMeta($id, $routing, $alias);
		
		return $id;
	}
	
	public static function createMeta($id, $routing, $alias) {
		$routing = self::prepareRouting($routing);
		update_post_meta($id, Domain_Util::ATTRIBUT_TARGET, $routing);
		update_post_meta($id, Domain_Util::ATTRIBUT_ALIAS, $alias);
	}

	public static function prepareRouting($routing)
	{
		return str_replace(array("https://", "http://"), "", $routing);
	}
	
	public static function update($host, $target, $alias) {
		MySQL_Proxy::updateDomain($host, $target, $alias);
	}
	
	public static function delete($id) {
		if(get_post_type($id) == Domain_Util::POST_TYPE) {
			$domain = new Domain($id);
			MySQL_Proxy::deleteDomain($domain->host);
		}
	}

	public static function prepareDomain($name)
	{
		return io_umlaute(strtolower(str_replace(" ", "-", $name)));
	}
	
	public static function freigabe($id) {
		if(get_post_type($id) == Domain_Util::POST_TYPE) {
			$target = get_post_meta($id, Domain_Util::ATTRIBUT_TARGET, true);
			delete_post_meta($id, Domain_Util::ATTRIBUT_TARGET);
			
			$alias = get_post_meta($id, Domain_Util::ATTRIBUT_TARGET, true);
			delete_post_meta($id, Domain_Util::ATTRIBUT_TARGET);
			
			$domain = new Domain($id);
			
			MySQL_Proxy::insertDomain($domain->host, $target, $alias);
		}
	}
}
