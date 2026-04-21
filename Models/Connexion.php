<?php

class Connexion {
    private static $pdo = null;

    public static function GetConnexion() {
        if (self::$pdo === null) {
            try {
                $pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
                self::$pdo = new PDO(
                    'mysql:host=localhost;dbname=;charset=utf8mb4',
                    '',
                    '',
                    $pdo_options
                );
                self::$pdo->exec("SET SESSION sql_mode='';");
                // 🔧 Désactiver le mode ONLY_FULL_GROUP_BY pour cette session
                self::$pdo->exec("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));");

            } catch (Exception $e) {
                die('ERREUR : ' . $e->getMessage());
            }
        }
        return self::$pdo;
    }
}
