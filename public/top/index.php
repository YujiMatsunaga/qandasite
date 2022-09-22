<?php
session_start();

// ファイルの読み込み
require_once '../../app/UserLogic.php';
require_once '../../app/CategoryLogic.php';
require_once '../../app/QuestionLogic.php';

// エラーメッセージ
$err = [];

// ログインしているか判定して、していたらログイン画面へ移す
$result = UserLogic::checkLogin();
if ($result) {
    header('Location: ../userLogin/home.php');
    return;
}

// カテゴリ処理
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
	<link rel="stylesheet" type="text/css" href="../css/question.css">
    <script src="https://kit.fontawesome.com/7bf203e5c7.js" crossorigin="anonymous"></script>
    <title>novus</title>
</head>

<body>
	<!--メニュー-->
    <header>
        <div class="navbar bg-dark text-white">
			<div class="navtext h2" id="headerlogo"><a href="<?php echo (($result) ? '../userLogin/home.php' : '../top/index.php'); ?>" style="color: white;">novus</a></div>
			<ul class="nav justify-content-center">
                <li id="li"><a class="nav-link small text-white" href="../question/index.php">質問ページ</a></li>
                <li id="li"><a class="nav-link small text-white" href="../article/index.php">記事ページ</a></li>
                <li id="li"><a class="nav-link small text-white" href="../bookApi/index.php">ライブラリ</a></li>
				<li><a href="../userLogin/form.php" class="nav-link small text-white" id="logo"><i class="fa-solid fa-arrow-right-to-bracket text-white" style="padding-right:10px;"></i></a></li>
			    <li><a href="../userRegister/form.php" class="nav-link small text-white" id="logo"><i class="fa-solid fa-user-plus text-white"></i></a></li>
            </ul>
        </div>
    </header>

	<!--中央コンテンツ-->
	<div class="wrapper">
	    <div class="container">
	        <div class="text-center">
			    <h4 class="mt-4">質問を検索</h4>
                <form method="GET">
                    <div class="form-row text-center">
                        <div id="keyword" class="form-group col-row">
                            <input name="keyword" type="text" class="form-control" id="question" placeholder="キーワード" 
							value="<?php echo isset($_GET['name']) ? htmlspecialchars($_GET['name']): '' ?>">
                        </div><br>
                        <div class="form-group col-row">
                            <label class="small">カテゴリー</label>
                            <select class="custom-select mr-sm-2" id="inlineFormCustomSelect" name="category">
			    		        <option></option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?php echo $category['cate_id']; ?>"
                                <?php if (isset($_GET['category']) && $category['cate_id'] == $_GET['category']): ?>
                                      selected
                                <?php endif; ?>>
			    			    <?php echo $category['category_name']; ?>
                                </option>
			    			    <?php endforeach; ?>
                            </select>
                        </div>
                    </div><br>
                    <button type="submit" class="btn btn-primary mt-3 mb-3" name="search">検索</button>
			    </form>

		        <div id="news" class="text-center">
		            <?php // 取得データを表示する ?>
	                <?php if (isset($searchQuestion) && count($searchQuestion)): ?>
		                <p class="alert alert-success"><?php echo count($searchQuestion) ?>件見つかりました。</p>
		        		<div class="fw-bold mt-2 mb-2 h5">検索結果</div>
	                    <!--質問表示-->
		                <?php foreach ($searchQuestion as $value): ?>
		        	        <!--題名-->
		        	        <div style="overflow: hidden; overflow-wrap: break-word;">
							    <a href="../question/qDisp.php? question_id=<?php echo $value['question_id']; ?>">「<?php echo htmlspecialchars($value['title']); ?>」</a>
							</div>
		        	        <!--アイコン-->
		        	        <div class="level-icon">
		        				<!--アイコンをクリックするとユーザーページへ-->
                                <?php if ($value['icon'] !== null && !empty($value['icon'])): ?> 
		        					<a name="icon" href="<?php 
		        			    	// user_idをユーザーページに引き継ぐ
		        			    	echo "userPage.php?user_id=".$value['user_id']; ?>">
                                    <img src="img/<?php echo $value['icon']; ?>"></a>
                                <?php else: ?>
		        			    	<a name="icon" href="<?php 
		        			    	// user_idをユーザーページに引き継ぐ
		        			    	echo "userPage.php?user_id=".$value['user_id']; ?>">
		        			    	<?php echo "<img src="."img/sample_icon.png".">"; ?></a>
                                <?php endif; ?>
                            </div>
		        		    <!--ユーザー名-->
		        		    <div class="pb-3 small">
								<!--名前をクリックすると、自分の名前ならmypage,他人ならuserpageに遷移-->
						        <a name="name" class="text-dark" href="<?php echo "userPage.php?user_id=".$value['user_id']; ?>">
                                <p><?php echo htmlspecialchars($value['name']) ?>さん</p></a>
							</div>
		        		    <!-- メッセージ：本文が50文字以上なら省略 -->
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
							<hr id="dot">
		            	<?php endforeach; ?>
		            <?php elseif (isset($searchQuestion) && count($searchQuestion) == 0): ?>
		            	<p class="alert alert-danger">検索対象は見つかりませんでした。</p>
		        	<?php endif; ?>
        
		            <!-- 通常時、新着の質問を表示 -->
		            <?php if (!isset($searchQuestion) && isset($newQuestion)): ?>
		        		<hr size="4"><div class="fw-bold mb-4 h5 pt-3">新着の質問</div>
		        	    <?php foreach ($newQuestion as $value): ?>
		        		    <!--題名-->
		        		    <div style="overflow: hidden; overflow-wrap: break-word;">
							    <a href="../question/qDisp.php? question_id=<?php echo $value['question_id']; ?>">「<?php echo htmlspecialchars($value['title']); ?>」</a>
							</div>
		        	        <!--アイコン-->
		        	        <div class="level-icon">
		        				<!--アイコンをクリックするとユーザーページへ-->
		        			    <?php if ($value['icon'] !== null && !empty($value['icon'])): ?> 
		        					<a name="icon" href="<?php 
		        			    	//user_idをユーザーページに引き継ぐ
		        			    	echo "userPage.php?user_id=".$value['user_id']; ?>">
                                    <img src="img/<?php echo $value['icon']; ?>"></a>
                                <?php else: ?>
		        		    		<a name="icon" href="<?php 
		        		    			//user_idをユーザーページに引き継ぐ
		        		    			echo "userPage.php?user_id=".$value['user_id']; ?>">
		        		    			<?php echo "<img src="."img/sample_icon.png".">"; ?></a>
                                <?php endif; ?>
                            </div>
		        		    <!--ユーザー名-->
		        		    <div class="pb-3 small">
								<!--名前をクリックすると、自分の名前ならmypage,他人ならuserpageに遷移-->
						        <a name="name" class="text-dark" href="<?php echo "userPage.php?user_id=".$value['user_id']; ?>">
                                <p><?php echo htmlspecialchars($value['name']) ?>さん</p></a>
							</div>
		        		    <!-- メッセージ：本文が50文字以上なら省略 -->
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
							<hr id="dot">
		        	    <?php endforeach; ?>
		            <?php endif; ?>
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
