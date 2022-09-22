<?php
// 記事削除
require_once "../../../app/ArticleAct.php";
require_once "../../../app/Token.php";

use Novus\ArticleAct;
use Novus\Token;

$act = new ArticleAct();
$act->begin(1);

// ログインチェック
$act->checkLogin();

// トークンチェック
Token::validate();

$article_id = filter_input(INPUT_POST, "article_id", FILTER_SANITIZE_NUMBER_INT);

// 記事削除
$act->delete($article_id);

// ajax呼び出し。 戻り値を出力
echo "success";


