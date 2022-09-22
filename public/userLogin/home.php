<?php
session_start();

// ファイル読み込み
require_once '../../app/UserLogic.php';
require_once '../../app/QuestionLogic.php';
require_once '../../app/LevelLogic.php';
require_once '../../app/Functions.php';

// エラーメッセージ
$err = [];

// ログインしているか判定して、していなかったらログイン画面へ移す
$result = UserLogic::checkLogin();
if (!$result) {
    $_SESSION['login_err'] = '再度ログインして下さい';
    header('Location: ../userLogin/form.php');
    return;
}
$login_user = $_SESSION['login_user'];

// 上位３位のレベルを表示
$level = LevelLogic::levelTop3();
$paging = LevelLogic::levelRanking();
if (!$level) {
	$err[] = '表示するレベルがありません';
}

// 最新の質問を表示
$newQuestion = QuestionLogic::newQuestion();
if (!$newQuestion) {
	$err['question'] = '質問の読み込みに失敗しました';
}
?>


<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../css/top.css">
    <link rel="stylesheet" type="text/css" href="../css/mypage.css">
    <script src="https://kit.fontawesome.com/7bf203e5c7.js" crossorigin="anonymous"></script>
    <title>novus</title>
</head>

<body>
	<!--メニュー-->
    <header>
        <div class="navbar bg-dark text-white">
            <div class="navtext h2" id="headerlogo"><a href="<?php echo (($result) ? '../userLogin/home.php' : '../top/index.php'); ?>" style="color: white;">novus</a></div>
            <ul class="nav justify-content-center">
                <li class="nav-item">
					<form type="hidden" action="mypage.php" method="POST" name="mypage">
				        <a class="nav-link small text-white" href="../myPage/index.php">マイページ</a>
				        <input type="hidden">
                    </form>
                </li>
			<li id="li"><a class="nav-link active small text-white" href="../question/index.php">質問ページ</a></li>
            <li id="li"><a class="nav-link small text-white" href="../article/index.php">記事ページ</a></li>
            <li id="li"><a class="nav-link small text-white" href="../bookApi/index.php">ライブラリ</a></li>
            <li id="li"><a class="nav-link small text-white" href="../todo/index.php">TO DO LIST</a></li>
            <li id="li"><a class="nav-link small text-white" href="<?php echo "logout.php?=user_id=".$login_user['user_id']; ?>">ログアウト</a></li>
        </ul>
        </div>
    </header>

	<!--中央コンテンツ-->
	<div class="wrapper">
	    <div class="container">
	        <div class="text-center">
				<!--レベル上位３人を出す-->
                <div class="form-row text-center">
                    <div id="title">
		    		    <h2 class="heading" id="rankingtitle">レベルランキング TOP3</h2>
						<!--順位表示-->
                        <?php $i = 1; ?>
		    			<?php foreach ($level as $value): ?>
							<?php
                            switch ($i) {
                            case 1: ?>
                                <?php echo "<p id='first'>1位</p>";
                                break;
                            case 2:
                                echo "<p id='second'>2位</p>";
                                break;
                            case 3:
                                echo "<p id='third'>3位</p>";
                                break;
                            } ?>
                            <!--ユーザーが登録した画像を表示-->
                            <div class="level-icon"><br>
                                <?php if ($value['icon'] !== null && !empty($value['icon'])): ?> 
		    				    	<!--画像をクリックすると、自分のアイコンならmypage,他人ならuserpageに遷移-->
		    				    	<a name="icon" href="<?php if ($value['user_id'] === $_SESSION['login_user']['user_id']) {
		    				    		echo '../myPage/index.php'; } else {
                                        echo "../myPage/userPage.php?user_id=".$value['user_id'] ;} ?>">
                                    <img src="../top/img/<?php echo $value['icon']; ?>"></a>
                                <?php else: ?>
		    				    	<!--上記と同じ処理-->
		    				    	<!-- <form type="hidden" name="userpage" action="-->
		    				    	<a name="icon" href="<?php if ($value['user_id'] === $_SESSION['login_user']['user_id']) { 
		    				    		echo '../myPage/index.php'; } else {
							    		//user_idをユーザーページに引き継ぐ
		    				    		echo "../myPage/userPage.php?user_id=".$value['user_id'] ;} ?>">
		    				    		<?php echo "<img src="."../top/img/sample_icon.png".">"; ?></a>
		    				    	    <!-- <input id="imginput" type="submit" value=""></form> -->
                                <?php endif; ?>
                            </div>
						    <!--名前-->
                            <div class="text-center">
							    <!--名前をクリックすると、自分の名前ならmypage,他人ならuserpageに遷移-->
						        <a name="name" class="text-dark" href="<?php if ($value['user_id'] === $_SESSION['login_user']['user_id']) {
		    				    		echo '../myPage/index.php'; } else {
                                        echo "../myPage/userPage.php?user_id=".$value['user_id'] ;} ?>">
                                       <p><?php echo htmlspecialchars($value['name']) ?>さん</p></a>
							    <!--レベル-->
                                <p>Lv.<?php echo $value['level']; ?></p>
                            </div>
                            <?php $i++ ;?>
                        <?php endforeach; ?>
		        		<a class="small mb-5" href="../level/list.php">ランキング詳細<i class="fa-solid fa-arrow-right"></i></a><hr size="5">
		        	</div>
                </div>

			    <!-- 通常時、新着の質問を表示 -->
		        <?php if (isset($newQuestion)): ?>
		        	<div class="fw-bold mb-4 h5 pt-3">新着の質問</div>
		        	<?php foreach ($newQuestion as $value): ?>
						<!--題名-->
						<div style="overflow: hidden; overflow-wrap: break-word;"><a href="../question/qdisp.php? question_id=<?php echo $value['question_id']?>">
						    「<?php echo htmlspecialchars($value['title']) ?>」</a>
						</div>
					    <!--アイコン-->
					    <div class="level-icon">
                            <?php if ($value['icon'] !== null && !empty($value['icon'])): ?> 
		    					<!--画像をクリックすると、自分のアイコンならmypage,他人ならuserpageに遷移-->
		    					<a name="icon" href="<?php if ($value['user_id'] === $_SESSION['login_user']['user_id']) {
		    						echo '../myPage/index.php'; } else {
                                    echo "../myPage/userPage.php?user_id=".$value['user_id'] ;} ?>">
                                <img src="../top/img/<?php echo $value['icon']; ?>"></a>
                            <?php else: ?>
		    					<!--上記と同じ処理-->
		    					<a name="icon" href="<?php if ($value['user_id'] === $_SESSION['login_user']['user_id']) { 
		    						echo '../myPage/index.php'; } else {
									//user_idをユーザーページに引き継ぐ
		    						echo "../myPage/userPage.php?user_id=".$value['user_id'] ;} ?>">
		    						<?php echo "<img src="."../top/img/sample_icon.png".">"; ?></a>
                            <?php endif; ?>
                        </div>
						<!--ユーザー名-->
						<div class="pb-3 small">
							<!--名前をクリックすると、自分の名前ならmypage,他人ならuserpageに遷移-->
						    <a name="name" class="text-dark" href="<?php if ($value['user_id'] === $_SESSION['login_user']['user_id']) {
		    						echo '../myPage/index.php'; } else {
                                    echo "../myPage/userPage.php?user_id=".$value['user_id'] ;} ?>">
                                   <?php echo htmlspecialchars($value['name']) ?>さん</a></div>
		        		<!--本文：50文字以上だと省略-->
						<div style="overflow: hidden; overflow-wrap: break-word;">
							<?php if (mb_strlen($value['message']) > 50): ?>
								<?php $limit_content = mb_substr($value['message'],0,50); ?>
								<?php echo htmlspecialchars($limit_content); ?>…
							<?php else: ?>
								<?php echo htmlspecialchars($value['message']); ?>
							<?php endif; ?>
						</div>
						<!-- カテゴリと投稿日時を横並びにする処理 -->
						<div class="block">
							<!--カテゴリ-->
							<div style="color: black; display: inline-block;" class="artFootLeft badge rounded-pill border border-secondary ml-3">
							    <?php echo htmlspecialchars($value['category_name']); ?>
							</div>
							<!--投稿日時-->
							<div style="display: inline-block;" class="small pb-4">
								<!-- 更新されていた場合、その日付を優先表示 -->
								<?php if (!isset($value['upd_date'])): ?>
									投稿：<?php echo date('Y/m/d H:i', strtotime($value['post_date'])); ?>
								<?php else: ?>
									更新：<?php echo date('Y/m/d H:i', strtotime($value['upd_date'])); ?>
								<?php endif; ?>
							</div>
						</div>
		        	<?php endforeach; ?>
		        <?php endif; ?>
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
