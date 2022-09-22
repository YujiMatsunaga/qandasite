<?php
//ファイル読み込み
require_once '../../app/Dbconnect.php';

class ArticleLogic
{
    /**
     * 特定ユーザーの記事を表示する
     * @param int $user_id
     * @return bool $result
     */
    public static function userArticle()
    {
    $result = false;
    $arr = [];
    $arr[] = $_SESSION['login_user']['user_id'];

    $sql = 'SELECT article_id, title, message, post_date, upd_date, name, icon, category_name
            FROM article_posts
            INNER JOIN users ON users.user_id = article_posts.user_id 
            INNER JOIN categories ON categories.cate_id = article_posts.cate_id
            WHERE users.user_id = ?
            ORDER BY article_posts.article_id DESC';

    try {
        $stmt = connect()->prepare($sql);
        // SQL実行
        $result = $stmt-> execute($arr);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $data;
    } catch(\Exception $e) {
        // エラーの出力
        echo $e;
        // ログの出力
        error_log($e, 3, '../error.log');
        return $result;
    }
    }
}