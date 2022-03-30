<?php
//セッションを開始
session_start();
//エスケープ処理やデータをチェックする関数を記述したファイルの読み込み
require '../libs/functions.php';
//メールアドレス等を記述したファイルの読み込み
require '../libs/mailvars.php';

//お問い合わせ日時を日本時間に
date_default_timezone_set('Asia/Tokyo');

//POSTされたデータをチェック
$_POST = checkInput($_POST);

//固定トークンを確認（CSRF対策）
if (isset($_POST['ticket'], $_SESSION['ticket'])) {
  $ticket = $_POST['ticket'];
  if ($ticket !== $_SESSION['ticket']) {
    //トークンが一致しない場合は処理を中止
    die('Access denied');
  }
} else {
  //トークンが存在しない場合（入力ページにリダイレクト）
  //die( 'Access Denied（直接このページにはアクセスできません）' );  //処理を中止する場合
  $dirname = dirname($_SERVER['SCRIPT_NAME']);
  $dirname = $dirname == DIRECTORY_SEPARATOR ? '' : $dirname;
  //サーバー変数 $_SERVER['HTTPS'] が取得出来ない環境用（オプション）
  if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) and $_SERVER['HTTP_X_FORWARDED_PROTO'] === "https") {
    $_SERVER['HTTPS'] = 'on';
  }
  $url = (empty($_SERVER['HTTPS']) ? 'http://' : 'https://') . $_SERVER['SERVER_NAME'] . $dirname . '/contact.php';
  header('HTTP/1.1 303 See Other');
  header('location: ' . $url);
  exit; //忘れないように
}

//変数にエスケープ処理したセッション変数の値を代入
$name = h($_SESSION['name']);
$email = h($_SESSION['email']);
$title = h($_SESSION['title']);
$contents = h($_SESSION['contents']);

//メール本文の組み立て
$mail_body = 'コンタクトページからのお問い合わせ' . "\n\n";
$mail_body .=  date("Y年m月d日 H時i分") . "\n\n";
$mail_body .=  "お名前： " . $name . "\n";
$mail_body .=  "Email： " . $email . "\n";
$mail_body .=  "＜お問い合わせ内容＞" . "\n" . $contents;

//-------- sendmail（mb_send_mail）を使ったメールの送信処理------------

//メールの宛先（名前<メールアドレス> の形式）。値は mailvars.php に記載
$mailTo = mb_encode_mimeheader(MAIL_TO_NAME) . "<" . MAIL_TO . ">";

//Return-Pathに指定するメールアドレス
$returnMail = MAIL_RETURN_PATH; //
//mbstringの日本語設定
mb_language('ja');
mb_internal_encoding('UTF-8');

// 送信者情報（From ヘッダー）の設定
$header = "From: " . mb_encode_mimeheader($name) . "<" . $email . ">\n";
$header .= "Cc: " . mb_encode_mimeheader(MAIL_CC_NAME) . "<" . MAIL_CC . ">\n";
$header .= "Bcc: <" . MAIL_BCC . ">";

//メールの送信（結果を変数 $result に格納）
if (ini_get('safe_mode')) {
  //セーフモードがOnの場合は第5引数が使えない
  $result = mb_send_mail($mailTo, $title, $mail_body, $header);
} else {
  $result = mb_send_mail($mailTo, $title, $mail_body, $header, '-f' . $returnMail);
}

//メール送信の結果判定
if ($result) {
  //成功した場合はセッションを破棄
  $_SESSION = array(); //空の配列を代入し、すべてのセッション変数を消去 
  session_destroy(); //セッションを破棄
} else {
  //送信失敗時（もしあれば）
  echo "失敗っすね";
}
