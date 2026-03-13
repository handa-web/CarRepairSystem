<?php
/*
	日別処理
	1.前日以前プラン料金削除
	2.利用完了
*/

include_once('../config/library.php');
include_once('../config/db-connect.php');
include_once('../config/mailer.php');

$result_mess_list = [];

$date = new DateTimeImmutable('today');

$delete_time = $date->modify('-3 month')->format('Y-m-d H:i:s');

// 3ヶ月前のデータを削除
$sql = "DELETE dat_resv, dat_resv_mail, dat_staff_cale FROM dat_resv LEFT JOIN dat_resv_mail USING (resv_id) LEFT JOIN dat_staff_cale USING (resv_id) WHERE resv_status4_time < ?";
if ($res = sql($sql, $delete_time)) {
	$result_mess_list[] = '3ヶ月前のデータを削除しました。';
}
else {
	$result_mess_list[] = '3ヶ月前のデータの削除に失敗しました。';
}

// 翌年の祝日データを取得
$yaer = $date->modify('+1 year')->format('Y');
$month = $date->format('m');
$day = $date->format('d');
if ($month == 1 && $day == 1) {
	$url = 'https://holidays-jp.github.io/api/v1/' . $year . '/date.json'; // $year を使用
	if ($result = file_get_contents($url)) {
		$json_data = json_decode(mb_convert_encoding($result, 'UTF-8'), true); // 配列としてデコード
		$err = 0;
		foreach ($json_data as $time => $name) {
			$par = [];
			$par[':time'] = $time;
			$par[':name'] = $name;
			$sql = "INSERT INTO mst_holiday VALUES(:time, :name) ON DUPLICATE KEY UPDATE holiday_name = :name";
			if (! $res = sql($sql, $par)) $err = 1;
		}
		if (! $err) {
			$result_mess_list[] = '祝日データを更新しました。';
		}
		else {
			$result_mess_list[] = '祝日の更新に失敗しました。';
		}
	}
	else {
		$result_mess_list[] = '祝日の更新に失敗しました。JSONデータの取得に失敗しました。';
	}
}

$mail_subj = '【car_repair】日別処理完了';
$mail_text = "日別処理が完了しました。\n";
$mail_text .= "================================================================================\n";
foreach ($result_mess_list as $mess) {
	$mail_text .= $mess . "\n";
}
$mail_text .= "================================================================================\n";
$mail_text .= "処理日時：" . $date->format('Y-m-d H:i:s') . "\n";
$mail_text .= "================================================================================\n";
$mail_text .= "car_repair予約システム.\n";

send_mailer('handa@us-dsgn.com', $mail_subj, $mail_text);

?>
