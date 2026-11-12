<?php

if (!class_exists('Database')) {

class Database {
    private static ?mysqli $connection = null;
    
    public static function getConnection(): mysqli
    {
        if (self::$connection === null) {
            $host = 'localhost';
            $user = 'root';
            $pass = '';
            $db = 'AFROTOUCH';
            
            self::$connection = new mysqli($host, $user, $pass, $db);
            
            if (self::$connection->connect_error) {
                die("Connection failed: " . self::$connection->connect_error);
            }
            
            self::$connection->set_charset("utf8mb4");
        }
        
        return self::$connection;
    }
    
    public static function query(string $sql): mysqli_result|bool
    {
        $result = self::getConnection()->query($sql);
        if ($result === false) {
            error_log("Database Query Error: " . self::getConnection()->error);
        }
        return $result;
    }
    
    public static function prepare(string $sql): mysqli_stmt|bool
    {
        $stmt = self::getConnection()->prepare($sql);
        if ($stmt === false) {
            error_log("Database Prepare Error: " . self::getConnection()->error);
            return false;
        }
        return $stmt;
    }
    
    public static function escape(string $value): string
    {
        return self::getConnection()->real_escape_string($value);
    }
    
    public static function getLastInsertId(): string
    {
        return self::getConnection()->insert_id;
    }
}

}
