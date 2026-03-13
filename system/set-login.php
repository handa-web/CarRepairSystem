<?php

$result = 'error';
$data = [];
$mess = '';

if ($_POST) {

	session_name('car_repair_RESV-SYSTEM_from_2024');
	session_start();

	$csrf_token = getCsrfToken();

	include_once('../../config/library.php');
	include_once('../../config/db-connect.php');

	$csrf_token = isset($_POST['csrf_token']) ? (string)$_POST['csrf_token'] : '';
	$staff_mail = isset($_POST['staff_mail']) ? (string)$_POST['staff_mail'] : '';
	$staff_pass = isset($_POST['staff_pass']) ? (string)$_POST['staff_pass'] : '';

	if (checkCsrfToken($csrf_token)) {

		if ($staff_mail and $staff_pass) {

			$sql = "SELECT * FROM mst_staff WHERE staff_mail = ? LIMIT 0, 1";
			if ($res = sql($sql, $staff_mail)) {

				$dat = $res[0];

				if (! $dat['staff_stop']) {

					if (password_verify($staff_pass, $dat['staff_pass'])) {

						session_regenerate_id(true);
						$_SESSION['id'] = (int)$dat['staff_id'];
						$result = 'success';

					}
					else $mess = 'ログインに失敗しました。メールアドレスまたはパスワードが異なります。';

				}
				else $mess = 'ログインに失敗しました。アカウントが停止されています。';

			}
			else $mess = 'ログインに失敗しました。メールアドレスまたはパスワードが異なります。';

		}
		else $mess = 'ログインに失敗しました。必須項目を入力してください。';

	}
	else $mess = 'データの取得に失敗しました。画面を更新してもう一度送信してください。';

}
else $mess = 'データの受信に失敗しました。';

$json = ['result' => $result, 'data' => $data, 'mess' => $mess];

echo json_encode($json);

?>
