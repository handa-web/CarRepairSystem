<?php

include_once('./in-init.php');

$client_list = [];
$sql = "SELECT * FROM mst_client WHERE client_stop = 0";
if ($res = sql($sql)) {
	foreach ($res as $row) {
		$client_list[$row['client_id']] = $row['client_name'].($row['client_stop'] ? '【停止中】' : '');
	}
}

$rep_size_list = [];
$sql = "SELECT * FROM mst_rep_size WHERE rep_size_stop = 0";
if ($res = sql($sql)) {
	foreach ($res as $row) {
		$rep_size_list[$row['rep_size_id']] = $row['rep_size_name'].($row['rep_size_stop'] ? '【停止中】' : '');
	}
}

$rep_shape_list = [];
$sql = "SELECT * FROM mst_rep_shape WHERE rep_shape_stop = 0";
if ($res = sql($sql)) {
	foreach ($res as $row) {
		$rep_shape_list[$row['rep_shape_id']] = $row['rep_shape_name'].($row['rep_shape_stop'] ? '【停止中】' : '');
	}
}

$rep_parts_list = [];
$sql = "SELECT * FROM mst_rep_parts WHERE rep_parts_stop = 0";
if ($res = sql($sql)) {
	foreach ($res as $row) {
		$rep_parts_list[$row['rep_parts_id']] = $row['rep_parts_name'].($row['rep_parts_stop'] ? '【停止中】' : '');
	}
}

$rep_level_list = [];
$sql = "SELECT * FROM mst_rep_level WHERE rep_level_stop = 0";
if ($res = sql($sql)) {
	foreach ($res as $row) {
		$rep_level_list[$row['rep_level_id']] = $row['rep_level_name'].($row['rep_level_stop'] ? '【停止中】' : '');
	}
}

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
			<li><a href="./resv-list.php">予約管理</a></li>
			<li>新規登録</li>
		</ol>
	</nav>

	<form id="resv_form">
		<input type="hidden" name="csrf_token" value="<?=h($csrf_token);?>">
		<input type="hidden" name="new" value="1">

		<p class="list_title">新規登録</p>
		
		<p class="war_text" style="display: none;"></p>

		<div class="edit_list">
			<div class="item">
				<div class="title">分類選択</div>
				<div class="data in">
					<select name="rep_size_id" class="on">
						<?php foreach ($rep_size_list as $id => $name) { ?>
						<option value="<?=h($id);?>"><?=h($name);?></option>
						<?php } ?>
					</select>
				</div>
			</div>
			<div class="item">
				<div class="title">型選択</div>
				<div class="data in">
					<select name="rep_size_id" class="on">
						<?php foreach ($rep_shape_list as $id => $name) { ?>
						<option value="<?=h($iid);?>"><?=h($name);?></option>
						<?php } ?>
					</select>
				</div>
			</div>
			<div class="item">
				<div class="title">修理日程</div>
				<div class="data">新規登録後に設定可能</div>
			</div>
			<div class="item">
				<div class="title">顧客名</div>
				<div class="data in">
					<select name="client_id" class="on" required>
						<option value=""></option>
						<?php foreach ($client_list as $id => $name) { ?>
						<option value="<?=h($id);?>"><?=h($name);?></option>
						<?php } ?>
					</select>
				</div>
			</div>
			<div class="item">
				<div class="title">予約者名（任意）</div>
				<div class="data in"><input type="text" name="resv_client_name" class="on" maxlength="255"></div>
			</div>
			<div class="item">
				<div class="title">メールアドレス</div>
				<div class="data in"><input type="email" name="resv_client_mail" class="on" maxlength="255" value="" required></div>
			</div>
			<div class="item">
				<div class="title">電話番号（任意）</div>
				<div class="data in"><input type="tel" name="resv_client_tel" class="on" maxlength="13"></div>
			</div>
			<div class="item">
				<div class="title">備考（任意）</div>
				<div class="data in"><textarea name="resv_text" rows="5" maxlength="1000" class="on"></textarea></div>
			</div>
			<div class="item">
				<div class="title">メモ（任意）</div>
				<div class="data in"><textarea name="resv_memo" rows="5" maxlength="1000" class="on"></textarea></div>
			</div>
		</div>

		<ul class="button_list">
			<li><button type="button" class="button back" onclick="history.back();">戻る</button></li>
			<li><button type="submit" class="button edit">登録</button></li>
		</ul>

	</form>

</main>

</div>

<?php include_once('./in-footer.php'); ?>
<script src="./js/resv.js"></script>
</body>
</html>
