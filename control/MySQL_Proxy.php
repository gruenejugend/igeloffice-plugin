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

	private static function getNewID($db, $table, $atribute) {
		$db = self::login($db);

		$sql = "SELECT MAX(" . $atribute . ") AS MaxID FROM " . $table;
		$statement = $db->query($sql);
		$row = $statement->fetch_assoc();

		self::logout($db);

		return $row['MaxID'] + 1;
	}

	/*
	 * STANDARD SQL-OPERATIONEN (CRUD)
	 */
	private static function create($db, $table, $vars) {
		$db = self::login($db);

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

	private static function read($db, $table, $columns = "*", $where = "") {
		$db = self::login($db);

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

	private static function update($db, $table, $values, $where = "") {
		$db = self::login($db);

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

	private static function delete($db, $table, $where = "")	{
		$db = self::login($db);

		$sql = "DELETE FROM " . $table;
		if ($where != "") {
			$sql .= " WHERE " . $where;
		}
		$db->query($sql);

		self::logout($db);
	}

	/*
	 * DOMAIN-OPERATIONEN (CRUD)
	 */
	public static final function createDomain($host, $target, $alias) {
		self::create(Domain_Util::DB,
			Domain_Util::DOMAINTABLE, array(
			'id' => self::getNewID(Domain_Util::DOMAINTABLE, "id"),
			Domain_Util::HOST => $host,
			Domain_Util::TARGET => $target,
			'pgpSubdomain' => 0,
			Domain_Util::ALIAS => $alias,
			'active' => 1
		));
	}

	public static final function readDomain($host) {
		$values = self::read(Domain_Util::DB, Domain_Util::DOMAINTABLE, "*", Domain_Util::HOST . " = '" . $host . "'");

		return array(
			'id' => $values['id'],
			Domain_Util::HOST => $values[Domain_Util::HOST],
			Domain_Util::TARGET => $values[Domain_Util::TARGET],
			Domain_Util::ALIAS => $values[Domain_Util::ALIAS]
		);
	}

	public static final function updateDomain($host, $target) {
		self::update(Domain_Util::DB,
			Domain_Util::DOMAINTABLE,
			array(Domain_Util::TARGET => $target),
			Domain_Util::HOST . "='" . $host . "'");
	}

	public static final function deleteDomain($host) {
		self::delete(Domain_Util::DB, Domain_Util::DOMAINTABLE, Domain_Util::HOST . "='" . $host . "'");
	}
}
