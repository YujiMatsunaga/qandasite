<?php

namespace Qanda;

require_once __DIR__ . '/../config/def.php';

class Database
{
  private static $instance;

  public static function getInstance()
  {
    try {
      if (!isset(self::$instance)) {
        self::$instance = new \PDO(
          DB_URL,
          DB_USER,
          DB_PASS,
          [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_OBJ,
            \PDO::ATTR_EMULATE_PREPARES => false,
            ]
          );
      }
      return self::$instance;
    } catch (\PDOException $e) {
      $e->getMessage();
      exit;
    }
  }
}
