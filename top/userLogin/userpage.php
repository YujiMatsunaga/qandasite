<?php

session_start();
//ファイル読み込み
require_once '../../classes/UserLogic.php';
require_once '../../classes/LevelLogic.php';
require_once '../../functions.php';

//ログインしているか判定して、していなかったら新規登録画面へ移す
$result = UserLogic::checkLogin();
if (!$result) {
    $_SESSION['login_err'] = 'ユーザーを登録してログインして下さい';
    header('Location: userCreate/signup_form.php');
    return;
}
$login_user = $_SESSION['login_user'];

$data = $_GET;
$user_id = filter_input(INPUT_GET, 'user_id');

$data = LevelLogic::displayUsers($_GET);
if (!$data) {
	$err[] = '表示するレベルがありません';
}

//画像が入っていたら表示
$showicon = UserLogic::showIcon($_GET);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../../css/mypage.css" />
    <link rel="stylesheet" type="text/css" href="../../css/top.css" />
    <link rel="stylesheet" href="../../level/level_anime.css">
    <title>User Page</title>
</head>

<body>
    <!--メニュー-->
    <header>
        <div class="navtext-container">
            <div class="navtext">Q&A SITE</div>
        </div>
    </header>
    <!--コンテンツ-->
    <section class="wrapper">
        <div class="container">
            <div class="content">
                <h2 class="heading">MY ACCOUNT</h2>
                <div class="list">
                    <!--ユーザーが登録した画像を表示-->
                    <div class="list-item">
                        <?php if (isset($data['icon'])): ?> 
                            <img src="../img/<?php echo $data['icon']; ?>">
                        <?php else: ?>
                        <?php echo "<img src="."../img/sample_icon.png".">"; ?>
                        <?php endif; ?>
                    </div>
                    <!--ユーザーが登録した名前を表示-->
                    <div class="text">
                        名前：<?php echo $data['name']; ?>
                    </div>
                    <!--ユーザーの現レベルを表示-->
                    <div class="text">
                        Lv.<?php
                           if (isset($data['level'])) {
                               echo htmlspecialchars($data['level'], ENT_QUOTES, 'UTF-8'); 
                           } else {
                               echo '1';
                           } ?>
                    </div>
                    <div class="text">
                        コメント：<?php
                            if (isset($data['comment'])) {
                               echo htmlspecialchars($data['comment'], ENT_QUOTES, 'UTF-8'); 
                            } else {
                               echo 'Let us introduce yourself!';
                            } ?>
                    </div>
                    <a href="login_top.php" id="back">戻る</a>
                </div>
            </div>
        </div>
    </section>
	<!-- フッタ -->
    <footer>
        <hr>
	    <p class="text-center">Copyright (c) HTMQ All Rights Reserved.</p>
    </footer>
</body>
</html>
