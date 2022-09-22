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
    $_SESSION['login_err'] = '再度ログインして下さい';
    header('Location: ../userLogin/home.php');
    return;
}

// カテゴリ処理
$categories = CategoryLogic::getCategory();

$cate_id = $question['cate_id'];

// 質問選択処理
$question_id = filter_input(INPUT_POST, 'question_id');
if (!$question_id == filter_input(INPUT_POST, 'question_id')) {
    $err[] = '質問を選択し直してください';
}
if (count($err) === 0) {
    // 質問を引っ張る処理
    $question = QuestionLogic::displayQuestion($_POST);
    if (!$question) {
        $err[] = '質問の読み込みに失敗しました';
    }
}

// ボタン押下時の処理（成功でページ移動）
if (isset($_POST['q_edit_conf'])) {
    $_SESSION['q_data']['title'] = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
    $_SESSION['q_data']['category'] = filter_input(INPUT_POST, 'category',FILTER_SANITIZE_SPECIAL_CHARS);
    $_SESSION['q_data']['message'] = filter_input(INPUT_POST, 'message');
    $_SESSION['q_data']['question_id'] = filter_input(INPUT_POST, 'question_id',FILTER_SANITIZE_NUMBER_INT);
    if (empty($_SESSION['q_data']['title'])) {
        $err['title'] = '質問タイトルを入力してください';
    }
    if (empty($_SESSION['q_data']['category'])) {
        $err['category'] = 'カテゴリを選択してください';
    }
    if (empty($_SESSION['q_data']['message'])) {
        $err['message'] = '本文を入力してください';
    }
    if (empty($_SESSION['q_data']['question_id'])) {
        $err['q_id'] = '質問IDが選択されていません';
    }
    if (!empty($_SESSION['q_data']['title'])) {
        $limitTitle = 150;
        // 文字数チェック
        if (mb_strlen($_SESSION['q_data']['title']) > $limitTitle) {
        $err['title'] = '150文字以内で入力してください';
        }
    }
    if (!empty($_SESSION['q_data']['message'])) {
        $limitMessage = 1500;
        // 文字数チェック
        if (mb_strlen($_SESSION['q_data']['message']) > $limitMessage) {
        $err['message'] = '1500文字以内で入力してください';
        }
    }
    if (count($err) === 0) {
        header('Location: qEditComp.php');
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <script src="https://kit.fontawesome.com/7bf203e5c7.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" type="text/css" href="../../public/css/mypage.css">
    <link rel="stylesheet" type="text/css" href="../../public/css/top.css">
    <link rel="stylesheet" type="text/css" href="../../public/css/question.css">
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
                <p class="h4 pb-3 mt-3">質問内容</p>
                <form method="POST" action="">
                    <div class="text-danger pt-2">
                        <?php if (isset($err['q_id'])): ?>
                        <?php echo $err['q_id']; ?>
                        <?php endif; ?>
                    </div>
                    <div class="text-danger pt-2">
                        <?php if (isset($err['title'])): ?>
                        <?php echo $err['title']; ?>
                        <?php endif; ?>
                    </div>
                    <!--題名-->
                    <div class="fw-bold pb-1">題名</div>
                    <div>
                        <input type="text" name="title" value="<?php echo $question['title']; ?>" required>
                    </div>
                    <!--カテゴリー-->
                    <div class="fw-bold pt-3 pb-1">カテゴリ</div>
                    <div>
                        <?php if (isset($err['category'])): ?>
                        <?php echo $err['category']; ?>
                        <?php endif; ?>
                    </div>
                    <div>
                        <select id="category" name="category"  required>
                            <?php foreach ($categories as $value): ?>
                            <option 
                                value="<?php echo $value['cate_id']; ?>"
                                <?php if ($value['cate_id'] == $question['cate_id']): ?>
                                selected
                                <?php endif; ?>> 
                                <?php echo $value['category_name']; ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="text-danger pt-2">
                        <?php if (isset($err['message'])): ?>
                            <?php echo $err['message']; ?>
                        <?php endif; ?>
                    </div>
                    <!--本文-->
                    <div class="fw-bold pt-3 pb-1">本文</div>
                    <div>
                        <textarea name="message" rows="5" class="w-100"><?php echo $question['message']; ?></textarea>
                    </div>
                    <input type="hidden" name="question_id" value="<?php echo $question['question_id']; ?>">
                    <input type="submit" name="q_edit_conf" class="btn btn-warning mt-5 mb-5" value="修正する">
                </form>
                <button type="button" class="btn btn-outline-dark fw-bold" onclick="history.back()">戻る</button>
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

<script type="text/javascript">
$(function() {
    $('#category').val(<?php echo $cate_id ?>);
    setupPreview();
});
</script>