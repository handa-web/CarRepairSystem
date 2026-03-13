<?php

include_once('./in-init.php');

$sql = "SELECT * FROM mst_shop WHERE shop_id = 1 LIMIT 0, 1";
if ($res = sql($sql)) {
	$shop = $res[0];
	if ($shop['shop_resv_stop']) {
		$mess = '現在、予約の新規受付は停止しております。';
	}
}

$date = createDateTime(date: $set_date, Immutable: true);
$set_date = $date->format('Y-m-d');

$rep_size_list = [];
$sql = "SELECT * FROM mst_rep_size WHERE rep_size_stop = 0";
if ($res = sql($sql)) $rep_size_list = $res;

$rep_shape_list = [];
$sql = "SELECT * FROM mst_rep_shape WHERE rep_shape_stop = 0";
if ($res = sql($sql)) $rep_shape_list = $res;

$rep_level_list = [];
$sql = "SELECT * FROM mst_rep_level WHERE rep_level_stop = 0";
if ($res = sql($sql)) $rep_level_list = $res;

$rep_parts_list = [];
$sql = "SELECT * FROM mst_rep_parts WHERE rep_parts_stop = 0";
if ($res = sql($sql)) $rep_parts_list = $res;

$csrf_token = getCsrfToken();

?>
<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>修理のご予約 | car_repair予約システム</title>
	<!-- css -->
	<link rel="stylesheet" href="./css/destyle.css">
	<link rel="stylesheet" href="./css/base.css">
	<!-- other -->
	<meta name="robots" content="noindex, nofollow">
	<link rel="icon" href="./favicon.ico">
</head>
<body>
<?php include_once('./in-header.php'); ?>

<div class="main_wrap">

<?php include_once('./in-aside.php') ?>

<main id="main">

	<nav class="bread">
		<ol>
			<li><a href="./">TOP</a></li>
			<li>修理のご予約</li>
		</ol>
	</nav>

	<?php if ($mess) { ?>

	<p class="war_text"><?=h($mess);?></p>

	<?php } else { ?>

	<form action="./rep-sele-date.php" method="get">
		<input type="hidden" name="date" value="<?=h($set_date);?>">
		<p class="list_title">修理項目選択</p>
		<div class="edit_list">
			<div class="item">
				<div class="title">分類選択</div>
				<div class="data">
					<select name="size" required>
						<option value="">選択してください</option>
						<?php foreach ($rep_size_list as $row) {?>
						<option value="<?=h($row['rep_size_id']);?>"><?=h($row['rep_size_name']);?></option>
						<?php } ?>
					</select>
				</div>
			</div>
			<div class="item">
				<div class="title">型選択</div>
				<div class="data">
					<select name="shape" required>
						<option value="">選択してください</option>
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
								if ($row['rep_parts_id'] == 1) $src = './img/parts-t-front.png';
								elseif ($row['rep_parts_id'] == 2) $src = './img/parts-t-left.png';
								elseif ($row['rep_parts_id'] == 3) $src = './img/parts-t-right.png';
								elseif ($row['rep_parts_id'] == 4) $src = './img/parts-t-roof.png';
								elseif ($row['rep_parts_id'] == 5) $src = './img/parts-t-rear.png';
							?>
							<label>
								<img src="<?=h($src);?>" alt="">
								<span><input type="checkbox" name="parts[]" value="<?=h($row['rep_parts_id']);?>" class="parts_check" data-parts_id="1"><?=h($row['rep_parts_name']);?></span>
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
								<span><input type="checkbox" name="parts[]" value="<?=h($row['rep_parts_id']);?>" class="parts_check" data-parts_id="1"><?=h($row['rep_parts_name']);?></span>
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
								<span><input type="checkbox" name="parts[]" value="<?=h($row['rep_parts_id']);?>" class="parts_check" data-parts_id="1"><?=h($row['rep_parts_name']);?></span>
							</label>
							<?php } ?>
						</div>
					</div>
				</div>
			</div>
			<div class="item">
				<div class="title">レベル選択</div>
				<div class="data">
					<select name="level" required>
						<option value="">選択してください</option>
						<?php foreach ($rep_level_list as $row) {?>
						<option value="<?=h($row['rep_level_id']);?>"><?=h($row['rep_level_name']);?></option>
						<?php } ?>
					</select>
				</div>
			</div>
		</div>

		<ul class="button_list">
			<li><a href="./" class="button back">戻る</a></li>
			<li><button type="submit" class="button submit">次へ</button></li>
		</ul>

		<?php } ?>

	</form>

</main>

</div>

<?php include_once('./in-footer.php'); ?>
<script>
$(function() {

	const shape = $('select[name="shape"]');
	const parts = $('.parts_block');
	let id = shape.val();

	shape.on('change', function() {
		id = shape.val();
		parts.children('.parts').hide();
		if (id > 0) {
			parts.children('.parts[data-shape_id="' + id + '"]').show();
		}
	})
	.triggerHandler('change');

});
</script>
</body>
</html>
