<?php
namespace Novus;

require_once 'Action.php';
require_once "Log.php";
require_once 'Utils.php';

// ToDo取得系
define("QUERY_TODO_LIST", "SELECT todo_id,user_id,title,status,remind_date FROM todo WHERE user_id=:user_id");

// ToDo更新系
define("INSERT_TODO", "INSERT INTO todo (user_id,title,status,remind_date) VALUES (:user_id, :title, 'active', :remind_date)");
define("UPDATE_TODO", "UPDATE todo SET title=:title,remind_date=:remind_date WHERE todo_id=:todo_id AND user_id=:user_id");
define("UPDATE_TODO_STATUS", "UPDATE todo SET status=:state WHERE todo_id=:todo_id AND user_id=:user_id");
define("DELETE_TODO", "DELETE FROM todo WHERE todo_id=:todo_id AND user_id=:user_id");

// タイトルの長さ
define("TITLE_LENGTH", 128);

// ToDO関連のクラス
class TodoAct extends Action
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

    // todo一覧取得
    public function get()
    {
        // todo情報取得
        $retInfo = array(); 
        $activeList = array(); //やることリスト
        $finList = array(); //終わったことリスト
        $stmt = $this->conn->prepare(QUERY_TODO_LIST);
        $stmt->bindValue(':user_id', $this->member['user_id']);
        $result = $stmt->execute();
        if ($result) {
            while ($rec =  $stmt->fetch(\PDO::FETCH_ASSOC)) {
                switch ($rec['status']) {
                    case 'active':
                        $activeList[] = $rec;
                        break;
                    case 'finish':
                        $finList[] = $rec;
                        break;
                }
            }
        }
        $retInfo['activeList'] = $activeList;
        $retInfo['finList'] = $finList;
        return $retInfo;
    }

    // 登録
    public function add($title, $remind_date)
    {
        if (!$title || !$remind_date) {
            return;
        }
        $stmt = $this->conn->prepare(INSERT_TODO);
        $stmt->bindValue(':user_id', $this->member['user_id']);
        $stmt->bindValue(':title', $title);
        $stmt->bindValue(':remind_date', $remind_date);
        $stmt->execute();
    }

    // 編集
    public function edit($todo_id, $title, $remind_date)
    {
        if (!$todo_id || !$title || !$remind_date ) {
            return;
        }
        $stmt = $this->conn->prepare(UPDATE_TODO);
        $stmt->bindValue(':todo_id', $todo_id);
        $stmt->bindValue(':user_id', $this->member['user_id']);
        $stmt->bindValue(':title', $title);
        $stmt->bindValue(':remind_date', $remind_date);
        return $stmt->execute();
    }

    // 状態変更
    public function toggle($todo_id, $state)
    {
        if (!$todo_id) {
            return;
        }
        if ($state != 'active' && $state != 'finish') {
            return;
        }
        $stmt = $this->conn->prepare(UPDATE_TODO_STATUS);
        $stmt->bindValue(':todo_id', $todo_id);
        $stmt->bindValue(':user_id', $this->member['user_id']);
        $stmt->bindValue(':state', $state);
        return $stmt->execute();
    }

    // 削除
    public function delete($todo_id)
    {
        if (!$todo_id) {
            return;
        }
        $stmt = $this->conn->prepare(DELETE_TODO);
        $stmt->bindValue(':todo_id', $todo_id);
        $stmt->bindValue(':user_id', $this->member['user_id']);
        return $stmt->execute();
    }

    // ページ表示がないファイルは、mode=1で呼ぶ
    public function printFooter($mode = 0) {
        if ($mode === 0) {
            echo '<div class="row m-2">';
            echo '<div class="col-sm-8">';
            echo '<a class="btn btn-success m-2" href="' . DOMAIN . '/public/userLogin/home.php">ホーム画面へ</a>';
            echo '<a href="'. DOMAIN.'/public/mypage/index.php" class="btn btn-primary m-2">マイページへ</a>';
            echo '</div>';
            echo '</div>';
        }
        echo '</div>';
        echo '<hr><footer class="h-10">';
        echo '<div class="footer-item text-center">';
        echo '<h4>novus</h4>';
        echo '<ul class="nav nav-pills nav-fill">';
        echo '<li class="nav-item">';
        echo '<a class="nav-link small" href="' . DOMAIN . '/public/article/index.php">記事</a>';
        echo '</li>';
        echo '<li class="nav-item">';
        echo '<a class="nav-link small" href="' . DOMAIN . '/public/question/index.php">質問</a>';
        echo '</li>';
        echo '<li class="nav-item">';
        echo '<a class="nav-link small" href="' . DOMAIN . '/public/bookApi/index.php">本検索</a>';
        echo '</li>';
        echo '<li class="nav-item">';
        echo '<a class="nav-link small" href="' . DOMAIN . '/public/contact/index.php">お問い合わせ</a>';
        echo '</li>';
        echo '</ul>';
        echo '</div>';
        echo '<p class="text-center small mt-2">Copyright (c) HTMQ All Rights Reserved.</p>';
        echo '</footer>';
        echo '</body>';
        echo '</html>';
    }
}
