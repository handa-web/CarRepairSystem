<?php

// 処理
// result : success / error
// data : 配列
// mess : 文字列

header('Content-Type: application/json; charset=utf-8');

$result = 'error';
$data = [];
$mess = '';

if ($_POST) {

	session_name('car_repair_RESV');
	session_start();

	include_once('../config/library.php');

	$csrf_token = (isset($_POST['csrf_token'])) ? (string)$_POST['csrf_token'] : '';

	if (checkCsrfToken($csrf_token)) {

		$_SESSION = [];
		session_destroy();
		$result = 'success';

	}
	else $mess = 'データの取得に失敗しました。画面を更新してもう一度送信してください。';

}
else $mess = 'データの受信に失敗しました。';

$json = ['result' => $result, 'data' => $data, 'mess' => $mess];

echo json_encode($json);

?>
