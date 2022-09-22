<?php
session_start();

// ファイルの読み込み
require_once '../../app/UserLogic.php';

$data = $_GET;
$logout = filter_input(INPUT_GET, 'user_id');

// ログイン可否を判定、セッションが切れていたらログインを促すメッセージ
$data = UserLogic::checkLogin();
if (!$data) {
    exit ('セッションが切れているので、再ログインをして下さい');
    header('Location: ../userLogin/form.php');
    return;
}

// ログアウトをする
UserLogic::logout($_GET);
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
<body class="h-100 bg-secondary p-4 p-md-5 text-center">
    <div class = "container bg-white p-5">
        <div class="row align-items-start">
            <h2 class="my-3 h1">ログアウト完了</h2>
            <p>ログアウトが完了しました</p>
            <div class="text-center">
                <br><br><a class="btn btn-secondary" href="../top/index.php" role="button">TOPページ</a>
            </div>
        </div>
    </div>
</body>
</html>