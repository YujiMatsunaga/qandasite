<?php

session_start();
//ファイル読み込み
require_once '../../classes/UserLogic.php';
require_once '../../classes/LevelLogic.php';
require_once '../../functions.php';
//エラーメッセージ
$err = [];

//ログインしているか判定して、していなかったら新規登録画面へ移す
$result = UserLogic::checkLogin();
if (!$result) {
    $_SESSION['login_err'] = 'ユーザーを登録してログインして下さい';
    header('Location: ../userCreate/signup_form.php');
    return;
}
$login_user = $_SESSION['login_user'];

//レベル情報の取得
$data = LevelLogic::getLevel();
$paging = LevelLogic::levelRanking();
if (!$data || !$paging) {
    $err[] = 'レベルの取り込みに失敗しました';
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
    <title>レベルランキング詳細</title>
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
            <li><a href="../userEdit/edit_user.php">会員情報 編集</a></li>
            <li><a href="../../question/qhistory.php">質問 履歴</a></li>
            <li><a href="../../">記事 履歴</a></li>
            <li><a href="#contact">お問い合わせ</a></li>
            <li>
                <form type="hidden" action="logout.php" method="POST">
				    <input type="submit" name="logout" value="ログアウト" id="logout" style="text-align:left;">
                </form>
            </li>
        </ul>
    </header>
    <!--コンテンツ-->
    <section class="wrapper">
        <div class="container">
            <div class="content">
                <h2 class="heading">レベルランキング</h2>
                <div class="level-list">
                    <?php foreach($data as $value): ?>
                    <!--ユーザー登録画像の表示-->
                    <div class="level-icon"><br>
                        <?php if (isset($value['icon'])): ?> 
                        <!--画像をクリックすると、自分のアイコンならmypage,他人ならuserpageに遷移-->
							<a name="icon" href="<?php if ($value['user_id'] === $_SESSION['login_user']['user_id']) {
								echo '../userLogin/mypage.php'; } else {
                                echo "../userLogin/userpage.php?user_id=".$value['user_id'] ;} ?>">
                            <img src="../img/<?php echo $value['icon']; ?>"></a>
                        <?php else: ?>
							<!--上記と同じ処理-->
							<a name="icon" href="<?php if ($value['user_id'] === $_SESSION['login_user']['user_id']) { 
								echo '../userLogin/mypage.php'; } else {
								echo "../userLogin/userpage.php?user_id=".$value['user_id'] ;} ?>">
								<?php echo "<img src="."../img/sample_icon.png".">"; ?></a>
                        <?php endif; ?>
                    </div>
                    <div class="text">
                        <!--名前の表示-->
                        <?php echo $value['name']; ?>
                        <!--レベルの表示-->
                        Lv.<?php echo $value['level']; ?>
                    </div>
                    <?php endforeach ?>
                </div>
            </div>
        </div>
    </section>
	<!-- フッタ -->
    <footer> 
        <ul class="pagination">
            <li class="page">
                <?php for ($x=1; $x <= $paging ; $x++) { ?>
	            <a href="?page=<?php echo $x ?>"><?php echo $x; ?></a>
                <?php } // End of for ?>
            </li>
        </ul>
        <hr><p>Copyright (c) HTMQ All Rights Reserved.</p>
    </footer>
</body>
</html>

