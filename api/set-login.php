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

	$csrf_token = isset($_POST['csrf_token']) ? (string)$_POST['csrf_token'] : '';

	if (checkCsrfToken($csrf_token)) {

		include_once('../config/db-connect.php');

		$client_mail = isset($_POST['client_mail']) ? (string)$_POST['client_mail'] : '';
		$client_pass = isset($_POST['client_pass']) ? (string)$_POST['client_pass'] : '';	

		if ($client_mail and $client_pass) {

			$dat = [];
			$sql = "SELECT * FROM mst_client WHERE client_mail = ? LIMIT 0, 1";
			$par = [$client_mail];
			if ($res = sql($sql, $par)) {

				$dat = $res[0];

				if (password_verify($client_pass, $dat['client_pass'])) {

					session_regenerate_id(true);
					$_SESSION['id'] = (int)$dat['client_id'];
					$result = 'success';

				}
				else $mess = 'ログインに失敗しました。メールアドレスまたはパスワードが異なります。';

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
