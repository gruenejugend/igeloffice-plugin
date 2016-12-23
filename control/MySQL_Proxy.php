<?php

/**
 * Description of Domain_Proxy
 *
 * @author KWM
 */
final class MySQL_Proxy {
	public static final function createDomain($host, $target, $alias)
	{
		self::create(Domain_Util::DOMAINTABLE, array(
			'id' => self::getNewID(Domain_Util::DOMAINTABLE, "id"),
			Domain_Util::HOST => $host,
			Domain_Util::TARGET => $target,
			'pgpSubdomain' => 0,
			Domain_Util::ALIAS => $alias,
			'active' => 1
		));
	}

	private static function create($table, $vars)
	{
		$db = self::login("manager");

		$columns = "";
		$values = "";
		foreach ($vars AS $column => $value) {
			if ($value != "") {
				if ($columns != "") {
					$columns .= ", ";
				}

				if ($values != "") {
					$values .= ", ";
				}

				$columns .= $column;
				if (is_numeric($value)) {
					$values .= $value;
				} else {
					$values .= "'" . $value . "'";
				}
			}
		}

		$sql = "INSERT INTO " . $table . " (" . $columns . ") VALUES (" . $values . ")";
		$db->query($sql);

		self::logout($db);
	}

	/*
	 * STANDARD SQL-OPERATIONEN (CRUD)
	 */

	private static final function login($datenbank) {
		$db = new mysqli(MYSQL_HOST, MYSQL_USER, MYSQL_PASSWORT, $datenbank);
		if($db->connect_errno) { return false; }
		return $db;
	}

	private static final function logout($db) {
		$db->close();
	}

	private static function getNewID($table, $atribute)
	{
		$db = self::login("manager");

		$sql = "SELECT MAX(" . $atribute . ") AS MaxID FROM " . $table;
		$statement = $db->query($sql);
		$row = $statement->fetch_assoc();

		self::logout($db);

		return $row['MaxID'] + 1;
	}

	public static final function readDomain($host)
	{
		$values = self::read(Domain_Util::DOMAINTABLE, "*", Domain_Util::HOST . " = '" . $host . "'");

		return array(
			'id' => $values['id'],
			Domain_Util::HOST => $values[Domain_Util::HOST],
			Domain_Util::TARGET => $values[Domain_Util::TARGET],
			Domain_Util::ALIAS => $values[Domain_Util::ALIAS]
		);
	}

	private static function read($table, $columns = "*", $where = "")
	{
		$db = self::login("manager");

		if (is_array($columns)) {
			$columns = implode(", ", $columns);
		}

		$sql = "SELECT " . $columns . " FROM " . $table;
		if ($where != "") {
			$sql .= " WHERE " . $where;
		}
		$statement = $db->query($sql);
		$row = $statement->fetch_assoc();

		self::logout($db);

		return $row;
	}


	/*
	 * DOMAIN-OPERATIONEN (CRUD)
	 */

	public static final function updateDomain($host, $target)
	{
		self::update(Domain_Util::DOMAINTABLE,
			array(Domain_Util::TARGET => $target),
			Domain_Util::HOST . "='" . $host . "'");
	}

	private static function update($table, $values, $where = "")
	{
		$db = self::login("manager");

		$set = "";
		foreach ($values AS $column => $value) {
			if ($value != "") {
				if ($set != "") {
					$set .= ", ";
				}

				$set .= $column . "=";
				if (is_numeric($value)) {
					$set .= $value;
				} else {
					$set .= "'" . $value . "'";
				}
			}
		}

		$sql = "UPDATE " . $table . " SET " . $set;
		if ($where != "") {
			$sql .= " WHERE " . $where;
		}
		$db->query($sql);

		self::logout($db);
	}

	public static final function deleteDomain($host)
	{
		self::delete(Domain_Util::DOMAINTABLE, Domain_Util::HOST . "='" . $host . "'");
	}

	private static function delete($table, $where = "")
	{
		$db = self::login("manager");

		$sql = "DELETE FROM " . $table;
		if ($where != "") {
			$sql .= " WHERE " . $where;
		}
		$db->query($sql);

		self::logout($db);
	}
}
