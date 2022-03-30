<?php
// 記事一覧表示
require_once "../../app/ArticleAct.php";
require_once '../../app/Token.php';
require_once '../../app/Utils.php';
require_once "../../app/VendorUtils.php";

use Qanda\ArticleAct;
use Qanda\Token;
use Qanda\Utils;
use Qanda\VendorUtils;

$act = new ArticleAct();
$act->begin();
Token::create(); //Token生成

$retinfo = NULL;
$articleid = filter_input(INPUT_GET, "articleid", FILTER_VALIDATE_INT);

if ($articleid) {
  // 記事詳細情報取得
  $retinfo = $act->article($articleid);
}
if ($retinfo == NULL) {
  // 記事がない場合は、記事一覧へリダイレクト
  header("Location: " . DOMAIN . "/public/article/index.php");
  exit;
}

// カテゴリ名
$catename = $retinfo["category"][$retinfo["article"]["CATE_ID"]];

// 投稿日時
$postdt = Utils::compatiDate($retinfo["article"]["UPD_DATE"], "Y/m/d H:i");

// 投稿内容
$message = $retinfo['article']['MESSAGE'];
$message = VendorUtils::markDown($message); // マークダウンの変換
$message = Utils::trimHtmlTag($message); // 一部タグを許容、他は変換
$message = Utils::compatiStr($message); // 改行を <br/>
?>

<div class="row m-2">
  <div class="col-sm-8"></div>
  <?php if (isset($_SESSION['login_user'])) : ?>
    <div class="col-sm-4"><?php echo $act->getMemberName(); ?>さんがログイン</div>
  <?php endif ?>
</div>

<!-- ここから本文 -->
<h5 class="artListTitle mt-3 font-weight-bold">記事詳細</h5>
<!-- 投稿者名といいね数 -->
<div class="row m-2">
  <div class="col-sm-9"><?php echo $retinfo["user"]["NAME"]; ?>さんの投稿</div>
  <div class="col-sm-2">
    <?php
    if (isset($_SESSION['login_user']) && $retinfo["article"]["USER_ID"] != $act->getMemberId()) {
      if ($retinfo["postlike"] == NULL || $retinfo["postlike"]["LIKE_FLG"] == 0) {
        // いいねボタン押下で、いいねにする
        print('<a class="btn btn-primary" id="btnlike">いいね</a>');
      } else {
        // いいね済み。ボタン押下で、いいねを解除
        print('<a class="btn btn-primary active" id="btnlike">いいね[済]</a>');
      }
    }
    ?>
  </div>
  <div class="col-sm-1">
    &hearts; <span id="postlikecnt"><?php echo $retinfo["postlikecnt"]; ?></span>
  </div>
</div>

<!-- タイトルと本文 -->
<div class="container-fluid">
  <!-- タイトル -->
  <div class="row m-2 form-group" style="height:55vh;">
    <div class="col-sm-12" style="word-break:break-all;">
      <h2 class="artDetailTitle"><?php echo Utils::h($retinfo['article']['TITLE']); ?></h2>
      <span class="artDetailContents" id="preview"><?php echo $message; ?></span>
    </div>
  </div>
  <!-- カテゴリバッジ -->
  <div class="row m-2 form-group">
    <div class="catename col-sm-1">
      <div class="badge rounded-pill bg-danger p-2">
        <?php echo $catename; ?>
      </div>
    </div>
    <div class="col-sm1-11"></div>
  </div>
  <!-- 日付 -->
  <div class="row m-2 form-group">
    <div class="artFootLeft col-sm-2"><?php echo $postdt; ?></div>
    <div class="artFootLeft col-sm-10"></div>
  </div>
</div>

<hr>
<div class="row m-2">
  <div class="col-sm-6">
    <a class="btn btn-warning m-2" href="<?php echo DOMAIN; ?>/public/todo/index.php">todoへ</a>
    <?php if (isset($_SESSION['login_user'])) : ?>
      <a class="btn btn-success m-2" href="<?php echo DOMAIN; ?>/top/userLogin/login_top.php">ホーム画面へ</a>
    <?php else : ?>
      <a class="btn btn-success m-2" href="<?php echo DOMAIN; ?>/top/toppage/top.php">ホーム画面へ</a>
    <?php endif ?>
  </div>
  <div class="col-sm-6">
    <a class="btn btn-primary m-2" href="<?php echo DOMAIN; ?>/public/article/index.php">一覧に戻る</a>
    <?php
    if ($retinfo["article"]["USER_ID"] == $act->getMemberId()) {
      // 自分が投稿した記事
      printf(
        '<a class="btn btn-primary m-2" href="%sarticle/postedit.php?articleid=%d">編集する</a>',
        DOMAIN . "/public/",
        $articleid
      );
      print('<div class="btn btn-primary m-2" id="btndelete">削除する</div>');
    }
    ?>
  </div>
</div>

<script type="text/javascript">
  // いいねボタンを押した
  function onPostLike() {
    // ボタンを一時的に無効にする
    // ボタンを
    const $btnlike = $('#btnlike').off().removeClass('active');

    // 送信(ajax)
    var $data = 'articleid=' + <?php echo $_GET['articleid']; ?> + '&token=<?php echo $_SESSION["token"]; ?>';
    formapiCallback('article/process/postlike.php', $data, function($result) {

      const $postlikecnt = $('#postlikecnt');
      const cnt = parseInt($postlikecnt.html());

      // ボタンを戻す
      $btnlike.click(onPostLike);

      // 画面に反映
      if ($result == 'likeset') {
        // いいねにした
        $btnlike.addClass('active');
        $btnlike.html('いいね[済]');
        $postlikecnt.html(cnt + 1);
      } else {
        // いいね解除。ボタンを再いいねできるようにする。
        $btnlike.html('いいね');
        $postlikecnt.html(cnt > 0 ? cnt - 1 : 0);
      }
    });
  }
  // 削除ボタンを押した
  function onDelete() {
    swal({
      text: '削除してもよろしいですか？',
      icon: 'warning',
      buttons: true,
      dangerMode: true
    }).then(function(isConfirm) {
      if (isConfirm) {

        var $data = 'articleid=' + <?php echo $articleid; ?> +
          '&token=<?php echo $_SESSION["token"]; ?>';

        formapiCallback('article/process/delete.php', $data, function($retcode) {
          // 投稿一覧画面へ
          jumpapi('article/index.php');
        });
      }
    });
  }
  // 初期化
  $(function() {
    // ボタン
    $('#btnlike').click(onPostLike);
    $('#btndelete').click(onDelete);

  });
</script>

<?php
$act->end(1);
?>