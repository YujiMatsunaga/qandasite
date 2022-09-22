<?php
session_start();

// ファイルの読み込み
require_once '../../app/QuestionLogic.php';
require_once '../../app/CategoryLogic.php';
require_once '../../app/UserLogic.php';

// エラーメッセージ
$err = [];

// ログインチェック
$result = UserLogic::checkLogin();
if (!$result) {
    $_SESSION['login_err'] = 'ユーザーを登録してログインして下さい';
    header('Location: ../userLogin/form.php');
    return;
}
//カテゴリ処理
$categories = CategoryLogic::getCategory();

// ボタン押下時の処理（成功でベストアンサー登録）
if (isset($_POST['a_best_comp'])) {
    // エラーチェック
    if(!$_POST['question_id'] || !$_POST['answer_id'] || !$_POST['answer_user_id']) {
        $err['a_id'] = '返答が選択されていません';
    } else {
        $_SESSION['a_data']['answer_id'] = filter_input(INPUT_POST, 'answer_id', FILTER_SANITIZE_SPECIAL_CHARS);
        $_SESSION['a_data']['answer_user_id'] = filter_input(INPUT_POST, 'answer_user_id', FILTER_SANITIZE_SPECIAL_CHARS);
        $_SESSION['a_data']['question_id'] = filter_input(INPUT_POST, 'question_id', FILTER_SANITIZE_SPECIAL_CHARS);
    }
    // ページ読み込み処理
    // 質問取得の処理
    $answer = QuestionLogic::displayOneAnswer($_SESSION['a_data']['answer_id']);
    // ベストアンサー登録
    $best = QuestionLogic::bestAnswer();
        if (empty($best)) {
            $err[] = 'ベストアンサー登録に失敗しました';
        }
    // 経験値を加算する処理
    $plusEXP = UserLogic::plusEXP($_SESSION['a_data']['answer_user_id'], 40);
        if (!$plusEXP) {
            $err['plusEXP'] = '経験値加算処理に失敗しました';
        }
} else { // 通常時処理
    $question_id = filter_input(INPUT_POST, 'question_id');
    if (empty($question_id)) {
        $err[] = '質問を選択し直してください';
    }
    $answer_id = filter_input(INPUT_POST, 'answer_id');
    if (empty($answer_id)) {
        $err[] = '返答を選択し直してください';
    }
    $answer_user_id = filter_input(INPUT_POST, 'answer_user_id');
    if (empty($answer_user_id)) {
        $err[] = 'ユーザーを選択し直してください';
    }
    if (count($err) === 0) {
        // 質問取得の処理
        $answer = QuestionLogic::displayOneAnswer($answer_id);
        if (empty($answer)) {
            $err[] = '返答の読み込みに失敗しました';
        }
    }
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
                <p class="h4 mt-4">返答内容</p>
                <!-- 通常時処理 -->
                <?php if (!isset($_POST['a_best_comp'])): ?>
                    <div>以下の返答をベストアンサーに選択しますか？</div>
                    <div>※一度選択すると、変更できません</div>
                    <form method="POST" action="">
                        <div>
                            <?php if (isset($err['a_id'])): ?>
                            <?php echo $err['a_id']; ?>
                            <?php endif; ?>
                        </div>
                        <div>
                            <?php if (isset($err['message'])): ?>
                            <?php echo $err['message']; ?>
                            <?php endif; ?>
                        </div>
                        <div class="fw-bold pt-3 pb-1">本文</div>
                        <div style="overflow: hidden; overflow-wrap: break-word;"><?php echo htmlspecialchars($answer['message'], \ENT_QUOTES, 'UTF-8'); ?></div>
                        <input type="hidden" name="question_id" value="<?php echo $question_id; ?>">
                        <input type="hidden" name="answer_id" value="<?php echo $answer_id; ?>">
                        <input type="hidden" name="answer_user_id" value="<?php echo $answer_user_id; ?>">
                        <input type="submit" name="a_best_comp" class="btn btn-warning mt-5 mb-5" value="選択">
                    </form>
                    <button type="button" class="btn btn-outline-dark fw-bold mb-5" onclick="history.back()">戻る</button>
                <!-- ボタン押下時の処理 -->
                <?php elseif (isset($_POST['a_best_comp']) && count($err) === 0): ?>
                    <?php if (empty($err)): ?>
                        <div>ベストアンサー登録が完了しました</div>
                    <?php else: ?>
                        <?php foreach ($err as $row): ?>
                            <?php echo $row ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    <button type="button" class="mt-4" onclick="location.href='../../userLogin/home.php'">TOP</button>
                    <button type="button" class="mb-5" onclick="location.href='qDisp.php?question_id=<?php echo $_SESSION['a_data']['question_id']; ?>'">質問へ</button>
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