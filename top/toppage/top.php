<?php

session_start();

//ファイルの読み込み
require_once '../../classes/CategoryLogic.php';
require_once '../../classes/QuestionLogic.php';

//エラーメッセージ
$err = [];

$categories = CategoryLogic::getCategory();

  //質問を引っ張る処理
if(isset($_GET['search'])){
  $searchQuestion = QuestionLogic::searchQuestion($_GET);
  if(!$searchQuestion){
    $err['question'] = '質問の読み込みに失敗しました';
  }else{
		$newQuestion = QuestionLogic::newQuestion();
	}
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../../css/top.css" />
    <script src="https://kit.fontawesome.com/7bf203e5c7.js" crossorigin="anonymous"></script>
    <title>トップ画面</title>
</head>


<body class="p-3">
	<!-- ヘッダ -->
	<header>
	  <nav class="navbar navbar-expand-lg" style="background-color:rgba(55, 55, 55, 0.98);">
		<div class="container-fluid">
			<a class="avbar-brand font-weight-bold h3 text-white" href="top.php">Q&A SITE</a>
			<span class="navbar-text">
			<a href="../userLogin/login_form.php"><i class="fa-solid fa-arrow-right-to-bracket text-white" style="padding-right:10px;"></i></a>
			<a href="../userCreate/signup_form.php"><i class="fa-solid fa-user-plus text-white"></i></a>
			</span>
		</div>

		<ul class="nav">
			<li class="nav-item">
				<a class="nav-link active small text-white" href="#">質問コーナー</a>
			</li>
			<li class="nav-item">
				<a class="nav-link small text-white" href="../../public/article/index.php">記事コーナー</a>
			</li>
			<li class="nav-item">
				<a class="nav-link small text-white" href="#">本ライブラリ</a>
			</li>
		</ul>
	</header>
<!-- コンテンツ（中央カラム） -->
	<div id="content" class="text-center mt-2"  style="background-color:rgba(236, 235, 235, 0.945);">
		<div class="text-center pt-5">
			<h5>質問を検索</h5>
              <form method="GET">
                <div class="form-row text-center">
                  <div id="keyword" class="form-group col-row">
                    <div>
                      <input name="keyword" type="text" class="form-control" id="question" placeholder="キーワード" value="<?php echo isset($_GET['name']) ? htmlspecialchars($_GET['name']) : '' ?>">
                    </div>
                  </div>
                  <br>
                  <div class="form-group col-row">
                    <label class="small">カテゴリー</label>
                    <select class="custom-select mr-sm-2" id="inlineFormCustomSelect" name="category">
					    <option></option>
                        <?php foreach($categories as $category){ ?>
                            <option value="<?php echo $category['cate_id'] ?>"
                        <?php if(isset($_GET['category']) && $category['cate_id'] == $_GET['category']) : ?>
                              selected
                        <?php endif; ?>>
						<?php echo $category['category_name'] ?>
                        </option>
						<?php } ?>
                    </select>
                  </div>
                </div>
                <br>
                <button type="submit" class="btn btn-primary" name="search">検索</button><hr>
			</form>
        </div>

		<div id="news" class="text-center">
		    <?php //③取得データを表示する ?>
	        <?php if(isset($searchQuestion) && count($searchQuestion)): ?>
		    <p class="alert alert-success"><?php echo count($searchQuestion) ?>件見つかりました。</p>
	    
		    <?php foreach($searchQuestion as $value): ?>
		    	<div><a href="question_disp.php? question_id=<?php echo $value['question_id']?>">題名：<?php echo htmlspecialchars($value['title']) ?></a></div>
		    	<div>カテゴリ：<?php echo htmlspecialchars($value['category_name']) ?></div>
		    	<div>本文：<?php echo htmlspecialchars($value['message']) ?></div>
		    	<div>名前：<?php echo htmlspecialchars($value['name']) ?></div>
		    	<div><?php echo htmlspecialchars($value['icon']) ?></div>
		    	<div>日時：<?php echo htmlspecialchars($value['post_date']) ?></div>
		    	<?php endforeach; ?>
		    <?php elseif (isset($searchQuestion) && count($searchQuestion) == 0): ?>
		    	<p class="alert alert-danger">検索対象は見つかりませんでした。</p>
		    <?php elseif(isset($newQuestion)): ?>
		    	<?php foreach($newQuestion as $value): ?>
		    		<div><a href="question_disp.php? question_id=<?php echo $value['question_id']?>">題名：<?php echo htmlspecialchars($value['title']) ?></a></div>
		    		<div>カテゴリ：<?php echo htmlspecialchars($value['category_name']) ?></div>
		    		<div>本文：<?php echo htmlspecialchars($value['message']) ?></div>
		    		<div>名前：<?php echo htmlspecialchars($value['name']) ?></div>
		    		<div><?php echo htmlspecialchars($value['icon']) ?></div>
		    	<div>日時：<?php echo htmlspecialchars($value['post_date']) ?></div>
		    	<?php endforeach; ?>
		    <?php endif; ?>
		</div>
	</div>
	
	<!-- フッタ -->
	<footer class="h-10">
		<div class="footer-item text-center">
			<h4>Q&A SITE</h4>
			<ul class="nav nav-pills nav-fill">
                <li class="nav-item">
					<a class="nav-link small" href="../../public/article/index.php">記事</a>
				</li>
				<li class="nav-item">
					<a class="nav-link small" href="#">質問</a>
				</li>
				<li class="nav-item">
					<a class="nav-link small" href="#">本検索</a>
				</li>
				<li class="nav-item">
					<a class="nav-link small" href="#">お問い合わせ</a>
				</li>
			</ul>
		</div>
		<p class="text-center small mt-2">Copyright (c) HTMQ All Rights Reserved.</p>
	</footer>
</body>
</html>
