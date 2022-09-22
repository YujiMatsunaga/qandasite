<?php
session_start();

// ファイルの読み込み
require_once '../../app/QuestionLogic.php';
require_once '../../app/UserLogic.php';

// エラーメッセージ
$err = [];

// ログインチェック
$result = UserLogic::checkLogin();
if (!$result) {
    $_SESSION['login_err'] = '再度ログインして下さい';
    header('Location: ../userLogin/home.php');
    return;
}

// データ受け渡しチェック
if (isset($_SESSION['q_data']['title']) &&
    isset($_SESSION['q_data']['category']) &&
    isset($_SESSION['q_data']['message']) &&
    isset($_SESSION['q_data']['question_id'])
) {
    //質問を登録する処理
    $question = QuestionLogic::editQuestion();
    if (!$question) {
        $err[] = '変更の保存に失敗しました';
    }
}

// 質問IDから質問内容を取り込む処理
$data = QuestionLogic::displayQuestion($_SESSION['q_data']);
if (!$data) {
    $err[] = '質問の取り込みに失敗しました';
}
$title = $data['title'];
$category = $data['category_name'];
$message = $data['message'];
$question_id = $_SESSION['q_data']['question_id'];
?>

<!DOCTYPE HTML>
<html lang="ja">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>novus</title>
<link rel="stylesheet" href="style.css">
<script src="https://kit.fontawesome.com/7bf203e5c7.js" crossorigin="anonymous"></script>
<link rel="stylesheet" type="text/css" href="../css/mypage.css">
<link rel="stylesheet" type="text/css" href="../css/top.css">
<link rel="stylesheet" type="text/css" href="../css/question.css">
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
                <p class="h4 pb-3 mt-3">編集完了</p>
                <p>以下の内容で保存しました</p>
                <!--題名-->
                <div class="fw-bold pb-1">題名</div>
                <div style="overflow: hidden; overflow-wrap: break-word;"><?php echo $title; ?></div>
                <!--カテゴリー-->
                <div class="fw-bold pt-3 pb-1">カテゴリ</div>
                <div><?php echo $category; ?></div>
                <!--本文-->
                <div class="fw-bold pt-3 pb-1">本文</div>
                <div style="overflow: hidden; overflow-wrap: break-word;"><?php echo nl2br(htmlspecialchars($message, FILTER_SANITIZE_SPECIAL_CHARS, 'UTF-8')); ?></div>
                <form method="GET" action="qdisp.php">
                    <input type="hidden" name= "question_id" value="<?php echo $question_id; ?>">
                    <input type="submit" value="質問へ">
                </form>
                <button type="button" onclick="location.href='../userLogin/home.php'">TOP</button>
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


