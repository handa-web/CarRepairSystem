<?php

function setStaffCale($span_list = [], $resv_id = 0, $plan_id = 0) {

	global $db;

	if ($span_list && ($resv_id || $plan_id)) {

		$mess = '';

		foreach ($span_list as $i => $row) {

			if (isset($row['date']) && isset($row['time1']) && isset($row['time2'])) {
				$start = $row['date'].' '.$row['time1'];
				$end = $row['date'].' '.$row['time2'];
			}
			elseif (isset($row['date_time1']) && isset($row['date_time2'])) {
				$start = $row['date_time1'];
				$end = $row['date_time2'];
			}
			else {
				$mess = '期間の日時が正しくありません。';
				break;
			}

			if (checkDateTime($start, 'Y-m-d H:i:s') && checkDateTime($end, 'Y-m-d H:i:s')) {

				$staff_id = (int)$row['staff_id'];
				$start_date = new DateTimeImmutable($start);
				$end_date = new DateTimeImmutable($end);

				$set_date = $start_date;
				$time1 = $set_date->format('H:i:s');
				while ($set_date <= $end_date) {

					$date = $set_date->format('Y-m-d');
					$time2 =$set_date->format('H:i:s');

					if ($set_date->format('H:i:s') === '18:00:00') {

						$sql = "SELECT * FROM dat_staff_cale WHERE staff_id = ? AND staff_cale_date = ? AND ( (staff_cale_time1 >= ? AND staff_cale_time1 < ?) OR (staff_cale_time1 <= ? AND staff_cale_time2 > ?) OR (staff_cale_time2 > ? AND staff_cale_time2 <= ?) ) LIMIT 0, 1";
						$par = [$staff_id, $date, $time1, $time2, $time1, $time2, $time1, $time2];
						if (! $res = sql($sql, $par)) {
							$sql = "INSERT INTO dat_staff_cale VALUES (?, ?, ?, ?, ?, ?)";
							$par = [$staff_id, $date, $time1, $time2, $resv_id, $plan_id];
							if (sql($sql, $par) === false) {
								$mess = 'ご予約に失敗しました。期間の登録に失敗しました。';
								break 2;
							}
						}
						else {
							$mess = 'ご予約に失敗しました。すでに予約で埋まっております。';
							break 2;
						}
						$set_date = $set_date->modify('+1 day')->setTime(9, 0);
						$time1 = $set_date->format('H:i:s');

					}
					else if ($set_date->format('Y-m-d H:i:s') === $end_date->format('Y-m-d H:i:s')) {

						$date = $set_date->format('Y-m-d');
						$time2 = $end_date->format('H:i:s');
						$sql = "SELECT * FROM dat_staff_cale WHERE staff_id = ? AND staff_cale_date = ? AND ( (staff_cale_time1 >= ? AND staff_cale_time1 < ?) OR (staff_cale_time1 <= ? AND staff_cale_time2 > ?) OR (staff_cale_time2 > ? AND staff_cale_time2 <= ?) ) LIMIT 0, 1";
						$par = [$staff_id, $date, $time1, $time2, $time1, $time2, $time1, $time2];
						if (! $res = sql($sql, $par)) {
							$sql = "INSERT INTO dat_staff_cale VALUES (?, ?, ?, ?, ?, ?)";
							$par = [$staff_id, $date, $time1, $time2, $resv_id, $plan_id];
							if (sql($sql, $par) === false) {
								$mess = 'ご予約に失敗しました。期間の登録に失敗しました。';
								break 2;
							}
						}
						else {
							$mess = 'ご予約に失敗しました。すでに予約で埋まっております。';
							break 2;
						}
						$set_date = $set_date->modify('+30 minute');

					}
					else $set_date = $set_date->modify('+30 minute');

				}

			}
			else {
				$mess = '所要時間の登録に失敗しました。無効な日時です。';
				break;
			}
		}

	}
	else $mess = 'データの登録に失敗しました。';

	return $mess;

}

?>
