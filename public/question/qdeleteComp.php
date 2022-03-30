<?php
  session_start();

  //ファイルの読み込み
  require_once '../../app/QuestionLogic.php';
  require_once '../../app/CategoryLogic.php';
  require_once '../../app/UserLogic.php';

  // ログインチェック
  $result = UserLogic::checkLogin();
  if(!$result) {
    $_SESSION['login_err'] = '再度ログインして下さい';
    header('Location: ../../userLogin/form.php');
    return;
  }
  
  //error
  $err = [];

  $question_id = filter_input(INPUT_POST, 'question_id');
  if(!$question_id == filter_input(INPUT_POST, 'question_id', FILTER_SANITIZE_SPECIAL_CHARS)) {
    $err[] = '質問を選択し直してください';
  }
?>
<!DOCTYPE HTML>
<html lang="ja">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>質問削除</title>
<link rel="stylesheet" href="style.css">
<script src="https://kit.fontawesome.com/7bf203e5c7.js" crossorigin="anonymous"></script>
<link rel="stylesheet" type="text/css" href="../CSS/mypage.css" />
<link rel="stylesheet" type="text/css" href="../CSS/top.css" />
<link rel="stylesheet" type="text/css" href="../CSS/question.css" />
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
            <li class="top"><a href="login_top.php">TOP Page</a></li>
            <li><a href="../userEdit/list.php">My Page</a></li>
            <li><a href="#">TO DO LIST</a></li>
            <li><a href="../../qHistory.php">質問 履歴</a></li>
            <li><a href="../../">記事 履歴</a></li>
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
                <p class="h4 pb-3 mt-3">削除完了</p>
                <p>削除が成功しました</p>
                <button type="button" onclick="location.href='../../userLogin/home.php'">TOP</button>
                <button type="button" onclick="location.href='top.php'">質問TOPへ</button>
            </div>
        </div>
    </section>

    <!-- フッタ -->
    <footer class="h-10"><hr>
		    <div class="footer-item text-center">
		    	  <h4>Q&A SITE</h4>
		    	  <ul class="nav nav-pills nav-fill">
                <li class="nav-item">
		    			  <a class="nav-link small" href="#">記事</a>
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
