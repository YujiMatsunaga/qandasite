<?php
session_start();

// ファイルの読み込み
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

if (isset($_SESSION['a_data']['message']) &&
    isset($_SESSION['a_data']['a_user_id']) &&
    isset($_SESSION['a_data']['q_user_id']) &&
    isset($_SESSION['a_data']['question_id'])
) {
    // 今までの返信を取得して、自分の返信があった場合カウントする
    $hasDisplayed = QuestionLogic::displayAnswer($_SESSION['a_data']['question_id']);
    $count = 0;
    foreach ($hasDisplayed as $value) {
        if ($value['user_id'] == $_SESSION['a_data']['a_user_id']) {
            $count = $count + 1;
        }
    }
    // 返答を登録する処理
    $hasCreated = QuestionLogic::createAnswer();
    if (!$hasCreated) {
        $err['answer'] = '返信の登録に失敗しました';
    } 

    // 質問者≠返信者 且つ 一度目の返信 の場合、経験値加算処理
    if ($_SESSION['login_user']['user_id'] != $_SESSION['a_data']['q_user_id'] && $count == 0) {// 質問を投稿した本人「でない」 且つ 返信が一回目 のとき
        // 経験値を加算する処理
        $plusEXP = UserLogic::plusEXP($_SESSION['login_user']['user_id'], 10);
        if (!$plusEXP) {
            $err['plusEXP'] = '経験値加算処理に失敗しました';
        }
    }
} else {
    $err['other'] = '値の取得に失敗しました';
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
                <p class="h4">返信の投稿完了</p>
                <?php if (count($err) == 0): ?>
                    <p>返答の投稿が完了しました</p>
                <?php elseif ($err['other']): ?>
                    <p>リロードは無効です</p>
                <?php else: ?>
                    <p>返信の登録に失敗しました</p>
                <?php endif; ?>            
                <form method="GET" action="../question/qDisp.php">
                    <input type="hidden" name="question_id" value="<?php echo $_SESSION['a_data']['question_id']; ?>">
                    <input type="submit" class="btn btn-warning mb-3" name="q_disp" value="質問表示へ戻る">
                </form>
                <button type="button" class="btn btn-outline-dark fw-bold mb-5" onclick="location.href='../userLogin/home.php'">TOPへ</button>
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
