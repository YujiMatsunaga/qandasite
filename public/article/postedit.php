<?php
require_once "../../app/ArticleAct.php";
require_once '../../app/Token.php';
require_once "../../app/Utils.php";

use Novus\ArticleAct;
use Novus\Token;
use Novus\Utils;

// 記事投稿/編集
$act = new ArticleAct();
$act->begin();
$category = $act->categoryMap();

// Token生成
Token::create();

//ログインチェック
$act->checkLogin();

$retInfo = NULL;
$article_id = filter_input(INPUT_GET, "article_id", FILTER_VALIDATE_INT);
if (isset($article_id)) {
    // 記事詳細情報取得
    $retInfo = $act->article($article_id);
}

$article_id = 0;
$title = '';
$message = '';
$catval = 0;

// 初期は新規投稿モード
$modename = '投稿';
$modenamebtn = '投稿';

$article_id = filter_input(INPUT_GET, 'article_id', FILTER_SANITIZE_NUMBER_INT);
if ($article_id) {
    // ID指定の場合編集モードにする
    $modename = '編集画面';
    $modenamebtn = '編集反映';
    $retInfo = $act->article($article_id);
}

if ($retInfo != NULL && $retInfo['article'] != NULL) {
    // 編集モード時の、パラメータ設定
    $article_id = $_GET['article_id'];
    $title = Utils::h($retInfo['article']['title']);
    $message = Utils::h($retInfo['article']['message']);
    $catval = Utils::h($retInfo['article']['cate_id']);
    var_dump($retInfo['article']['cate_id']);
}
?>

<h5 class="artListTitle mt-3 font-weight-bold">記事<?php echo $modename; ?></h5>
<div class="container-fluid">
    <form method="POST" class="form-horizontal" name="NovusForm">
        <div class="row m-2 form-group">
            <div class="col-sm-12">
                <input type="text" class="form-control" id="title" name="title" placeholder="タイトル" value="<?php echo $title; ?>">
            </div>
        </div>
        <div class="row m-2 form-group" style="height:55vh">
            <!-- 入力欄 -->
            <div class="col-sm-6">
                <textarea class="form-control preview maxlength showCount " id="message" name="message" placeholder="本文" style="overflow: hidden; overflow-wrap: break-word; height: 100%; overflow:scroll; overflow-x: hidden; height:450px" data-maxlength="1500"><?php echo $message; ?></textarea>
            </div>
            <!-- プレビュー表示欄 -->
            <div class="col-sm-6">
                <div class="artpreview artContents preview" id="previewmsg" style="overflow:scroll; overflow-x: hidden; height:450px;"></div>
            </div>
        </div>
        <div class="row m-2 form-group">
            <div class="col-sm-3 mt-3">カテゴリ</div>
            <div class="col-sm-9 mt-3">
                <select id="category" name="category" style="width:100%;" placeholder="カテゴリ">
                    <?php
                    foreach ($category as $key => $val) {
                        printf('<option value="%s">%s</option>', $key, $val);
                    }
                    ?>
                </select>
            </div>
        </div>
        <div class="row m-2 form-group">
            <div class="col-sm-12 text-center">
                <input type="hidden" name="article_id" id="article_id" value="<?php echo $article_id; ?>">
                <input type="hidden" name="token" value="<?php echo $_SESSION["token"]; ?>">
                <div class="btn btn-success" onclick="onPostArticle();"><?php echo $modenamebtn; ?></div>
            </div>
            <div class="col-sm-12 text-right">
                <?php
                if ($article_id > 0) {
                    echo ('<div class="btn btn-warning" onClick="onDelete();">削除</div>');
                }
                ?>
                <a class="btn btn-primary" href="<?php echo DOMAIN; ?>/public/article/index.php">一覧に戻る</a>
                <div class="open btn btn-primary">説明</div>
                <div class="modal">
                    <div class="modal_bg"></div>
                    <div class="modal_window">
                        <div class="modal_title text-center">
                            <h3 class="mt-4 mb-4" style="font-weight: bold;">利用方法</h3>
                        </div>
                        <div class="text-center">
                            # おはよう → <span style="font-size:2.5rem; font-weight:bold; line-height:1.2; border-bottom: 3px double #ccc; display: inline-block;">おはよう</span><br>
                            ## おはよう → <span style="font-size:2rem; font-weight:500; line-height:1.2;">おはよう</span><br>
                            ### おはよう → <span style="font-size:1.75rem; font-weight:500; line-height:1.2;">おはよう</span><br><br>
                            - おはよう → <span>・おはよう</span><br><br>
                            ~~おはよう~~ → <del>おはよう</del><br><br>
                            `おはよう` → <span style="color:red;">おはよう</span><br><br>
                            ※各記号は半角のみ有効
                        </div>
                        <div class="close btn btn-danger mt-3 mr-3">CLOSE</div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script type="text/javascript">
    // 投稿後のブラウザバック対策
    $(document).ready(function() {
        if (window.performance.navigation.type == 2) {
            //遷移後に動かす処理
            swal({
                text: '不正な処理が行われました'
            }).then(function(isConfirm) {
                // トップに戻す
                jumpapi('article/index.php');
            });
        }
    });

    // 初期化
    $(function() {
        $('#message').keyup(function() {
            // 本文にキーが入力された
            // プレビュー画面に文字列を反映
            setupPreview();
        });
        // 初期値
        $('#category').val(<?php echo $catval; ?>);
        setupPreview();
    });

    // プレビュー画面に文字列を反映
    function setupPreview() {
        var text = $('#message').val();
        var text = trimHtmlTag(text); // 一部タグを許容
        var text = marked(text); // マークアップ文字置き換え
        var text = text.replace(/\n/g, '<br>'); // 改行
        $('#previewmsg').html(text);
    }

    // 投稿 or 編集反映ボタンを押した(jsバリデーション)
    function onPostArticle() {
        const postTitle = document.getElementById('title').value;
        const postMessage = document.getElementById('message').value;
        const postCategory = document.getElementById('category').value;
        if ($.trim(postTitle) === "") {
            onShow('タイトルに何も入力されていません');
            return;
        }
        if (!isStrLen(postTitle, 1, 150)) {
            onShow('タイトルは150文字以内にしてください');
            return;
        }
        if ($.trim(postMessage) === "") {
            onShow('本文に何も入力されていません');
            return;
        }
        if (!isStrLen(postMessage, 1, 1500)) {
            onShow('本文は1500文字以内にしてください');
            return;
        }
        if ($.trim(postCategory) === "") {
            onShow('カテゴリに何も入力されていません');
            return;
        }

        var $data = 'title=' + encodeURIComponent(document.getElementById('title').value) +
            '&message=' + encodeURIComponent(document.getElementById('message').value) +
            '&category=' + document.getElementById('category').value +
            '&article_id=' + document.getElementById('article_id').value +
            '&token=<?php echo $_SESSION["token"]; ?>';

        // 送信(ajax, phpバリデーション)
        formapiCallback('article/process/post.php', $data, function($retcode) { //ファイルの中身を読み込む
            // 送信完了後の処理
            if ($retcode == 'success') {
                swal({
                    text: '投稿しました'
                }).then(function(isConfirm) {
                    var $article_id = $('#article_id').val();
                    // 記事詳細へ戻す                                
                    jumpapi('article/detail.php?article_id=' + $article_id);
                });
            }
            switch ($retcode) {
                case 'no-title':
                    onShow('タイトルに何も入力されていません');
                    break;
                case 'invalid-title':
                    onShow('タイトルは150文字以内で入力してください');
                    break;
                case 'no-message':
                    onShow('本文に何も入力されていません');
                    break;
                case 'invalid-message':
                    onShow('本文は1500文字以内で入力してください');
                    break;
                case 'failed-category':
                    onShow('カテゴリに何も入力されていません');
                    break;
                default:
                    break;
            }
        });
    }

    // 削除ボタンクリック
    function onDelete() {
        swal({
            text: '削除してもよろしいですか？',
            icon: 'warning',
            buttons: true,
            dangerMode: true
        }).then(function(isConfirm) {
            if (isConfirm) {
                var $data = 'article_id=' + <?php echo $article_id; ?> + '&token=<?php echo $_SESSION["token"]; ?>';

                formapiCallback('article/process/delete.php', $data, function($retcode) {
                    // 投稿一覧画面へ
                    jumpapi('article/index.php');
                });
            }
        });
    }

    $(function() {
        $(".open").click(function() {
            $(".modal").fadeIn();
        });
        $(".close").click(function() {
            $(".modal").fadeOut();
        });
        $(".modal_bg").click(function() {
            $(".modal").fadeOut();
        });
    });
    
    //サロゲートペアを考慮した文字数を返す関数
    var getValueLength = function(value) {
        return (value.match(/[\uD800-\uDBFF][\uDC00-\uDFFF]|[\s\S]/g) || []).length;
    };

    var $maxlengthElems = $(".maxlength");
    var $showCountElems = $(".showCount");
    //data-maxlength属性を指定した要素でshowCountクラスが指定されていれば入力文字数を表示
    $showCountElems.each(function() {
        //data-maxlength 属性の値を取得
        const dataMaxlength = $(this).data("maxlength");
        //data-maxlength 属性の値が存在し数値であれば
        if (dataMaxlength && !isNaN(dataMaxlength)) {
            //p要素のコンテンツを作成（.countSpanを指定したspan要素にカウントを出力。初期値は0）
            var countSpanHtml = '<span class="countSpan">0</span>/' + parseInt(dataMaxlength);
            var countHtml = '<p class="countSpanWrapper">' + countSpanHtml + "</p>";
            
            //入力文字数を表示する p 要素を追加
            $(this).after(countHtml);
        }
    });

    $showCountElems.on("input", function() {
        //上記で作成したカウントを出力する span 要素を取得
        var $countSpan = $(this).parent().find(".countSpan");
        //カウントを出力する span 要素が存在すれば
        if ($countSpan.length !== 0) {
            //入力されている文字数
            //サロゲートペアを考慮した文字数を取得
            var count = getValueLength($(this).val());
            //span 要素に文字数を出力
            $countSpan.text(count);
            //文字数が dataMaxlength（data-maxlength 属性の値）より大きい場合は文字を赤色に
            var dataMaxlength = $(this).data("maxlength");
            if (count > dataMaxlength) {
                $countSpan.css("color", "red");
                $countSpan.addClass("overMaxCount");
            } else {
                $countSpan.css("color", "");
                $countSpan.removeClass("overMaxCount");
            }
        }
    });
</script>

<?php
$act->printFooter(1);
?>