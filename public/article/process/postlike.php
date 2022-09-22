<?php
// いいね編集処理
require_once "../../../app/ArticleAct.php";
require_once "../../../app/Token.php";
require_once "../../../app/Utils.php";

use Novus\ArticleAct;
use Novus\Token;

$act = new ArticleAct();
$act->begin(1);

// ログインチェック
$act->checkLogin();

// トークンチェック
Token::validate();

$article_id = filter_input(INPUT_POST, "article_id", FILTER_SANITIZE_NUMBER_INT);
if ($article_id) {
    $retInfo = $act->article($article_id); //記事詳細情報
}
$user_id = $retInfo["user"]["user_id"]; //表示している記事のユーザーIDを取得
if ($article_id) {
    // 記事にいいね設定/解除
    $retcode = $act->postLikeArticle($article_id);
    if ($retcode === 'likeset' && $retInfo["postLike"] === false) { //もし『いいねを押されたら』且つ『その人からのいいねデータがなければ』
        $act->addEXP($user_id, 5);
    }
    // ajax呼び出し。 戻り値を出力
    echo $retcode;
} else {
    // ajax呼び出し。 戻り値を出力
    echo "failed";
}
