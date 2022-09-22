<?php
session_start();

//ファイルの読み込み
require_once '../../app/QuestionLogic.php';
require_once '../../app/UserLogic.php';

//エラーメッセージ
$err = [];

// ログインチェック
$result = UserLogic::checkLogin();
if(!$result) {
    $_SESSION['login_err'] = '再度ログインして下さい';
    header('Location: ../userLogin/home.php');
    return;
}

$a_message = filter_input(INPUT_POST, 'a_message');
$a_user_id = filter_input(INPUT_POST, 'a_user_id', FILTER_SANITIZE_NUMBER_INT);
$q_user_id = filter_input(INPUT_POST, 'q_user_id', FILTER_SANITIZE_NUMBER_INT);
$question_id = filter_input(INPUT_POST, 'question_id', FILTER_SANITIZE_NUMBER_INT);

//バリデーション
if (!$a_message) {
    $err[] = '本文を入力してください';
}
if (!$a_user_id) {
    $err[] = 'ユーザーを選択し直してください';
}
if (!$q_user_id) {
    $err[] = '質問を選択し直してください';
}
if (!$question_id) {
    $err['question_id'] = '質問を選択し直してください';
}
if (!empty($a_message)) {
    $limitMessage = 1500;
    // 文字数チェック
    if (mb_strlen($a_message) > $limitMessage) {
    $err['message'] = '1500文字以内で入力してください';
    }
}

// 投稿ボタン押下時の内部処理（成功でページ移動）
if (isset($_POST['a_comp'])) {
    $_SESSION['a_data']['message'] = filter_input(INPUT_POST, 'a_message');
    $_SESSION['a_data']['a_user_id'] = filter_input(INPUT_POST, 'a_user_id', FILTER_SANITIZE_NUMBER_INT);
    $_SESSION['a_data']['q_user_id'] = filter_input(INPUT_POST, 'q_user_id', FILTER_SANITIZE_NUMBER_INT);
    $_SESSION['a_data']['question_id'] = filter_input(INPUT_POST, 'question_id', FILTER_SANITIZE_NUMBER_INT);
    if (empty($_SESSION['a_data']['message'])) {
        $err['message'] = '本文が入力されていません';
    }
    if (empty($_SESSION['a_data']['a_user_id'])) {
        $err['q_id'] = 'ユーザーが選択されていません';
    }
    if (empty($_SESSION['a_data']['question_id'])) {
        $err['q_id'] = '質問IDが選択されていません';
    }
    if (count($err) === 0) {
        header('Location: aCreateComp.php');
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="../css/mypage.css">
    <link rel="stylesheet" type="text/css" href="../css/question.css">
    <link rel="stylesheet" type="text/css" href="../css/top.css">
    <title>novus</title>
</head>

<body>
    <!--メニュー-->
    <header>
    <div class="navbar bg-dark text-white">
        <div class="navtext h2" id="headerlogo"><a href="<?php echo (($result) ? '../userLogin/home.php' : '../top/index.php'); ?>" style="color: white;">novus</a></div>
			<ul class="nav justify-content-center">
                <li class="nav-item"><form type="hidden" action="mypage.php" method="POST" name="mypage">
			    	    <a class="nav-link small text-white" href="../myPage/index.php">マイページ</a>
			    	    <input type="hidden">
                    </form>
                </li>
			    <li id="li"><a class="nav-link active small text-white" href="../userLogin/home.php">TOPページ</a></li>
                <li id="li"><a class="nav-link small text-white" href="../todo/index.php">TO DO LIST</a></li>
                <li id="li"><a class="nav-link small text-white" href="<?php echo "../userLogin/logout.php?=user_id=".$_SESSION['login_user']['user_id']; ?>">ログアウト</a></li>
            </ul>
		</div>
    </header>

    <!--コンテンツ-->
    <div class="wrapper">
        <div class="container">
            <div class="content">
                <p class="h4">返信の内容確認</p>
                <?php if (empty($err)): ?>
                    <p>以下の内容でよろしいですか？</p>
                <?php else: ?>
                    <?php foreach ($err as $value): ?>
                        <p class="text-danger"><?php  echo $value; ?></p>
                    <?php endforeach; ?>
                <?php endif; ?>
                <!--回答内容の確認-->
                <div class="fw-bold pb-1">内容</div>
                <form method="POST" action="">
                    <textarea name="a_message" rows="5" class="w-100"><?php echo $a_message; ?></textarea>
                    <input type="hidden" name="a_user_id" value="<?php echo $a_user_id; ?>">
                    <input type="hidden" name="q_user_id" value="<?php echo $q_user_id; ?>">
                    <input type="hidden" name="question_id" value="<?php echo $question_id; ?>">
                    <input type="submit" name="a_comp" value="投稿">
                </form>
                <button type="button" class="btn btn-outline-dark fw-bold mb-5" onclick="location.href='../userLogin/home.php'">TOP</button>
                <button type="button" class="btn btn-outline-dark fw-bold mb-5" onclick="history.back()">戻る</button>
            </div>
        </div>
    </div>

    <!-- フッタ -->
    <footer class="h-10"><hr>
        <div class="footer-item text-center">
                <h4>novus</h4>
                <ul class="nav nav-pills nav-fill">
                    <li class="nav-item">
                        <a class="nav-link small" href="../article/index.php">記事</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link small" href="../question/index.php">質問</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link small" href="../bookApi/index.php">本検索</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link small" href="../contact/index.php">お問い合わせ</a>
                    </li>
                </ul>
        </div>
        <p class="text-center small mt-2">Copyright (c) HTMQ All Rights Reserved.</p>
    </footer>
</body>
</html>
