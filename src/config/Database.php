<?php
class Database {
    private static $conn = null;

    public static function getConnection() {
        if (self::$conn == null) {
            try {
                self::$conn = new PDO("mysql:host=" . "srv1575.hstgr.io" . ";dbname=" . "u564798502_cllaguarima", "u564798502_cllaguarima", "Cllaguarima.2025");
                self::$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$conn->exec("set names utf8");
            } catch(PDOException $exception) {
                echo "Error de conexión: " . $exception->getMessage();
            }
        }
        return self::$conn;
    }
}