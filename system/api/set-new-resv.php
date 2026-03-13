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

		$resv_id = isset($_POST['resv_id']) ? (int)$_POST['resv_id'] : 0;
		$rep_size_id = isset($_POST['rep_size_id']) ? (int)$_POST['rep_size_id'] : 0;
		$rep_shape_id = isset($_POST['rep_shape_id']) ? (int)$_POST['rep_shape_id'] : 0;
		$rep_parts_id_list = isset($_POST['rep_parts_id_list']) ? (string)$_POST['rep_parts_id_list'] : '';
		$rep_level_id = isset($_POST['rep_level_id']) ? (int)$_POST['rep_level_id'] : 0;
		$client_id = isset($_POST['client_id']) ? (int)$_POST['client_id'] : 0;
		$resv_client_name = isset($_POST['resv_client_name']) ? (string)$_POST['resv_client_name'] : '';
		$resv_client_mail = isset($_POST['resv_client_mail']) ? (string)$_POST['resv_client_mail'] : '';
		$resv_client_tel = isset($_POST['resv_client_tel']) ? (string)$_POST['resv_client_tel'] : '';
		$resv_text = isset($_POST['resv_text']) ? (string)$_POST['resv_text'] : '';
		$resv_memo = isset($_POST['resv_memo']) ? (string)$_POST['resv_memo'] : '';

		$_SESSION['new_plan'] = [];
		$_SESSION['new_resv']['rep_size_id'] = $rep_size_id;
		$_SESSION['new_resv']['rep_shape_id'] = $rep_shape_id;
		$_SESSION['new_resv']['rep_parts_id_list'] = $rep_parts_id_list;
		$_SESSION['new_resv']['rep_level_id'] = $rep_level_id;
		$_SESSION['new_resv']['client_id'] = $client_id;
		$_SESSION['new_resv']['resv_client_name'] = $resv_client_name;
		$_SESSION['new_resv']['resv_client_mail'] = $resv_client_mail;
		$_SESSION['new_resv']['resv_client_tel'] = $resv_client_tel;
		$_SESSION['new_resv']['resv_text'] = $resv_text;
		$_SESSION['new_resv']['resv_memo'] = $resv_memo;

		$result = 'success';

	}
	else $mess = 'データの取得に失敗しました。画面を更新してもう一度送信してください。';

}
else $mess = 'データの受信に失敗しました。';

$json = ['result' => $result, 'data' => $data, 'mess' => $mess];

echo json_encode($json);

?>
