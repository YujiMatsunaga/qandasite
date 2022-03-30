<?php
  session_start();

  //ファイルの読み込み
  require_once '../../classes/QuestionLogic.php';

  //error
  $err = [];

  $a_message = filter_input(INPUT_POST, 'a_message', FILTER_SANITIZE_SPECIAL_CHARS);
  $user_id = filter_input(INPUT_POST, 'user_id', FILTER_SANITIZE_SPECIAL_CHARS);
  $question_id = filter_input(INPUT_POST, 'question_id', FILTER_SANITIZE_SPECIAL_CHARS);

  if(!$a_message) {
    $err[] = '本文を入力してください';
  }
  if(!$user_id) {
    $err[] = 'ユーザーを選択し直してください';
  }
  if(!$question_id) {
    $err['question_id'] = '質問を選択し直してください';
  }

  // 投稿ボタン押下時の内部処理（成功でページ移動）
  if(isset($_POST['a_comp'])){
    $_SESSION['a_data']['message'] = filter_input(INPUT_POST, 'a_message', FILTER_SANITIZE_SPECIAL_CHARS);
    $_SESSION['a_data']['user_id'] = filter_input(INPUT_POST, 'user_id', FILTER_SANITIZE_SPECIAL_CHARS);
    $_SESSION['a_data']['question_id'] = filter_input(INPUT_POST, 'question_id', FILTER_SANITIZE_SPECIAL_CHARS);

    if(empty($_SESSION['a_data']['message'])) {
      $err['q_id'] = '本文が入力されていません';
    }
    if(empty($_SESSION['a_data']['user_id'])) {
      $err['q_id'] = 'ユーザーが選択されていません';
    }
    if(empty($_SESSION['a_data']['question_id'])) {
      $err['q_id'] = '質問IDが選択されていません';
    }

      if (count($err) === 0){
        header('Location: answer_create_comp.php');
      }
    }


?>


<div>投稿内容の確認</div>
<div>以下の内容でよろしいですか？</div>
<div>内容：<div><?php echo $a_message ?></div></div>

<form method="POST" action="">
  <input type="hidden" name="a_message" value="<?php echo $a_message; ?>">
  <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
  <input type="hidden" name="question_id" value="<?php echo $question_id ?>">
  <input type="submit" name="a_comp"value="投稿">
</form>
<button type="button" onclick="location.href='../../top/userLogin/login_top.php'">TOP</button>

<button type="button" onclick="history.back()">戻る</button>
