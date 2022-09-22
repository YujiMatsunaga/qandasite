<?php 
session_start();

// ファイル読み込み
require_once '../../app/UserLogic.php';

// エラーメッセージ
$err = []; 
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

<body class="h-100 bg-secondary p-4 p-md-5">
    <div class = "container bg-white p-5 text-center small">
        <!--エラーが発生した場合、メッセージと戻る画面を作成-->
        <?php if (count($err) > 0): ?>
            <?php foreach ($err as $e): ?>
                <p><?php echo $e; ?></p>
                <?php endforeach; ?>
                <div class="text-center">
                    <br><br><a class="btn btn-secondary" href="" role="button">ログイン画面に戻る</a>
                </div>
        <?php else: ?>
        <div class="row align-items-start">
            <h1 class="my-3 h1">情報更新が<br>完了しました</h1>
            <!--TOPページへ-->
            <form action="../myPage/index.php" method="POST" name="editDone">
                <div class="text-center">
                    <input type="submit" class="btn btn-secondary mt-4" value="MyPageに戻る">
                </div>
            </form>
        <?php endif; ?>
        </div>
    </div>
</body>
</html>
