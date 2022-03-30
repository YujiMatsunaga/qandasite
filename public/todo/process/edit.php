<?php
// ToDo編集
require_once "../../../app/TodoAct.php";
require_once '../../../app/Token.php';

use Qanda\TodoAct;
use Qanda\Token;
use Qanda\Utils;

$act = new ToDoAct();
$act->begin(1);

// トークンチェック
Token::validate();

$edittodoid = filter_input(INPUT_POST, 'edittodoid', FILTER_VALIDATE_INT);
$edittodotitle = filter_input(INPUT_POST, 'edittodotitle');
$edittododt = filter_input(INPUT_POST, 'edittododt');

$edittodotitle = Utils::mbtrim($edittodotitle);

if (!Utils::isStrLen($edittodotitle, 128)) {
  // 範囲外
  header('Location: ' . DOMAIN . '/public/todo/index.php?errid=invalidtitle');
  exit;
}

if (!Utils::checkDatetimeFormat($edittododt)) {
  // 日付フォーマットが違う
  header('Location: ' . DOMAIN . '/public/todo/index.php?errid=invalidformatdt');
  exit;
}

// ToDo編集
$act->edit($edittodoid, $edittodotitle, $edittododt);

// ToDo一覧へリダイレクト
header('Location: ' . DOMAIN . '/public/todo/index.php');
