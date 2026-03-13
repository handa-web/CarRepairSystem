<?php

if (stristr($_SERVER['PHP_SELF'], basename(__FILE__))) {
	header('Location: ../');
	exit;
}

//html出力時エスケープ処理
function h($str = '') {
	$str = htmlspecialchars($str, ENT_QUOTES, 'UTF-8', false);
	return $str;
}

//日付成型
function setDateText($date = '', $format = 'Y-m-d') {
	if (! checkDateTime($date, $format)) return $date;
	$week_list = ['日', '月', '火', '水', '木', '金', '土'];
	$date = createDateTime($date);
	$week = $date->format('w');
	$week_text = '（'.$week_list[$week].'）';
	$text = $date->format('Y年n月j日').$week_text;
	return $text;
}

function getCsrfToken() {

	if (session_status() === PHP_SESSION_ACTIVE) {

		$token = bin2hex(random_bytes(32));
		$_SESSION['csrf_token'] = $token;
		return $_SESSION['csrf_token'];

	} else {
		throw new Exception('Session is not started.');
	}

}

function checkCsrfToken(string $token = '') {
	return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function setCaleDate(int $year, int $month, int $day): DateTime
{

	if ($month < 1) {
		$year--;
		$month = 12;
	}
	elseif ($month > 12) {
		$year++;
		$month = 1;
	}

	if ($year < 2024) $year = 2024;
	elseif ($year > (int)date('Y') + 2) $year = (int)date('Y') + 2;

	$date = new DateTime($year.'-'.$month.'-1');

	// 日付の調整
	if ($day == 0) {
		$date->modify('last day of previous month');
	}
	elseif ($day > $date->format('t')) {
		$date->modify('first day of next month');
	}
	else {
		$date->setDate($year, $month, $day);
	}

	return $date;
}
function formatDateTime($datetime = '') {

	$week_list = ['日', '月', '火', '水', '木', '金', '土'];

	$mess = '';
	if (checkDateTime($datetime)) {
		$datetime = new DateTime($datetime);
		$year = (int)$datetime->format('Y');
		$month = (int)$datetime->format('m');
		$day = (int)$datetime->format('d');
		$week = $datetime->format('w');
		$hours = (int)$datetime->format('H');
		$minutes = (int)$datetime->format('i');
		$mess = $year . '年' . $month . '月' . $day . '日（' . $week_list[$week] . '） ' . $hours . '時' . $minutes . '分';
	}
	elseif (checkDateTime($datetime, 'Y-m-d')) {
		$datetime = new DateTime($datetime);
		$year = (int)$datetime->format('Y');
		$month = (int)$datetime->format('m');
		$day = (int)$datetime->format('d');
		$week = $datetime->format('w');
		$mess = $year . '年' . $month . '月' . $day . '日（' . $week_list[$week] . '） ';
	}
	elseif (checkDateTime($datetime, 'H:i:s')) {
		$datetime = new DateTime($datetime);
		$week = $datetime->format('w');
		$hours = (int)$datetime->format('H');
		$minutes = (int)$datetime->format('i');
		$mess = $hours . '時' . $minutes . '分';
	}

	return $mess;

}
function formatTime($timeString) {
	// 時刻文字列を日時オブジェクトに変換
	$datetime = new DateTime($timeString);

	// 時と分を取得
	$hours = (int)$datetime->format('H');
	$hours_text = $hours ? $hours.'時間' : '';
	$minutes = (int)$datetime->format('i');
	$minutes_text = $minutes ? $minutes.'分' : '';

	// 日本語の表現を作成
	$formattedString = $hours_text.$minutes_text;

	return $formattedString;
}

function createDateTime($date = 'today', $Immutable = false) {
	try {
		if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
			$date .= ' 00:00:00';
		}
		if ($Immutable == false) $d = new DateTime($date);
		else $d = new DateTimeImmutable($date);
	} catch (Exception $e) {
		if ($Immutable == false) $d = new DateTime('');
		else $d = new DateTimeImmutable('');
	}
	return $d;
}

function checkDateTime($date = '', $format = 'Y-m-d H:i:s') {
	$d = DateTime::createFromFormat($format, $date);
	return $d && $d->format($format) === $date;
}

function setStaffCale($span_list = [], $resv_id = 0, $plan_id = 0) {

	$mess = '';
	if ($span_list && ($resv_id || $plan_id)) {

		foreach ($span_list as $row) {

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

						$sql = "SELECT * FROM dat_staff_cale WHERE staff_id = ? AND staff_cale_date = ? AND ( (staff_cale_time1 >= ? AND staff_cale_time1 < ?) OR (staff_cale_time1 <= ? AND ? < staff_cale_time2) OR (staff_cale_time2 > ? AND staff_cale_time2 <= ?) ) LIMIT 0, 1";
						$par = [$staff_id, $date, $time1, $time2, $time1, $time2, $time1, $time2];
						if (! $res = sql($sql, $par)) {
							$sql = "INSERT INTO dat_staff_cale VALUES (?, ?, ?, ?, ?, ?, 0)";
							$par = [$staff_id, $date, $time1, $time2, $resv_id, $plan_id];
							if (sql($sql, $par) === false) {
								$mess = 'ご予約に失敗しました。期間の登録に失敗しました。';
								break 2;
							}
						}
						else {
							$mess = 'ご予約に失敗しました。すでに予約で埋まっております。2';
							break 2;
						}
						$set_date = $set_date->modify('+1 day')->setTime(9, 0);
						$time1 = $set_date->format('H:i:s');

					}
					else if ($set_date->format('Y-m-d H:i:s') === $end_date->format('Y-m-d H:i:s')) {

						$date = $set_date->format('Y-m-d');
						$time2 = $end_date->format('H:i:s');
						$sql = "SELECT * FROM dat_staff_cale WHERE staff_id = ? AND staff_cale_date = ? AND ( (staff_cale_time1 >= ? AND staff_cale_time1 < ?) OR (staff_cale_time1 <= ? AND ? < staff_cale_time2) OR (staff_cale_time2 > ? AND staff_cale_time2 <= ?) ) LIMIT 0, 1";
						$par = [$staff_id, $date, $time1, $time2, $time1, $time2, $time1, $time2];
						if (! $res = sql($sql, $par)) {
							$sql = "INSERT INTO dat_staff_cale VALUES (?, ?, ?, ?, ?, ?, 0)";
							$par = [$staff_id, $date, $time1, $time2, $resv_id, $plan_id];
							if (sql($sql, $par) === false) {
								$mess = 'ご予約に失敗しました。期間の登録に失敗しました。';
								break 2;
							}
						}
						else {
							$mess = 'ご予約に失敗しました。すでに予約で埋まっております。1';
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
	else $mess = 'データの登録に失敗しました。'.$span_list[0]['staff_id'];

	return $mess;

}

// function checkImage($file = []) {

// 	$mess = '';
// 	$max_width = 2000;
// 	$max_height = 2000;
// 	$max_size = 2 * 1024 * 1024;
// 	$type_list = ['image/jpeg', 'image/png', 'image/gif'];

// 	if ($file) {
// 		if ($file['size'] > $max_size) {
// 			$mess = '画像のサイズが大きすぎます。最大サイズは ' . ($max_size / 1024 / 1024) . 'MB です。';
// 		}
// 		else {
// 			$size = getimagesize($file['tmp_name']);
// 			if (! $size) {
// 				$mess = '画像の取得に失敗しました。';
// 			}
// 			else {
// 				if ($size[0] > $max_width || $size[1] > $max_height) {
// 					$mess = '画像の寸法が大きすぎます。最大寸法は ' . $max_width . ' x ' . $max_height . ' pxです。';
// 				}
// 				else {
// 					if (! in_array($size['mime'], $type_list)) {
// 						$mess = '画像の形式が正しくありません。';
// 					}
// 				}
// 			}
// 		}
// 	}

// 	return $mess;

// }

//画像の保存＆削除（reurn 0：画像なし / 1：画像あり）
function set_image($files = [], $path = '', $name = '', $image = '', $delete = 0, $width = 0, $height = 0) {
	if ($image) $ret = 1;
	else $ret = 0;
	if ($path and $name) {
		if ($image and $delete) {

			@unlink($path.$name.'.jpg');
			$ret = 0;

		}
		elseif ($file = $files['tmp_name']) {

			$type = strtolower(substr($files['name'], strrpos($files['name'], '.') + 1));
			if ($type == 'jpg' or $type == 'jpeg') $old_file = @imagecreatefromjpeg($file);
			elseif ($type == 'png') $old_file = @imagecreatefrompng($file);
			elseif ($type == 'gif') $old_file = @imagecreatefromgif($file);

			if ($old_file) {

				$exif = @exif_read_data($file, 0, true);
				$type = (int)$exif['IFD0']['Orientation'];

				$size = getimagesize($file);
				if ($type == 5 or $type == 6 or $type == 7 or $type == 8) {
					$size_width = $size[1];
					$size_height = $size[0];
				}
				else {
					$size_width = $size[0];
					$size_height = $size[1];
				}

				if (! ($width and $height)) {
					if ($width and $width < $size_width) {
						$height = floor($width / $size_width * $size_height);
					}
					elseif ($height and $height < $size_height) {
						$width = floor($height / $size_height * $size_width);
					}
					else {
						$width = $size_width;
						$height = $size_height;
					}
				}

				$tmp_file = imagecreatetruecolor($width, $height);
				$new_file = $path.$name.'.jpg';

				switch ($type) {
					//上下反転
					case 2:
						imagecopyresampled($tmp_file, $old_file, 0, 0, $size[0] - 1, 0, $width, $height, $size[0] * -1, $size[1]);
						break;
					//180度回転
					case 3:
						$old_file = imagerotate($old_file, 180, 0, 0);
						imagecopyresampled($tmp_file, $old_file, 0, 0, 0, 0, $width, $height, $size[0], $size[1]);
						break;
					//左右反転
					case 4:
						imagecopyresampled($tmp_file, $old_file, 0, 0, 0, $size[1] - 1, $width, $height, $size[0], $size[1] * -1);
						break;
					//上下反転&時計周りに270度回転（時計回りに90度回転＆左右反転）
					case 5:
						$old_file = imagerotate($old_file, -90, 0, 0);
						imagecopyresampled($tmp_file, $old_file, 0, 0, $size[1] - 1, 0, $width, $height, $size[1] * -1, $size[0]);
						break;
					//時計回りに90度回転
					case 6:
						$old_file = imagerotate($old_file, -90, 0, 0);
						imagecopyresampled($tmp_file, $old_file, 0, 0, 0, 0, $width, $height, $size[1], $size[0]);
						break;
					//上下反転&時計周りに270度回転（時計回りに270度回転＆左右反転）
					case 7:
						$old_file = imagerotate($old_file, -270, 0, 0);
						imagecopyresampled($tmp_file, $old_file, 0, 0, $size[1] - 1, 0, $width, $height, $size[1] * -1, $size[0]);
						break;
					//時計回りに270度回転
					case 8:
						$old_file = imagerotate($old_file, -270, 0, 0);
						imagecopyresampled($tmp_file, $old_file, 0, 0, 0, 0, $width, $height, $size[1], $size[0]);
						break;
					//
					default:
						imagecopyresampled($tmp_file, $old_file, 0, 0, 0, 0, $width, $height, $size[0], $size[1]);
				}
				if (imagejpeg($tmp_file, $new_file)) $ret = 1;
				header('Content-Type:image/jpeg');
				header('Content-Type: text/html; charset=utf-8');

			}
		}
	}
	return $ret;

}

?>
