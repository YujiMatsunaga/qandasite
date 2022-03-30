<?php require_once "process/confirm.php" ?>

<!DOCTYPE html>
<html lang="ja">

<?php require_once "process/head.php" ?>

<body>
  <div class="container">
    <div class="mt-5 col-8 offset-2">
      <h2 class="text-center fw-bold fs-2">入力内容の確認</h2>
      <p class="mt-4">
        以下の内容でよろしければ「送信する」を選択してください。<br>
        内容を変更する場合は「戻る」を選択して入力画面にお戻りください。
      </p>
      <div class="table-responsive confirm_table">
        <table class="table table-bordered">
          <caption>ご入力内容</caption>
          <tr>
            <th>お名前</th>
            <td><?php echo h($name); ?></td>
          </tr>
          <tr>
            <th>Email</th>
            <td><?php echo h($email); ?></td>
          </tr>
          <tr>
            <th>タイトル</th>
            <td><?php echo h($title); ?></td>
          </tr>
          <tr>
            <th>お問い合わせ内容</th>
            <td class="text-break"><?php echo nl2br(h($contents)); ?></td>
          </tr>
        </table>
      </div>
      <div class="offset-5">
        <form action="contact.php" method="post" class="confirm">
          <button type="submit" class="btn btn-secondary">戻る</button>
        </form>
        <form action="complete.php" method="post" class="confirm">
          <!-- 完了ページへ渡すトークンの隠しフィールド -->
          <input type="hidden" name="ticket" value="<?php echo h($ticket); ?>">
          <button type="submit" class="btn btn-success">送信する</button>
        </form>
      </div>
    </div>
  </div>
</body>

</html>