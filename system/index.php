<?php

include_once('./in-init.php');

$_SESSION['back'] = $now_url;

$set_date = isset($_GET['date']) ? $_GET['date'] : 'today';
$set_cale_date = isset($_GET['cale-date']) ? $_GET['cale-date'] : $set_date;
$set_next_month = isset($_GET['next-month']) ? (int)$_GET['next-month'] : 0;

$today = createDateTime();
$date = createDateTime(date: $set_date, Immutable: true);

$set_date = $date->format('Y-m-d');
$set_date_next = $date->modify('+1 day')->format('Y-m-d');
$set_date_prev = $date->modify('-1 day')->format('Y-m-d');

$work_time = createDateTime(date: $set_date.' 09:00:00', Immutable: true);
$work_time_end = createDateTime(date: $set_date.' 19:00:00', Immutable: true);

$set_resv_span = isset($_GET['resv-span']) ? (int)$_GET['resv-span'] : 0;
$set_plan_span = isset($_GET['plan-span']) ? (int)$_GET['plan-span'] : 0;

$staff_list = [];
$par = [$set_date];
$sql = "SELECT * FROM mst_staff LEFT JOIN (SELECT * FROM dat_staff_cale WHERE staff_cale_date = ? ORDER BY staff_cale_date, staff_cale_time1) AS cale USING (staff_id) ";
if (! $_SESSION['admin']) {
	if ($set_resv_span or $set_plan_span) {
		$sql .= "WHERE staff_id = ? ";
		$par[] = $_SESSION['id'];
	}
}
$sql .= "ORDER BY staff_stop, staff_sort, staff_id";
if ($res = sql($sql, $par)) {
	foreach ($res as $row) {

		if (! isset($staff_list[$row['staff_id']])) $staff_list[$row['staff_id']] = ['name' => $row['staff_name'], 'stop' => $row['staff_stop'], 'time_list'=> []];

		$cale_date = createDateTime(date: $row['staff_cale_date'], Immutable: true);
		$cale_time1 = createDateTime(date: $row['staff_cale_date'].' '.$row['staff_cale_time1'], Immutable: true);
		$cale_time2 = createDateTime(date: $row['staff_cale_date'].' '.$row['staff_cale_time2'], Immutable: true);

		$interval = $cale_time1->diff($cale_time2);
		$minutes = $interval->days * 24 * 60 + $interval->h * 60 + $interval->i;

		$id = 0;
		$name = '';
		$href = '';
		$class = '';
		if ($id = (int)$row['resv_id']) {
			$sql = "SELECT * FROM dat_resv WHERE resv_id = ? LIMIT 0, 1";
			if ($res2 = sql($sql, $id)) {
				$name = $res2[0]['resv_car_number'];
				$href = './resv.php?id='.$id;
				$class = 'resv';
			}
		}
		elseif ($id = (int)$row['plan_id']) {
			$sql = "SELECT * FROM mst_plan WHERE plan_id = ? LIMIT 0, 1";
			if ($res2 = sql($sql, $id)) {
				$name = $res2[0]['plan_name'];
				$href = './plan.php?id='.$id;
				$class = 'plan';
			}
		}

		$staff_list[$row['staff_id']]['time_list'][$cale_time1->format('Y-m-d H:i:s')] = ['id' => $id, 'name' => $name, 'href' => $href, 'class' => $class, 'minutes' => $minutes];

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
<div class="cale_body">

	<?php if ($set_resv_span or $set_plan_span) { ?>
	<p class="list_title">【<?=h($set_plan_span ? '予定期間選択' : '予約期間選択');?>】</p>
	<form id="set_form">

		<input type="hidden" name="csrf_token" value="<?=h($csrf_token);?>">
		<input type="hidden" name="new" value="1">
		<input type="hidden" name="staff_id" value="<?=h($_SESSION['new_span']['staff_id']);?>">
		<input type="hidden" name="date_time1" value="<?=h($_SESSION['new_span']['date_time1']);?>">
		<input type="hidden" name="date_time2" value="<?=h($_SESSION['new_span']['date_time2']);?>">
		<input type="hidden" name="resv_id" value="<?=h($set_resv_span);?>">
		<input type="hidden" name="plan_id" value="<?=h($set_plan_span);?>">

		<div class="war_text" style="display: none;"></div>

		<?php if ($_SESSION['new_span']['staff_id']) { ?>
		<div class="edit_list new_span">
			<div class="item">
				<div class="title">期間選択</div>
				<div class="data date_list">
					<div class="row">
						<p><?=h($staff_list[$_SESSION['new_span']['staff_id']]['name']);?></p>
						<p>
							<span><?=h($_SESSION['new_span']['date_time1']);?></span> ~ <span><?=h($_SESSION['new_span']['date_time2']);?></span>
						</p>
						<p>
							<button type="button" class="button back" onclick="history.back()">戻る</button>
							<button type="reset" class="button reset">取消</button>
							<button type="submit" class="button submit">決定</button>
						</p>
					</div>
				</div>
			</div>
		</div>
		<?php } else { ?>
		<p class="war"><?=h($set_plan_span ? '予定' : '予約');?>期間を選択してください。</p>
		<?php } ?>

	</form>
	<?php } ?>

	<div class="calendar_wrap">
		<div class="calendar">
			<?php
			$holiday = [];
			for ($i = 0; $i < 2; $i++) {

				$cale_date = createDateTime($set_cale_date);
				$cale_date->modify('first day of this month');
				if ($i) $cale_date->modify('+1 month');
				if ($set_next_month) $cale_date->modify('-1 month');

				$cale_last_date = (clone $cale_date)->modify('last day of this month');
				$set_cale_date_prev = (clone $cale_date)->modify('-1 month')->format('Y-m-d');
				$set_cale_date_next = (clone $cale_date)->modify('+1 month')->format('Y-m-d');

				$cale_this_year = $cale_date->format('Y');
				$cale_this_month = $cale_date->format('n');

				while($cale_date->format('w') != 0) {
					$cale_date->modify('-1 day');
				}

				while($cale_last_date->format('w') != 6) {
					$cale_last_date->modify('+1 day');
				}

				$sql = "SELECT * FROM mst_holiday WHERE holiday_date BETWEEN ? AND ? ORDER BY holiday_date";
				$par = [$cale_date->format('Y-m-d'), $cale_last_date->format('Y-m-d')];
				if ($res = sql($sql, $par)) {
					foreach ($res as $row) $holiday[] = $row['holiday_date'];
				}

			?>
			<div class="calendar_item">

				<div class="cale_data">
					<p class="year"><?=h($cale_this_year);?></p>
					<p class="month"><span><?=h($cale_this_month);?></span>月</p>
					<p class="link">
						<?php if ($i == 0) { ?>
						<a href="./?date=<?=h($set_date);?>&cale-date=<?=h($set_cale_date_prev);?>&resv-span=<?=h($set_resv_span);?>&plan-span=<?=h($set_plan_span);?>"><svg xmlns="http://www.w3.org/2000/svg" height="14" width="14" viewBox="0 0 448 512"><!--!Font Awesome Free 6.6.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M9.4 233.4c-12.5 12.5-12.5 32.8 0 45.3l160 160c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L109.2 288 416 288c17.7 0 32-14.3 32-32s-14.3-32-32-32l-306.7 0L214.6 118.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0l-160 160z"/></svg></a>
						<?php } else { ?>
						<a href="./?date=<?=h($set_date);?>&cale-date=<?=h($set_cale_date_next);?>&resv-span=<?=h($set_resv_span);?>&plan-span=<?=h($set_plan_span);?>"><svg xmlns="http://www.w3.org/2000/svg" height="14" width="14" viewBox="0 0 448 512"><!--!Font Awesome Free 6.6.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M438.6 278.6c12.5-12.5 12.5-32.8 0-45.3l-160-160c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L338.8 224 32 224c-17.7 0-32 14.3-32 32s14.3 32 32 32l306.7 0L233.4 393.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0l160-160z"/></svg></a>
						<?php } ?>
					</p>
				</div>

				<table class="cale_tbl">
					<thead>
						<tr>
							<?php foreach ($_SESSION['week_list'] as $week => $week_text) { ?>
							<th><?=h($week_text);?></th>
							<?php } ?>
						</tr>
					</thead>
					<tbody>
						<?php while ($cale_date < $cale_last_date) { ?>
						<tr>
							<?php
							foreach ($_SESSION['week_list'] as $week => $week_text) {
								$class = ($cale_date->format('n') == $cale_this_month && in_array($cale_date->format('Y-m-d'), $holiday)) ? 'holi' : '';
								if ($cale_date->format('n') == $cale_this_month && $cale_date == $date) {
									$class = $class ? $class.' today' : 'today';
								}
								if ($cale_date < $today) {
									$class = $class ? $class.' before' : 'before';
								}
							?>
							<td class="<?=h($class);?>">
								<?php if ($cale_date->format('n') == $cale_this_month) { ?>
								<a href="./?date=<?=h($cale_date->format('Y-m-d'));?>&cale-date=<?=h($cale_date->format('Y-m-d'));?>&next-month=<?=h($i);?>&resv-span=<?=h($set_resv_span);?>&plan-span=<?=h($set_plan_span);?>"><?=h($cale_date->format('j'));?></a>
								<?php } ?>
							</td>
							<?php $cale_date->modify('+1 day'); } ?>
						</tr>
						<?php } ?>
					</tbody>
				</table>

			</div>
			<?php } ?>
		</div>
	</div>

	<nav class="cale_nav">
		<div class="nav_reload"><a href="./?date=<?=h($set_date);?>&cale-date=<?=h($set_cale_date);?>&next-month=<?=h($set_next_month);?>&resv-span=<?=h($set_resv_span);?>&plan-span=<?=h($set_plan_span);?>"><svg xmlns="http://www.w3.org/2000/svg" height="20" width="20" viewBox="0 0 512 512"><!--!Font Awesome Free 6.6.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path fill="#ffffff" d="M142.9 142.9c-17.5 17.5-30.1 38-37.8 59.8c-5.9 16.7-24.2 25.4-40.8 19.5s-25.4-24.2-19.5-40.8C55.6 150.7 73.2 122 97.6 97.6c87.2-87.2 228.3-87.5 315.8-1L455 55c6.9-6.9 17.2-8.9 26.2-5.2s14.8 12.5 14.8 22.2l0 128c0 13.3-10.7 24-24 24l-8.4 0c0 0 0 0 0 0L344 224c-9.7 0-18.5-5.8-22.2-14.8s-1.7-19.3 5.2-26.2l41.1-41.1c-62.6-61.5-163.1-61.2-225.3 1zM16 312c0-13.3 10.7-24 24-24l7.6 0 .7 0L168 288c9.7 0 18.5 5.8 22.2 14.8s1.7 19.3-5.2 26.2l-41.1 41.1c62.6 61.5 163.1 61.2 225.3-1c17.5-17.5 30.1-38 37.8-59.8c5.9-16.7 24.2-25.4 40.8-19.5s25.4 24.2 19.5 40.8c-10.8 30.6-28.4 59.3-52.9 83.8c-87.2 87.2-228.3 87.5-315.8 1L57 457c-6.9 6.9-17.2 8.9-26.2 5.2S16 449.7 16 440l0-119.6 0-.7 0-7.6z"/></svg><span>再読み込み</span></a></div>
		<div class="nav_main">
			<div class="nav_today"><a href="./?resv-span=<?=h($set_resv_span);?>&plan-span=<?=h($set_plan_span);?>">今日</a></div>
			<div class="nav_date">
				<a href="./?date=<?=h($set_date_prev);?>&cale-date=<?=h($set_cale_date);?>&resv-span=<?=h($set_resv_span);?>&plan-span=<?=h($set_plan_span);?>"><svg xmlns="http://www.w3.org/2000/svg" height="14" width="14" viewBox="0 0 448 512"><!--!Font Awesome Free 6.6.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M9.4 233.4c-12.5 12.5-12.5 32.8 0 45.3l160 160c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L109.2 288 416 288c17.7 0 32-14.3 32-32s-14.3-32-32-32l-306.7 0L214.6 118.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0l-160 160z"/></svg></a>
				<?=h($date->format('Y'));?>年<span class="month"><?=h($date->format('m'));?></span>月<span class="date"><?=h($date->format('j'));?></span>日（<?=h($_SESSION['week_list'][$date->format('w')]);?>）
				<a href="./?date=<?=h($set_date_next);?>&cale-date=<?=h($set_cale_date);?>&resv-span=<?=h($set_resv_span);?>&plan-span=<?=h($set_plan_span);?>"><svg xmlns="http://www.w3.org/2000/svg" height="14" width="14" viewBox="0 0 448 512"><!--!Font Awesome Free 6.6.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M438.6 278.6c12.5-12.5 12.5-32.8 0-45.3l-160-160c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L338.8 224 32 224c-17.7 0-32 14.3-32 32s14.3 32 32 32l306.7 0L233.4 393.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0l160-160z"/></svg></a>
			</div>
			<div class="nav_date_set"></div>
		</div>
	</nav>
	<div class="cale_list">
		<table class="cale_time_tbl<?=h(($set_resv_span or $set_plan_span) ? ' on' : '');?>">
			<thead>
				<tr>
					<th class="name fixed"></th>
					<?php
					$time = $work_time;
					while ($time < $work_time_end) { ?>
					<td rowspan="2" data-time="<?=h($time->format('H'));?>"><?=h($time->format('H'));?><br>時</td>
					<?php $time = $time->modify('+1 hour'); } ?>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($staff_list as $staff_id => $staff) { ?>
				<tr class="<?=h($staff['stop'] ? 'stop' : '');?>">
					<th class="name fixed"><?=h($staff['name']);?></th>
					<?php
					$time = $work_time;
						while ($time < $work_time_end) {
							$date_time1 = $time->format('Y-m-d H:i:s');
							$date_time2 = $time->modify('+30 minutes')->format('Y-m-d H:i:s');
					?>
					<td data-time="<?=h($time->format('H'));?>">
						<?php if ($set_resv_span or $set_plan_span) { ?>
						<div class="base">
							<form class="<?=h(($_SESSION['new_span']['staff_id'] == $staff_id && $_SESSION['new_span']['date_time1'] <= $date_time1 && $date_time1 < $_SESSION['new_span']['date_time2']) ? 'set_span' : '');?>">
								<input type="hidden" name="csrf_token" value="<?=h($csrf_token);?>">
								<input type="hidden" name="staff_id" value="<?=h($staff_id);?>">
								<input type="hidden" name="resv_span" value="<?=h($set_resv_span);?>">
								<input type="hidden" name="plan_span" value="<?=h($set_plan_span);?>">
								<input type="hidden" name="date_time" value="<?=h($date_time1);?>">
								<button type="submit"></button>
							</form>
							<form class="<?=h(($_SESSION['new_span']['staff_id'] == $staff_id && $_SESSION['new_span']['date_time1'] <= $date_time2 && $date_time2 < $_SESSION['new_span']['date_time2']) ? 'set_span' : '');?>">
								<input type="hidden" name="csrf_token" value="<?=h($csrf_token);?>">
								<input type="hidden" name="staff_id" value="<?=h($staff_id);?>">
								<input type="hidden" name="resv_span" value="<?=h($set_resv_span);?>">
								<input type="hidden" name="plan_span" value="<?=h($set_plan_span);?>">
								<input type="hidden" name="date_time" value="<?=h($date_time2);?>">
								<button type="submit"></button>
							</form>
						</div>
						<?php } ?>
						<?php if (isset($staff['time_list'][$date_time1])) { ?>
						<button type="button" onclick="location.href='<?=h($staff['time_list'][$date_time1]['href']);?>'" class="set_data start0 <?=h($staff['time_list'][$date_time1]['class']);?>" data-minutes="<?=h($staff['time_list'][$date_time1]['minutes']);?>" <?=h(($set_resv_span or $set_plan_span) ? 'disabled' : '');?>>ID:<?=h($staff['time_list'][$date_time1]['id']);?><br><?=h($time->format('H:i'));?> ~ <?=h($time->modify('+'.$staff['time_list'][$date_time1]['minutes'].' minutes')->format('H:i'));?><br><?=h($staff['time_list'][$date_time1]['name']);?></button>
						<?php } if (isset($staff['time_list'][$date_time2])) { ?>
						<button type="button" onclick="location.href='<?=h($staff['time_list'][$date_time2]['href']);?>'" class="set_data start30 <?=h($staff['time_list'][$date_time2]['class']);?>" data-minutes="<?=h($staff['time_list'][$date_time2]['minutes']);?>" <?=h(($set_resv_span or $set_plan_span) ? 'disabled' : '');?>>ID:<?=h($staff['time_list'][$date_time2]['id']);?><br><?=h($time->modify('+30 minutes')->format('H:i'));?> ~ <?=h($time->modify('+'.$staff['time_list'][$date_time2]['minutes'].' minutes')->format('H:i'));?><br><?=h($staff['time_list'][$date_time2]['name']);?></button>
						<?php } ?>
					</td>
					<?php $time = $time->modify('+1 hour'); } ?>
				</tr>
				<?php } ?>
			</tbody>
		</table>
		<button type="button" class="cale_pop_span" data-resv_span="<?=h($set_resv_span);?>" data-plan_span="<?=h($set_plan_span);?>" data-csrf_token="<?=h($csrf_token);?>" style="display: none;">登録</button>
	</div>
</div>
</main>

</div>
<?php include_once('./in-footer.php'); ?>
<script src="./js/new-span.js"></script>
</body>
</html>
