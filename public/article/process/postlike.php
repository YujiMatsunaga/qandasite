<?php
// いいね編集処理
require_once "../../../app/ArticleAct.php";
require_once "../../../app/Token.php";

use Qanda\ArticleAct;
use Qanda\Token;

$act = new ArticleAct();
$act->begin(1);

// トークンチェック
Token::validate();

$articleid = filter_input(INPUT_POST, "articleid", FILTER_SANITIZE_NUMBER_INT);
if ($articleid) {
  // 記事にいいね設定/解除
  $retcode = $act->postlikearticle($articleid);

  // ajax呼び出し。 戻り値を出力
  echo $retcode;
} else {
  // ajax呼び出し。 戻り値を出力
  echo "failed";
}
