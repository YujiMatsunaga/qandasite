<?php

session_start();

//ファイル読み込み
require_once '../../classes/UserLogic.php';
require_once '../../functions.php';

//エラーメッセージ
$err = [];

//ログインしているか判定して、していなかったらログインへ移す
$result = UserLogic::checkLogin();
if (!$result) {
    $_SESSION['login_err'] = 'ユーザーを登録してログインして下さい';
    header('Location: ../userCreate/signup_form.php');
    return;
}
$login_user = $_SESSION['login_user'];

if (!empty($_POST['formcheck'])) {
    $_SESSION['telEdit'] = $_POST['tel'];
    $tel = filter_input(INPUT_POST, 'tel');

    //バリデーション
    if(empty($_SESSION['telEdit'])){
        $err['tel'] = '電話番号を入力してください';
    }
    //電話で重複チェック
    $checkDuplicate = UserLogic::checkDuplicateByTel($_SESSION['telEdit']);
    if ($checkDuplicate['cnt'] > 0){
        $err['tel'] = 'この電話番号は既に登録されています';
    }
}

//エラーがなかった場合の処処理
if (count($err) === 0 && (isset($_POST['check']))) {
    
    //ユーザーを登録する
    $userEdit = UserLogic::editUserTel($_SESSION);
    header('Location: editDone.php');
    //既に存在しているアカウントの場合
    if(!$userEdit){
    $err[] = '更新に失敗しました';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../../css/mypage.css" />
    <title>変更確認画面[tel]</title>
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
            <li class="top"><a href="../userLogin/login_top.php">TOPページ</a></li>
            <li><a href="../userLogin/mypage.php">MyPageに戻る</a></li>
            <li><a href="#projects">質問 履歴</a></li>
            <li><a href="#contact">記事 履歴</a></li>
            <li><a href="#contact">お問い合わせ</a></li>
            <li>
                <form action="../login/logout.php" method="POST">
                    <input type="submit" name="logout" value="ログアウト">
                </form>
            </li>
        </ul>
    </header>

    <section class="wrapper">
        <div class="container">
            <div class="content">
                <h2 class="heading">アカウント編集画面</h2>
                <form action="" method="POST">
                <input type="hidden" name="check" value="checked">
                <h1 style="text-align:center;">入力情報の確認</h1>
        <p class="my-2" style="text-align:center;">ご入力内容に変更が必要な場合は、<br>下記のボタンを押して、変更してください。</p>
        <?php if (!empty($err) && $err === "err"): ?>
            <p class="err">＊会員情報更新に失敗しました。</p>
        <?php endif ?>
                    <div class="list">
                        <!--ユーザーが登録した電話を表示-->
                        <div class="text">
                            <label for="name">[Tel]</label>
                            <p><span name="name" class="check-info"><?php echo htmlspecialchars($_SESSION['telEdit'], ENT_QUOTES, 'UTF-8'); ?></span></p>
                            <!--未記入時等のエラーメッセージ表示-->
                            <?php if (isset($err['tel'])) : ?>
                                <p class="text-danger"><?php echo $err['tel']; ?></p>
                            <?php endif; ?>
                        </div>
                        <br><br>
                        <!--エラーが発生した場合、メッセージと戻る画面を作成-->
                        <?php if (count($err) > 0) :?>
                        <div class="col-4 bg-secondary">
                            <a href="../userEdit/telEdit.php" class="back-btn text-white">再入力する</a>
                        </div>
                        <?php else :?>
                        <div class="col-4 bg-secondary">
                            <a href="../userEdit/telEdit.php" class="p-2 text-white bg-secondary">戻る</a>
                            <p><button type="submit" class="btn-edit-check">変更</button></p>
                        </div>
                        <?php endif ?>
                    </div>
                </form>
            </div>
        </div>
    </section>

	<!-- フッタ -->
    <footer>
        <div class="">
            <br><br><hr>
	        <p class="text-center">Copyright (c) HTMQ All Rights Reserved.</p>
        </div>
    </footer>
</body>
</html>
