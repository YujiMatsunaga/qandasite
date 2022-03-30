<?php

session_start();

//ファイル読み込み
require_once '../../classes/UserLogic.php';
require_once '../../functions.php';

//ログインしているか判定して、していなかったら新規登録画面へ移す
$result = UserLogic::checkLogin();
if (!$result) {
    $_SESSION['login_err'] = 'ユーザーを登録してログインして下さい';
    header('Location: ../userCreate/signup_form.php');
    return;
}
$login_user = $_SESSION['login_user'];

$_SESSION['edit'] = $_POST;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../../css/mypage.css" />
    <link rel="stylesheet" type="text/css" href="../../css/top.css" />
    <title>会員情報一覧</title>
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
            <li class="top"><a href="../userLogin/login_top.php">TOPページ</a></li>
            <li><a href="../userLogin/mypage.php">MyPageに戻る</a></li>
            <li><a href="#projects">質問 履歴</a></li>
            <li><a href="#contact">記事 履歴</a></li>
            <li><a href="#contact">お問い合わせ</a></li>
            <li>
                <form action="../userLogin/logout.php" method="POST">
                    <input type="submit" name="logout" value="ログアウト">
                </form>
            </li>
        </ul>
    </header>

    <section class="wrapper">
        <div class="container">
            <div class="content">
                <h2 class="heading">アカウント編集画面</h2><br>
                    <div class="list">
                        <!--ユーザーが登録した名前を表示-->
                        <div class="text">
                            <label id="editdisp" for="name" style="padding-bottom:10px;">Name:&ensp;</label>
                            <input id="name" type="text" name="name" value="<?php echo htmlspecialchars($login_user['name'], ENT_QUOTES, 'UTF-8'); ?>" disabled>
                            <a class="edit" href="nameEdit.php" role="button" id="edit">&ensp;編集</a>
                        </div>
                        <!--ユーザーが登録した電話番号を表示-->
                        <div class="text">
                            <label id="editdisp"  for="tel" style="padding-bottom:10px;">Tel:&ensp;</label>
                            <input id="tel" type="text" name="tel" value="<?php echo htmlspecialchars($login_user['tel'], ENT_QUOTES, 'UTF-8'); ?>" disabled>
                            <a class="edit" href="telEdit.php" role="button" id="edit">&ensp;編集</a>
                        </div>
                        <!--ユーザーが登録したメールアドレスを表示-->
                        <div class="text">
                            <label id="editdisp"  for="email" style="padding-bottom:10px;">Email:&ensp;</label>
                            <input id="email" type="email" name="email" value="<?php 
                                if(isset($login_user['email'])) {echo htmlspecialchars($login_user['email'], ENT_QUOTES,'UTF-8');
                                } else { echo ''; }?>" disabled>
                            <a class="edit" href="emailEdit.php" role="button" id="edit">&ensp;編集</a>
                        </div>   
                        <!--パスワード入力（非表示）--> 
                        <div class="text">
                            <label id="editdisp"  for="password" style="padding-bottom:10px;">Password:&ensp;</label>
                            <input id="password" type="text" name="password" value="＊＊＊＊＊＊" disabled>
                            <a class="edit" href="passwordEdit.php" role="button" id="edit">&ensp;編集</a>
                            <p style="color:#dc3545; font-size:9px;">セキュリティ保護のため表示していません</p>
                        </div>
                        <!--アイコン用の画像を選択-->
                        <div class="text">
                            <label id="editdisp"  for="password" style="padding-bottom:10px;">Icon:&ensp;</label>
                            <input id="icon" type="text" name="icon" value="<?php 
                                if(isset($login_user['icon'])) {echo $login_user['icon'];
                                } else { echo ''; }?>" disabled>
                            <a class="edit" href="iconEdit.php" role="button" id="edit">&ensp;編集</a>
                        </div>
                        <!--コメント入力--> 
                        <div class="text">
                            <label id="editdisp"  for="comment" style="padding-bottom:10px;">comment:&ensp;</label>
                            <input id="comment" type="text" name="comment" value="<?php 
                                if(isset($login_user['comment'])) {echo htmlspecialchars($login_user['comment'], ENT_QUOTES,'UTF-8');
                                } else { echo '&emsp;Introduce Yourself!'; }?>" disabled>
                                <a class="edit" href="commentEdit.php" role="button" id="edit">&ensp;編集</a>
                        </div>
                        <br><br>
                        <a class="edit" href="../userDelete/userDelete.php" role="button">ユーザー削除</a>
                    </div>
                </form>
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
