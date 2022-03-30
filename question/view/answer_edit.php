<?php
  session_start();

  //ファイルの読み込み
  require_once '../../classes/QuestionLogic.php';
  require_once '../../classes/UserLogic.php';

  $result = UserLogic::checkLogin();
  if(!$result) {
    $_SESSION['login_err'] = 'ユーザーを登録してログインして下さい';
    header('Location: ../../top/userLogin/login_top.php');
    return;
  }


  //error
  $err = [];

  // ボタン押下時の処理（成功でページ移動）
  if(isset($_POST['a_edit_conf'])){
    $_SESSION['a_data']['message'] = filter_input(INPUT_POST, 'a_message', FILTER_SANITIZE_SPECIAL_CHARS);
    $_SESSION['a_data']['answer_id'] = filter_input(INPUT_POST, 'answer_id', FILTER_SANITIZE_SPECIAL_CHARS);

    // エラーチェック
    if(empty($_SESSION['a_data']['message'])) {
        $err['message'] = '本文を入力してください';
    }
    if(empty($_SESSION['a_data']['answer_id'])) {
        $err['a_id'] = '返答が選択されていません';
    }
    if (count($err) === 0){
        header('Location: answer_edit_comp.php');
    }
  }else{
    // 非ボタン押下時（通常時）の処理
    $answer_id = filter_input(INPUT_POST, 'answer_id');
    if(empty($answer_id)) {
      $err[] = '質問を選択し直してください';
    }
    if (count($err) === 0){
      //質問を引っ張る処理
      $answer = QuestionLogic::displayOneAnswer($answer_id);
      if(!$answer){
          $err[] = '返答の読み込みに失敗しました';
      }
    }
  }

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>質問編集</title>
</head>

<body>
  <div>質問内容</div>
  <form method="POST" action="">
  <div>
    <?php if(isset($err['a_id'])): ?>
    <?php echo $err['a_id'] ?>
    <?php endif; ?>
  </div>
    <div><?php if(isset($err['message'])): ?>
      <?php echo $err['message'] ?>
      <?php endif; ?>
    </div>
    <div>本文：
      <textarea name="a_message"><?php echo $answer['message'] ?></textarea>
    </div>
    <input type="hidden" name="answer_id" value="<?php echo $answer_id ?>">
    <input type="submit" name="a_edit_conf">
  </form>
  <button type="button" onclick="history.back()">戻る</button>
</body>