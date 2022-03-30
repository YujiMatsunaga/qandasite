<?php require_once "process/complete.php" ?>

<!DOCTYPE html>
<html lang="ja">
<?php require_once "process/head.php" ?>

<body>
  <div class="container">
    <div class="d-flex align-items-center justify-content-center mt-5 fs-3 fw-bold">
      <?php if ($result) : ?>
        <p>
          送信しました。<br>
          お問い合わせありがとうございました。<br>
          ご入力いただいた内容を確認後、３営業日以内に返信致します。<br>
          <a href="contact.php">
            トップへ戻る
          </a><br>
        </p>
      <?php else : ?>
        <p>
          送信に失敗しました。<br>
          しばらくしてもう一度お試しください。<br>
          ご迷惑をおかけして誠に申し訳ございません。<br>
          <a href="contact.php">
            トップへ戻る
          </a><br>
        </p>
      <?php endif; ?>
    </div>
  </div>
</body>

</html>