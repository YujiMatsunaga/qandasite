<?php

  session_start();

  //ファイルの読み込み
  require_once '../../classes/QuestionLogic.php';
  require_once '../../classes/UserLogic.php';

  // ログインチェック
  $result = UserLogic::checkLogin();
  if(!$result) {
    $_SESSION['login_err'] = 'ユーザーを登録してログインして下さい';
    header('Location: ../../top/userLogin/login_top.php');
    return;
  }

  //error
  $err = [];

  // データ受け渡しチェック
  if (isset($_SESSION['q_data']['title']) &&
    isset($_SESSION['q_data']['category']) &&
    isset($_SESSION['q_data']['message']) &&
    isset($_SESSION['q_data']['question_id'])
    ){
      //質問を登録する処理
      $question = QuestionLogic::editQuestion();
        if(!$question){
          $err[] = '変更の保存に失敗しました';
        }
    }
  // 質問IDから質問内容を取り込む処理
  $data = QuestionLogic::displayQuestion($_SESSION['q_data']);
    if(!$data){
      $err[] = '変更の保存に失敗しました';
    }
  $title = $data['title'];
  $category = $data['category_name'];
  $message = $data['message'];
  $question_id = $_SESSION['q_data']['question_id'];

?>


<div>以下の内容で保存しました</div>
<div>題名：<?php echo $title ?></div>
<div>カテゴリー：<?php echo $category ?></div>
<div>本文：<?php echo $message ?></div>

<form method="GET" action="question_disp.php">
  <input type="hidden" name= "question_id" value="<?php echo $question_id ?>">
  <input type="submit" value="質問へ">
</form>
<button type="button" onclick="">TOP</button>


