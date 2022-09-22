<?php
// ToDoステータス変更
require_once "../../../app/TodoAct.php";
require_once "../../../app/Token.php";

use Novus\TodoAct;
use Novus\Token;

$act = new TodoAct();
$act->begin(1);

// ログインチェック
$act->checkLogin();

// トークンチェック
Token::validate();

$todoId = filter_input(INPUT_POST, "todoId", FILTER_SANITIZE_NUMBER_INT);
$state = filter_input(INPUT_POST, "state", FILTER_SANITIZE_SPECIAL_CHARS);

// ステータス変更
$act->toggle($todoId, $state);

// ajax呼び出し。 戻り値を出力
echo "success";

