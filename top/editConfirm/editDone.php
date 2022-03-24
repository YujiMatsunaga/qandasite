<?php 
session_start();
//ファイル読み込み
require_once '../../classes/UserLogic.php';
require_once '../../functions.php';

$err = []; 

?> 

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../../css/top.css" />
    <title>会員登録完了画面</title>
</head>

<body class="h-100 bg-secondary p-4 p-md-5">
    <div class = "container bg-white p-5 text-center small">
        <!--エラーが発生した場合、メッセージと戻る画面を作成-->
        <?php if (count($err) > 0) :?>
            <?php foreach($err as $e) :?>
                <p><?php echo $e ?></p>
                <?php endforeach ?>
                <div class="text-center">
                    <br><br><a class="btn btn-secondary" href="signup_form.php" role="button">登録画面に戻る</a>
                </div>
        <?php else :?>
        <div class="row align-items-start">
            <h1 class="my-3 h1">情報更新が<br>完了しました</h1>
            <!--TOPページへ-->
            <form action="../userLogin/mypage.php" method="POST" name="editDone">
                <div class="text-center">
                    <!--トークン-->
			    	<input type="hidden" name="csrf_token" value="<?php echo h(setToken()); ?>">
                    <br><br>
                    <input type="submit" value="MyPageに戻る">
                </div>
            </form>
        <?php endif ?>
        </div>
    </div>
</body>
</html>