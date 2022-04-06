<?php
session_start();

//ファイル読み込み
require_once '../../app/UserLogic.php';
require_once '../../app/Functions.php';

//エラーメッセージ
$err = [];

//ログインしているか判定して、していなかったらログインへ移す
$result = UserLogic::checkLogin();
if(!$result) {
    $_SESSION['login_err'] = '再度ログインして下さい';
    header('Location: ../userLogin/form.php');
    return;
}
$login_user = $_SESSION['login_user'];

if(!empty($_POST['formcheck'])) {
    $_SESSION['commentEdit'] = $_POST['comment'];
    $comment = filter_input(INPUT_POST, 'comment');
    //バリデーション
    $limitComment = 50;
    if(isset($_SESSION['commentEdit'])) {
        // 文字数チェック
        if(mb_strlen($comment) > $limitComment) {
        $err['comment'] = '50文字以内で入力してください';
        }
    }
}

//エラーがなかった場合の処処理
if(count($err) === 0 && (isset($_POST['check']))) {
    //ユーザーを登録する
    $userEdit = UserLogic::editUserComment($_SESSION);
    header('Location: complete.php');
    //既に存在しているアカウントの場合
    if(!$userEdit) {
    $err[] = '更新に失敗しました';
    }
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
    <title>変更確認画面[comment]</title>
</head>

<body>
    <!--メニュー-->
    <header>
        <div class="navbar bg-dark text-white">
            <div class="navtext h2" id="headerlogo">novus</div>
            <ul class="nav justify-content-center">
                <li id="li"><a class="nav-link active small text-white" href="../userLogin/home.php">TOPページ</a></li>
                <li id="li"><a class="nav-link active small text-white" href="../myPage/index.php">MyPageに戻る</a></li>
			    <li id="li"><a class="nav-link active small text-white" href="../userEdit/index.php">【編集】会員情報</a></li>
                <li id="li"><a class="nav-link small text-white" href="../myPage/qHistory.php">【履歴】質問</a></li>
                <li id="li"><a class="nav-link small text-white" href="../myPage/aHistory.php">【履歴】記事</a></li>
                <li id="li"><a class="nav-link small text-white" href="<?php echo "../userLogin/logout.php?=user_id=".$login_user['user_id']; ?>">ログアウト</a></li>
            </ul>
        </div>
    </header>

    <!--中央コンテンツ-->
    <div class="wrapper">
        <div class="container">
            <div class="content">
                <h2 class="heading mt-5">アカウント編集画面</h2>
                <form action="" method="POST">
                <input type="hidden" name="check" value="checked">
                <h1 class="my-3 h1" style="text-align:center;">入力情報の確認</h1>
                <p class="my-2" style="text-align:center;">ご入力内容に変更が必要な場合は、下記の<br>ボタンを押して、変更を行ってください。</p>
                <?php if(!empty($err) && $err === "err"): ?>
                    <p class="err">＊会員情報更新に失敗しました。</p>
                <?php endif ?>
                    <div class="list">
                        <!--ユーザーが登録したコメントを表示-->
                        <div class="text">
                            <label for="comment">[Comment]</label>
                            <p><span name="comment" class="check-info"><?php echo htmlspecialchars($_SESSION['commentEdit'], ENT_QUOTES, 'UTF-8'); ?></span></p>
                            <!--エラーメッセージ表示-->
                            <?php if(!$_SESSION['commentEdit']): ?>
                            <?php echo "記入をしなくてよろしいですか？"; ?>
                            <?php endif; ?>
                            <?php if(isset($err['comment'])): ?>
                                <p class="text-danger"><?php echo $err['comment']; ?></p>
                            <?php endif; ?>
                        </div>
                        <!--エラーが発生した場合、メッセージと戻る画面を作成-->
                        <?php if(count($err) > 0): ?>
                        <div class="text-center">
                            <a href="../userEdit/comment.php" class="p-2 text-white bg-secondary">再入力する</a>
                        </div>
                        <?php else: ?>
                        <div class="text-center">
                            <a href="../userEdit/comment.php" class="p-2 text-white bg-secondary">戻る</a>
                            <p><button type="submit">変更</button></p>
                        </div>
                        <?php endif; ?>
                    </div>
                </form>
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

