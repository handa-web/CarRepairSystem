<?php

// 処理
// result : success / error
// data : 配列
// mess : 文字列

header('Content-Type: application/json; charset=utf-8');
header('Content_Language: ja');

// ini_set('display_errors', 0);

$result = 'error';
$data = [];
$mess = '';

if ($_POST) {

	session_name('car_repair_RESV-SYSTEM_from_2024');
	session_start();

	$csrf_token = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '';

	include_once('../../config/library.php');

	if (checkCsrfToken($csrf_token)) {

		$edit = isset($_POST['edit']) ? (int)$_POST['edit'] : 0;

		$shop_code = isset($_POST['shop_code']) ? (string)$_POST['shop_code'] : '';
		$shop_name = isset($_POST['shop_name']) ? (string)$_POST['shop_name'] : '';
		$shop_zip = isset($_POST['shop_zip']) ? str_replace('-', '', (string)$_POST['shop_zip']) : '';
		$shop_addr = isset($_POST['shop_addr']) ? (string)$_POST['shop_addr'] : '';
		$shop_mail = isset($_POST['shop_staff']) ? (string)$_POST['shop_staff'] : '';
		$shop_tel = isset($_POST['shop_tel']) ? (string)$_POST['shop_tel'] : '';
		$shop_fax = isset($_POST['shop_fax']) ? (string)$_POST['shop_fax'] : '';
		// $shop_mail1 = isset($_POST['shop_mail1']) ? (string)$_POST['shop_mail1'] : '';
		$shop_mail2 = isset($_POST['shop_mail2']) ? (string)$_POST['shop_mail2'] : '';
		$shop_mail3 = isset($_POST['shop_mail3']) ? (string)$_POST['shop_mail3'] : '';
		$mail_status1 = isset($_POST['shop_resv_mail_status1']) ? (string)$_POST['shop_resv_mail_status1'] : '';
		$mail_status2 = isset($_POST['shop_resv_mail_status2']) ? (string)$_POST['shop_resv_mail_status2'] : '';
		$mail_status3 = isset($_POST['shop_resv_mail_status3']) ? (string)$_POST['shop_resv_mail_status3'] : '';
		$mail_status4 = isset($_POST['shop_resv_mail_status4']) ? (string)$_POST['shop_resv_mail_status4'] : '';
		$mail_status5 = isset($_POST['shop_resv_mail_status5']) ? (string)$_POST['shop_resv_mail_status5'] : '';
		$shop_resv_stop = isset($_POST['shop_resv_stop']) ? (int)$_POST['shop_resv_stop'] : 0;
		$shop_stop = isset($_POST['shop_stop']) ? (int)$_POST['shop_stop'] : 0;

		include_once('../../config/db-connect.php');

		$sql = "UPDATE mst_shop SET shop_code = ?, shop_name = ?, shop_zip = ?, shop_addr = ?, shop_staff = ?, shop_tel = ?, shop_fax = ?, shop_mail2 = ?, shop_mail3 = ?, shop_resv_mail_status1 = ?, shop_resv_mail_status2 = ?, shop_resv_mail_status3 = ?, shop_resv_mail_status4 = ?, shop_resv_mail_status5 = ?, shop_resv_stop = ?, shop_stop = ? WHERE shop_id = 1";
		$par = [$shop_code, $shop_name, $shop_zip, $shop_addr, $shop_mail, $shop_tel, $shop_fax, $shop_mail2, $shop_mail3, $mail_status1, $mail_status2, $mail_status3, $mail_status4, $mail_status5, $shop_resv_stop, $shop_stop];
		if (sql($sql, $par) !== false) {
			$result = 'success';
			$mess = 'データを更新しています。';
			if (isset($_SESSION['shop'])) $_SESSION['shop'] = [];
		}
		else $mess = 'データの更新に失敗しました。画面を更新してもう一度送信してください。';

	}
	else $mess = 'データの取得に失敗しました。画面を更新してもう一度送信してください。';

}
else $mess = 'データの受信に失敗しました。';

$json = ['result' => $result, 'data' => $data, 'mess' => $mess];

echo json_encode($json);

?>
