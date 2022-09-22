<?php
require_once "../../../app/TodoAct.php";
require_once '../../../app/Token.php';

use Novus\TodoAct;
use Novus\Token;

$act = new ToDoAct();
$act->begin(1);

// ログインチェック
$act->checkLogin();

// トークンチェック
Token::validate();

$todoId = filter_input(INPUT_POST, 'todoId', FILTER_SANITIZE_NUMBER_INT);

$act->delete($todoId);

// ajax呼び出し。 戻り値を出力
echo 'success';
