<?php

namespace App\Database;

use PDO;
use PDOException;

class DatabaseConnection
{
    private static $pdo;
    private static $dbFile = 'database.db';
    
    /**
     * Ottiene la connessione al database
     */
    public static function connect(): ?PDO
    {
        if (self::$pdo === null) {
            try {
                self::$pdo = new PDO('sqlite:' . self::$dbFile);
                self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                return null;
            }
        }
        return self::$pdo;
    }
    
    /**
     * Chiude la connessione
     */
    public static function close(): void
    {
        self::$pdo = null;
    }
}
