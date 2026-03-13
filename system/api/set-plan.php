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
	$edit = isset($_POST['edit']) ? (int)$_POST['edit'] : 0;
	$dele = isset($_POST['dele']) ? (int)$_POST['dele'] : 0;

	$plan_id = isset($_POST['plan_id']) ? (int)$_POST['plan_id'] : 0;
	$plan_name = isset($_POST['plan_name']) ? (string)$_POST['plan_name'] : '';
	$plan_memo = isset($_POST['plan_memo']) ? (string)$_POST['plan_memo'] : '';

	$date = new DateTimeImmutable();
	$today = $date->format('Y-m-d');

	if (checkCsrfToken($csrf_token)) {

		if ($new) {

			if ($plan_name) {

				$db->beginTransaction();

				$sql = "INSERT INTO mst_plan VALUES (null, ?, ?)";
				$par = [$plan_name, $plan_memo];
				if ($res = sql($sql, $par)) {
	
					$plan_id = $res;
	
				}
				else $mess = 'データの登録に失敗しました。';
	
				if (! $mess) {
					$db->commit();
					$result = 'success';
					$data = ['id' => $plan_id];
					$mess = 'データを登録しています。';
					unset($_SESSION['new_plan'], $_SESSION['new_span']);
				}
				else $db->rollBack();

			}
			else $mess = 'データの登録に失敗しました。必須項目を入力してください。';

		}
		elseif ($edit) {

			if ($plan_id && $plan_name) {

				$db->beginTransaction();

				$sql = "UPDATE mst_plan SET plan_name = ?, plan_memo = ? WHERE plan_id = ?";
				$par = [$plan_name, $plan_memo, $plan_id];
				if ($res = sql($sql, $par)) {

				}
				else $mess = 'データの更新に失敗しました。';

				if (! $mess) {
					$db->commit();
					$result = 'success';
					$mess = 'データを更新しています。';
					unset($_SESSION['new_plan'], $_SESSION['new_span']);
				}
				else $db->rollBack();

			}
			else $mess = 'データの更新に失敗しました。必須項目を入力してください。';

		}
		else if ($dele) {

			if ($plan_id) {

				$db->beginTransaction();

				$sql = "DELETE FROM mst_plan WHERE plan_id = ?";
				if (sql($sql, $plan_id) !== false) {

					$sql = "DELETE FROM dat_staff_cale WHERE plan_id = ?";
					if (sql($sql, $plan_id) === false) {
						$mess = 'データの削除に失敗しました。';
					}

				}

				if (! $mess) {
					$db->commit();
					$result = 'success';
					$mess = 'データを更新しています。';
					unset($_SESSION['new_plan'], $_SESSION['new_span']);
				}
				else $db->rollBack();

			}
			else $mess = 'データの削除に失敗しました。必須項目を入力してください。';

		}
		else $mess = 'データの取得に失敗しました。';

	}
	else $mess = 'データの取得に失敗しました。画面を更新してもう一度送信してください。';

}
else $mess = 'データの受信に失敗しました。';

$json = ['result' => $result, 'data' => $data, 'mess' => $mess];

echo json_encode($json);

?>
