<?php

// 処理
// result : success / error
// data : 配列
// mess : 文字列

header('Content-Type: application/json; charset=utf-8');
header('Content_Language: ja');

$result = 'error';
$data = [];
$mess = '';

if ($_POST) {

	$csrf_token = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '';

	session_name('car_repair_RESV-SYSTEM_from_2024');
	session_start();

	include_once('../../config/library.php');

	if (! isset($_SESSION['resv_date_list'])) $_SESSION['resv_date_list'] = [];

	if (checkCsrfToken($csrf_token)) {

		$staff_id = isset($_POST['staff_id']) ? (int)$_POST['staff_id'] : 0;
		$resv_span = isset($_POST['resv_span']) ? (int)$_POST['resv_span'] : 0;
		$plan_span = isset($_POST['plan_span']) ? (int)$_POST['plan_span'] : 0;
		$date_time = isset($_POST['date_time']) ? $_POST['date_time'] : '';
	
		$reset = isset($_POST['reset']) ? (int)$_POST['reset'] : 0;

		if ($reset || checkDateTime($date_time, 'Y-m-d H:i:s')) {

			$date_time1 = createDateTime($date_time)->format('Y-m-d H:i:s');
			$date_time2 = createDateTime($date_time)->modify('+30 minutes')->format('Y-m-d H:i:s');

			if ($reset || ($_SESSION['new_span']['staff_id'] == $staff_id && $_SESSION['new_span']['date_time1'] == $date_time1)) {
				$_SESSION['new_span'] = [];
			}
			else {
				if ($staff_id == $_SESSION['new_span']['staff_id'] && $resv_span == $_SESSION['new_span']['resv_span'] && $plan_span == $_SESSION['new_span']['plan_span']) {
					if ($_SESSION['new_span']['date_time1'] < $date_time2) {
						$_SESSION['new_span']['date_time2'] = $date_time2;
					}
					else {
						$_SESSION['new_span']['date_time1'] = $date_time1;
					}
				}
				else {
					$_SESSION['new_span'] = [
						'staff_id' => $staff_id,
						'resv_span' => $resv_span,
						'plan_span' => $plan_span,
						'date_time1' => $date_time1,
						'date_time2' => $date_time2,
					];
				}
			}

			$result = 'success';
			$data = ['id' => $to_edit_id];

		}
		else $mess = 'データの取得に失敗しました。無効な日時です。';

	}
	else $mess = 'データの取得に失敗しました。画面を更新してもう一度送信してください。';

}
else $mess = 'データの受信に失敗しました。';

$json = ['result' => $result, 'data' => $data, 'mess' => $mess];

echo json_encode($json);

?>
