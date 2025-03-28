<?php

namespace Utility;

if (!defined('MAIN_SCRIPT')) die(__FILE__ . ':' . __LINE__ . ' says: Go away!' . ' ');
//require_once "utility.inc.php";

class DBConn
{
    public static function getConnection(): \PDO
    {
        return getVerbindung();
    }
}