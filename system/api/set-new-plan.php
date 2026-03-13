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

	$csrf_token = isset($_POST['csrf_token']) ? (string)$_POST['csrf_token'] : '';

	session_name('car_repair_RESV-SYSTEM_from_2024');
	session_start();

	include_once('../../config/library.php');

	if (checkCsrfToken($csrf_token)) {

		$plan_name = isset($_POST['plan_name']) ? (string)$_POST['plan_name'] : '';
		$plan_memo = isset($_POST['plan_memo']) ? (string)$_POST['plan_memo'] : '';

		$_SESSION['new_resv'] = [];
		$_SESSION['new_plan']['plan_name'] = $plan_name;
		$_SESSION['new_plan']['plan_memo'] = $plan_memo;

		$result = 'success';

	}
	else $mess = 'データの取得に失敗しました。画面を更新してもう一度送信してください。';

}
else $mess = 'データの受信に失敗しました。';

$json = ['result' => $result, 'data' => $data, 'mess' => $mess];

echo json_encode($json);

?>
