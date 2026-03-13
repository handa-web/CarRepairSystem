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

		$new = isset($_POST['new']) ? (int)$_POST['new'] : 0;
		$edit = isset($_POST['edit']) ? (int)$_POST['edit'] : 0;
		$list = isset($_POST['list']) ? (int)$_POST['list'] : 0;
		$dele = isset($_POST['dele']) ? (int)$_POST['dele'] : 0;

		$client_id = isset($_POST['client_id']) ? (int)$_POST['client_id'] : 0;
		$client_name = isset($_POST['client_name']) ? (string)$_POST['client_name'] : '';
		$client_zip = isset($_POST['client_zip']) ? str_replace('-', '', str_replace('〒', '', (string)$_POST['client_zip'])) : '';
		$client_addr = isset($_POST['client_addr']) ? (string)$_POST['client_addr'] : '';
		$client_staff = isset($_POST['client_staff']) ? (string)$_POST['client_staff'] : '';
		$client_mail = isset($_POST['client_mail']) ? (string)$_POST['client_mail'] : '';
		$client_tel = isset($_POST['client_tel']) ? str_replace('-', '', (string)$_POST['client_tel']) : '';
		$client_pass = isset($_POST['client_pass']) ? (string)$_POST['client_pass'] : '';
		$client_pass2 = isset($_POST['client_pass2']) ? (string)$_POST['client_pass2'] : '';
		$client_memo = isset($_POST['client_memo']) ? (string)$_POST['client_memo'] : '';
		$client_stop = isset($_POST['client_stop']) ? (int)$_POST['client_stop'] : 0;

		$id_list = isset($_POST['id_list']) ? (array)$_POST['id_list'] : [];

		$date = (new DateTime())->format('Y-m-d');

		include_once('../../config/db-connect.php');

		if ($dele) {

			if ($_SESSION['admin']) {

				if ($client_id) {

					$db->beginTransaction();

					$sql = "DELETE mst_client FROM mst_client WHERE client_id = ?";
					$par = [$client_id];
					if (sql($sql, $par) === false) $mess = 'データの削除に失敗しました。';

					$sql = "DELETE dat_resv, dat_resv_mail, dat_staff_cale FROM dat_resv LEFT JOIN dat_resv_mail USING (resv_id) LEFT JOIN dat_staff_cale USING (resv_id) WHERE client_id = ?";
					$par = [$client_id];
					if (sql($sql, $par) === false) $mess = 'データの削除に失敗しました。';

					if (! $mess) {
						$db->commit();
						$result = 'success';
						$mess = 'データを削除しています。';
					}
					else $db->rollBack();

				}
				else $mess = 'データの削除に失敗しました。必須項目を入力してください。';

			}
			else $mess = 'データの削除に失敗しました。権限がありません。';

		}
		elseif ($new) {

			if ($client_name && $client_staff && $client_mail && $client_tel && $client_pass && $client_pass2) {

				if (hash_equals($client_pass, $client_pass2)) {

					$client_hash_pass = password_hash($client_pass, PASSWORD_BCRYPT);

					$sql = "SELECT client_id FROM mst_client WHERE client_mail = ? LIMIT 0, 1";
					if (! $res = sql($sql, $client_mail)) {

						$sql = "INSERT INTO mst_client VALUES(null, ?, ?, ?, ?, ?, ?, ?, ?)";
						$par = [$client_name, $client_zip, $client_addr, $client_staff, $client_mail, $client_hash_pass, $client_tel, $client_memo, $date, $client_stop];
						if ($res = sql($sql, $par)) {
							$result = 'success';
							$data['id'] = (int)$res;
							$mess = 'データを登録しています。';
						}
						else $mess = 'データの登録に失敗しました。';

					}
					else $mess = 'データの登録に失敗しました。メールアドレスが重複しています。';

				}
				else $mess = 'データの登録に失敗しました。パスワードが一致しません。';

			}
			else $mess = 'データの登録に失敗しました。必須項目を入力してください。';

		}
		elseif ($list) {

			foreach ($id_list as $id) {
				$stop = isset($_POST['stop'][$id]) ? (int)$_POST['stop'][$id] : 0;
				$sql = "UPDATE mst_client SET client_stop = ? WHERE client_id = ?";
				$par = [$stop, $id];
				if (sql($sql, $par) === false) {
					$mess = 'データの更新に失敗しました。';
					break;
				}
			}
			if (! $mess) {
				$result = 'success';
				$mess = 'データを更新しています。';
			}

		}
		elseif ($edit) {

			if ($client_id && $client_name && $client_staff && $client_mail && $client_tel) {

				$sql = "UPDATE mst_client SET client_name = ?, client_zip = ?, client_addr = ?, client_staff = ?, client_mail = ?, client_tel = ?, client_stop = ? WHERE client_id = ?";
				$par = [$client_name, $client_zip, $client_addr, $client_staff, $client_mail, $client_tel, $client_stop, $client_id];
				if ($res = sql($sql, $par)) {
					$result = 'success';
					$mess = 'データを更新しています。';
				}
				else $mess = 'データの更新に失敗しました。';

			}
			else $mess = 'データの更新に失敗しました。必須項目を入力してください。';

		}
		else $mess = 'データの取得に失敗しました。';

	}
	else $mess = 'データの取得に失敗しました。画面を更新してもう一度送信してください。';

}
else $mess = 'データの受信に失敗しました。';

$json = ['result' => $result, 'data' => $data, 'mess' => $mess];

echo json_encode($json);

?>
