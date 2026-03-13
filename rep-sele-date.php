<?php

include_once('./in-init.php');

$size_id = isset($_GET['size']) ? (int)$_GET['size'] : 0;
$shape_id = isset($_GET['shape']) ? (int)$_GET['shape'] : 0;
$parts_id_list = isset($_GET['parts']) ? (array)$_GET['parts'] : [];
$level_id = isset($_GET['level']) ? (int)$_GET['level'] : 0;

$set_date = isset($_GET['date']) ? $_GET['date'] : '';

if ($size_id && $shape_id && $parts_id_list && $level_id) {

	$parts_id = implode(',', $parts_id_list);

	$date = createDateTime(date: $set_date, Immutable: true);
	$set_date = $date->format('Y-m-d');

	$parts_text = '';
	foreach ($parts_id_list as $val) $parts_text .= '&parts'.urlencode('[]').'='.h($val);
	$rep_param = 'size='.h($size_id).'&shape='.h($shape_id).$parts_text.'&level='.h($level_id);

	$today = createDateTime(Immutable: true);
	$set_date_next_month = ($today->modify('+2 month')->format('Y-m-d') <= $set_date) ? $set_date : $date->modify('+1 month')->format('Y-m-d');
	$set_date_prev_month = ($set_date <= $today->format('Y-m-d')) ? $set_date : $date->modify('-1 month')->format('Y-m-d');

	$set_start_date = $date->modify('+3 day');
	$set_end_date = $set_start_date->modify('next month');

	$time_list = [
		['start' => '09:00', 'end' => '11:00', 'date_list' => []],
		['start' => '11:00', 'end' => '13:00', 'date_list' => []],
		['start' => '14:00', 'end' => '16:00', 'date_list' => []],
		['start' => '16:00', 'end' => '18:00', 'date_list' => []],
	];

	foreach($time_list as $i => $time) {
		$start_date = $set_start_date->modify($time['start']);
		while ($start_date <= $set_end_date) {
			$cale_date = $start_date->format('Y-m-d');
			$time_list[$i]['date_list'][$cale_date] = ['date' => $start_date, 'num' => 0];
			$start_date = $start_date->modify('+1 day');
		}
	}

	$staff_num = 0;
	$sql = "SELECT COUNT(staff_id) AS num FROM mst_staff WHERE staff_stop = 0 AND staff_id != 1";
	if ($res = sql($sql)) $staff_num = $res[0]['num'];

	$staff = [];
	$sql = "SELECT * FROM dat_staff_cale INNER JOIN mst_staff USING(staff_id) WHERE staff_stop = 0 AND staff_id != 1 AND staff_cale_date BETWEEN ? AND ? ORDER BY staff_cale_date, staff_cale_time1";
	$par = [$set_start_date->format('Y-m-d'), $set_end_date->format('Y-m-d')];
	if ($res = sql($sql, $par)) {
		foreach ($res as $row) {
			$cale_date = createDateTime(date: $row['staff_cale_date'])->format('Y-m-d');
			$cale_time1 = createDateTime(date: $row['staff_cale_time1'])->format('H:i');
			$cale_time2 = createDateTime(date: $row['staff_cale_time2'])->format('H:i');
			foreach($time_list as $i => $time) {
				if ($time['start'] <= $cale_time1 && $cale_time1 < $time['end']) {
					$time_list[$i]['date_list'][$cale_date]['num']++;
				}
				else {
					if ($time['start'] < $cale_time2) {
						$time_list[$i]['date_list'][$cale_date]['num']++;
					}
				}
			}
		}
	}

	$holiday = [];
	$sql = "SELECT * FROM mst_holiday WHERE holiday_date BETWEEN ? AND ? ORDER BY holiday_date";
	$par = [$set_start_date->format('Y-m-d'), $set_end_date->format('Y-m-d')];
	if ($res = sql($sql, $par)) {
		foreach ($res as $row) {
			$holiday[] = $row['holiday_date'];
		}
	}

}
else $mess = '項目を選択してください。';

?>
<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>入庫日選択 | car_repair予約システム</title>
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
			<li><a href="./rep-sele.php">修理のご予約</a></li>
			<li>入庫日選択</li>
		</ol>
	</nav>

	<?php if ($mess) { ?>
	<p class="war_text"><?=h($mess);?></p>
	<?php } else { ?>

	<div id="resv_date">
		<p class="list_title">入庫日選択</p>
		<p class="war_text" style="display: none;"></p>
		<div class="cale_nav">
			<div class="left">
				<a href="./rep-sele-date.php?date=<?=h($set_date_prev_month);?>&<?=h($rep_param);?>" class="<?=h(($set_date <= $today->format('Y-m-d')) ? '' : 'on')?>"><</a>
			</div>
			<div class="middle">
				<a href="./rep-sele-date.php?date=<?=h($set_date);?>&<?=h($rep_param);?>">
					<p class="year"><?=h($set_start_date->format('Y'));?>年</p>
					<p class="date"><span><?=h($set_start_date->format('n'));?></span>月<span><?=h($set_start_date->format('j'));?></span>日（<?=h($_SESSION['week_list'][$set_start_date->format('w')]);?>）</p>
				</a>
			</div>
			<p> ～ </p>
			<div class="middle">
				<a href="./rep-sele-date.php?date=<?=h($set_date);?>&<?=h($rep_param);?>">
					<p class="year"><?=h($set_end_date->modify('-1 day')->format('Y'));?>年</p>
					<p class="date"><span><?=h($set_end_date->modify('-1 day')->format('n'));?></span>月<span><?=h($set_end_date->modify('-1 day')->format('j'));?></span>日（<?=h($_SESSION['week_list'][$set_end_date->modify('-1 day')->format('w')]);?>）</p>
				</a>
			</div>
			<!-- <div class="middle">
				<a href="./rep-sele-date.php?date=<?=h($set_date);?>&<?=h($rep_param);?>">
					<p class="year"><?=h($set_start_date->format('Y'));?>年</p>
					<p class="date"><span><?=h($set_start_date->format('n'));?></span>月<span><?=h($set_start_date->format('j'));?></span>日（<?=h($_SESSION['week_list'][$set_start_date->format('w')]);?>）～ <span> <?=h($set_end_date->format('n'));?></span>月<span><?=h($set_end_date->format('j'));?></span>日（<?=h($_SESSION['week_list'][$set_end_date->format('w')]);?>）</p>
				</a>
			</div> -->
			<div class="right">
				<a href="./rep-sele-date.php?date=<?=h($set_date_next_month);?>&<?=h($rep_param);?>" class="<?=h(($today->modify('+2 month')->format('Y-m-d') <= $set_date) ? '' : 'on');?>">></a>
			</div>
		</div>
		<p class="cale_cap">
			<span><img src="./img/icon-circle.svg" alt="" width="18" height="18">受付中</span>
			<span><img src="./img/icon-triangle.svg" alt="" width="18" height="18">残りわずか</span>
			<span><img src="./img/icon-cross.svg" alt="" width="18" height="18">受付終了</span>
		</p>
		<div class="cale_list">
			<table>
				<thead>
					<tr>
						<th>時間</th>
						<?php foreach ($time_list[0]['date_list'] as $cale_date => $row) { ?>
						<th data-time="<?=h($row['date']->format('Y-m-d'));?>"><?=h($row['date']->format('m/d'));?><br>（<?=h($_SESSION['week_list'][$row['date']->format('w')]);?>）</th>
						<?php } ?>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($time_list as $i => $time) { ?>
					<tr>
						<th class="time"><?=h($time['start']);?><br>～<br><?=h($time['end']);?></th>
						<?php
						foreach ($time['date_list'] as $cale_date => $row) {
							$datetime = createDateTime(date: $cale_date.' '.$time['start'])->format('Y-m-d H:i:s');
							$week = createDateTime(date: $cale_date)->format('w');
							if (in_array($cale_date, $holiday) || $week == 0) {
								$src = './img/icon-cross.svg';
							}
							else {
								if ($row['num'] >= $staff_num) $src = './img/icon-cross.svg';
								elseif ($row['num'] >= ($staff_num - 1)) $src = './img/icon-triangle.svg';
								else $src = './img/icon-circle.svg';
							}
						?>
						<td class="w60 ct <?=h(($row['num'] >= $staff_num) ? 'off' : '');?>">
							<?php if ($row['num'] >= $staff_num) { ?>
							<img src="<?=h($src);?>" alt="" width="32" height="32">
							<?php } else { ?>
							<form action="./rep-sele-date-check.php" method="get">
								<input type="hidden" name="size" value="<?=h($size_id);?>">
								<input type="hidden" name="shape" value="<?=h($shape_id);?>">
								<?php foreach ($parts_id_list as $val) { ?>
								<input type="hidden" name="parts[]" value="<?=h($val);?>">
								<?php } ?>
								<input type="hidden" name="level" value="<?=h($level_id);?>">
								<input type="hidden" name="date" value="<?=h($cale_date);?>">
								<input type="hidden" name="index" value="<?=h($i);?>">
								<button type="submit"><img src="<?=h($src);?>" alt="" width="32" height="32"></button>
							</form>
							<?php } ?>
						</td>
						<?php } ?>
					</tr>
					<?php } ?>
				</tbody>
			</table>
		</div>
	</div>

	<?php } ?>

	<ul class="button_list">
		<li><a href="./rep-sele.php" class="button back">戻る</a></li>
	</ul>

</main>

</div>

<?php include_once('./in-footer.php'); ?>
</body>
</html>
