<?php
session_start();

define("DSN", "mysql:host=localhost; dbname=qanda; charset=utf8mb4");
define("DB_USER", "root");
define("DB_PASS", "");

require_once("db.php");