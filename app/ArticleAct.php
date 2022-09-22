<?php
namespace Novus;

require_once "Action.php";
require_once "Log.php";
require_once "Utils.php";

// 記事取得
define("QUERY_ARTICLE_LIST", "SELECT article_id,user_id,title,message,post_date,upd_date,cate_id FROM article_posts %s ORDER BY upd_date DESC LIMIT :limit offset :offset");
define("QUERY_ARTICLE", "SELECT article_id,user_id,title,message,post_date,upd_date,cate_id FROM article_posts WHERE article_id=:article_id");
define("QUERY_ARTICLE_COUNT", "SELECT COUNT(article_id) AS cnt FROM article_posts %s");
define("QUERY_ARTICLE_LIKEME_LIST", "SELECT A.article_id,A.user_id,A.title,A.message,A.post_date,A.upd_date,A.cate_id FROM article_posts A, article_likes B WHERE A.article_id = B.article_id AND B.user_id=:user_id AND B.like_flg=1 ORDER BY A.upd_date DESC LIMIT :limit");

// 記事更新系
define("INSERT_ARTICLE", "INSERT INTO article_posts (user_id,title,message,post_date,upd_date,cate_id) VALUES (:user_id, :title, :message, now(), now(), :cate_id)");
define("UPDATE_ARTICLE", "UPDATE article_posts SET title=:title,message=:message,cate_id=:cate_id,upd_date=now() WHERE article_id=:article_id AND user_id=:user_id");
define("DELETE_ARTICLE", "DELETE FROM article_posts WHERE article_id=:article_id AND user_id=:user_id");

// カテゴリ一覧
define("QUERY_CATEGORY_LIST", "SELECT cate_id, category_name, color FROM categories");

// いいね取得
define("QUERY_POSTLIKE", "SELECT a_like_id,user_id,article_id,like_flg FROM article_likes WHERE article_id=:article_id AND user_id=:user_id");
define("QUERY_POSTLIKE_COUNTLIST", "SELECT article_id,COUNT(a_like_id) AS like_cnt FROM article_likes WHERE article_id IN (%s) AND like_flg=1 GROUP BY article_id");

// いいね更新
define("INSERT_POSTLIKE", "INSERT INTO article_likes (user_id, article_id, like_flg) VALUES (:user_id, :article_id, 1)");
define("UPDATE_POSTLIKE", "UPDATE article_likes SET like_flg=:like_flg WHERE a_like_id=:a_like_id");
define("DELETE_POSTLIKE", "DELETE FROM article_likes WHERE article_id=:article_id");

// レベル, 経験値取得
define("QUERY_LEVEL", "SELECT level, exp FROM users WHERE user_id=:user_id");

// レベル, 経験値更新
define("UPDATE_LEVEL", "UPDATE users SET exp=:exp, level=:level WHERE user_id=:user_id");

//経験値更新
define("UPDATE_EXP", "UPDATE users SET exp=:exp WHERE user_id=:user_id");

// 記事/いいね関連クラス
class ArticleAct extends Action
{
    // $mode>=0の場合、明示的にbeginを呼び出す
    public function __construct($mode = -1) {
        try {
            if ($mode >= 0) {
                $this->begin($mode);
            }
        }catch (\Exception $e) {
            Log::error($e);
            echo $e;
        }
    }

    // 記事一覧
    public function articleList($page, $searchText = "", $searchCategory = "")
    {
        try {
            $inWhere = "";
            // 検索指定時
            if ($searchText !== "" || $searchCategory !== "") {
                $inWhere .= "WHERE ";
            }
        
            if ($searchText !== "") {
                // 検索文字指定不可の文字をエスケープ
                $searchText = Utils::convertSQL($searchText);
                $inWhere .= "title LIKE :title ESCAPE '#' ";
            }
        
            if ($searchCategory !== "") {
                if ($searchText !== "") {
                    $inWhere .= "AND ";
                }
                $inWhere .= "cate_id = :category";
            }
        
            // retrieve:取得
            $retInfo = [
                'category' => $this->categoryMap(),
                'page' => $page,
                'total' => 0,
                'maxPage' => 0,
                'articleList' => [],
                'userMap' => [],
                'postLikeMap' => [],
            ];
            $retInfo['category'] = $this->categoryMap();
        
            // 記事全体の数
            $retInfo["total"] = $this->countArticleListTotal($inWhere, $searchText, $searchCategory);
            if ($retInfo["total"] <= 0) {
                return $retInfo;
            }
            $retInfo["maxPage"] = ceil(($retInfo["total"]) / LISTCOUNT);
        
            if ($page > $retInfo["maxPage"]) {
                $retInfo["page"] = $retInfo["maxPage"];
            } else {
                $retInfo["page"] = $page;
            }
        
            // 記事リスト(指定されたpage 20件)
            $retInfo["articleList"] = $this->searchArticleList($inWhere, $searchText, $searchCategory, $retInfo["page"]);
        
            // ユーザ情報
            $retInfo["userMap"] = $this->memberMap($retInfo["articleList"], "user_id");
        
            // いいね数
            $retInfo["postLikeMap"] = $this->likeCountMap($retInfo["articleList"]);
        
            return $retInfo;
        } catch (\Exception $e) {
            Log::error($e);
            echo $e;
        }
    }

    private function searchArticleList($inWhere, $searchText, $searchCategory, $page)
    {
        // 記事リスト(指定されたpage 20件)
        $stmt = $this->conn->prepare(sprintf(QUERY_ARTICLE_LIST, $inWhere));

        if ($searchText != "") {
            $stmt->bindValue(":title", "%$searchText%", \PDO::PARAM_STR);
        }
    
        if ($searchCategory != "") {
            $stmt->bindValue(":category", (int)$searchCategory, \PDO::PARAM_INT);
        }

        $offset = ($page - 1) * LISTCOUNT;
        $stmt->bindValue(":offset", (int)$offset, \PDO::PARAM_INT);
        $stmt->bindValue(":limit", (int)LISTCOUNT, \PDO::PARAM_INT);
        Log::sql($stmt->queryString, [
            ':title' => "%$searchText%",
            ':category' => (int)$searchCategory,
            ':limit' => (int)LISTCOUNT,
            ':offset' => (int)$offset,
        ]);

        $result = $stmt->execute();

        if ($result) {
            $retList = [];
            while ($rec =  $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $retList[] = $rec;
            }
            return $retList;
        }
        return [];
    }

    private function countArticleListTotal($inWhere, $searchText, $searchCategory)
    {
        // 記事全体の数
        $stmt = $this->conn->prepare(sprintf(QUERY_ARTICLE_COUNT, $inWhere));
        if ($searchText !== "") {
            $stmt->bindValue(":title", "%$searchText%", \PDO::PARAM_STR);
        }
    
        if ($searchCategory !== "") {
            $stmt->bindValue(":category", (int)$searchCategory, \PDO::PARAM_INT);
        }
    
        Log::sql($stmt->queryString, [
            ':title' => "%$searchText%",
            ':category' => (int)$searchCategory,
        ]);
    
        $result = $stmt->execute();
        $cntrec = $result ? $stmt->fetch(\PDO::FETCH_ASSOC) : null;
        return $cntrec == null ? 0 : $cntrec["cnt"];
    }

    // 記事単独
    public function article($article_id)
    {
        $retInfo = array();

        // 記事
        {
            $stmt = $this->conn->prepare(QUERY_ARTICLE);
            $stmt->bindValue(':article_id', $article_id);
            $result = $stmt->execute();
            $article = $result ? $stmt->fetch(\PDO::FETCH_ASSOC) : null; //三項演算子
            $retInfo['article'] = $article;

            // ユーザ情報
            $retInfo['user'] = $this->memberRef($article['user_id']);
        }

            // いいねした?
            $retInfo['postLike'] = $this->postLike($article_id);

        // いいね数取得
        {
            $stmt = $this->conn->query(sprintf(QUERY_POSTLIKE_COUNTLIST, $article_id));
            $postLikeCnt = $stmt->fetch(\PDO::FETCH_ASSOC);
            $retInfo['postLikeCnt'] = $postLikeCnt == null ? 0 : $postLikeCnt['like_cnt'];
        }

        // カテゴリ
        $retInfo['category'] = $this->categoryMap();

        return $retInfo;
    }

    // 記事投稿
    public function create($title, $message, $cate_id)
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
    public function update($article_id, $title, $message, $cate_id)
    {
        // 更新
        $stmt = $this->conn->prepare(UPDATE_ARTICLE);
        $stmt->bindValue(':article_id', $article_id);
        $stmt->bindValue(':user_id', $this->member['user_id']);
        $stmt->bindValue(':title', $title);
        $stmt->bindValue(':message', $message);
        $stmt->bindValue(':cate_id', $cate_id);
        $stmt->execute();
    }

    // 記事削除
    public function delete($article_id)
    {
        try {
            $this->conn->beginTransaction();
            {
                $stmt = $this->conn->prepare(DELETE_ARTICLE);
                $stmt->bindValue(':user_id', $this->member['user_id']);
                $stmt->bindValue(':article_id', $article_id);
                $stmt->execute();
            }
        
            {
            $stmt = $this->conn->prepare(DELETE_POSTLIKE);
            $stmt->bindValue(':article_id', $article_id);
            $stmt->execute();
            }
        
            $this->conn->commit();
        } catch (\Exception $e) {
            $this->conn->rollback();
            echo $e;
        }
    }

    // いいね押下/解除時の処理
    public function postLikeArticle($article_id)
    {
        $retcode = 'error';
        try {
            $this->conn->beginTransaction();

            // 登録済み?
            $postLike = $this->postLike($article_id);
            if ($postLike == null) {
                // 登録
                $stmt = $this->conn->prepare(INSERT_POSTLIKE);
                $stmt->bindValue(':user_id', $this->member['user_id']);
                $stmt->bindValue(':article_id', $article_id);
                $stmt->execute();
                $retcode = 'likeset';
            } else {
                // （現在の状態を反転させて）更新
        $likeflg = $postLike['like_flg'] == 1 ? 0 : 1; //三項演算子
        $stmt = $this->conn->prepare(UPDATE_POSTLIKE);
                $stmt->bindValue(':like_flg', $likeflg);
                $stmt->bindValue(':a_like_id', $postLike['a_like_id']);
                $stmt->execute();
                $retcode = $likeflg == 1 ? 'likeset' : 'reset'; //三項演算子
            }

            $this->conn->commit();
        } catch (\Exception $e) {
            $this->conn->rollback();
        }
        return $retcode;
    }

    //経験値付与処理
    public function addEXP($user_id, $plus_exp)
    {
        try {
            $stmt = $this->conn->prepare(QUERY_LEVEL);
            $stmt->bindValue(':user_id', $user_id);
            $data = $stmt->execute();
            if (! $data) {
                return null;
            }
            $data = $stmt->fetch(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            echo $e;
        }
        // QUERY_LEVELで取得した経験値とレベルを定義
        $exp = $data['exp'];
        $level = $data['level'];

        $new_exp = $exp + $plus_exp; // 合算経験値
        $new_level = floor($new_exp / 100) + 1; //合算レベル

        // 取得レベルと合算レベルの比較
        if ($level < $new_level) {
            try {
                $stmt = $this->conn->prepare(UPDATE_LEVEL);// 経験値とレベルを更新
                $stmt->bindValue(':user_id', $user_id);
                $stmt->bindValue(':exp', $new_exp);
                $stmt->bindValue(':level', $new_level);
                $data = $stmt-> execute();
                $_SESSION['login_user']['level'] = $new_level;
                $_SESSION['login_user']['exp'] = $new_exp;
                return $data;
            } catch (\Exception $e) {
                echo $e;
            }
        } else {
            // レベルに変化なし
            try {
                $stmt = $this->conn->prepare(UPDATE_EXP);// 経験値のみ付与
                $stmt->bindValue(':user_id', $user_id);
                $stmt->bindValue(':exp', $new_exp);
                $data = $stmt-> execute();
                $_SESSION['login_user']['level'] = $new_level;
                $_SESSION['login_user']['exp'] = $new_exp;
                return $data;
            } catch (\Exception $e) {
                echo $e;
            }
        }
    }


    // カテゴリマップ。戻り値は cate_id とカテゴリ名の連想配列。
    public function categoryMap()
    {
        $keymap = array();
        $result = $this->conn->query(QUERY_CATEGORY_LIST);
        if ($result) {
            while ($rec =  $result->fetch(\PDO::FETCH_ASSOC)) {
                $keymap[$rec['cate_id']] = $rec['category_name'];
            }
        }
        return $keymap;
    }

    // カテゴリカラーマップ。戻り値は cate_id とカテゴリカラーの連想配列。
    function categoryColorMap()
    {
        $keymap = array();
        $result = $this->conn->query(QUERY_CATEGORY_LIST);
        if ($result) {
            while ($rec =  $result->fetch(\PDO::FETCH_ASSOC)) {
                $keymap[$rec['cate_id']] = $rec['color'];
            }
        }
        return $keymap;
    }

    // カテゴリIdチェック
    public function isCategory($cate_id)
    {
        $catmap = $this->categoryMap();
        return isset($catmap[$cate_id]);
    }

    // いいね情報取得
    public function postLike($article_id)
    {
        if (isset($_SESSION['login_user'])) {
            $stmt = $this->conn->prepare(QUERY_POSTLIKE);
            $stmt->bindValue(':article_id', $article_id);
            $stmt->bindValue(':user_id', $this->member['user_id']);
            $result = $stmt->execute();
            $postLike = $result ? $stmt->fetch(\PDO::FETCH_ASSOC) : null;
            return $postLike;
        }
    }

    // いいねした記事の一覧取得
    public function postlikeArticleList()
    {
        $retInfo = array();

        // 記事一覧
        $retList = array();
        $stmt = $this->conn->prepare(QUERY_ARTICLE_LIKEME_LIST);
        $stmt->bindValue(':user_id', $this->member['user_id']);
        $stmt->bindValue(':limit', (int)LISTCOUNT_MYPAGE, \PDO::PARAM_INT);
        $result = $stmt->execute();
        if ($result) {
            while ($mem = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $retList[] = $mem;
            }
        }
        $retInfo['articleList'] = $retList;

        // ユーザ情報
        $retInfo['userMap'] = $this->memberMap($retList, 'user_id');

        // いいね数
        $retInfo['postLikeMap'] = $this->likeCountMap($retList);

        return $retInfo;
    }

    // いいね数を返す。戻り値は article_id といいね数の連想配列。
    public function likeCountMap($articles)
    {
        $countmap = array();
        if (count($articles) == 0) {
            return $countmap;
        }

        // where句の作成
        $ids = array();
        foreach ($articles as $art) {
            $ids[] = $art['article_id'];
        }
        $inClause = substr(str_repeat(',?', count($ids)), 1);

        // いいね数取得
        $stmt = $this->conn->prepare(sprintf(QUERY_POSTLIKE_COUNTLIST, $inClause));
        $result = $stmt->execute($ids);
        if ($result) {
            while ($mem = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $countmap[$mem['article_id']] = $mem['like_cnt'];
            }
        }
        return $countmap;
    }
}
