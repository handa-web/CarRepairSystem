<?php

include_once('./in-init.php');

$plan_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$dat = [];
$cale_list = [];
$sql = "SELECT * FROM mst_plan ";
$sql .= "WHERE plan_id = ? LIMIT 0, 1";
if ($res = sql($sql, $plan_id)) {

	$dat = $res[0];

}
elseif ($res !== false) $mess = 'データが見つかりませんでした。';
else $mess = 'データの取得に失敗しました。';

$cale_list = [];
if ($dat) {
	$sql = "SELECT * FROM dat_staff_cale LEFT JOIN mst_staff USING (staff_id) WHERE plan_id = ? ORDER BY staff_cale_date, staff_cale_time1";
	if ($res = sql($sql, $plan_id)) {
		foreach ($res as $row) {
			$cale_list[] = $row;
		}
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
			<li><a href="./plan-list.php">予定管理</a></li>
			<li>詳細</li>
		</ol>
	</nav>

	<?php if ($mess) { ?>
	<p class="mess"><?=h($mess);?></p>
	<?php } ?>

	<?php if ($dat) { ?>
	<form id="plan_form">
		<input type="hidden" name="csrf_token" value="<?=h($csrf_token);?>">
		<input type="hidden" name="plan_id" value="<?=h($plan_id);?>">
		<input type="hidden" name="edit" value="1">

		<p class="war_text" style="display: none;"></p>

		<p class="list_title">予定確認</p>
		<div class="edit_list">
			<div class="item">
				<div class="title">予定ID</div>
				<div class="data"><?=h($dat['plan_id']);?></div>
			</div>
			<div class="item">
				<div class="title">予定名</div>
				<div class="data in"><input type="text" name="plan_name" value="<?=h($dat['plan_name']);?>" class="on"></div>
			</div>
			<div class="item">
				<div class="title">予定内容</div>
				<div class="data in"><textarea name="plan_memo" rows="6" class="on"><?=h($dat['plan_memo']);?></textarea></div>
			</div>
			<div class="item">
				<div class="title">予定日程</div>
				<div class="data date_list">
					<?php foreach ($cale_list as $row) { ?>
					<div class="row">
						<p>
							<span><?=h($row['staff_name']);?>　<?=h(formatDateTime($row['staff_cale_date']. ' '.$row['staff_cale_time1']));?> ~ <?=h(formatDateTime($row['staff_cale_time2']));?></span>
							<button type="button" class="button dele span_butt span_dele" data-id="<?=h($row['staff_id']);?>" data-date="<?=h($row['staff_cale_date']);?>" data-time1="<?=h($row['staff_cale_time1']);?>" data-time2="<?=h($row['staff_cale_time2']);?>">削除</button>
						</p>
					</div>
					<?php } ?>
					<button type="button" class="button new span_butt new_plan_span" data-id="<?=h($plan_id);?>">追加</button>
				</div>
			</div>
		</div>

	</form>

	<?php } ?>

	<ul class="button_list">
		<li><a href="<?=h($_SESSION['back']);?>" class="button back">戻る</a></li>
		<?php if ($dat) { ?>
		<li><button type="submit" form="plan_form" class="button edit">更新</button></li>
		<?php } ?>
	</ul>

</main>

</div>

<?php include_once('./in-footer.php'); ?>
<script src="./js/plan.js"></script>
</body>
</html>
