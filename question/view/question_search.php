<?php 

session_start();

//ファイルの読み込み
  require_once '../../classes/CategoryLogic.php';
  require_once '../../classes/QuestionLogic.php';

//error
$err = [];

$categories = CategoryLogic::getCategory();

//検索ボタン押下時、条件に合った質問を表示
if(isset($_GET['search'])){
  $searchQuestion = QuestionLogic::searchQuestion($_GET);
  if(!$searchQuestion){
    $err['question'] = '質問の読み込みに失敗しました';
	}
}else{// 通常時は、新着の質問を表示する
	$newQuestion = QuestionLogic::newQuestion();
}

?>
<!DOCTYPE HTML>
<html lang="ja">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>質問</title>
<link rel="stylesheet" href="style.css">
<!-- Bootstrap読み込み -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css">
</head>

<body>
	<h1 class="col-xs-6 col-xs-offset-3">質問 Page</h1>

	<a href="question_create.php">質問を投稿する</a>
	<div class="col-xs-6 col-xs-offset-3 well">
		<!-- 検索フォーム  -->
		<form method="get">
			<div class="form-group">
				<input name="keyword" class="form-control" value="<?php echo isset($_GET['name']) ? htmlspecialchars($_GET['name']) : '' ?>">
			</div>
			<div class="form-group">
				<select name="category">
					<option></option>
					<?php foreach($categories as $category){ ?>
						<option 
							value="<?php echo $category['cate_id'] ?>"
							<?php if(isset($_GET['category']) && $category['cate_id'] == $_GET['category']) : ?>
								selected
							<?php endif; ?>
						> 
							<?php echo $category['category_name'] ?>
						</option>
					<?php } ?>
				</select>
			</div>
			<button type="submit" class="btn btn-default" name="search">検索</button>
		</form>
	</div>

	<div class="col-xs-6 col-xs-offset-3">
		<!-- 検索ボタン押下時、取得データを表示する -->
		<?php if(isset($searchQuestion) && count($searchQuestion)): ?>
			<p class="alert alert-success"><?php echo count($searchQuestion) ?>件見つかりました。</p>
			<?php foreach($searchQuestion as $value): ?>
				<div><a href="question_disp.php? question_id=<?php echo $value['question_id']?>">題名：<?php echo htmlspecialchars($value['title']) ?></a></div>
				<div>カテゴリ：<?php echo htmlspecialchars($value['category_name']) ?></div>
				<div>本文：<?php echo htmlspecialchars($value['message']) ?></div>
				<div>名前：<?php echo htmlspecialchars($value['name']) ?></div>
				<div><?php echo htmlspecialchars($value['icon']) ?></div>
				<!-- 更新されていた場合、その日付を優先表示 -->
				<div>
					<?php if (!isset($value['upd_date'])): ?>
							投稿：<?php echo htmlspecialchars($value['answer_date'])  ?>
						<?php else: ?>
							更新：<?php echo htmlspecialchars($value['upd_date']) ?>
						<?php endif; ?>
        </div>
			<?php endforeach; ?>

		<?php elseif (isset($searchQuestion) && count($searchQuestion) == 0): ?>
			<p class="alert alert-danger">検索対象は見つかりませんでした。</p>
		
			<!-- 通常時、新着の質問を表示 -->
		<?php elseif(isset($newQuestion)): ?>
			<div>新着の質問</div>
			<?php foreach($newQuestion as $value): ?>
				<div><a href="question_disp.php? question_id=<?php echo $value['question_id']?>">題名：<?php echo htmlspecialchars($value['title']) ?></a></div>
				<div>カテゴリ：<?php echo htmlspecialchars($value['category_name']) ?></div>
				<div>本文：<?php echo htmlspecialchars($value['message']) ?></div>
				<div>名前：<?php echo htmlspecialchars($value['name']) ?></div>
				<div><?php echo htmlspecialchars($value['icon']) ?></div>
			<div>日時：<?php echo htmlspecialchars($value['post_date']) ?></div>
			<?php endforeach; ?>
		<?php endif; ?>
		
	</div>
	<button type="button" onclick="location.href='../../top/userLogin/login_top.php'">TOP</button>
</body>
</html>
