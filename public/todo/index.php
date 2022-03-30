<?php
require_once "../../app/TodoAct.php";
require_once '../../app/Token.php';
require_once '../../app/Utils.php';

use Qanda\TodoAct;
use Qanda\Token;
use Qanda\Utils;

// ToDo一覧取得
$act = new ToDoAct();
$retinfo = $act->begin();
$retinfo = $act->get();

// Token生成
Token::create();

// 7日後（リマインドの日付の初期値に使用）
$remainddt = Utils::addDay(7, 1);

// エラーコード
$errid = filter_input(INPUT_GET, 'errid');

?>

<div class="row m-2">
  <div class="col-sm-8"></div>
  <div class="col-sm-4"><?php echo $act->getMemberName(); ?>さん</div>
</div>

<h5>Todo</h5>
<form method="POST" class="form-horizontal" name="qandaAddForm" action="<?php echo DOMAIN . '/public/todo/process/add.php'; ?>">
  <div class="row m-2">
    <div class="col-sm-6">
      <input type="text" class="form-control" id="newtodotitle" name="newtodotitle" value="" maxlength="64" />
    </div>
    <div class="col-sm-3">
      <input type="text" class="form-control dateTimePickerForm" id="newtododt" name="newtododt" maxlength="16" value="<?php echo $remainddt; ?>" />
    </div>
    <div class="col-sm-3">
      <input type="hidden" name="token" value="<?php echo $_SESSION["token"]; ?>" />
      <div class="btn btn-primary" onClick="onAddToDo();">ToDoリスト</div>
    </div>
  </div>
</form>

<h5>やること:</h5>
<table class="table table-striped">
  <tr>
    <th style="width: 5%">#</th>
    <th style="width: 45%">Task</th>
    <th style="width: 20%">DeadLine</th>
    <th style="width: 30%">Actions</th>
  </tr>
  <?php
  $idx = 1;
  foreach ($retinfo['activelist'] as $active) {
    $escapetitle = Utils::h($active['TITLE']);
    echo '<tr><td>' . $idx . '</td><td>' . $escapetitle . '</td><td>'
      . Utils::dayFormat($active['REMIND_DATE']) . '</td><td>';
    echo '<span class="btn btn-link done" todoid="' . $active['TODO_ID'] . '">Done</span>';
    echo '<span class="btn btn-link edit" todoid="' . $active['TODO_ID'] . '" todotitle="' . $escapetitle
      . '" tododt="' . Utils::dayFormat($active['REMIND_DATE']) . '">Edit</span>';
    echo '<span class="btn btn-link delete" todoid="' . $active['TODO_ID'] . '">Delete</span>';
    echo '</td></tr>';
    $idx++;
  }
  if (count($retinfo['activelist']) == 0) {
    echo '<tr><td colspan="4" class="text-center">1件もありません</td></tr>';
  }
  ?>
</table>

<h5>終わったこと:</h5>
<table class="table table-striped">
  <tr>
    <th style="width: 5%">#</th>
    <th style="width: 45%">Task</th>
    <th style="width: 20%">DeadLine</th>
    <th style="width: 30%">Actions</th>
  </tr>
  <?php

  $idx = 1;
  foreach ($retinfo['finlist'] as $fin) {
    $escapetitle = Utils::h($fin['TITLE']);
    echo '<tr><td>' . $idx . '</td><td>' . $escapetitle
      . '</td><td>' . Utils::dayFormat($fin['REMIND_DATE']) . '</td><td>';
    echo '<span class="btn btn-link return" todoid="' . $fin['TODO_ID'] . '">Return</span>';
    echo '<span class="btn btn-link edit" todoid="' . $fin['TODO_ID'] . '" todotitle="' . $escapetitle
      . '" tododt="' . Utils::dayFormat($fin['REMIND_DATE']) . '">Edit</span>';
    echo '<span class="btn btn-link delete" todoid="' . $fin['TODO_ID'] . '">Delete</span>';
    echo '</td></tr>';
    $idx++;
  }
  if (count($retinfo['finlist']) == 0) {
    echo '<tr><td colspan="4" class="text-center">1件もありません</td></tr>';
  }
  ?>
</table>


<!-- 編集ダイアログ  -->
<div class="modal fade" id="demoNormalModal" tabindex="-1" role="dialog" aria-labelledby="modal" aria-hidden="true">
  <form method="POST" class="form-horizontal" name="qandaEditForm" action="<?php echo DOMAIN . '/public/todo/process/edit.php'; ?>">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="demoModalTitle">ToDo編集</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="row m-2">
            <div class="col-sm-8">
              <input type="text" class="form-control" id="edittodotitle" name="edittodotitle" value="" maxlength="64" />
            </div>
            <div class="col-sm-4">
              <input type="text" class="form-control dateTimePickerForm" id="edittododt" name="edittododt" maxlength="16" value="" />
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">閉じる</button>
          <button type="button" class="btn btn-primary" id="updateToDo">更新</button>
          <input type="hidden" id="edittodoid" name="edittodoid" value="" />
          <input type="hidden" name="token" value="<?php echo $_SESSION["token"]; ?>" />
        </div>
      </div>
    </div>
  </form>
</div>
<!-- /編集ダイアログ  -->

<script type="text/javascript">
  // ToDo追加ボタンを押した
  function onAddToDo() {
    var $newtodotitle = document.getElementById('newtodotitle').value;
    if (isEmpty($newtodotitle)) {
      onShow('ToDoを入力してください');
      return;
    }
    // 送信
    document.qandaAddForm.submit();
  }

  // Editボタンを押したとき
  function onEdit() {
    // 編集前の値を取得
    var todoid = $(this).attr('todoid');
    var title =  $(this).attr('todotitle');
    var tododt = $(this).attr('tododt');

    // エディットに設定
    $('#edittodotitle').val(title);
    $('#edittododt').val(tododt);
    $('#edittodoid').val(todoid);

    // 編集モーダル表示(ブートストラップ)
    $('#demoNormalModal').modal('show');

    // 編集が確定したとき
    $('#updateToDo').off().click(function() {
      var $edittodotitle = document.getElementById('edittodotitle').value;
      if (isEmpty($edittodotitle)) {
        onShow('ToDoを入力してください');
        return;
      }

      // 送信
      document.qandaEditForm.submit();
    });
  }

  // 削除ボタンを押した
  function onDelete() {
    var todoid = $(this).attr('todoid');
    swal({
      text: '削除してもよろしいですか？',
      icon: 'warning',
      buttons: true,
      dangerMode: true
    }).then(function(isConfirm) {
      if (isConfirm) {
        var $data = 'todoid=' + todoid + '&token=<?php echo $_SESSION["token"]; ?>';
        // ajax呼び出し
        formapiCallback('todo/process/delete.php', $data, function(result) {
          // 一覧を再読み込み
          jumpapi('todo/index.php');
        });
      }
    });
  }

  // ステータス更新(done)
  function toggleFinish() {
    var todoid = $(this).attr('todoid');
    var $data = 'state=finish&todoid=' + todoid + '&token=<?php echo $_SESSION["token"]; ?>';
    // ajax呼び出し
    formapiCallback('todo/process/toggle.php', $data, function(result) {
      // 一覧を再読み込み
      jumpapi('todo/index.php');
    });
  }
  // ステータス更新(return)
  function toggleActive() {
    var todoid = $(this).attr('todoid');
    var $data = 'state=active&todoid=' + todoid + '&token=<?php echo $_SESSION["token"]; ?>';
    // ajax呼び出し
    formapiCallback('todo/process/toggle.php', $data, function(result) {
      // 一覧を再読み込み
      jumpapi('todo/index.php');
    });
  }

  // 初期化処理
  $(function() {
    // 日付フィールドの初期化
    $('.dateTimePickerForm').datetimepicker({
      formatTime: 'H:i',
      formatDate: 'd.m.Y',
      language: 'ja'
    });

    // ボタン/リンク
    $('.btn.btn-link.done').click(toggleFinish);
    $('.btn.btn-link.return').click(toggleActive);
    $('.btn.btn-link.edit').click(onEdit);
    $('.btn.btn-link.delete').click(onDelete);

    <?php
    if ($errid) {
      // 更新に失敗したとき、リダイレクトして todo/index.phpに引数指定で呼び出されるので
      // ダイアログ表示
      if ($errid == 'invalidtitle') {
        echo 'swal("タイトルに誤りがあります");';
      } else if ($errid == 'invalidformatdt') {
        echo 'swal("リマインドの日付に誤りがあります");';
      }
    }
    ?>

  });
</script>

<?php
$act->end();
?>