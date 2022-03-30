<?php
  session_start();

  //ファイルの読み込み
  require_once '../../classes/QuestionLogic.php';
  require_once '../../classes/UserLogic.php';

  //error
  $err = [];


  if(isset($_SESSION['a_data']['message']) &&
    isset($_SESSION['a_data']['user_id']) &&
    isset($_SESSION['a_data']['question_id'])
  ){
    //返答を登録する処理
    $hasCreated = QuestionLogic::createAnswer();
    
    if(!$hasCreated){
      $err['answer'] = '返信の読み込みに失敗しました';
    }elseif($hasCreated){
      // 経験値を加算する処理
      $plusEXP = UserLogic::plusEXP($_SESSION['login_user']['user_id'], 10);
    }
    if(!$plusEXP){
      $err['plusEXP'] = '経験値加算処理に失敗しました';
    }
  }
?>


<div>返答の投稿が完了しました</div>
<form method="GET" action="question_disp.php">
  <input type="hidden" name="question_id" value="<?php echo $_SESSION['a_data']['question_id'] ?>">
  <input type="submit" name="q_disp"value="質問へ">
</form>
<button type="button" onclick="location.href='../../top/userLogin/login_top.php'">TOP</button>
