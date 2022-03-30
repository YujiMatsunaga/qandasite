<?php
//セッションを開始
session_start();
//セッションIDを更新して変更（セッションハイジャック対策）
session_regenerate_id(TRUE);
//エスケープ処理やデータチェックを行う関数のファイルの読み込み
require '../libs/functions.php';
//NULL 合体演算子を使ってセッション変数を初期化
$name = $_SESSION['name'] ?? NULL;
$email = $_SESSION['email'] ?? NULL;
$email_check = $_SESSION['email_check'] ?? NULL;
$tel = $_SESSION['tel'] ??  NULL;
$title = $_SESSION['title'] ?? NULL;
$contents = $_SESSION['contents'] ?? NULL;
$error = $_SESSION['error'] ?? NULL;

//個々のエラーを NULL で初期化
$error_name = $error['name'] ?? NULL;
$error_email = $error['email'] ?? NULL;
$error_email_check = $error['email_check'] ?? NULL;
$error_tel = $error['tel'] ?? NULL;
$error_subject = $error['title'] ?? NULL;
$error_body = $error['contents'] ?? NULL;



//CSRF対策の固定トークンを生成
if (!isset($_SESSION['ticket'])) {
  //セッション変数にトークンを代入
  $_SESSION['ticket'] = bin2hex(random_bytes(32));
}
//トークンを変数に代入
$ticket = $_SESSION['ticket'];
