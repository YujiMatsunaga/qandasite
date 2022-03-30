<?php
// 記事削除

require_once "../../../app/ArticleAct.php";
require_once "../../../app/Token.php";

use Qanda\ArticleAct;
use Qanda\Token;

$act = new ArticleAct();
$act->begin(1);

// トークンチェック
Token::validate();

$articleid = filter_input(INPUT_POST, "articleid", FILTER_SANITIZE_NUMBER_INT);

// 記事削除
$act->deletearticle($articleid);

// ajax呼び出し。 戻り値を出力
echo "success";


