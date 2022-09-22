<?php

session_start();
//ファイル読み込み
require_once '../../app/UserLogic.php';
require_once '../../app/LevelLogic.php';
require_once '../../app/Functions.php';
//エラーメッセージ
$err = [];

//ログインしているか判定して、していなかったら新規登録画面へ移す
$result = UserLogic::checkLogin();
if(!$result) {
    $_SESSION['login_err'] = '再ログインして下さい';
    header('Location: ../userLogin/form.php');
    return;
}
$login_user = $_SESSION['login_user'];

//レベル情報の取得
$data = LevelLogic::getLevel();
$paging = LevelLogic::levelRanking();
if(!$data || !$paging) {
    $err[] = 'レベルの取り込みに失敗しました';
}
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
            <div class="navtext h2" id="headerlogo"><a href="<?php echo(($result) ? '../userLogin/home.php' : '../top/index.php'); ?>" style="color: white;">novus</a></div>
            <ul class="nav justify-content-center">
			<li id="li"><a class="nav-link active small text-white" href="../userLogin/home.php">TOPページ</a></li>
			<li id="li"><a class="nav-link active small text-white" href="../userEdit/index.php">【編集】会員情報</a></li>
            <li id="li"><a class="nav-link small text-white" href="../myPage/qHistory.php">【履歴】質問</a></li>
            <li id="li"><a class="nav-link small text-white" href="../myPage/aHistory.php">【履歴】記事</a></li>
            <li id="li"><a class="nav-link small text-white" href="../todo/index.php">TO DO LIST</a></li>
            <li id="li"><a class="nav-link small text-white" href="<?php echo "../userLogin/logout.php?=user_id=".$login_user['user_id']; ?>">ログアウト</a></li>
        </ul>
        </div>
    </header>

    <!--コンテンツ-->
    <div class="wrapper">
        <div class="container">
            <div class="content">
                <h2 class="heading mt-5">レベルランキング</h2>
                <div class="level-list">
                    <!--順位表示-->
                    <?php $i = 1; 
                        if(isset($_GET['page'])) {
                        $i += ($_GET['page'] - 1) * 10; } ?>
                    <?php foreach($data as $value): ?>
                        <?php
                        switch($i) {
                            case 1:
                                echo "<p id='first'>1位</p>";
                                break;
                            case 2:
                                echo "<p id='second'>2位</p>";
                                break;
                            case 3:
                                echo "<p id='third'>3位</p>";
                                break;
                            default:
                               echo "<p id='rank'>".$i."位"."</p>";
                        } ?>
                        <!--ユーザー登録画像の表示-->
                        <div class="level-icon">
                        <!--画像をクリック、自分ならmypageに遷移-->
                        <?php if($value['icon'] !== null && !empty($value['icon'])): ?> 
							<a name="icon" href="<?php if($value['user_id'] === $_SESSION['login_user']['user_id']) {
								echo '../myPage/index.php'; } else {
                                echo "../myPage/userPage.php?user_id=".$value['user_id'] ;} ?>">
                            <img src="../top/img/<?php echo $value['icon']; ?>"></a>
                        <!--画像をクリック、他人ならuserpageに遷移-->
                        <?php else: ?>
							<a name="icon" href="<?php if($value['user_id'] === $_SESSION['login_user']['user_id']) { 
								echo '../myPage/index.php'; } else {
								echo "../myPage/userPage.php?user_id=".$value['user_id'] ;} ?>">
								<?php echo "<img src="."../top/img/sample_icon.png".">"; ?></a>
                        <?php endif; ?>
                        </div>
                        <div class="text-center">
                            <!--名前の表示-->
                            <!--名前をクリックすると、自分の名前ならmypage,他人ならuserpageに遷移-->
					    	<a name="name" class="text-dark" href="<?php if($value['user_id'] === $_SESSION['login_user']['user_id']) {
		    		    			echo '../myPage/index.php'; } else {
                                    echo "../myPage/userPage.php?user_id=".$value['user_id'] ;} ?>">
                                   <p><?php echo htmlspecialchars($value['name']) ?>さん</p></a>
                            <!--レベルの表示-->
                            Lv.<?php echo $value['level']; ?>
                        </div><br>
                        <?php $i++; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

	<!-- フッタ -->
    <footer> 
        <!--ページネーション-->
        <ul class="pagination">
            <li class="page">
                <?php for($x=1; $x <= $paging ; $x++) { ?>
	            <a href="?page=<?php echo $x ?>"><?php echo $x; ?></a>
                <?php } // forの終わり ?>
            </li>
        </ul>
		<div class="footer-item text-center"><hr>
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

