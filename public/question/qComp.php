<?php
session_start();

//ファイルの読み込み
require_once '../../app/QuestionLogic.php';
require_once '../../app/UserLogic.php';

// エラーメッセージ
$err = [];

// ログインチェック
$result = UserLogic::checkLogin();
if (!$result) {
    $_SESSION['login_err'] = '再度ログインして下さい';
    header('Location: ../userLogin/home.php');
    return;
}

// データ受け渡しチェック
if (isset($_SESSION['q_data']['user_id']) &&
    isset($_SESSION['q_data']['title']) &&
    isset($_SESSION['q_data']['category']) &&
    isset($_SESSION['q_data']['message'])
) {
    // 質問を登録する処理
    $hasCreated = QuestionLogic::createQuestion();
    if (!$hasCreated) {
        $err[] = '登録に失敗しました';
    } elseif($hasCreated) {
        // 経験値を加算する処理
        $plusEXP = UserLogic::plusEXP($_SESSION['login_user']['user_id'], 10);
    }
    if (!$plusEXP) {
        $err['plusEXP'] = '経験値加算処理に失敗しました';
    }
}

// 最新の質問を取得する処理
$hasTaken = QuestionLogic::newQuestion();
if (!$hasTaken) {
    $err[] = '質問の取り込みに失敗しました';
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="../css/mypage.css">
    <link rel="stylesheet" type="text/css" href="../css/top.css">
    <title>novus</title>

</head>
<body>
    <!--メニュー-->
    <header>
    <div class="navbar bg-dark text-white">
        <div class="navtext h2" id="headerlogo"><a href="<?php echo (($result) ? '../userLogin/home.php' : '../top/index.php'); ?>" style="color: white;">novus</a></div>
			<ul class="nav justify-content-center">
                <li class="nav-item"><form type="hidden" action="mypage.php" method="POST" name="mypage">
			    	    <a class="nav-link small text-white" href="../myPage/index.php">マイページ</a>
			    	    <input type="hidden">
                    </form>
                </li>
			    <li id="li"><a class="nav-link active small text-white" href="../userLogin/home.php">TOPページ</a></li>
                <li id="li"><a class="nav-link small text-white" href="../todo/index.php">TO DO LIST</a></li>
                <li id="li"><a class="nav-link small text-white" href="<?php echo "../userLogin/logout.php?=user_id=".$_SESSION['login_user']['user_id']; ?>">ログアウト</a></li>
            </ul>
		</div>
    </header>

    <!--コンテンツ-->
    <div class="wrapper">
        <div class="container">
            <div class="content">
                <p class="h4 mt-5">投稿完了</p>
                <p class="pb-3">以下の内容で投稿が完了しました</p>
                <!--題名-->
                <div class="fw-bold pb-1">題名</div>
                <div style="overflow: hidden; overflow-wrap: break-word;"><?php echo $hasTaken[0]['title']; ?></div>
                <!--カテゴリー-->
                <div class="fw-bold pt-3 pb-1">カテゴリ</div>
                <div><?php echo $hasTaken[0]['category_name']; ?></div>
                <!--本文-->
                <div class="fw-bold pt-3 pb-1">本文</div>
                <div style="overflow: hidden; overflow-wrap: break-word;"><?php echo nl2br(htmlspecialchars($hasTaken[0]['message'], FILTER_SANITIZE_SPECIAL_CHARS, 'UTF-8')); ?></div>
                <form method="GET" name="form1" action="qDisp.php">
                    <input type="hidden" name="question_id" value="<?php echo $hasTaken[0]['question_id']; ?>">
                    <a href="javascript:form1.submit()" class="btn btn-warning mt-2">詳細画面へ</a>
                </form>
                <a href="index.php" class="btn btn-outline-dark fw-bold">TOP</a>
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