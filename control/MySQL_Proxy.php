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
    //Host CRUD
    public static final function createHost($host) {
        $id = self::getNewID(Domain_Util::TABLE_HOST, "id");
        self::create(Domain_Util::DB,
            Domain_Util::TABLE_HOST,
            array(
                'id'                            => $id,
                Domain_Util::TABLE_HOST_C_HOST  => $host
            ));
        return $id;
    }

    public static final function updateHost($id, $host) {
        self::update(Domain_Util::DB,
            Domain_Util::TABLE_HOST,
            array(
                Domain_Util::TABLE_HOST_C_HOST  => $host
            ),
            Domain_Util::TABLE_HOST_C_ID."=".$id
        );
    }

    public static final function getHostByID($id) {
        return self::read(Domain_Util::DB, Domain_Util::TABLE_HOST, Domain_Util::TABLE_HOST_C_HOST, "id = '" . $id . "'")[Domain_Util::TABLE_HOST_C_HOST];
    }

    public static final function deleteHost($id) {
        self::delete(Domain_Util::DB,
            Domain_Util::TABLE_HOST,
            Domain_Util::TABLE_HOST_C_ID."=".$id);
    }

    //Proxy
    public static final function createProxy($hostID, $target, $location = "") {
        $id = self::getNewID(Domain_Util::TABLE_PROXY, "id");
        self::create(Domain_Util::DB,
            Domain_Util::TABLE_PROXY,
            array(
                'id'                                => $id,
                Domain_Util::TABLE_PROXY_C_ACTIVE   => 1,
                Domain_Util::TABLE_PROXY_C_TARGET   => $target,
                Domain_Util::TABLE_PROXY_C_HOST     => $hostID,
                Domain_Util::TABLE_PROXY_C_LOCATION => $location
            ));
        return $id;
    }

    public static final function getProxyByID($settingID) {
        return self::read(Domain_Util::DB,
            Domain_Util::TABLE_PROXY,
            "*",
            Domain_Util::TABLE_PROXY_C_ID."=".$settingID
        );
    }

    public static final function updateProxy($settingsID, $target, $location) {
        self::create(Domain_Util::DB,
            Domain_Util::TABLE_PROXY,
            array(
                Domain_Util::TABLE_PROXY_C_TARGET   => $target,
                Domain_Util::TABLE_PROXY_C_LOCATION => $location
            ),
            Domain_Util::TABLE_PROXY_C_ID." = ".$settingsID);
    }

    public static final function deleteProxy($settingsID) {
        self::delete(Domain_Util::DB,
            Domain_Util::TABLE_PROXY,
            Domain_Util::TABLE_PROXY_C_ID."=".$settingsID);
    }

    //Redirect
    public static final function createRedirect($hostID, $target, $location = "/") {
        $id = self::getNewID(Domain_Util::TABLE_REDIRECTS, "id");
        self::create(Domain_Util::DB,
            Domain_Util::TABLE_REDIRECTS,
            array(
                "id"                                    => $id,
                Domain_Util::TABLE_REDIRECTS_C_HOST     => $hostID,
                Domain_Util::TABLE_REDIRECTS_C_LOCATION => $location,
                Domain_Util::TABLE_REDIRECTS_C_TARGET   => $target,
                Domain_Util::TABLE_REDIRECTS_C_MODE     => Domain_Util::TABLE_REDIRECTS_C_MODE_E_PERM
            ));
        return $id;
    }

    public static final function getRedirectByID($settingID) {
        return self::read(Domain_Util::DB,
            Domain_Util::TABLE_REDIRECTS,
            "*",
            Domain_Util::TABLE_REDIRECTS_C_ID."=".$settingID
        );
    }

    public static final function updateRedirect($settingsID, $target, $location = "/") {
        self::update(Domain_Util::DB,
            Domain_Util::TABLE_REDIRECTS,
            array(
                Domain_Util::TABLE_REDIRECTS_C_TARGET   => $target,
                Domain_Util::TABLE_REDIRECTS_C_LOCATION => $location
            ),
            Domain_Util::TABLE_REDIRECTS_C_HOST." = ".$settingsID);
    }

    public static final function deleteRedirect($settingsID) {
        self::delete(Domain_Util::DB,
            Domain_Util::TABLE_REDIRECTS,
            Domain_Util::TABLE_REDIRECTS_C_HOST."=".$settingsID);
    }
}
