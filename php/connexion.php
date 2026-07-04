<?php

class DB
{
    public PDO $db;

    public function __construct()
    {
        $config = require __DIR__ . '/config.php';
        $dbConf = $config['db'];

        $dsn = "mysql:host={$dbConf['host']};dbname={$dbConf['name']};charset={$dbConf['charset']}";

        try {
            $this->db = new PDO($dsn, $dbConf['user'], $dbConf['pass'], [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $e) {
            error_log('DB connection failed: ' . $e->getMessage());
            die('Impossible de se connecter à la base de données.');
        }
    }
}
