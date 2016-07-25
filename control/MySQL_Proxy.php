<?php

/**
 * Description of Domain_Proxy
 *
 * @author KWM
 */
final class MySQL_Proxy {
	private static final function login($datenbank) {
		$db = new mysqli(MYSQL_HOST, MYSQL_USER, MYSQL_PASSWORT, $datenbank);
		if($db->connect_errno) { return false; }
		return $db;
	}
	
	private static final function logout($db) {
		$db->close();
	}
	
	public static final function insertDomain($host, $target, $www) {
		$db = self::login("manager");
		
		$idSelectSQL = "SELECT MAX(id) AS MaxID FROM " + Domain_Util::DOMAINTABLE;
		$statement = $db->query($idSelectSQL);
		$row = $statement->fetch_assoc();
		
		$id = $row['MaxID']+1;
		
		$insertSQL = "INSERT INTO " + Domain_Util::DOMAINTABLE + " VALUES(" + $id + ", '" + $host + "', 'http://" + $target + "', 0, " + $www + ", 0)";
		$db->query($insertSQL);
		
		self::logout($db);
	}
	
	public static final function updateDomain($host, $target, $www) {
		$db = self::login("manager");
		
		$result = self::getDomain($host);
		$set = "";
		
		if($result[Domain_Util::TARGET] != $target) {
			$set = Domain_Util::TARGET + " = '" + $target + "'";
		}
		
		if($result[Domain_Util::ALIAS != $www]) {
			$set += ($set != "" ? ", " : "") + Domain_Util::ALIAS + " = " + $www;
		}
		
		if($set != "") {
			$updateSQL = "UPDATE " + Domain_Util::DOMAINTABLE + " SET " + $set + " WHERE " + Domain_Util::HOST + " = '" + $host + "'";
			$db->query($updateSQL);
		}
		
		self::logout($db);
	}
	
	public static final function deleteDomain($host) {
		$db = self::login("manager");
		
		$delete = "DELETE FROM " + Domain_Util::DOMAINTABLE + " WHERE " + Domain_Util::HOST + " = '" + $host + "'";
		$db->query($delete);
		
		self::logout($db);
	}
	
	public static final function getDomain($host) {
		$db = self::login("manager");
		
		$select = "SELECT * FROM " + Domain_Util::DOMAINTABLE + " WHERE " + Domain_Util::HOST + " = '" + $host + "'";
		$statement = $db->query($select);
		$row = $statement->fetch_assoc();
		
		$return = array(
			Domain_Util::TARGET			=> $row[Domain_Util::TARGET],
			Domain_Util::ALIAS			=> $row[Domain_Util::ALIAS],
			Domain_Util::SSL			=> $row[Domain_Util::SSL]
		);
		
		self::logout($db);
		return $return;
	}
}
