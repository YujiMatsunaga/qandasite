<?php

namespace Qanda;

require_once 'Action.php';
require_once 'Utils.php';

// ToDo取得系
define("QUERY_TODO_LIST", "SELECT TODO_ID,USER_ID,TITLE,STATE,REMIND_DATE FROM todo WHERE USER_ID=:user_id");

// ToDo更新系
define("INSERT_TODO", "INSERT INTO todo (USER_ID,TITLE,STATE,REMIND_DATE) VALUES (:user_id, :title, 'active', :remind_date)");
define("UPDATE_TODO", "UPDATE todo SET TITLE=:title,REMIND_DATE=:remind_date WHERE TODO_ID=:todo_id AND USER_ID=:user_id");
define("UPDATE_TODO_STATE", "UPDATE todo SET STATE=:state WHERE TODO_ID=:todo_id AND USER_ID=:user_id");
define("DELETE_TODO", "DELETE FROM todo WHERE TODO_ID=:todo_id AND USER_ID=:user_id");

// タイトルの長さ
define("TITLE_LENGTH", 128);

// ToDO関連のクラス
class TodoAct extends Action
{
  // コンストラクタ($mode>=0の場合、明示的にbeginを呼び出す
  function __construct($mode = -1) {
    if ($mode >= 0) {
      $this->begin($mode);
    }
  }

  // ToDo一覧取得
  function get()
  {
    $retinfo = array();

    // ToDo情報取得
    $activelist = array();
    $finlist = array();
    $stmt = $this->conn->prepare(QUERY_TODO_LIST);
    $stmt->bindValue(':user_id', $this->member['user_id']);
    $result = $stmt->execute();
    if ($result) {
      while ($rec =  $stmt->fetch(\PDO::FETCH_ASSOC)) {
        switch ($rec['STATE']) {
          case 'active':
            $activelist[] = $rec;
            break;
          case 'finish':
            $finlist[] = $rec;
            break;
        }
      }
    }
    $retinfo['activelist'] = $activelist;
    $retinfo['finlist'] = $finlist;

    return $retinfo;
  }
  // ToDo登録
  function add($title, $remind_date)
  {
    if (!$title || !$remind_date) {
      return;
    }
    // DB登録
    $stmt = $this->conn->prepare(INSERT_TODO);
    $stmt->bindValue(':user_id', $this->member['user_id']);
    $stmt->bindValue(':title', $title);
    $stmt->bindValue(':remind_date', $remind_date);
    $stmt->execute();
  }

  // ToDo編集
  function edit($todo_id, $title, $remind_date)
  {
    if (!$todo_id || !$title || !$remind_date ) {
      return;
    }
    // DB登録
    $stmt = $this->conn->prepare(UPDATE_TODO);
    $stmt->bindValue(':todo_id', $todo_id);
    $stmt->bindValue(':user_id', $this->member['user_id']);
    $stmt->bindValue(':title', $title);
    $stmt->bindValue(':remind_date', $remind_date);
    return $stmt->execute();
  }

  // ToDo 状態変更
  function toggle($todo_id, $state)
  {
    if (!$todo_id) {
      return;
    }
    if ($state != 'active' && $state != 'finish') {
      return;
    }
    $stmt = $this->conn->prepare(UPDATE_TODO_STATE);
    $stmt->bindValue(':todo_id', $todo_id);
    $stmt->bindValue(':user_id', $this->member['user_id']);
    $stmt->bindValue(':state', $state);
    return $stmt->execute();
  }
  
  // ToDo削除
  function delete($todo_id)
  {
    if (!$todo_id) {
      return;
    }
    $stmt = $this->conn->prepare(DELETE_TODO);
    $stmt->bindValue(':todo_id', $todo_id);
    $stmt->bindValue(':user_id', $this->member['user_id']);
    return $stmt->execute();
  }
}
