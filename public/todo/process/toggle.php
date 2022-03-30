<?php
// ToDoステータス変更
require_once "../../../app/TodoAct.php";
require_once "../../../app/Token.php";

use Qanda\TodoAct;
use Qanda\Token;

$act = new TodoAct();
$act->begin(1);

// トークンチェック
Token::validate();

$todoid = filter_input(INPUT_POST, "todoid");
$state = filter_input(INPUT_POST, "state");

// ToDoステータス変更
$act->toggle($todoid, $state);

// ajax呼び出し。 戻り値を出力
echo "success";

