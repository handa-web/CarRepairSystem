<?php

include_once('./in-init.php');

$csrf_token = getCsrfToken();

?>
<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>SYSTEM</title>
	<!-- css -->
	<link rel="stylesheet" href="./css/destyle.css">
	<link rel="stylesheet" href="./css/base.css">
	<!-- other -->
	<meta name="robots" content="noindex, nofollow">
	<link rel="icon" href="../favicon.ico">
</head>
<body>
<?php include_once('./in-header.php'); ?>

<div class="main_wrap">

<?php include_once('./in-aside.php') ?>

<main id="main">

	<nav class="bread">
		<ol>
			<li><a href="./">TOP</a></li>
			<li><a href="./plan-list.php">予定管理</a></li>
			<li>新規登録</li>
		</ol>
	</nav>

	<form id="plan_form">
		<input type="hidden" name="csrf_token" value="<?=h($csrf_token);?>">
		<input type="hidden" name="new" value="1">

		<p class="war_text" style="display: none;"></p>

		<p class="list_title">予定 新規登録</p>
		<div class="edit_list">
			<div class="item">
				<div class="title">予定名</div>
				<div class="data in"><input type="text" name="plan_name" class="on" required></div>
			</div>
			<div class="item">
				<div class="title">メモ</div>
				<div class="data in"><textarea name="plan_memo" rows="5" class="on"></textarea></div>
			</div>
			<div class="item">
				<div class="title">予定日程</div>
				<div class="data date_list">新規登録後に設定可能</div>
			</div>
		</div>

		<ul class="button_list">
			<li><button type="button" class="button back" onclick="history.back();">戻る</button></li>
			<li><button type="submit" class="button edit">新規登録</button></li>
		</ul>

	</form>

</main>

</div>

<?php include_once('./in-footer.php'); ?>
<script src="./js/plan.js"></script>
</body>
</html>
