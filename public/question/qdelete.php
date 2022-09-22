<?php
session_start();

// ファイルの読み込み
require_once '../../app/QuestionLogic.php';
require_once '../../app/CategoryLogic.php';
require_once '../../app/UserLogic.php';

// エラーメッセージ
$err = [];

// ログインチェック処理
$result = UserLogic::checkLogin();
if (!$result) {
    $_SESSION['login_err'] = '再度ログインして下さい';
    header('Location: ../userLogin/form.php');
    return;
}

// カテゴリ処理
$categories = CategoryLogic::getCategory();

// バリデーション
$question_id = filter_input(INPUT_POST, 'question_id', FILTER_SANITIZE_SPECIAL_CHARS);
if (!$question_id == filter_input(INPUT_POST, 'question_id', FILTER_SANITIZE_SPECIAL_CHARS)) {
    $err[] = '質問を選択し直してください';
}

// エラーがない場合質問読み込む
if (count($err) === 0) {
    // 質問を引っ張る処理
    $question = QuestionLogic::displayQuestion($_POST);
    if (!$question) {
        $err[] = '質問の読み込みに失敗しました';
    }
}

// 削除処理
if (isset($_POST['q_dlt'])) {
    $_SESSION['q_data']['question_id'] = filter_input(INPUT_POST, 'question_id', FILTER_SANITIZE_SPECIAL_CHARS);
    if (!$_SESSION['q_data']['question_id']) {
        $err['q_id'] = '質問IDが選択されていません';
    }
    $dlt = QuestionLogic::deleteQuestion($_SESSION['q_data']['question_id']);
    if (!$dlt) {
        $err[] = '質問の削除に失敗しました';
    }
    if (count($err) === 0) {
        header('Location: qDeleteComp.php');
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <script src="https://kit.fontawesome.com/7bf203e5c7.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" type="text/css" href="../css/mypage.css">
    <link rel="stylesheet" type="text/css" href="../css/top.css">
    <link rel="stylesheet" type="text/css" href="../css/question.css">
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
                <p class="h4 pb-3 mt-3">質問内容</p>
                <form method="POST" action="">
                    <!--題名-->
                    <div class="fw-bold pb-1">題名</div>
                    <div style="overflow: hidden; overflow-wrap: break-word;"><?php echo $question['title']; ?></div>
                    <!--カテゴリー-->
                    <div class="fw-bold pt-3 pb-1">カテゴリ</div>
                    <div><?php echo $question['category_name']; ?></div>
                    <!--本文-->
                    <div class="fw-bold pt-3 pb-1">本文</div>
                    <div style="overflow: hidden; overflow-wrap: break-word;"><?php echo nl2br(htmlspecialchars($question['message'], FILTER_SANITIZE_SPECIAL_CHARS, 'UTF-8')); ?></div>
                    <div>添付</div>
                    <input type="hidden" name="question_id" value="<?php echo $question['question_id']; ?>">
                    <i class="fa-solid fa-trash-can mt-3"><input class="fw-bold " type="submit" name="q_dlt" id="delete" value="削除"></i>
                </form>
                <button type="button" class="mb-4 mt-5 btn btn-outline-dark" onclick="history.back()">戻る</button>
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