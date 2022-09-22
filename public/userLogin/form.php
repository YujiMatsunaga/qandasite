<?php
session_start();

// ファイル読み込み
require_once '../../app/UserLogic.php';
require_once '../../app/Dbconnect.php';
require_once '../../app/Functions.php';

// ログインしているか判定して、していたらログイン画面へ移す
$result = UserLogic::checkLogin();
if ($result) {
    header('Location: ../userLogin/home.php');
    return;
}

// ログインチェック
$login_err = isset($_SESSION['login_err']) ? $_SESSION['login_err'] : null;
unset($_SESSION['login_err']);
$err = $_SESSION;

// セッション変数を全て解除する
$_SESSION = array();
?>

<!--ログインフォーム-->
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
    <form class="row g-3 bg-white p-2 p-md-5 shadow-sm" action="complete.php" method="POST" name="login">
        <h1 class="my-3">ログインフォーム</h1>
<<<<<<< Updated upstream
            <?php if(isset($err['msg'])): ?>
                <p><?php echo $err['msg']; ?></p>
            <?php endif; ?>
=======
        <?php if (isset($err['msg'])): ?>
            <p><?php echo $err['msg']; ?></p>
        <?php endif; ?>
>>>>>>> Stashed changes
        <p class="my-2">下記項目を記入して下さい。</p>
        <!--電話番号を記入-->
        <div class="row my-4">
            <label for="tel" class="form-label font-weight-bold">Phone</label>
            <div class="md-3">
                <input type="tel" oninput="value = value.replace(/[０-９]/g,s => 
<<<<<<< Updated upstream
                String.fromCharCode(s.charCodeAt(0) - 65248)).replace(/\D/g,'');" 
                class="form-control col-10" name="tel">
=======
                String.fromCharCode(s.charCodeAt(0) - 65248)).replace(/\D/g,'');" class="form-control col-10" name="tel">
>>>>>>> Stashed changes
                <!--欄の下に未記入時のエラーメッセージ表示-->
                <?php if (isset($err['tel'])): ?>
                    <p class="text-danger"><?php echo $err['tel']; ?></p>
                <?php endif; ?>
            </div>
        </div>
        <!--パスワードを記入-->
        <div class="row my-4">
            <label for="password" class="form-label font-weight-bold">*Password</label>
            <div class="md-3">
               <input type="password" class="form-control col-6" id="inputPassword8" name="password">
               <!--欄の下に未記入時のエラーメッセージ表示-->
               <?php if (isset($err['password'])): ?>
                    <p class="text-danger"><?php echo $err['password']; ?></p>
                <?php endif; ?>
            </div>
        </div>
        <!--送信ボタン-->
        <div class="col-12 my-4 text-center">
            <p><input type="submit" class="btn btn-primary mt-3" value="Log in"></p>
            <!--登録画面へ-->
            <a href = "../userRegister/form.php">新規登録はこちら</a>
            <p><a class="mb-2 btn btn-outline-dark mt-5" href="../top/index.php" role="button">TOPに戻る</a></p>
        </div>
    </form>
</body>
</html>