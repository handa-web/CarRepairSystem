<?php

include_once('./in-init.php');

$size_id = isset($_GET['size']) ? (int)$_GET['size'] : 0;
$shape_id = isset($_GET['shape']) ? (int)$_GET['shape'] : 0;
$parts_id_list = isset($_GET['parts']) ? (array)$_GET['parts'] : [];
$level_id = isset($_GET['level']) ? (int)$_GET['level'] : 0;
$set_date = isset($_GET['date']) ? $_GET['date'] : '';
$index = isset($_GET['index']) ? (int)$_GET['index'] : 0;

$parts_text = '';
foreach ($parts_id_list as $val) $parts_text .= '&parts'.urlencode('[]').'='.h($val);
$rep_param = 'size='.h($size_id).'&shape='.h($shape_id).$parts_text.'&level='.h($level_id);

$time_list = [
	['start' => '09:00', 'end' => '11:00'],
	['start' => '11:00', 'end' => '13:00'],
	['start' => '14:00', 'end' => '16:00'],
	['start' => '16:00', 'end' => '18:00'],
];

if ($size_id && $shape_id && $parts_id_list && $level_id) {

	$rep_size_name = '';
	$sql = "SELECT * FROM mst_rep_size WHERE rep_size_id = ? AND rep_size_stop = 0 LIMIT 0, 1";
	if ($res = sql($sql, $size_id)) $rep_size_name = $res[0]['rep_size_name'];

	$rep_shape_name = '';
	$sql = "SELECT * FROM mst_rep_shape WHERE rep_shape_id = ? AND rep_shape_stop = 0 LIMIT 0, 1";
	if ($res = sql($sql, $shape_id)) $rep_shape_name = $res[0]['rep_shape_name'];

	$rep_parts_name_list = [];
	$sql = "SELECT * FROM mst_rep_parts WHERE rep_parts_stop = 0";
	if ($res = sql($sql)) {
		foreach ($res as $row) {
			if (in_array($row['rep_parts_id'], $parts_id_list)) $rep_parts_name_list[] = $row['rep_parts_name'];
		}
	}

	$rep_level_name = '';
	$rep_level_hour = 0;
	$sql = "SELECT * FROM mst_rep_level WHERE rep_level_id = ? AND rep_level_stop = 0 LIMIT 0, 1";
	if ($res = sql($sql, $level_id)) {
		$rep_level_name = $res[0]['rep_level_name'];
		$rep_level_hour = (int)$res[0]['rep_level_hour'];
	}

	if (checkDateTime($set_date, 'Y-m-d')) {

		if (isset($time_list[$index])) {

			$date = $set_date;
			$set_rep_minutes = $rep_level_hour * 60;
			$rep_minutes = 0;

			$in_date = createDateTime($set_date.' '.$time_list[$index]['start'], Immutable: true);
			$cale_start = $in_date->modify('+1 day');
			$cale_end = $cale_start->modify('+2 month');

			$holiday = [];
			$sql = "SELECT * FROM mst_holiday WHERE holiday_date BETWEEN ? AND ? ORDER BY holiday_date";
			$par = [$cale_start->format('Y-m-d'), $cale_end->format('Y-m-d')];
			if ($res = sql($sql, $par)) {
				foreach ($res as $row) {
					$holiday[] = $row['holiday_date'];
				}
			}

			$staff_list = [];
			$sql = "SELECT * FROM mst_staff WHERE staff_stop = 0 AND staff_id != 1 ORDER BY staff_sort, staff_id";
			if ($staff_list = sql($sql)) {

				$new_span = [];
				$new_span_row = [];
				$continue = 0;

				$cale = $cale_start;
				while ($cale <= $cale_end) {

					$date = $cale->format('Y-m-d');
					$time1 = $cale->format('H:i:s');
					$time2 = $cale->modify('+30 minutes')->format('H:i:s');
					$week = $cale->format('w');
					$no_staff = true;

					if (! (in_array($date, $holiday) || ($week == 0))) {

						if (isset($new_span_row['staff_id'])) {
							$staff_id = (int)$new_span_row['staff_id'];
							$sql = "SELECT * FROM dat_staff_cale WHERE staff_id = ? AND staff_cale_date = ? AND ( (staff_cale_time1 >= ? AND staff_cale_time1 < ?) OR (staff_cale_time1 <= ? AND staff_cale_time2 > ?) OR (staff_cale_time2 > ? AND staff_cale_time2 <= ?) ) ORDER BY staff_cale_date, staff_cale_time1";
							$par = [$staff_id, $date, $time1, $time2, $time1, $time2, $time1, $time2];
							if (! $res = sql($sql, $par)) {
								$no_staff = false;
								$continue++;
								$new_span_row['time2'] = $time2;
							}
						}
						else {
							foreach ($staff_list as $staff) {
								$staff_id = (int)$staff['staff_id'];
								$sql = "SELECT * FROM dat_staff_cale WHERE staff_id = ? AND staff_cale_date = ? AND ( (staff_cale_time1 >= ? AND staff_cale_time1 < ?) OR (staff_cale_time1 <= ? AND staff_cale_time2 > ?) OR (staff_cale_time2 > ? AND staff_cale_time2 <= ?) ) ORDER BY staff_cale_date, staff_cale_time1";
								$par = [$staff_id, $date, $time1, $time2, $time1, $time2, $time1, $time2];
								if (! $res = sql($sql, $par)) {
									$no_staff = false;
									$continue++;
									$new_span_row = ['staff_id' => $staff_id, 'date' => $date, 'time1' => $time1, 'time2' => $time2];
									break;
								}
							}
						}

						if ($set_rep_minutes <= ($rep_minutes + $continue * 30)) {
							if ($new_span_row) {
								$new_span[] = $new_span_row;
								$rep_minutes += $continue * 30;
								$new_span_row = [];
							}
							$continue = 0;
							$out_date = $cale->modify('+1 day');
							break;
						}
						elseif ($time2 == '18:00:00') {
							if ($new_span_row) {
								if ($continue >= 4) {
									$new_span[] = $new_span_row;
									$rep_minutes += $continue * 30;
								}
								$new_span_row = [];
							}
							$continue = 0;
							$cale = $cale->modify('+1 day 09:00:00');
						}
						else {
							if ($no_staff) {
								if ($new_span_row) {
									if ($continue >= 4) {
										$new_span[] = $new_span_row;
										$rep_minutes += $continue * 30;
									}
									$new_span_row = [];
								}
								$continue = 0;
							}
							$cale = $cale->modify('+30 minutes');
						}

					}
					else $cale = $cale->modify('+1 day 09:00:00');

				}

				if ($continue && ($rep_minutes < $set_rep_minutes)) {
					$new_span[] = $new_span_row;
					$rep_minutes += $continue * 30;
					$new_span_row = [];
					$continue = 0;
				}

				if ($rep_minutes < $set_rep_minutes) {
					$mess = 'ご予約に失敗しました。すでに予約で埋まっております。';
				}

			}
			else $mess = '現在、ご予約可能なスタッフがおりません。';

		}
		else $mess = '時間が正しくありません。';

	}
	else $mess = '日付が正しくありません。';

}
else $mess = 'データが正しくありません。';

$dat = [];
$sql = "SELECT * FROM mst_client LEFT JOIN (SELECT *, MAX(resv_status1_time) FROM dat_resv GROUP BY client_id) AS resv USING(client_id) WHERE client_id = ? LIMIT 0, 1";
if ($res = sql($sql, $_SESSION['id'])) {
	$dat = $res[0];
	if (! $dat['resv_client_mail']) $dat['resv_client_mail'] = $dat['client_mail'];
	if (! $dat['resv_client_name']) $dat['resv_client_name'] = $dat['client_staff'];
	if (! $dat['resv_client_tel']) $dat['resv_client_tel'] = $dat['client_tel'];
}
else $mess = 'お客様情報の取得に失敗しました。';

$csrf_token = getCsrfToken();

?>
<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>ご予約確認 | car_repair予約システム</title>
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
			<li><a href="./rep-sele.php?id=<?=h($repair_id);?>&date=<?=h($set_date);?>&t=<?=h($set_type);?>">修理のご予約</a></li>
			<li><a href="./rep-sele-date.php?date=<?=h($set_date);?>&<?=h($rep_param);?>">入庫日選択</a></li>
			<li>ご予約確認</li>
		</ol>
	</nav>

	<?php if ($mess) { ?>
	<p class="mess"><?=h($mess);?></p>
	<ul class="button_list">
		<li><a href="./rep-sele.php?id=<?=h($repair_id);?>&date=<?=h($set_date);?>&t=<?=h($set_type);?>" class="button back">戻る</a></li>
	</ul>
	<?php } else { ?>

	<form id="resv_form">
		<input type="hidden" name="csrf_token" value="<?=h($csrf_token);?>">
		<input type="hidden" name="size" value="<?=h($size_id);?>">
		<input type="hidden" name="shape" value="<?=h($shape_id);?>">
		<?php foreach ($parts_id_list as $val) { ?>
		<input type="hidden" name="parts[]" value="<?=h($val);?>">
		<?php } ?>
		<input type="hidden" name="level" value="<?=h($level_id);?>">
		<input type="hidden" name="client_id" value="<?=h($_SESSION['id']);?>">
		<input type="hidden" name="in_date" value="<?=h($in_date->format('Y-m-d'));?>">
		<?php foreach ($new_span as $i => $row) { ?>
		<input type="hidden" name="new_span[<?=h($i);?>][staff_id]" value="<?=h($row['staff_id']);?>">
		<input type="hidden" name="new_span[<?=h($i);?>][date]" value="<?=h($row['date']);?>">
		<input type="hidden" name="new_span[<?=h($i);?>][time1]" value="<?=h($row['time1']);?>">
		<input type="hidden" name="new_span[<?=h($i);?>][time2]" value="<?=h($row['time2']);?>">
		<?php } ?>
		<input type="hidden" name="out_date" value="<?=h($out_date->format('Y-m-d'));?>">

		<p class="war_text" style="display: none;"></p>

		<p class="list_title">予約内容確認</p>
		<div class="edit_list">
			<div class="item">
				<div class="title">入庫日</div>
				<div class="data"><?=h(setDateText($in_date->format('Y-m-d')));?></div>
			</div>
			<div class="item">
				<div class="title">納車予定日</div>
				<div class="data"><?=h(setDateText($out_date->format('Y-m-d')));?></div>
			</div>
			<div class="item">
				<div class="title">分類選択</div>
				<div class="data"><?=h($rep_size_name);?></div>
			</div>
			<div class="item">
				<div class="title">型選択</div>
				<div class="data"><?=h($rep_shape_name);?></div>
			</div>
			<div class="item">
				<div class="title">箇所選択</div>
				<div class="data">
					<?php foreach ($rep_parts_name_list as $name) { ?>
					<p><?=h($name);?></p>
					<?php } ?>
				</div>
			</div>
			<div class="item">
				<div class="title">レベル選択</div>
				<div class="data"><?=h($rep_level_name);?></div>
			</div>
		</div>

		<p class="list_title">予約者情報入力</p>
		<div class="edit_list">
			<div class="item">
				<div class="title">会社名</div>
				<div class="data"><?=h($dat['client_name']);?></div>
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
				<div class="title">車両ナンバー（数字４桁）</div>
				<div class="data in"><input type="text" name="resv_car_number" value="<?=h(isset($dat['resv_car_number']) ? $dat['resv_car_number'] : '');?>" class="on" maxlength="4" required></div>
			</div>
			<div class="item">
				<div class="title">画像追加（任意）</div>
				<div class="data in">
					<ul id="img_list" class="img_list">
						<?php for ($i = 1; $i <= 10; $i++) { ?>
						<li><img src="" alt="" class="file_img" style="display: none"><input type="file" name="resv_img<?=h($i);?>"><button type="button" class="button file">画像選択</button><button type="button" class="button file_dele" style="display: none;">削除</button></li>
						<?php } ?>
					</ul>
					<p>※2M以下のjpgまたはpng</p>
				</div>
			</div>
			<div class="item">
				<div class="title">備考（任意）</div>
				<div class="data in"><textarea name="resv_text" rows="5" maxlength="255" class="on"></textarea></div>
			</div>
		</div>

		<ul class="button_list">
			<li><a href="./rep-sele-date.php?<?=h($rep_param);?>" class="button back">戻る</a></li>
			<li><button type="submit" class="button submit">予約する</button></li>
		</ul>

	</form>

	<?php } ?>

</main>

</div>

<?php include_once('./in-footer.php'); ?>
</body>
</html>
