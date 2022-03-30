<?php
// ToDo追加処理
require_once "../../../app/TodoAct.php";
require_once '../../../app/Token.php';
require_once '../../../app/Utils.php';

use Qanda\TodoAct;
use Qanda\Token;
use Qanda\Utils;

$act = new ToDoAct();
$act->begin(1);

// トークンチェック
Token::validate();

$newtodotitle = filter_input(INPUT_POST, 'newtodotitle');
$newtododt = filter_input(INPUT_POST, 'newtododt');

$newtodotitle = Utils::mbtrim($newtodotitle);

if (!Utils::isStrLen($newtodotitle, 128)) {
  // 範囲外
  header('Location: ' . DOMAIN . '/public/todo/index.php?errid=invalidtitle');
  exit;
}

if (!Utils::checkDatetimeFormat($newtododt)) {
  // 日付フォーマットが違う
  header('Location: ' . DOMAIN . '/public/todo/index.php?errid=invalidformatdt');
  exit;
}

// ToDo追加
$act->add($newtodotitle, $newtododt);

// ToDo一覧へリダイレクト
header('Location: ' . DOMAIN . '/public/todo/index.php');
