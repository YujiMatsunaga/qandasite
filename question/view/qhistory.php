<?php

session_start();
//ファイル読み込み
require_once '../../classes/UserLogic.php';
require_once '../../classes/QuestionLogic.php';
require_once '../../functions.php';

//エラー
$err = [];

//ログインしているか判定して、していなかったら新規登録画面へ移す
$result = UserLogic::checkLogin();
if (!$result) {
    $_SESSION['login_err'] = 'ユーザーを登録してログインして下さい';
    header('Location: userCreate/signup_form.php');
    return;
}
$login_user = $_SESSION['login_user'];
$question_id = filter_input(INPUT_GET, 'question_id');


//自身が投稿した質問を表示
$question = QuestionLogic::userQuestion();
if (isset($question['title']) || isset($question['category_name']) || isset($question['message']) || isset($question['upd_date']) || isset($question['post_date']) || isset($question['name'])) {
if (!$question) {
    $err[] = 'まだ投稿した質問はありません';
}
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../../css/mypage.css" />
    <link rel="stylesheet" type="text/css" href="../../css/top.css" />
    <title>過去の質問履歴</title>
</head>

<body>
    <!--メニュー-->
    <header>
        <div class="navtext-container">
            <div class="navtext">Q&A SITE</div>
        </div>
        <input type="checkbox" class="menu-btn" id="menu-btn">
        <label for="menu-btn" class="menu-icon"><span class="navicon"></span></label>
        <ul class="menu">
            <li class="top"><a href="../top/userLogin/login_top.php">TOPページ</a></li>
            <li><a href="../userEdit/edit_user.php">会員情報 編集</a></li>
            <li><a href="../../study-main/ARTICLE/ahistory.php">質問 履歴</a></li>
            <li><a href="#contact">記事 履歴</a></li>
            <li><a href="#contact">お問い合わせ</a></li>
            <li>
                <form action="logout.php" method="POST">
                    <input type="submit" name="logout" value="ログアウト">
                </form>
            </li>
        </ul>
    </header>

    <section class="wrapper">
        <div class="container">
            <div class="content">
                <h2 class="heading">HISTORY OF QUESTION</h2>
                <div class="list">
                    <!--ユーザーが投稿した質問を表示-->
                    <div class="text">
                        <div>質問履歴</div>
                        <div>題名：<?php echo $question['title'] ?></div>
                        <div>カテゴリ：<?php echo $question['category_name'] ?></div>
                        <div>本文：<?php echo $question['message'] ?></div>
                        <div>日付：
                          <?php if (!isset($question['upd_date'])): ?>
                            <?php echo $question['post_date']  ?>
                          <?php else: ?>
                            <?php echo $question['upd_date'] ?>
                          <?php endif; ?>
                        </div>
                        <div>名前：<?php echo $question['name'] ?></div>
                        <div>アイコン：
                          <?php if(!isset($question['icon'])): ?>
                            <?php echo $question['post_date']  ?>
                          <?php else: ?>
                            <?php echo $question['icon'] ?>
                          <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

	<!-- フッタ -->
    <footer>
        <div class="">
            <br><br><hr>
	        <p class="text-center">Copyright (c) HTMQ All Rights Reserved.</p>
        </div>
    </footer>
</body>
</html>
