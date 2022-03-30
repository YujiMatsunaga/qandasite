<?php
    session_start();

    // ファイルの読み込み
    require_once '../../classes/UserLogic.php';

    // エラーメッセージ
    $err = [];

    $tel = filter_input(INPUT_POST, 'tel');
    $password = filter_input(INPUT_POST, 'password');
    //未入力チェック
    if(!$tel = filter_input(INPUT_POST, 'tel')) {
        $err['tel'] = '電話番号を入力してください';
    }
    // 文字数チェック
    if (strlen($tel) > 12) {
        $err_msg['tel'] = '12文字で入力してください';
    }
    if(!$password = filter_input(INPUT_POST, 'password')) {
        $err['password'] = 'パスワードを入力してください';
    }
    //正規表現
    if (!preg_match("/\A[a-z\d]{4,20}+\z/i", $password)){
        $err['password'] = 'パスワードは英数字4文字以上20文字以下にしてください';
    }

    // エラーがあったらフォーム画面に戻す
    if (count($err)>0){
        $_SESSION = $err;
        header('Location: login_form.php');
        return;
    }

    // ログインに成功した時の処理
    $result = UserLogic::login($tel, $password);
    // ログインに失敗した時の処理
    if (!$result){
        header('Location: login_form.php');
        return;
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../../css/top.css" />
    <title>ログイン完了画面</title>
</head>

<body class="h-100 bg-secondary p-4 p-md-5 text-center">
    <form class="row g-3 bg-white p-2 p-md-5 shadow-sm" enctype="multipart/form-data" action="signup_check.php" method="POST" name="login">
        <div class = "container bg-white p-5">
            <div class="row align-items-start">
                <h2 class="my-3 h1">ログインが<br>完了しました</h2>
                <div class="text-center">
                    <br><br><a class="btn btn-secondary" href="login_top.php" role="button">TOPページ</a>
                </div>
            </div>
        </div>
    </form>
</body>
</html>