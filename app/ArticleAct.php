<?php

namespace Qanda;

require_once "Action.php";
require_once "Utils.php";

// 記事取得系
define("QUERY_ARTICLE_LIST", "SELECT ARTICLE_ID,USER_ID,TITLE,MESSAGE,POST_DATE,UPD_DATE,CATE_ID,ARTICLE_IMAGE FROM article_posts %s ORDER BY UPD_DATE DESC LIMIT :limit OFFSET :offset");
define("QUERY_ARTICLE", "SELECT ARTICLE_ID,USER_ID,TITLE,MESSAGE,POST_DATE,UPD_DATE,CATE_ID,ARTICLE_IMAGE FROM article_posts WHERE ARTICLE_ID=:article_id");
define("QUERY_ARTICLE_COUNT", "SELECT COUNT(ARTICLE_ID) AS CNT FROM article_posts %s");
define("QUERY_ARTICLE_LIKEME_LIST", "SELECT A.ARTICLE_ID,A.USER_ID,A.TITLE,A.MESSAGE,A.POST_DATE,A.UPD_DATE,A.CATE_ID,A.ARTICLE_IMAGE FROM article_posts A, article_likes B WHERE A.ARTICLE_ID = B.ARTICLE_ID AND B.USER_ID=:user_id AND B.LIKE_FLG=1 ORDER BY A.UPD_DATE DESC LIMIT :limit");

// 記事更新系
define("INSERT_ARTICLE", "INSERT INTO article_posts (USER_ID,TITLE,MESSAGE,POST_DATE,UPD_DATE,CATE_ID) VALUES (:user_id, :title, :message, now(), now(), :cate_id)");
define("UPDATE_ARTICLE", "UPDATE article_posts SET TITLE=:title,MESSAGE=:message,CATE_ID=:cate_id,UPD_DATE=now() WHERE ARTICLE_ID=:article_id AND USER_ID=:user_id");
define("DELETE_ARTICLE", "DELETE FROM article_posts WHERE ARTICLE_ID=:article_id AND USER_ID=:user_id");

// カテゴリ一覧
define("QUERY_CATEGORY_LIST", "SELECT CATE_ID,CATEGORY_NAME FROM categories");

// いいね取得系
define("QUERY_POSTLIKE", "SELECT A_LIKE_ID,USER_ID,ARTICLE_ID,LIKE_FLG FROM article_likes WHERE ARTICLE_ID=:article_id AND USER_ID=:user_id");
define("QUERY_POSTLIKE_COUNTLIST", "SELECT ARTICLE_ID,COUNT(A_LIKE_ID) AS LIKECNT FROM article_likes WHERE ARTICLE_ID IN (%s) AND LIKE_FLG=1 GROUP BY ARTICLE_ID");

// いいね更新系
define("INSERT_POSTLIKE", "INSERT INTO article_likes (USER_ID, ARTICLE_ID, LIKE_FLG) VALUES (:user_id, :article_id, 1)");
define("UPDATE_POSTLIKE", "UPDATE article_likes SET LIKE_FLG=:like_flg WHERE A_LIKE_ID=:a_like_id");
define("DELETE_POSTLIKE", "DELETE FROM article_likes WHERE ARTICLE_ID=:article_id");

// タイトルの長さ
define("TITLE_LENGTH", 150);

// 記事の長さ
define("MESSAGE_LENGTH", 1500);

// 記事/いいね関連クラス
class ArticleAct extends Action
{
  function __construct($mode = -1) {
    if ($mode >= 0) {
      $this->begin($mode);
    }
  }

  // 記事一覧
  function articlelist($page, $searchtext = "")
  {
    $retinfo = array(); {
      // 検索指定時、where句を追加する
      $retlist = array();
      $inWhere = "";

      // 検索指定時
      if ($searchtext != "") {
      // 検索文字指定不可の文字をエスケープ
        $searchtext = Utils::convertSQL($searchtext);
        $inWhere = "WHERE TITLE LIKE '%" . $searchtext . "%' ESCAPE '#'";
      }

      // 記事リスト(指定されたpage 20件)
      $offset = $page * LISTCOUNT;
      $stmt = $this->conn->prepare(sprintf(QUERY_ARTICLE_LIST, $inWhere));
      $stmt->bindValue(":limit", (int)LISTCOUNT, \PDO::PARAM_INT);
      $stmt->bindValue(":offset", (int)$offset, \PDO::PARAM_INT);
      $result = $stmt->execute();
      if ($result) {
        while ($rec =  $stmt->fetch(\PDO::FETCH_ASSOC)) {
          $retlist[] = $rec;
        }
      }
      $retinfo["articlelist"] = $retlist;

      // ユーザ情報
      $retinfo["usermap"] = $this->membermap($retlist, "USER_ID");

      // いいね数
      $retinfo["postlikemap"] = $this->likecountmap($retlist);
    }

    // 記事全体の数
    {
      $stmt = $this->conn->prepare(sprintf(QUERY_ARTICLE_COUNT, $inWhere));
      $result = $stmt->execute();
      $cntrec = $result ? $stmt->fetch(\PDO::FETCH_ASSOC) : NULL;
      $cnt = $cntrec == NULL ? 0 : $cntrec["CNT"];
      $retinfo["cnt"] = $cnt;
      $maxpage = floor(($cnt - 1) / LISTCOUNT);
      $retinfo["MAXPAGE"] = $maxpage;
    }

    // カテゴリ
    $retinfo['category'] = $this->categorymap();
    return $retinfo;
  }

  // 記事単独
  function article($article_id)
  {
    $retinfo = array();
    // 記事
    {
      $stmt = $this->conn->prepare(QUERY_ARTICLE);
      $stmt->bindValue(':article_id', $article_id);
      $result = $stmt->execute();
      $article = $result ? $stmt->fetch(\PDO::FETCH_ASSOC) : NULL;
      $retinfo['article'] = $article;

      // ユーザ情報
      $retinfo['user'] = $this->memberref($article['USER_ID']);
    }

    // いいねした?
    $retinfo['postlike'] = $this->postlike($article_id);

    // いいね数取得
    {
      $stmt = $this->conn->query(sprintf(QUERY_POSTLIKE_COUNTLIST, $article_id));
      $postlikecnt = $stmt->fetch(\PDO::FETCH_ASSOC);
      $retinfo['postlikecnt'] = $postlikecnt == NULL ? 0 : $postlikecnt['LIKECNT'];
    }

    // カテゴリ
    $retinfo['category'] = $this->categorymap();

    return $retinfo;
  }

  // 記事投稿
  function postarticle($title, $message, $cate_id)
  {
    // 登録
    $stmt = $this->conn->prepare(INSERT_ARTICLE);
    $stmt->bindValue(':user_id', $this->member['user_id']);
    $stmt->bindValue(':title', $title);
    $stmt->bindValue(':message', $message);
    $stmt->bindValue(':cate_id', $cate_id);
    $stmt->execute();
  }

  // 記事更新
  function updatearticle($articleid, $title, $message, $cate_id)
  {
    // 更新
    $stmt = $this->conn->prepare(UPDATE_ARTICLE);
    $stmt->bindValue(':article_id', $articleid);
    $stmt->bindValue(':user_id', $this->member['user_id']);
    $stmt->bindValue(':title', $title);
    $stmt->bindValue(':message', $message);
    $stmt->bindValue(':cate_id', $cate_id);
    $stmt->execute();
  }

  // 記事削除
  function deletearticle($articleid)
  {
    try {
      $this->conn->beginTransaction(); {
        $stmt = $this->conn->prepare(DELETE_ARTICLE);
        $stmt->bindValue(':user_id', $this->member['user_id']);
        $stmt->bindValue(':article_id', $articleid);
        $stmt->execute();
      } 
      {
        $stmt = $this->conn->prepare(DELETE_POSTLIKE);
        $stmt->bindValue(':article_id', $articleid);
        $stmt->execute();
      }

      $this->conn->commit();
    } catch (\Exception $e) {
      $this->conn->rollback();
      echo 'error';
    }
  }

  // いいね押下/解除時の処理
  function postlikearticle($articleid)
  {
    $retcode = 'error';
    try {
      $this->conn->beginTransaction();

      // 登録済み?
      $postlike = $this->postlike($articleid);
      if ($postlike == NULL) {
        // 登録
        $stmt = $this->conn->prepare(INSERT_POSTLIKE);
        $stmt->bindValue(':user_id', $this->member['user_id']);
        $stmt->bindValue(':article_id', $articleid);
        $stmt->execute();
        $retcode = 'likeset';
      } else {
        // （現在の状態を反転させて）更新

        $likeflg = $postlike['LIKE_FLG'] == 1 ? 0 : 1;
        $stmt = $this->conn->prepare(UPDATE_POSTLIKE);
        $stmt->bindValue(':like_flg', $likeflg);
        $stmt->bindValue(':a_like_id', $postlike['A_LIKE_ID']);
        $stmt->execute();
        $retcode = $likeflg == 1 ? 'likeset' : 'reset';
      }

      $this->conn->commit();
    } catch (\Exception $e) {
      $this->conn->rollback();
    }
    return $retcode;
  }

  // カテゴリマップ。戻り値は CATE_ID とカテゴリ名の連想配列。
  function categorymap()
  {
    $keymap = array();
    $result = $this->conn->query(QUERY_CATEGORY_LIST);
    if ($result) {
      while ($rec =  $result->fetch(\PDO::FETCH_ASSOC)) {
        $keymap[$rec['CATE_ID']] = $rec['CATEGORY_NAME'];
      }
    }
    return $keymap;
  }

  // カテゴリIdチェック
  function isCategory($cate_id)
  {
    $catmap = $this->categorymap();
    return isset($catmap[$cate_id]);
  }

  // いいね情報取得
  function postlike($article_id)
  {
    if (isset($_SESSION['login_user'])){
      $stmt = $this->conn->prepare(QUERY_POSTLIKE);
      $stmt->bindValue(':article_id', $article_id);
      $stmt->bindValue(':user_id', $this->member['user_id']);
      $result = $stmt->execute();
      $postlike = $result ? $stmt->fetch(\PDO::FETCH_ASSOC) : NULL;
      return $postlike;
    }
  }

  // いいねした記事の一覧取得
  function postlikeArticleList()
  {
    $retinfo = array();

    // 記事一覧
    $retlist = array();
    $stmt = $this->conn->prepare(QUERY_ARTICLE_LIKEME_LIST);
    $stmt->bindValue(':user_id', $this->member['user_id']);
    $stmt->bindValue(':limit', (int)LISTCOUNT_MYPAGE, \PDO::PARAM_INT);
    $result = $stmt->execute();
    if ($result) {
      while ($mem = $stmt->fetch(\PDO::FETCH_ASSOC)) {
        $retlist[] = $mem;
      }
    }
    $retinfo['articlelist'] = $retlist;

    // ユーザ情報
    $retinfo['usermap'] = $this->membermap($retlist, 'USER_ID');

    // いいね数
    $retinfo['postlikemap'] = $this->likecountmap($retlist);

    return $retinfo;
  }

  // いいね数を返す。戻り値は ARTICLE_ID といいね数の連想配列。
  function likecountmap($articles)
  {
    $countmap = array();
    if (count($articles) == 0) {
      return $countmap;
    }

    // where句の作成
    $ids = array();
    foreach ($articles as $art) {
      $ids[] = $art['ARTICLE_ID'];
    }
    $inClause = substr(str_repeat(',?', count($ids)), 1);

    // いいね数取得
    $stmt = $this->conn->prepare(sprintf(QUERY_POSTLIKE_COUNTLIST, $inClause));
    $result = $stmt->execute($ids);
    if ($result) {
      while ($mem = $stmt->fetch(\PDO::FETCH_ASSOC)) {
        $countmap[$mem['ARTICLE_ID']] = $mem['LIKECNT'];
      }
    }
    return $countmap;
  }
}
