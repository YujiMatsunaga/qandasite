<?php
  session_start();

  //ファイルの読み込み
  require_once '../../classes/QuestionLogic.php';
  require_once '../../classes/CategoryLogic.php';
  require_once '../../classes/UserLogic.php';

  $result = UserLogic::checkLogin();
  if(!$result) {
    $_SESSION['login_err'] = 'ユーザーを登録してログインして下さい';
    header('Location: ../../top/userLogin/login_top.php');
    return;
  }
  $categories = CategoryLogic::getCategory();

  //error
  $err = [];

  $question_id = filter_input(INPUT_POST, 'question_id');
  if(!$question_id == filter_input(INPUT_POST, 'question_id')) {
    $err[] = '質問を選択し直してください';
  }

  if (count($err) === 0){
    //質問を引っ張る処理
    $question = QuestionLogic::displayQuestion($_POST);
    if(!$question){
        $err[] = '質問の読み込みに失敗しました';
    }
  }
  
  if(isset($_POST['q_dlt'])){
    $_SESSION['q_data']['question_id'] = filter_input(INPUT_POST, 'question_id', FILTER_SANITIZE_SPECIAL_CHARS);
    if(!$_SESSION['q_data']['question_id']) {
      $err['q_id'] = '質問IDが選択されていません';
    }
    
    $dlt = QuestionLogic::deleteQuestion($_SESSION['q_data']['question_id']);
    if(!$dlt){
        $err[] = '質問の削除に失敗しました';
    }

    if (count($err) === 0){
        header('Location: question_delete_comp.php');
    }
}

var_dump($err);

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>質問削除</title>
</head>

<body>
  <div>質問内容</div>
  <form method="POST" action="">
  <div>題名：<div><?php echo $question['title'] ?></div>
    <div>カテゴリ：<div><?php echo $question['category_name'] ?></div></div>
    <div>本文：<div><?php echo $question['message'] ?></div></div>
    <div>添付</div>
    <input type="hidden" name="question_id" value="<?php echo $question['question_id'] ?>">
    <input type="submit" name="q_dlt"value="削除">
  </form>
  <button type="button" onclick="history.back()">戻る</button>
</body>