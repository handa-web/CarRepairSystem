<?php

// 処理
// result : success / error
// data : 配列
// mess : 文字列

header('Content-Type: application/json; charset=utf-8');
header('Content_Language: ja');

$week_list = ['日', '月', '火', '水', '木', '金', '土'];
$result = 'error';
$data = [];
$mess = '';

if ($_POST) {

	session_name('car_repair_RESV-SYSTEM_from_2024');
	session_start();

	include_once('../../config/library.php');
	include_once('../../config/db-connect.php');
	include_once('../../in-functions.php');

	$csrf_token = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '';

	$new = isset($_POST['new']) ? (int)$_POST['new'] : 0;
	$dele = isset($_POST['dele']) ? (int)$_POST['dele'] : 0;

	$staff_id = isset($_POST['staff_id']) ? (int)$_POST['staff_id'] : 0;
	$staff_cale_date = isset($_POST['staff_cale_date']) ? (string)$_POST['staff_cale_date'] : '';
	$staff_cale_time1 = isset($_POST['staff_cale_time1']) ? (string)$_POST['staff_cale_time1'] : '';
	$staff_cale_time2 = isset($_POST['staff_cale_time2']) ? (string)$_POST['staff_cale_time2'] : '';
	$resv_id = isset($_POST['resv_id']) ? (int)$_POST['resv_id'] : 0;
	$plan_id = isset($_POST['plan_id']) ? (int)$_POST['plan_id'] : 0;

	$date_time1 = isset($_POST['date_time1']) ? (string)$_POST['date_time1'] : '';
	$date_time2 = isset($_POST['date_time2']) ? (string)$_POST['date_time2'] : '';

	if (checkCsrfToken($csrf_token)) {

		if ($dele) {

			if ($staff_id && $staff_cale_date && $staff_cale_time1 && $staff_cale_time2) {

				$sql = "DELETE FROM dat_staff_cale WHERE staff_id = ? AND staff_cale_date = ? AND staff_cale_time1 = ? AND staff_cale_time2 = ?";
				$par = [$staff_id, $staff_cale_date, $staff_cale_time1, $staff_cale_time2];
				if ($res = sql($sql, $par)) {
					$result = 'success';
					$mess = 'データを削除しています。';
				}
				else $mess = 'データの削除に失敗しました。';

			}
			else $mess = 'データの削除に失敗しました。必須項目を入力してください。';

		}
		elseif ($new) {

			if ($staff_id && $date_time1 && $date_time2) {

				$new_span = [];
				$new_span[] = ['staff_id' => $staff_id, 'date_time1' => $date_time1, 'date_time2' => $date_time2];

				if ($resv_id) {
					if ($set_mess = setStaffCale($new_span, $resv_id, 0)) {
						$mess = $set_mess;
					}
				}
				elseif ($plan_id) {
					if ($set_mess = setStaffCale($new_span, 0, $plan_id)) {
						$mess = $set_mess;
					}
				}
				else {
					$mess = 'データの更新に失敗しました。';
				}

				if (! $mess) {
					$result = 'success';
					$mess = 'データを登録しています。';
					$_SESSION['new_span'] = [];
				}

			}
			else $mess = 'データの登録に失敗しました。必須項目を入力してください。';

		}
		else $mess = 'データの取得に失敗しました。';

	}
	else $mess = 'データの取得に失敗しました。画面を更新してもう一度送信してください。';

}
else $mess = 'データの受信に失敗しました。';

$json = ['result' => $result, 'data' => $data, 'mess' => $mess];

echo json_encode($json);

?>
