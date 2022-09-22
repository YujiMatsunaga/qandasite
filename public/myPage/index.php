<?php
session_start();

//ファイル読み込み
require_once '../../app/UserLogic.php';

//ログインしているか判定して、していなかったらログイン画面へ移す
$result = UserLogic::checkLogin();
if (!$result) {
    $_SESSION['login_err'] = '再度ログインして下さい';
    header('Location: ../userLogin/form.php');
    return;
}
$login_user = $_SESSION['login_user'];

//画像情報の取得
$showicon = UserLogic::showIcon();
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../css/mypage.css">
    <link rel="stylesheet" type="text/css" href="../css/top.css">
    <link rel="stylesheet" href="../../css/level_anime.css">
    <title>novus</title>
</head>


<body>
	<!--メニュー-->
    <header>
        <div class="navbar bg-dark text-white">
            <div class="navtext h2" id="headerlogo"><a href="<?php echo(($result) ? '../userLogin/home.php' : '../top/index.php'); ?>" style="color: white;">novus</a></div>
            <ul class="nav justify-content-center">
			<li id="li"><a class="nav-link active small text-white" href="../userLogin/home.php">TOPページ</a></li>
			<li id="li"><a class="nav-link active small text-white" href="../userEdit/index.php">【編集】会員情報</a></li>
            <li id="li"><a class="nav-link small text-white" href="qHistory.php">【履歴】質問</a></li>
            <li id="li"><a class="nav-link small text-white" href="aHistory.php">【履歴】記事</a></li>
            <li id="li"><a class="nav-link small text-white" href="../todo/index.php">TO DO LIST</a></li>
            <li id="li"><a class="nav-link small text-white" href="<?php echo "../userLogin/logout.php?=user_id=".$login_user['user_id']; ?>">ログアウト</a></li>
        </ul>
        </div>
    </header>
    
    <!--前回のレベルと変化があった際にのみレベルモーダルを表示させる-->
    <?php if ($_SESSION['login_user']['level'] !== $_SESSION['login_user']['pre_level']): ?>
        <!--モーダル-->
        <div id="modal-content">
            <p style="text-align:center;"><?php require_once '../level/animation.php'; ?></p>
	        <p><a id="modal-close" class="button-link" onclick="modal_onclick_close()" >CLOSE</a></p>
        </div>
        <!-- 2番目に表示されるモーダル（半透明な膜） -->
        <div id="modal-overlay"></div>
        <!-- JavaScript -->
        <script type="text/javascript">
            function modal_onclick_close()
            {
            document.getElementById("modal-content").style.display = "none";
            document.getElementById("modal-overlay").style.display = "none";
            }
        </script>
    <?php endif; ?>

    <!--中央コンテンツ-->
    <div class="wrapper">
        <div class="container">
            <div class="content">
                <h2 class="heading mt-5">MY ACCOUNT</h2>
                <div class="list">
                    <!--ユーザーが登録した画像を表示-->
                    <div class="list-item">
                        <?php if ($login_user['icon'] !== null && !empty($login_user['icon'])): ?> 
                            <img src="../top/img/<?php echo $login_user['icon']; ?>">
                        <?php else: ?>
                            <?php echo "<img src="."../top/img/sample_icon.png".">"; ?>
                        <?php endif; ?>
                    </div>
                    <br>
                    <!--ユーザーが登録した名前を表示-->
                    <div class="text">
                        <p style="display: inline-block;" class="fw-bold">名前　</p>
                        <p style="display: inline-block;">
                            <?php echo htmlspecialchars($login_user['name'], ENT_QUOTES, 'UTF-8'); ?>
                        </p>
                    </div>
                    <!--ユーザーの現レベルを表示-->
                    <div class="text">
                        <p style="display: inline-block;" class="fw-bold">レベル　</p>
                        <p style="display: inline-block;">Lv.</p><?php
                            if (isset($login_user['level'])) {
                                echo htmlspecialchars($login_user['level'], ENT_QUOTES, 'UTF-8'); 
                            } else {
                                echo '1';
                            } ?>
                    </div>
                    <!-- ユーザーの現経験値と、次のレベルまでの経験値を表示 -->
                    <div class="text">
                        <p style="display: inline-block;" class="fw-bold">EXP　</p>
                        <p style="display: inline-block;"><?php 
                            if (isset($login_user['exp'])) {
                                echo htmlspecialchars($login_user['exp'], ENT_QUOTES, 'UTF-8'); 
                            } else {
                                echo '0';
                            }
                        ?>
                        </p>
                        <!-- 次のレベルまでの経験値表示 -->
                        <p class="small">次のレベルまで、<?php 
                            if (isset($login_user['exp'])) {
                                $current_exp = htmlspecialchars($login_user['exp'], ENT_QUOTES, 'UTF-8') % 100; 
                                echo 100 - $current_exp;
                            } else {
                                echo '100';
                            }?>EXP です。</p>
                    </div>
                    <div class="text">
                        <p class="fw-bold">コメント</p>
                        <p class="text-break small"><?php
                            if (isset($login_user['comment'])) {
                               echo htmlspecialchars($login_user['comment'], ENT_QUOTES, 'UTF-8'); 
                            } else {
                               echo 'Let us introduce yourself!';
                            } ?></p>
                    </div>
                </div>
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
