<?php

class Database
{
    public static string $error = "";
    private static ?PDO $pdo = null;

    //vypůjčeno =)
    private static function getPDO () : PDO
    {
        return self::$pdo == null ? self::initializePDO() : self::$pdo;
    }

    #region initialize pdo
    //vypůjčeno =)
    private static function initializePDO() : PDO{
        $host = "127.0.0.1";
        $db = "fill db connection";
        $user = "fill db user";
        $pass = "fill db password";
        $charset = "utf8mb4";

        $dsn = "mysql:host=$host;dbname=$db;charset=$charset";

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
            PDO::ATTR_EMULATE_PREPARES => false
        ];

        self::$pdo = new PDO($dsn, $user, $pass, $options);

        return self::$pdo;
    }
    #endregion


    public static function select($sql, $params=null)
    {
        $stmt = self::getPDO()->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchObject();
    }

    public static function fetch($sql, $params=null )
    {
        $stmt = self::getPDO()->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    //metoda vrací počet řádků
    public static function getNumberOfRows($sql, $params = null) : int
    {
        $stmt = self::getPDO()->prepare($sql);
        $stmt->execute($params);

        return $stmt->rowCount();
    }

    //metoda vrací pouze jestli se SQL příkaz povedl nebo ne
    public static function checkSuccess($sql, $params=null) : bool
    {
        $stmt = self::getPDO()->prepare($sql);
        return $stmt->execute($params);
    }

    //metoda vrací poslední vložené id
    public static  function  insert ($sql, $params = null) : bool|int {
        $stmt = self::getPDO()->prepare($sql);

       if(!$stmt->execute($params)) return  false;

        return  self::getPDO()->lastInsertId();
    }
}
