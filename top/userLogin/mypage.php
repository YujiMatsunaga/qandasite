<?php
session_start();
//ファイル読み込み
require_once '../../classes/UserLogic.php';

//ログインしているか判定して、していなかったら新規登録画面へ移す
$result = UserLogic::checkLogin();
if (!$result) {
    $_SESSION['login_err'] = 'ユーザーを登録してログインして下さい';
    header('Location: ../userLogin/login_form.php');
    return;
}
$login_user = $_SESSION['login_user'];

if (isset($_POST['mypage'])) {
//モーダル表示の呼び込み
$user_data = UserLogic::levelModal();
var_dump($user_data);
if (!$user_data) {
  $err[] = 'レベル表示に失敗しました';
  return;
}
}

//画像情報の取得
$showicon = UserLogic::showIcon();
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
    <title>My Page</title>
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
            <li class="top"><a href="login_top.php">TOPページ</a></li>
            <li><a href="../userEdit/edit_user.php">会員情報 編集</a></li>
            <li><a href="../../question/view/qhistory.php">質問 履歴</a></li>
            <li><a href="../../">記事 履歴</a></li>
            <li><a href="#contact">お問い合わせ</a></li>
            <li>
                <form type="hidden" action="logout.php" method="POST">
				    <input type="submit" name="logout" value="ログアウト" id="logout" style="text-align:left;">
                </form>
            </li>
        </ul>
    </header>
    
    <!--モーダル-->
    <div id="modal-content">
	    <p style="text-align:canter;"><?php require_once 'level_anime.php'; ?></p>
	    <p><a id="modal-close" class="button-link" onclick="modal_onclick_close()" >CLOSE</a></p>
    </div>
    <!-- 2番目に表示されるモーダル（オーバーウエィ）半透明な膜 -->
    <div id="modal-overlay" ></div>
    <!-- JavaScript -->
    <script type="text/javascript">
        function modal_onclick_close(){
        document.getElementById("modal-content").style.display = "none";
        document.getElementById("modal-overlay").style.display = "none";
        }
    </script>

    <section class="wrapper">
        <div class="container">
            <div class="content">
                <h2 class="heading">MY ACCOUNT</h2>
                <div class="list">
                    <!--ユーザーが登録した画像を表示-->
                    <div class="list-item">
                        <?php if (isset($login_user['icon'])): ?> 
                            <img src="../img/<?php echo $login_user['icon']; ?>">
                        <?php else: ?>
                        <?php echo "<img src="."../img/sample_icon.png".">"; ?>
                        <?php endif; ?>
                    </div>
                    <!--ユーザーが登録した名前を表示-->
                    <div class="text">
                        名前：<?php echo htmlspecialchars($login_user['name'], ENT_QUOTES, 'UTF-8'); ?>
                    </div>
                    <!--ユーザーの現レベルを表示-->
                    <div class="text">
                        Lv.<?php
                           if (isset($login_user['level'])) {
                               echo htmlspecialchars($login_user['level'], ENT_QUOTES, 'UTF-8'); 
                           } else {
                               echo '1';
                           } ?>
                    </div>
                    <div class="text">
                        コメント：<?php
                            if (isset($login_user['comment'])) {
                               echo htmlspecialchars($login_user['comment'], ENT_QUOTES, 'UTF-8'); 
                            } else {
                               echo 'Let us introduce yourself!';
                            } ?>
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
