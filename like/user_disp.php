<?php

//ユーザーIDと投稿IDを元にいいね値の重複チェックを行っています
function check_favolite_duplicate($user_id,$post_id){
    $dsn='mysql:dbname=db;host=localhost;charset=utf8';
    $user='root';
    $password='';
    $dbh=new PDO($dsn,$user,$password);
    $sql = "SELECT *
            FROM favorite
            WHERE user_id = :user_id AND post_id = :post_id";
    $stmt = $dbh->prepare($sql);
    $stmt->execute(array(':user_id' => $user_id ,
                         ':post_id' => $post_id));
    $favorite = $stmt->fetch();
    return $favorite;
}

?>

<form class="favorite_count" action="#" method="post">
        <input type="hidden" name="post_id">
        <button type="button" name="favorite" class="favorite_btn">
        <?php if (!check_favolite_duplicate($_SESSION['user_id'],$post_id)): ?>
          いいね
        <?php else: ?>
          いいね解除
        <?php endif; ?>
        </button>
</form>