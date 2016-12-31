<?php

/**
 * Description of Domain_Proxy
 *
 * @author KWM
 */
final class MySQL_Proxy {
	public static function getNewID($table, $atribute) {
		global $wpdb;

        $sql = "SELECT MAX(" . $atribute . ") AS MaxID FROM ".Domain_Util::DB.".".$table." LIMIT 1";
        $row = $wpdb->get_results($sql, ARRAY_A);
        return $row[0]['MaxID']+1;

	}

	/*
	 * STANDARD SQL-OPERATIONEN (CRUD)
	 */
	private static function create($table, $vars) {
        global $wpdb;

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
                if (is_numeric($value) || is_bool($value)) {
                    $values .= $value;
                } else {
                    $values .= "'" . $value . "'";
                }
            }
        }

		$sql = "INSERT INTO " . $table . " (" . $columns . ") VALUES (" . $values . ")";
        $wpdb->query($sql);
	}

	private static function read($table, $columns = "*", $where = "") {
        global $wpdb;

		if (is_array($columns)) {
			$columns = implode(", ", $columns);
		}

		$sql = "SELECT " . $columns . " FROM ".$table;
		if ($where != "") {
			$sql .= " WHERE " . $where;
		}
		$row = $wpdb->get_results($sql, ARRAY_A);

		return $row;
	}

	private static function exists($table, $where) {
        global $wpdb;

        $sql = "SELECT * FROM ".$table." WHERE ".$where;

        return count($wpdb->get_results($sql))>0;
    }

	private static function update($table, $values, $where = "") {
        global $wpdb;

		$set = "";
		foreach ($values AS $column => $value) {
			if ($value != "") {
				if ($set != "") {
					$set .= ", ";
				}

				$set .= $column . "=";
				if (is_numeric($value) || is_bool($value)) {
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

		$wpdb->query($sql);
	}

	private static function delete($table, $where = "")	{
        global $wpdb;

	    $sql = "DELETE FROM " . $table;
        if ($where != "") {
            $sql .= " WHERE " . $where;
        }

        $wpdb->query($sql);
	}

	/*
	 * DOMAIN-OPERATIONEN (CRUD)
	 */
    //Host CRUD
    public static final function createHost($host, $zweck) {
        $id = self::getNewID(Domain_Util::TABLE_HOST, "id");
        self::create(
            Domain_Util::DB.".".Domain_Util::TABLE_HOST,
            array(
                'id'                                => $id,
                Domain_Util::TABLE_HOST_C_HOST      => $host,
                Domain_Util::TABLE_HOST_C_TLS       => Domain_Control::isNotVM($zweck)?"0":"1",
                Domain_Util::TABLE_HOST_C_ACTIVE    => 1
            ));
        return $id;
    }

    public static final function updateHost($id, $host) {
        self::update(
            Domain_Util::DB.".".Domain_Util::TABLE_HOST,
            array(
                Domain_Util::TABLE_HOST_C_HOST  => $host
            ),
            Domain_Util::TABLE_HOST_C_ID."=".$id
        );
    }

    public static final function getHostByID($id) {
        return self::read(
            Domain_Util::DB.".".Domain_Util::TABLE_HOST,
            Domain_Util::TABLE_HOST_C_HOST,
            "id = " . $id
        )[0][Domain_Util::TABLE_HOST_C_HOST];
    }

    public static final function getIDByHost($host) {
        return self::read(
            Domain_Util::DB.".".Domain_Util::TABLE_HOST,
            Domain_Util::TABLE_HOST_C_ID,
            Domain_Util::TABLE_HOST_C_HOST." = ".$host
        )[0][Domain_Util::TABLE_HOST_C_ID];
    }

    public static final function checkHostExists($host) {
        return self::exists(Domain_Util::DB.".".Domain_Util::TABLE_HOST, Domain_Util::TABLE_HOST_C_HOST." = '".$host."'");
    }

    public static final function deleteHost($id) {
        self::delete(
            Domain_Util::DB.".".Domain_Util::TABLE_HOST,
            Domain_Util::TABLE_HOST_C_ID." = ".$id);
    }

    //Proxy
    public static final function createProxy($hostID, $target, $location = "") {
        $id = self::getNewID(Domain_Util::TABLE_PROXY, "id");
        self::create(
            Domain_Util::DB.".".Domain_Util::TABLE_PROXY,
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
        return self::read(
            Domain_Util::DB.".".Domain_Util::TABLE_PROXY,
            "*",
            Domain_Util::TABLE_PROXY_C_ID." = ".$settingID
        )[0];
    }

    public static final function updateProxy($settingsID, $target, $location) {
        self::create(
            Domain_Util::DB.".".Domain_Util::TABLE_PROXY,
            array(
                Domain_Util::TABLE_PROXY_C_TARGET   => $target,
                Domain_Util::TABLE_PROXY_C_LOCATION => $location
            ),
            Domain_Util::TABLE_PROXY_C_ID." = ".$settingsID);
    }

    public static final function deleteProxy($settingsID) {
        self::delete(
            Domain_Util::DB.".".Domain_Util::TABLE_PROXY,
            Domain_Util::TABLE_PROXY_C_ID." = ".$settingsID);
    }

    //Redirect
    public static final function createRedirect($hostID, $target, $location = "/") {
        $id = self::getNewID(Domain_Util::TABLE_REDIRECTS, "id");
        self::create(
            Domain_Util::DB.".".Domain_Util::TABLE_REDIRECTS,
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
        return self::read(
            Domain_Util::DB.".".Domain_Util::TABLE_REDIRECTS,
            "*",
            Domain_Util::TABLE_REDIRECTS_C_ID."=".$settingID
        )[0];
    }

    public static final function updateRedirect($settingsID, $target, $location = "/") {
        self::update(
            Domain_Util::DB.".".Domain_Util::TABLE_REDIRECTS,
            array(
                Domain_Util::TABLE_REDIRECTS_C_TARGET   => $target,
                Domain_Util::TABLE_REDIRECTS_C_LOCATION => $location
            ),
            Domain_Util::TABLE_REDIRECTS_C_HOST." = ".$settingsID);
    }

    public static final function deleteRedirect($settingsID) {
        self::delete(
            Domain_Util::DB.".".Domain_Util::TABLE_REDIRECTS,
            Domain_Util::TABLE_REDIRECTS_C_ID." = ".$settingsID);
    }
}
