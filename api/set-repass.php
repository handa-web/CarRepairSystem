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

	if (isset($_SESSION['id']) && $_SESSION['id']) {

		$_SESSION['id'] = (int)$_SESSION['id'];
		$csrf_token = isset($_POST['csrf_token']) ? (string)$_POST['csrf_token'] : '';

		if (checkCsrfToken($csrf_token)) {

			include_once('../config/db-connect.php');

			$client_pass = isset($_POST['client_pass']) ? (string)$_POST['client_pass'] : '';
			$client_new_pass = isset($_POST['client_new_pass']) ? (string)$_POST['client_new_pass'] : '';
			$client_new_pass2 = isset($_POST['client_new_pass2']) ? (string)$_POST['client_new_pass2'] : '';

			if ($client_pass && $client_new_pass && $client_new_pass2) {

				if (preg_match('/^(?=.*[a-zA-Z0-9@$!%*?&]).{8,}$/', $client_pass) && preg_match('/^(?=.*[a-zA-Z0-9@$!%*?&]).{8,}$/', $client_new_pass)) {

					if (hash_equals($client_new_pass, $client_new_pass2)) {

						$client_hash_pass = password_hash($client_new_pass, PASSWORD_BCRYPT);

						$sql = "SELECT client_id FROM mst_client WHERE client_id = ? LIMIT 0, 1";
						if ($res = sql($sql, $_SESSION['id'])) {

							$sql = "UPDATE mst_client SET client_pass = ? WHERE client_id = ?";
							$par = [$client_hash_pass, $_SESSION['id']];
							if (sql($sql, $par) !== false) {

								include_once('../config/mailer.php');

								$mail1_subj = '【パスワード変更】'.$site_name;

								$mail1_text = "この度はcar_repair予約システムのご利用ありがとうございます。。\n";
								$mail1_text .= "パスワード変更を行いました。\n";
								$mail1_text .= "--------------------------------------------------------------------------------\n";
								$mail1_text .= "このメールはシステムより自動送信されています。\n";
								$mail1_text .= "本メールに心当たりのない場合はお手数ですが破棄してください。\n";
								$mail1_text .= "何かご不明な点がございましたら、お気軽にお問い合わせください。\n";
								$mail1_text .= "https://example.com/contact.php\n";

								send_mailer($client_mail, $mail1_subj, $mail1_text);

								$mess = 'パスワードを再設定しました。';
								$result = 'success';

							}
							else $mess = 'パスワードの再設定に失敗しました。';

						}
						else $mess = 'パスワードの再設定に失敗しました。このメールアドレスの登録されていません。';

					}
					else $mess = 'パスワードの再設定に失敗しました。確認用パスワードが異なります。';

				}
				else $mess = 'パスワードの再設定に失敗しました。パスワードが条件を満たしていません。';

			}
			else $mess = 'パスワードの再設定に失敗しました。必須項目を入力してください。';

		}
		else $mess = 'データの取得に失敗しました。画面を更新してもう一度送信してください。';

	}
	else $mess = 'パスワードの再設定に失敗しました。ログインしてください。';

}
else $mess = 'データの受信に失敗しました。';

$json = ['result' => $result, 'data' => $data, 'mess' => $mess];

echo json_encode($json);

?>
