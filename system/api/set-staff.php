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

		$staff_id = isset($_POST['staff_id']) ? (int)$_POST['staff_id'] : 0;
		$staff_name = isset($_POST['staff_name']) ? (string)$_POST['staff_name'] : '';
		$staff_mail = isset($_POST['staff_mail']) ? (string)$_POST['staff_mail'] : '';
		$staff_pass = isset($_POST['staff_pass']) ? (string)$_POST['staff_pass'] : '';
		$staff_pass2 = isset($_POST['staff_pass2']) ? (string)$_POST['staff_pass2'] : '';
		$staff_tel = isset($_POST['staff_tel']) ? str_replace('-', '', (string)$_POST['staff_tel']) : '';
		$staff_memo = isset($_POST['staff_memo']) ? (string)$_POST['staff_memo'] : '';
		$staff_admin = isset($_POST['staff_admin']) ? (int)$_POST['staff_admin'] : 0;

		$staff_sort = isset($_POST['staff_sort']) ? (int)$_POST['staff_sort'] : 0;
		$staff_stop = isset($_POST['staff_stop']) ? (int)$_POST['staff_stop'] : 0;

		$id_list = isset($_POST['id_list']) ? (array)$_POST['id_list'] : [];

		include_once('../../config/db-connect.php');

		if ($dele) {

			if ($staff_id) {

				$sql = "SELECT * FROM mst_staff WHERE staff_id = ? LIMIT 0, 1";
				if ($res = sql($sql, $staff_id)) {

					$dat = $res[0];

					if (! $dat['staff_admin']) {
						$sql = "DELETE FROM mst_staff WHERE staff_id = ?";
						if (sql($sql, $staff_id) !== false) {
							$result = 'success';
							$mess = 'データを削除しています。';
						}
					}
					else $mess = 'データの削除に失敗しました。システム管理者は削除できません。';

				}
				else $mess = 'データの取得に失敗しました。';

			}
			else $mess = 'データの削除に失敗しました。必須項目を入力してください。';

		}
		elseif ($new) {

			if ($staff_name && $staff_mail && $staff_pass && $staff_pass2) {

				$sql = "SELECT * FROM mst_staff WHERE staff_mail = ? LIMIT 0, 1";
				if (! $res = sql($sql, $staff_mail)) {

					if (hash_equals($staff_pass, $staff_pass2)) {

						$staff_hash_pass = password_hash($staff_pass, PASSWORD_BCRYPT);

						$sql = "INSERT INTO mst_staff VALUES(null, ?, ?, ?, ?, ?, ?, ?, ?)";
						$par = [$staff_name, $staff_mail, $staff_hash_pass, $staff_tel, $staff_memo, $staff_admin, $staff_sort, $staff_stop];
						if ($res = sql($sql, $par)) {
							$result = 'success';
							$data['id'] = (int)$res;
							$mess = 'データを登録しています。';
						}

					}
					else $mess = 'データの登録に失敗しました。パスワードが一致しません。';

				}
				else $mess = 'データの登録に失敗しました。メールアドレスが重複しています。';

			}
			else $mess = 'データの登録に失敗しました。必須項目を入力してください。';

		}
		elseif ($list) {

			foreach ($id_list as $id) {
				$sort = isset($_POST['sort'][$id]) ? (int)$_POST['sort'][$id] : 0;
				$stop = isset($_POST['stop'][$id]) ? (int)$_POST['stop'][$id] : 0;
				$sql = "UPDATE mst_staff SET staff_sort = ?, staff_stop = ? WHERE staff_id = ?";
				$par = [$sort, $stop, $id];
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

			if ($_SESSION['admin'] ||  (! $_SESSION['admin'] && $_SESSION['id'] == $staff_id)) {
				
				if ($staff_id && $staff_name && $staff_mail) {

					// $sql = "SELECT * FROM mst_staff WHERE staff_mail = ? LIMIT 0, 1";
					// if (! $res = sql($sql, $staff_mail)) {

						$sql = "UPDATE mst_staff SET staff_name = ?, staff_mail = ?, staff_tel = ?, staff_memo = ?, staff_sort = ?, staff_stop = ? WHERE staff_id = ?";
						$par = [$staff_name, $staff_mail, $staff_tel, $staff_memo, $staff_sort, $staff_stop, $staff_id];
						if ($res = sql($sql, $par)) {
							$result = 'success';
							$mess = 'データを更新しています。';
						}
						else $mess = 'データの更新に失敗しました。';

					// }
					// else $mess = 'データの登録に失敗しました。メールアドレスが重複しています。';
	
				}
				else $mess = 'データの更新に失敗しました。必須項目を入力してください。';

			}
			else $mess = 'データの更新に失敗しました。権限がありません。';

		}
		else $mess = 'データの取得に失敗しました。';

	}
	else $mess = 'データの取得に失敗しました。画面を更新してもう一度送信してください。';

}
else $mess = 'データの受信に失敗しました。';

$json = ['result' => $result, 'data' => $data, 'mess' => $mess];

echo json_encode($json);

?>
