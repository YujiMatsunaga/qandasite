<?php
//セッションを開始
// session_start();
//エスケープ処理やデータチェックを行う関数のファイルの読み込み
require '../libs/functions.php';
require_once "config.php";
require_once "../../app/Action.php";
require_once "../app/ContactAct.php";

// use Qanda\ContactAct;

// $act = new ContactAct();
// $pdo = Database::getInstance();

//POSTされたデータをチェック
$_POST = checkInput($_POST);
//固定トークンを確認（CSRF対策）
if (isset($_POST['ticket'], $_SESSION['ticket'])) {
  $ticket = $_POST['ticket'];
  if ($ticket !== $_SESSION['ticket']) {
    //トークンが一致しない場合は処理を中止
    die('アクセスに失敗しました');
  }
} else {
  //トークンが存在しない場合は処理を中止（直接アクセスした場合）
  die('直接このページにアクセスすることはできません');
}


//POSTされたデータを変数に格納（値の初期化とデータの整形：前後にあるホワイトスペースを削除）
$name = trim(filter_input(INPUT_POST, 'name'));
$email = trim(filter_input(INPUT_POST, 'email'));
$email_check = trim(filter_input(INPUT_POST, 'email_check'));
$title = trim(filter_input(INPUT_POST, 'title'));
$contents = trim(filter_input(INPUT_POST, 'contents'));

//エラーメッセージを保存する配列の初期化
$error = array();
//値の検証（入力内容が条件を満たさない場合はエラーメッセージを配列 $error に設定）
if ($name == '') {
  $error['name'] = '*お名前は必須項目です。';
  //制御文字でないことと文字数をチェック
} else if (preg_match('/\A[[:^cntrl:]]{1,30}\z/u', $name) == 0) {
  $error['name'] = '*お名前は30文字以内でお願いします。';
}
if ($email == '') {
  $error['email'] = '*メールアドレスは必須です。';
} else { //メールアドレスを正規表現でチェック
  $pattern = '/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/uiD';
  if (!preg_match($pattern, $email)) {
    $error['email'] = '*メールアドレスの形式が正しくありません。';
  }
}
if ($email_check == '') {
  $error['email_check'] = '*確認用メールアドレスは必須です。';
} else { //メールアドレスを正規表現でチェック
  if ($email_check !== $email) {
    $error['email_check'] = '*メールアドレスが一致しません。';
  }
}
if ($title == '') {
  $error['title'] = '*タイトルは必須項目です。';
  //制御文字でないことと文字数をチェック
} else if (preg_match('/\A[[:^cntrl:]]{1,100}\z/u', $title) == 0) {
  $error['title'] = '*タイトルは100文字以内でお願いします。';
}
if ($contents == '') {
  $error['contents'] = '*内容は必須項目です。';
  //制御文字（タブ、復帰、改行を除く）でないことと文字数をチェック
} else if (preg_match('/\A[\r\n\t[:^cntrl:]]{1,1050}\z/u', $contents) == 0) {
  $error['contents'] = '*内容は1000文字以内でお願いします。';
}

//POSTされたデータとエラーの配列をセッション変数に保存
$_SESSION['name'] = $name;
$_SESSION['email'] = $email;
$_SESSION['email_check'] = $email_check;
$_SESSION['title'] = $title;
$_SESSION['contents'] = $contents;
$_SESSION['error'] = $error;
//チェックの結果にエラーがある場合は入力フォームに戻す
if (count($error) > 0) {
  //エラーがある場合
  $dirname = dirname($_SERVER['SCRIPT_NAME']);
  $dirname = $dirname == DIRECTORY_SEPARATOR ? '' : $dirname;
  //サーバー変数 $_SERVER['HTTPS'] が取得出来ない環境用（オプション）
  if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) and $_SERVER['HTTP_X_FORWARDED_PROTO'] === "https") {
    $_SERVER['HTTPS'] = 'on';
  }
  //入力画面（contact.php）の URL
  $url = (empty($_SERVER['HTTPS']) ? 'http://' : 'https://') . $_SERVER['SERVER_NAME'] . $dirname . '/contact.php';
  header('HTTP/1.1 303 See Other');
  header('location: ' . $url);
  exit;
} else {
  echo "aaa";
      $name = trim(filter_input(INPUT_POST, 'name'));
      $email = trim(filter_input(INPUT_POST, 'email'));
      $title = trim(filter_input(INPUT_POST, 'title'));
      $contents = trim(filter_input(INPUT_POST, 'contents'));

      $act->post($name, $email, $title, $contents);
      // var_dump($name);
      // var_dump($email);
      // var_dump($title);
      // var_dump($contents);

      // $stmt = $pdo->prepare("INSERT INTO contacts(name, email, title, contents) VALUES(:name, :email, :title, :contents)");
      // $stmt->bindValue("name", $name, PDO::PARAM_STR);
      // $stmt->bindValue("email", $email, PDO::PARAM_STR);
      // $stmt->bindValue("title", $title, PDO::PARAM_STR);
      // $stmt->bindValue("contents", $contents, PDO::PARAM_STR);
      // $stmt->execute();
      echo "DBに登録しました";
  echo"bb";
}