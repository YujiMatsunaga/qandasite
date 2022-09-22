<?php
session_start();

//ファイル読み込み
require_once '../../app/UserLogic.php';
require_once '../../app/LevelLogic.php';
require_once '../../app/Functions.php';

$data = $_GET;
$user_id = filter_input(INPUT_GET, 'user_id');

// レベル表示処理
$data = LevelLogic::displayUsers($_GET);
if (!$data) {
	$err['u_id'] = '表示する情報がありません';
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
    <link rel="stylesheet" href="../css/level_anime.css">
    <title>novus</title>
</head>

<body>
    <!--メニュー-->
    <header>
        <div class="navbar bg-dark text-white">
            <div class="navtext h2" id="headerlogo"><a style="color: white;" href="<?php ($result) ? '../userLogin/home.php' : '../top/index.php'; ?>">novus</a></div>
            <ul class="nav justify-content-center">
			    <li id="li"><a class="nav-link active small text-white" href="../top/index.php">TOPページ</a></li>
			    <li id="li"><a class="nav-link active small text-white" href="../question/index.php">質問ページ</a></li>
			    <li id="li"><a class="nav-link active small text-white" href="../article/index.php">記事ページ</a></li>      
            </ul>
		</div>
    </header>

    <!--中央コンテンツ-->
    <div class="wrapper">
        <div class="container">
            <div class="content">
                <h2 class="heading mt-5">USER ACCOUNT</h2>
                <?php if (!$user_id || !$data): ?>
                    <div class="alert alert-danger"><?php echo $err['u_id']; ?></div>
                <?php else: ?>
                    <div class="list">
                        <!--ユーザーが登録した画像を表示-->
                        <div class="list-item">
                            <?php if ($data['icon'] !== null && !empty($data['icon'])): ?> 
                                <img src="../top/img/<?php echo $data['icon']; ?>">
                            <?php else: ?>
                                <?php echo "<img src="."../top/img/sample_icon.png".">"; ?>
                            <?php endif; ?>
                        </div>
                        <br>
                        <!--ユーザーが登録した名前を表示-->
                        <div class="text">
                            <p style="display: inline-block;" class="fw-bold">名前　</p>
                            <p style="display: inline-block;">
                                <?php echo $data['name']; ?>さん
                            </p>
                        </div>
                        <!--ユーザーの現レベルを表示-->
                        <div class="text">
                            <p style="display: inline-block;" class="fw-bold">レベル　</p>
                            <p style="display: inline-block;">Lv.</p><?php
                                if (isset($data['level'])) {
                                    echo htmlspecialchars($data['level'], ENT_QUOTES, 'UTF-8'); 
                                } else {
                                    echo '1';
                                } ?>
                        </div>
                        <!--ユーザーのコメントを表示-->
                        <div class="text">
                            <p class="fw-bold">コメント</p>
                            <?php
                                if (isset($data['comment'])) {
                                   echo htmlspecialchars($data['comment'], ENT_QUOTES, 'UTF-8'); 
                                } else {
                                   echo 'Let us introduce yourself!';
                                } ?>
                        </div>
                    </div>
                <?php endif; ?>
                <p><a class="mb-2 btn btn-outline-dark mt-5" href="index.php" role="button">TOPに戻る</a></p>
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
