<?php 
session_start();

require_once '../../app/UserLogic.php';
require_once '../../app/Functions.php';

// エラーメッセージ
$err = [];

// ログインしているか判定して、していなかったらログイン画面へ移す
$result = UserLogic::checkLogin();
if (!$result) {
    $_SESSION['login_err'] = '再度ログインして下さい';
    header('Location: ../userLogin/form.php');
    return;
}
$login_user = $_SESSION['login_user'];

// エラーがなかった場合の処処理
if (count($err) === 0 && (isset($_POST['check']))) {
    // ユーザーを登録する
    $userDelete = UserLogic::deleteUser($_SESSION);
    // ログアウトをする
    UserLogic::logout($_SESSION);
    header('Location: complete.php');
    // 失敗した場合
    if (!$userDelete) {
    $err[] = '削除に失敗しました';
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
    <title>novus</title>
</head>

<!--中央コンテンツ-->
<body class="h-100 bg-secondary p-4 p-md-5">
    <form action="" method="POST" class="row g-3 bg-white p-2 p-md-5 shadow-sm">
        <input type="hidden" name="check" value="checked">
        <h1 class="my-3 h1" style="text-align:center;">会員登録削除の確認</h1>
        <p class="my-2" style="text-align:center;">本当に削除をしてよろしいですか？</p>
        <?php if (!empty($err) && $err === "err"): ?>
            <p class="err">＊会員削除に失敗しました。</p>
        <?php endif; ?>
        <hr>
        <div class="text-center">
            <a href="list.php" class="btn btn-secondary">戻る</a>
            <p><input type="submit" class="btn btn-primary mt-3" value="削除する"></p>
        </div>
        <div class="clear"></div>
    </form>
</body>
</html>