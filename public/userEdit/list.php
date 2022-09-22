<?php
session_start();

//ファイル読み込み
require_once '../../app/UserLogic.php';
require_once '../../app/Functions.php';

//ログインしているか判定して、していなかったら新規登録画面へ移す
$result = UserLogic::checkLogin();
if (!$result) {
    $_SESSION['login_err'] = '再度ログインして下さい';
    header('Location: ../userCreate/signup_form.php');
    return;
}
$login_user = $_SESSION['login_user'];

$_SESSION['edit'] = $_POST;
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../css/mypage.css">
    <link rel="stylesheet" type="text/css" href="../css/top.css">
    <title>novus</title>
</head>

<body>
    <!--メニュー-->
    <header>
        <div class="navbar bg-dark text-white">
            <div class="navtext h2" id="headerlogo"><a href="<?php echo (($result) ? '../userLogin/home.php' : '../top/index.php'); ?>" style="color: white;">novus</a></div>
            <ul class="nav justify-content-center">
                <li id="li"><a class="nav-link active small text-white" href="../userLogin/home.php">TOPページ</a></li>
                <li id="li"><a class="nav-link active small text-white" href="../myPage/index.php">MyPageに戻る</a></li>
			    <li id="li"><a class="nav-link active small text-white" href="../userEdit/index.php">【編集】会員情報</a></li>
                <li id="li"><a class="nav-link small text-white" href="../myPage/qHistory.php">【履歴】質問</a></li>
                <li id="li"><a class="nav-link small text-white" href="../myPage/aHistory.php">【履歴】記事</a></li>
                <li id="li"><a class="nav-link small text-white" href="<?php echo "../userLogin/logout.php?=user_id=".$login_user['user_id']; ?>">ログアウト</a></li>
            </ul>
        </div>
    </header>

    <div class="wrapper">
        <div class="container">
            <div class="content">
                <h2 class="heading mt-5">アカウント編集画面</h2><br>
                    <div class="list">
                        <!--ユーザーが登録した名前を表示-->
                        <div class="text">
                            <label id="editdisp" for="name" style="padding-bottom:10px;">Name:&ensp;</label>
                            <input id="name" type="text" name="name" value="<?php echo htmlspecialchars($login_user['name'], ENT_QUOTES, 'UTF-8'); ?>" disabled>
                            <a class="edit" href="name.php" role="button" id="edit">&ensp;編集</a>
                        </div>
                        <!--ユーザーが登録した電話番号を表示-->
                        <div class="text">
                            <label id="editdisp"  for="tel" style="padding-bottom:10px;">Tel:&ensp;</label>
                            <input id="tel" type="text" name="tel" value="<?php echo htmlspecialchars($login_user['tel'], ENT_QUOTES, 'UTF-8'); ?>" disabled>
                            <a class="edit" href="tel.php" role="button" id="edit">&ensp;編集</a>
                        </div>
                        <!--ユーザーが登録したメールアドレスを表示-->
                        <div class="text">
                            <label id="editdisp"  for="email" style="padding-bottom:10px;">Email:&ensp;</label>
                            <input id="email" type="email" name="email" value="<?php 
                                if (isset($login_user['email'])) { 
                                    echo htmlspecialchars($login_user['email'], ENT_QUOTES,'UTF-8'); }?>" disabled>
                            <a class="edit" href="email.php" role="button" id="edit">&ensp;編集</a>
                        </div>   
                        <!--パスワード入力（非表示）--> 
                        <div class="text">
                            <label id="editdisp"  for="password" style="padding-bottom:10px;">Password:&ensp;</label>
                            <input id="password" type="text" name="password" value="＊＊＊＊＊＊" disabled>
                            <a class="edit" href="password.php" role="button" id="edit">&ensp;編集</a>
                            <p style="color:#dc3545; font-size:9px;">セキュリティ保護のため表示していません</p>
                        </div>
                        <!--アイコン用の画像を選択-->
                        <div class="text">
                            <label id="editdisp"  for="password" style="padding-bottom:10px;">Icon:&ensp;</label>
                            <input id="icon" type="text" name="icon" value="<?php 
                                if (isset($login_user['icon'])) { echo $login_user['icon']; }?>" disabled>
                            <a class="edit" href="icon.php" role="button" id="edit">&ensp;編集</a>
                        </div>
                        <!--コメント入力--> 
                        <div class="text">
                            <label id="editdisp"  for="comment" style="padding-bottom:10px;">comment:&ensp;</label>
                            <input id="comment" type="text" name="comment" value="<?php 
                                if (isset($login_user['comment'])) {
                                    echo htmlspecialchars($login_user['comment'], ENT_QUOTES,'UTF-8'); }?>" disabled>
                                <a class="edit" href="comment.php" role="button" id="edit">&ensp;編集</a>
                        </div>
                        <br><br>
                        <a class="edit" href="../userDelete/confirm.php" role="button">ユーザー削除</a>
                        <p><a class="mb-2 btn btn-outline-dark mt-5" href="../userLogin/mypage.php" role="button">戻る</a></p>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- フッタ -->
    <footer class="h-10"><hr>
		<div class="footer-item text-center">
			<h3>novus</h3>
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
