<?php 
session_start();

// ファイルの読み込み
require_once '../../app/UserLogic.php';
require_once '../../app/CategoryLogic.php';
require_once '../../app/QuestionLogic.php';

// エラーメッセージ
$err = [];

// ログインしているか判定
$result = UserLogic::checkLogin();

//カテゴリ処理
$categories = CategoryLogic::getCategory();

// 検索ボタン押下時、条件に合った質問を表示
if (isset($_GET['search'])) {
	$keyword = filter_input(INPUT_GET, 'keyword', FILTER_SANITIZE_SPECIAL_CHARS);
	$category = filter_input(INPUT_GET, 'category', FILTER_SANITIZE_SPECIAL_CHARS);
    $searchQuestion = QuestionLogic::searchQuestion($keyword, $category);
    if (!$searchQuestion) {
        $err['question'] = '質問の読み込みに失敗しました';
	}
} else {  // 通常時は、新着の質問を表示する
	$newQuestion = QuestionLogic::newQuestion();
}
?>

<!DOCTYPE HTML>
<html lang="ja">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<title>novus</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://kit.fontawesome.com/7bf203e5c7.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" type="text/css" href="../css/mypage.css">
    <link rel="stylesheet" type="text/css" href="../css/top.css">
    <link rel="stylesheet" type="text/css" href="../css/question.css">
</head>
<body>
	<!--メニュー-->
    <header>
	    <?php if ($result): // ログインしていれば下記の表示 ?>
        <div class="navbar bg-dark text-white">
		<div class="navtext h2" id="headerlogo"><a href="<?php echo(($result) ? '../userLogin/home.php' : '../top/index.php'); ?>" style="color: white;">novus</a></div>
			<ul class="nav justify-content-center">
                <li class="nav-item"><form type="hidden" action="mypage.php" method="POST" name="mypage">
			    	    <a class="nav-link small text-white" href="../myPage/index.php">マイページ</a>
			    	    <input type="hidden">
                    </form>
                </li>
			    <li id="li"><a class="nav-link active small text-white" href="../userLogin/home.php">TOPページ</a></li>
                <li id="li"><a class="nav-link small text-white" href="../todo/index.php">TO DO LIST</a></li>
                <li id="li"><a class="nav-link small text-white" href="../../public/myPage/qHistory.php">【履歴】質問</a></li>
                <li id="li"><a class="nav-link small text-white" href="../../public/myPage/aHistory.php">【履歴】記事</a></li>
                <li id="li"><a class="nav-link small text-white" href="<?php echo "../userLogin/logout.php?=user_id=".$_SESSION['login_user']['user_id']; ?>">ログアウト</a></li>
            </ul>
		</div>
		<?php else: // 未ログインであれば下記の表示 ?>
        <div class="navbar bg-dark text-white">
		<div class="navtext h2" id="headerlogo"><a href="<?php echo (($result) ? '../userLogin/home.php' : '../top/index.php'); ?>" style="color: white;">novus</a></div>
            <ul class="nav justify-content-center">
			    <li id="li"><a class="nav-link active small text-white" href="../top/index.php">TOPページ</a></li>
			    <li id="li"><a class="nav-link active small text-white" href="../question/index.php">質問ページ</a></li>
			    <li id="li"><a class="nav-link active small text-white" href="../article/index.php">記事ページ</a></li>      
            </ul>
		</div>
		<?php endif; ?>
    </header>

	<!--コンテンツ-->
	<div class="wrapper">
        <div class="container">
            <div class="content">
                <h2 class="col-xs-6 col-xs-offset-3 pb-3 mt-4">質問サイトへようこそ</h2>
                <a href="qcreate.php" class="alert alert-dark">質問を投稿する</a>
                <div class="col-xs-6 col-xs-offset-3 well">
	                <!-- ②検索フォーム  -->
	                <form method="get">
	                	<div class="form-group">
	                		<input name="keyword" class="form-control mb-3" value="<?php echo isset($_GET['keyword']) ? htmlspecialchars($_GET['keyword']): '' ?>">
	                	</div>
	                	<div class="form-group">
							<label class="small">カテゴリー</label>
                            <select name="category">
                                <option></option>
                                <?php foreach ($categories as $category): ?>
                                    <option 
                                    value="<?php echo $category['cate_id']; ?>"
                                    <?php if (isset($_GET['category']) && $category['cate_id'] == $_GET['category']): ?>
                                        selected
                                    <?php endif; ?>> 
                                        <?php echo $category['category_name']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
	                	</div>
	                	<button type="submit" class="btn btn-primary mt-4" name="search">検索</button>
	                </form>
                </div>

	            <div class="col-xs-6 col-xs-offset-3">
        		    <!-- 検索ボタン押下時、取得データを表示する -->
        		    <?php if (isset($searchQuestion) && count($searchQuestion)): ?>
        			    <p class="alert alert-success"><?php echo count($searchQuestion); ?>件見つかりました。</p>
        			    <!-- 検索結果の表示 -->
						<div class="fw-bold mt-2 mb-2 h5">検索結果</div>
						<?php foreach ($searchQuestion as $value): ?>
							<!--題名-->
							<div style="overflow: hidden; overflow-wrap: break-word;"><a href="qDisp.php? question_id=<?php echo $value['question_id']; ?>">「<?php echo htmlspecialchars($value['title']); ?>」</a></div>
							<!--アイコン-->
							<?php if ($result): // ログイン可否で違うユーザーページへ ?>
								<?php if ($value['icon'] !== null && !empty($value['icon'])): ?>
									<a name="icon" href="<?php if ($result && $value['user_id'] === $_SESSION['login_user']['user_id']) {
									echo '../myPage/index.php'; } else {
									echo "../myPage/userPage.php?user_id=".$value['user_id'] ;} ?>">
									<img src="../top/img/<?php echo $value['icon']; ?>">
									</a>
								<?php else: ?>
									<a name="icon" href="<?php if ($result && $value['user_id'] === $_SESSION['login_user']['user_id']) {
									echo '../myPage/index.php'; } else {
									echo "../myPage/userPage.php?user_id=".$value['user_id'] ;} ?>">
									<img src="../top/img/sample_icon.png">
									</a>
								<?php endif; ?>
							<?php else: ?>
								<?php if ($value['icon'] !== null && !empty($value['icon'])): ?> 
								<img src="../top/img/<?php echo $value['icon']; ?>"></a>
							<?php else: ?>
							<!--アイコンをクリックするとユーザーページへ-->
							<a name="icon" href="<?php 
								//user_idをユーザーページに引き継ぐ
								echo "../top/userPage.php?user_id=".$value['user_id']; ?>">
								<?php echo "<img src="."../top/img/sample_icon.png".">"; ?></a>
								<?php endif; ?>
							<?php endif; ?>
							<!--ユーザー名-->
							<!--名前をクリックするとユーザーページへ-->
							<?php if ($result): // ログイン可否で違うユーザーページへ ?>
								<a name="name" class="text-dark" href="<?php if ($value['user_id'] === $_SESSION['login_user']['user_id']) {
								echo '../myPage/index.php'; } else {
								echo "../myPage/userPage.php?user_id=".$value['user_id'] ;} ?>">
								<p><?php echo htmlspecialchars($value['name']) ?>さん</p></a>
							<?php else: ?>
								<a name="name" class="text-dark" href="<?php echo "../top/userPage.php?user_id=".$value['user_id']; ?>">
								<p><?php echo htmlspecialchars($value['name']) ?>さん</p></a>
							<?php endif; ?>
							<div style="overflow: hidden; overflow-wrap: break-word;">
								<!-- メッセージ：本文が50文字以上なら省略 -->
								<?php if(mb_strlen($value['message']) > 50): ?>
									<?php $limit_content = mb_substr($value['message'],0,50); ?>
									<?php echo htmlspecialchars($limit_content); ?>…
								<?php else: ?>
									<?php echo htmlspecialchars($value['message']); ?>
								<?php endif; ?>
							</div>
							<!-- カテゴリと投稿日時を横並びにする処理 -->
							<div class="block">
								<!--カテゴリ-->
								<div style="color: black; display: inline-block;" class="artFootLeft badge rounded-pill border border-secondary ml-3"><?php echo htmlspecialchars($value['category_name']); ?></div>
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
						<hr>
						<?php endforeach; ?>
						
                    <!--検索結果が見つからなかった時-->
		            <?php elseif (isset($searchQuestion) && count($searchQuestion) == 0): ?>
			        	<p class="alert alert-danger">検索対象は見つかりませんでした。</p>
		    
			            <!-- 通常時、新着の質問を表示 -->
		                <?php elseif (isset($newQuestion)): ?>
		                	<hr size="5"><div class="fw-bold mt-2 mb-2 h5">新着の質問</div>
		                	<?php foreach ($newQuestion as $value): ?>
								<!--題名-->
								<div style="overflow: hidden; overflow-wrap: break-word;"><a href="qDisp.php? question_id=<?php echo $value['question_id']; ?>" style="overflow: hidden; overflow-wrap: break-word;">「<?php echo htmlspecialchars($value['title']); ?>」</a></div>
								<!--アイコン-->
								<?php if ($result): // ログイン可否で違うユーザーページへ ?>
								    <?php if ($value['icon'] !== null && !empty($value['icon'])): ?>
								    	<a name="icon" href="<?php if ($result && $value['user_id'] === $_SESSION['login_user']['user_id']) {
		    					    	echo '../myPage/index.php'; } else {
                                        echo "../myPage/userPage.php?user_id=".$value['user_id'] ;} ?>">
								    	<img src="../top/img/<?php echo $value['icon']; ?>">
								    	</a>
								    <?php else: ?>
								    	<a name="icon" href="<?php if ($result && $value['user_id'] === $_SESSION['login_user']['user_id']) {
								    	echo '../myPage/index.php'; } else {
								    	echo "../myPage/userPage.php?user_id=".$value['user_id'] ;} ?>">
								    	<img src="../top/img/sample_icon.png">
								    	</a>
								    <?php endif; ?>
								<?php else: ?>
									<?php if ($value['icon'] !== null && !empty($value['icon'])): ?> 
                                    	<img src="../top/img/<?php echo $value['icon']; ?>"></a>
									<?php else: ?>
										<!--アイコンをクリックするとユーザーページへ-->
										<a name="icon" href="<?php 
											//user_idをユーザーページに引き継ぐ
											echo "../top/userPage.php?user_id=".$value['user_id']; ?>">
											<?php echo "<img src="."../top/img/sample_icon.png".">"; ?></a>
                                    <?php endif; ?>
								<?php endif; ?>
								<!--ユーザー名-->
								<!--名前をクリックするとユーザーページへ-->
								<?php if ($result): // ログイン可否で違うユーザーページへ ?>
									<a name="name" class="text-dark" href="<?php if ($value['user_id'] === $_SESSION['login_user']['user_id']) {
		    						echo '../myPage/index.php'; } else {
                                    echo "../myPage/userPage.php?user_id=".$value['user_id'] ;} ?>">
                                   <p><?php echo htmlspecialchars($value['name']) ?>さん</p></a>
					 <!-- 通常時、新着の質問を表示 -->
		                <?php elseif (isset($newQuestion)): ?>
		                	<hr size="5"><div class="fw-bold mt-2 mb-2 h5">新着の質問</div>
		                	<?php foreach ($newQuestion as $value): ?>
								<!--題名-->
								<div style="overflow: hidden; overflow-wrap: break-word;"><a href="qDisp.php? question_id=<?php echo $value['question_id']; ?>" style="overflow: hidden; overflow-wrap: break-word;">「<?php echo htmlspecialchars($value['title']); ?>」</a></div>
								<!--アイコン-->
								<?php if ($result): // ログイン可否で違うユーザーページへ ?>
								    <?php if ($value['icon'] !== null && !empty($value['icon'])): ?>
								    	<a name="icon" href="<?php if ($result && $value['user_id'] === $_SESSION['login_user']['user_id']) {
		    					    	echo '../myPage/index.php'; } else {
                                        echo "../myPage/userPage.php?user_id=".$value['user_id'] ;} ?>">
								    	<img src="../top/img/<?php echo $value['icon']; ?>">
								    	</a>
								    <?php else: ?>
								    	<a name="icon" href="<?php if ($result && $value['user_id'] === $_SESSION['login_user']['user_id']) {
								    	echo '../myPage/index.php'; } else {
								    	echo "../myPage/userPage.php?user_id=".$value['user_id'] ;} ?>">
								    	<img src="../top/img/sample_icon.png">
								    	</a>
								    <?php endif; ?>
								<?php else: ?>
									<?php if ($value['icon'] !== null && !empty($value['icon'])): ?> 
                                    	<img src="../top/img/<?php echo $value['icon']; ?>"></a>
              <?php else: ?>
                  <!--アイコンをクリックするとユーザーページへ-->
                  <a name="icon" href="<?php 
                    //user_idをユーザーページに引き継ぐ
                    echo "../top/userPage.php?user_id=".$value['user_id']; ?>">
                    <?php echo "<img src="."../top/img/sample_icon.png".">"; ?></a>
                                  <?php endif; ?>

							<?php endif; ?>
							<!--ユーザー名-->
							<!--名前をクリックするとユーザーページへ-->
							<?php if ($result): // ログイン可否で違うユーザーページへ ?>
								<a name="name" class="text-dark" href="<?php if ($value['user_id'] === $_SESSION['login_user']['user_id']) {
								echo '../myPage/index.php'; } else {
								echo "../myPage/userPage.php?user_id=".$value['user_id'] ;} ?>">
								<p><?php echo htmlspecialchars($value['name']) ?>さん</p></a>
							<?php else: ?>
								<a name="name" class="text-dark" href="<?php echo "../top/userPage.php?user_id=".$value['user_id']; ?>">
								<p><?php echo htmlspecialchars($value['name']) ?>さん</p></a>
							<?php endif; ?>
                <!-- メッセージ：本文が50文字以上なら省略 -->
								<div style="overflow: hidden; overflow-wrap: break-word;">
									<?php if (mb_strlen($value['message']) > 50): ?>
										<?php $limit_content = mb_substr($value['message'],0,50); ?>
										<?php echo $limit_content; ?>…
								<?php else: ?>
									<?php echo htmlspecialchars($value['message']); ?>
								<?php endif; ?>
							</div>
							<!-- カテゴリと投稿日時を横並びにする処理 -->
							<div class="block">
								<!--カテゴリ-->
								<div style="color: black; display: inline-block;" class="artFootLeft badge rounded-pill border border-secondary ml-3"><?php echo htmlspecialchars($value['category_name']); ?></div>
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
						<hr>
						<?php endforeach; ?>
		            <?php endif; ?>
	            </div>
				<?php if ($result): // ログインの有無でリンクの変化 ?>
	            	<button type="button" class="mb-4 mt-5 btn btn-outline-dark" onclick="location.href='../userLogin/home.php'">TOP</button>
				<?php else: ?>
					<button type="button" class="mb-4 mt-5 btn btn-outline-dark" onclick="location.href='../top/index.php'">TOP</button>
				<?php endif; ?>
			</div>
		</div>
	</div>

	<!-- フッタ -->
	<footer class="h-10"><hr>
		<div class="footer-item text-center">
		    <h4>novus</h4>
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
