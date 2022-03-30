<?php
  session_start();

  //ファイルの読み込み
  require_once '../../classes/QuestionLogic.php';
  require_once '../../classes/CategoryLogic.php';
  require_once '../../classes/UserLogic.php';
  
  //error
  $err = [];

  // ログインチェック処理
  $result = UserLogic::checkLogin();
  if(!$result) {
    $_SESSION['login_err'] = 'ユーザーを登録してログインして下さい';
    header('Location: ../../top/userLogin/login_top.php');
    return;
  }

  // 返答の削除処理
  $dlt = QuestionLogic::deleteOneAnswer($_SESSION['a_data']['answer_id']);
  if(empty($dlt)){
    $err[] = '返答の削除に失敗しました';
  }

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>質問を削除しました</title>
</head>
<body>
  <div>削除が成功しました</div>
  <button type="button" onclick="location.href='../../top/userLogin/login_top.php'">TOP</button>
  <button type="button" onclick="location.href='question_disp.php?question_id=<?php echo $_SESSION['a_data']['question_id']  ?>'">質問へ戻る</button>
</body>