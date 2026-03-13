<?php

include_once('./in-init.php');

ini_set('display_errors', 1);

$rep_size_list = [];
$sql = "SELECT * FROM mst_rep_size WHERE rep_size_stop = 0";
if ($res = sql($sql)) $rep_size_list = $res;

$rep_shape_list = [];
$sql = "SELECT * FROM mst_rep_shape WHERE rep_shape_stop = 0";
if ($res = sql($sql)) $rep_shape_list = $res;

$rep_parts_list = [];
$sql = "SELECT * FROM mst_rep_parts WHERE rep_parts_stop = 0";
if ($res = sql($sql)) $rep_parts_list = $res;

$rep_level_list = [];
$sql = "SELECT * FROM mst_rep_level WHERE rep_level_stop = 0";
if ($res = sql($sql)) $rep_level_list = $res;

$client_list = [];
$sql = "SELECT * FROM mst_client WHERE client_stop = 0";
if ($res = sql($sql)) {
	foreach ($res as $row) {
		$client_list[(int)$row['client_id']] = $row['client_name'];
	}
}

$date = createDateTime();
$in_date = $date->format('Y-m-d');
$out_date = $date->modify('+7 day')->format('Y-m-d');

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

		<p class="list_title">予約内容確認</p>
		<div class="edit_list">
			<div class="item">
				<div class="title">車両ナンバー（数字４桁）</div>
				<div class="data in"><input type="text" name="resv_car_number" value="<?=h(isset($dat['resv_car_number']) ? $dat['resv_car_number'] : '');?>" class="on" maxlength="4" required></div>
			</div>
			<div class="item">
				<div class="title">入庫日</div>
				<div class="data in"><input type="date" name="in_date" value="<?=h($in_date);?>" class="on"></div>
			</div>
			<div class="item">
				<div class="title">納車予定日</div>
				<div class="data in"><input type="date" name="out_date" value="<?=h($out_date);?>" class="on"></div>
			</div>
			<div class="item">
				<div class="title">修理日程</div>
				<div class="data">新規登録後に設定可能</div>
			</div>
		</div>

		<p class="list_title"> 修理内容確認</p>
		<div class="edit_list">
			<div class="item">
				<div class="title">分類選択</div>
				<div class="data">
					<select name="rep_size_id" required>
						<option value="0">選択してください</option>
						<?php foreach ($rep_size_list as $row) {?>
						<option value="<?=h($row['rep_size_id']);?>"><?=h($row['rep_size_name']);?></option>
						<?php } ?>
					</select>
				</div>
			</div>
			<div class="item">
				<div class="title">型選択</div>
				<div class="data">
					<select name="rep_shape_id" required>
						<option value="0">選択してください</option>
						<?php foreach ($rep_shape_list as $row) {?>
						<option value="<?=h($row['rep_shape_id']);?>"><?=h($row['rep_shape_name']);?></option>
						<?php } ?>
					</select>
				</div>
			</div>
			<div class="item">
				<div class="title">箇所選択</div>
				<div class="data parts_list">
					<div class="parts_block">
						<div class="parts" data-shape_id="1" style="display: none;">
							<?php
							foreach ($rep_parts_list as $row) {
								$src = '';
								if ($row['rep_parts_id'] == 1) $src = '../img/parts-t-front.png';
								elseif ($row['rep_parts_id'] == 2) $src = '../img/parts-t-left.png';
								elseif ($row['rep_parts_id'] == 3) $src = '../img/parts-t-right.png';
								elseif ($row['rep_parts_id'] == 4) $src = '../img/parts-t-roof.png';
								elseif ($row['rep_parts_id'] == 5) $src = '../img/parts-t-rear.png';
							?>
							<label>
								<img src="<?=h($src);?>" alt="">
								<span><input type="checkbox" name="rep_parts_id[]" value="<?=h($row['rep_parts_id']);?>" class="parts_check" data-parts_id="1"><?=h($row['rep_parts_name']);?></span>
							</label>
							<?php } ?>
						</div>
						<div class="parts" data-shape_id="2" style="display: none;">
							<?php
							foreach ($rep_parts_list as $row) {
								$src = '';
								if ($row['rep_parts_id'] == 1) $src = './img/parts-c-front.png';
								elseif ($row['rep_parts_id'] == 2) $src = './img/parts-c-left.png';
								elseif ($row['rep_parts_id'] == 3) $src = './img/parts-c-right.png';
								elseif ($row['rep_parts_id'] == 4) $src = './img/parts-c-roof.png';
								elseif ($row['rep_parts_id'] == 5) $src = './img/parts-c-rear.png';
							?>
							<label>
								<img src="<?=h($src);?>" alt="">
								<span><input type="checkbox" name="rep_parts_id[]" value="<?=h($row['rep_parts_id']);?>" class="parts_check" data-parts_id="1"><?=h($row['rep_parts_name']);?></span>
							</label>
							<?php } ?>
						</div>
						<div class="parts" data-shape_id="3" style="display: none;">
							<?php
							foreach ($rep_parts_list as $row) {
								$src = '';
								if ($row['rep_parts_id'] == 1) $src = './img/parts-w-front.png';
								elseif ($row['rep_parts_id'] == 2) $src = './img/parts-w-left.png';
								elseif ($row['rep_parts_id'] == 3) $src = './img/parts-w-right.png';
								elseif ($row['rep_parts_id'] == 4) $src = './img/parts-w-roof.png';
								elseif ($row['rep_parts_id'] == 5) $src = './img/parts-w-rear.png';
							?>
							<label>
								<img src="<?=h($src);?>" alt="">
								<span><input type="checkbox" name="rep_parts_id[]" value="<?=h($row['rep_parts_id']);?>" class="parts_check" data-parts_id="1"><?=h($row['rep_parts_name']);?></span>
							</label>
							<?php } ?>
						</div>
					</div>
				</div>
			</div>
			<div class="item">
				<div class="title">レベル選択</div>
				<div class="data">
					<select name="rep_level_id" required>
						<option value="0">選択してください</option>
						<?php foreach ($rep_level_list as $row) {?>
						<option value="<?=h($row['rep_level_id']);?>"><?=h($row['rep_level_name']);?></option>
						<?php } ?>
					</select>
				</div>
			</div>
		</div>

		<p class="list_title">予約者情報</p>
		<div class="edit_list">
			<div class="item">
				<div class="title">会社名</div>
				<div class="data">
					<select name="client_id" required>
						<?php foreach ($client_list as $id => $name) { ?>
						<option value="<?=h($id);?>"><?=h($name);?></option>
						<?php } ?>
					</select>
				</div>
			</div>
			<div class="item">
				<div class="title">メールアドレス</div>
				<div class="data in"><input type="text" name="resv_client_mail" value="<?=h(isset($dat['resv_client_mail']) ? $dat['resv_client_mail'] : '');?>" class="on" required></div>
			</div>
			<div class="item">
				<div class="title">予約者名</div>
				<div class="data in"><input type="text" name="resv_client_name" value="<?=h(isset($dat['resv_client_name']) ? $dat['resv_client_name'] : '');?>" class="on" required></div>
			</div>
			<div class="item">
				<div class="title">電話番号</div>
				<div class="data in"><input type="text" name="resv_client_tel" value="<?=h(isset($dat['resv_client_tel']) ? $dat['resv_client_tel'] : '');?>" class="on" required></div>
			</div>
			<div class="item">
				<div class="title">画像追加（任意）</div>
				<div class="data">新規登録後に追加</div>
			</div>
			<div class="item">
				<div class="title">備考（任意）</div>
				<div class="data in"><textarea name="resv_text" rows="5" class="on"><?=h(isset($dat['resv_text']) ? $dat['resv_text'] : '');?></textarea></div>
			</div>
			<div class="item">
				<div class="title">メモ（任意）</div>
				<div class="data in"><textarea name="resv_memo" rows="5" class="on"><?=h(isset($dat['resv_memo']) ? $dat['resv_memo'] : '');?></textarea></div>
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
