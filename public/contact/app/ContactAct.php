<?php

namespace Qanda;

require_once '../../app/Action.php';
require_once '../../app/Utils.php';

define("INSERT_CONTACT", "INSERT INTO contacts(name, email, title, contents) VALUES(:name, :email, :title, :contents)");

class ContactAct extends Action
{
  // 登録
  function post($name, $email, $title, $contents)
  {
    $stmt = $this->conn->prepare(INSERT_CONTACT);
    $stmt->bindValue("name", $name, \PDO::PARAM_STR);
    $stmt->bindValue("email", $email, \PDO::PARAM_STR);
    $stmt->bindValue("title", $title, \PDO::PARAM_STR);
    $stmt->bindValue("contents", $contents, \PDO::PARAM_STR);
    $stmt->execute();
  }
}
