<?php 
session_start();
    
// ファイルの読み込み
require_once '../../app/UserLogic.php';
require_once '../../app/Functions.php';

// エラーメッセージ
$err = [];
    
// CSRF対策
if (isset($_POST['create'])) {
    $token = filter_input(INPUT_POST, 'csrf_token');
    // トークンがない、もしくは一致しない場合、処理を中止
    if (!isset($_SESSION['csrf_token']) || $token !== $_SESSION['csrf_token']) {
    exit('不正なリクエスト');
    }
    unset($_SESSION['csrf_token']);
}

// セッションの受け取り
if (!empty($_POST['formcheck'])) {
    $_SESSION['signUp'] = array($_POST['name'], $_POST['tel'], $_POST['email'], $_POST['password']);
    
    $name = filter_input(INPUT_POST, 'name');
    $tel = filter_input(INPUT_POST, 'tel');
    $email = filter_input(INPUT_POST, 'email');
    $password = filter_input(INPUT_POST, 'password');
    $password_conf = filter_input(INPUT_POST, 'password_conf');
    
    // バリデーション
    $limitName = 15;   // 名前の文字制限
    $limitTel = 12;    // 電話番号の文字制限
    $limitEmail = 35;  // メールアドレスの文字制限
    // 名前チェック
    if (!$_SESSION['signUp']['0']) {
        $err['name'] = '名前を入力してください';
    }
    if (mb_strlen($name) > $limitName) {
        $err['name'] = '15文字以内で入力してください';
    }
    // 電話チェック
    if (!$_SESSION['signUp']['1']) {
        $err['tel'] = '電話番号を入力してください';
    }
    if (strlen($tel) > $limitTel) {
        $err['tel'] = '12文字以内で入力してください';
    }
    // 重複チェック
    $checkDuplicate = UserLogic::checkDuplicateByTel($_SESSION['signUp']['1']);
    if ($checkDuplicate['cnt'] > 0) {
        $err['tel'] = 'この電話番号は既に登録されています';
    }
    // メールアドレスチェック
    if (isset($_SESSION['signUp']['2'])) {
        if (strlen($email) > $limitEmail) {
            $err['email'] = '35文字以内で入力してください';
        }
    }
    // パスワード正規表現
    if (!$_SESSION['signUp']['3']) {
        $err['password'] = 'パスワードを入力してください';
    }
    if (!preg_match("/\A[a-z\d]{4,20}+\z/i", $password)) {
        $err['password'] = 'パスワードは英数字4文字以上20文字以下にしてください';
    }
    if ($_SESSION['signUp']['3'] !== $password_conf) {
        $err['password'] = '確認用パスワードと異なっています';
    }
}
    
// エラーがなかった場合の処処理
if (count($err) === 0 && (isset($_POST['check']))) {
    // ユーザーを登録する
    $userCreate = UserLogic::createUser($_SESSION);
    header('Location: complete.php');
    // 既に存在しているアカウントの場合
    if (!$userCreate) {
    $err[] = '登録に失敗しました';
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

<body class="h-100 bg-secondary p-4 p-md-5">
    <form action="" method="POST" class="row g-3 bg-white p-2 p-md-5 shadow-sm">
        <input type="hidden" name="check" value="checked">
        <h1 class="my-3 h1" style="text-align:center;">入力情報の確認</h1>
        <p class="my-2" style="text-align:center;">ご入力内容に変更が必要な場合は、下記の<br>ボタンを押して、変更を行ってください。</p>
        <p class="my-1" style="text-align:center;">登録情報は後から変更することもできます。</p>
        <?php if (!empty($err) && $err === "err"): ?>
            <p class="err">＊会員登録に失敗しました。</p>
        <?php endif; ?>
        <hr>
    
        <div class="align-items-center">
            <!--名前の確認表示-->
            <div class="control">
                <p style="font-weight:bold;">[Name]</p>
                <p><span name="name" class="check-info"><?php echo htmlspecialchars($_SESSION['signUp']['0'], ENT_QUOTES); ?></span></p>
                <!--エラーメッセージ表示-->
                <?php if (isset($err['name'])): ?>
                    <p class="text-danger"><?php echo $err['name']; ?></p>
                <?php endif; ?>
            </div>
            <!--電話の確認表示-->
            <div class="control">
                <p style="font-weight:bold;">[Phone]</p>
                <p><span class="fas fa-angle-double-right"></span><span name="tel" class="check-info">
                        <?php echo htmlspecialchars($_SESSION['signUp']['1'], ENT_QUOTES); ?>
                    </span>
                </p>
                <!--エラーメッセージ表示-->
                <?php if (isset($err['tel'])): ?>
                    <p class="text-danger"><?php echo $err['tel']; ?></p>
                <?php endif; ?>
            </div>
            <!--メールの確認表示-->
            <div class="control">
                <p style="font-weight:bold;">[Email]</p>
                <p><span class="fas fa-angle-double-right"></span><span name="email" class="check-info"><?php echo htmlspecialchars($_SESSION['signUp']['2'], ENT_QUOTES); ?></span></p>
                <!--エラーメッセージ表示-->
                <?php if (isset($err['email'])): ?>
                    <p class="text-danger"><?php echo $err['email']; ?></p>
                <?php endif; ?>
            </div>
            <!--パスワードの確認表示-->
            <div class="control">
                <p style="font-weight:bold;">[Password]</p>
                <p><span class="fas fa-angle-double-right"></span><span name="password" class="check-info"><?php echo htmlspecialchars($_SESSION['signUp']['3'], ENT_QUOTES); ?></span></p>
                <!--エラーメッセージ表示-->
                <?php if (isset($err['password'])): ?>
                    <p class="text-danger"><?php echo $err['password']; ?></p>
                <?php endif; ?>
            </div>
            <br>

            <!--エラーが発生した場合、メッセージと戻る画面を作成-->
            <?php if (count($err) > 0) :?>
            <div class="text-center mb-5">
                <a href="form.php" class="btn btn-secondary" role="button">再入力する</a>
            </div>
            <?php else: ?>
                <div class="text-center pb-2">
                    <a href="form.php" class="btn btn-secondary mb-2" role="button">変更する</a>
                    <p><input type="submit" class="btn btn-primary" value="登録"></p>
                </div>
            <?php endif; ?>
        </div>
    </form>
</body>
</html>