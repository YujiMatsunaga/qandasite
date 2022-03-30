<?php
    session_start();

    //ファイルの読み込み
    require_once '../../classes/QuestionLogic.php';

    //error
    $err = [];

    // データの受け渡しチェック
    if (isset($_SESSION['a_data']['answer_id']) &&
        isset($_SESSION['a_data']['message'])
      ){        
      //返答を編集する処理
      $hasEditted = QuestionLogic::editAnswer();
      if(!$hasEditted){
        $err[] = '更新に失敗しました';
      }      
      //返答を取得する処理
      $hasTaken = QuestionLogic::displayOneAnswer($_SESSION['a_data']['answer_id']);
      if(!$hasTaken){
        $err[] = '返答の取り込みに失敗しました';
      }
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>返答編集完了</title>
</head>
<body>
  <div>編集完了</div>
  <div>以下の内容で編集が完了しました</div>
    <div>本文：<?php echo $hasTaken['message'] ?></div>

    <form method="GET" name="form1" action="question_disp.php">
      <input type="hidden" name="question_id" value="<?php echo $hasTaken['question_id']; ?>">
      <a href="javascript:form1.submit()">詳細画面へ</a>
    </form>
    <button type="button" onclick="location.href='../../top/userLogin/login_top.php'">TOP</button>
</body>
</html>