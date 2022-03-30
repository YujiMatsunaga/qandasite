<?php 
    session_start();
    
    require_once '../../classes/UserLogic.php';
    require_once '../../functions.php';

    //エラーメッセージ
    $err = [];
    
    //ログインしているか判定して、していなかったら新規登録画面へ移す
    $result = UserLogic::checkLogin();
    if (!$result) {
        $_SESSION['login_err'] = 'ユーザーを登録してログインして下さい';
        header('Location: ../userCreate/signup_form.php');
        return;
    }
    $login_user = $_SESSION['login_user'];

    //エラーがなかった場合の処処理
    if (count($err) === 0 && (isset($_POST['check']))) {
        
        //ユーザーを登録する
        $userDelete = UserLogic::deleteUser($_SESSION);
        header('Location: deleteDone.php');
        //失敗した場合
        if(!$userDelete){
        $err[] = '削除に失敗しました';
        }
    }

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../../css/top.css" />
    <title>会員削除確認画面</title>
</head>

<body class="h-100 bg-secondary p-4 p-md-5">
    <form action="" method="POST" class="row g-3 bg-white p-2 p-md-5 shadow-sm">
        <input type="hidden" name="check" value="checked">
        <h1 class="my-3 h1" style="text-align:center;">会員登録削除の確認</h1>
        <p class="my-2" style="text-align:center;">本当に削除をしてよろしいですか？</p>
        <?php if (!empty($err) && $err === "err"): ?>
            <p class="err">＊会員登録に失敗しました。</p>
        <?php endif ?>
        <hr>
        <div class="col-4 bg-secondary">
            <a href="edit_user.php" class="back-btn text-white">戻る</a>
        </div>
        <p><input type="submit" class="btn btn-primary" value="削除"></p>
        <div class="clear"></div>

    </div>
    </form>
        </div>
    </div>
</body>
</html>