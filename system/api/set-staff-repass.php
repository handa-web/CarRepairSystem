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

	session_name('car_repair_RESV-SYSTEM_from_2024');
	session_start();

	include_once('../../config/library.php');

	if (isset($_SESSION['id']) && $_SESSION['id']) {

		$_SESSION['id'] = (int)$_SESSION['id'];
		$csrf_token = isset($_POST['csrf_token']) ? (string)$_POST['csrf_token'] : '';

		if (checkCsrfToken($csrf_token)) {

			include_once('../../config/db-connect.php');

			$staff_mail = isset($_POST['staff_mail']) ? (string)$_POST['staff_mail'] : '';
			$staff_pass = isset($_POST['staff_pass']) ? (string)$_POST['staff_pass'] : '';
			$staff_pass2 = isset($_POST['staff_pass2']) ? (string)$_POST['staff_pass2'] : '';

			if ($_SESSION['admin'] ||  (! $_SESSION['admin'] && $_SESSION['id'] == $staff_id)) {

				if ($staff_pass && $staff_pass2) {

					if (preg_match('/^(?=.*[a-zA-Z0-9@$!%*?&]).{8,}$/', $staff_pass) && preg_match('/^(?=.*[a-zA-Z0-9@$!%*?&]).{8,}$/', $staff_pass)) {

						if (hash_equals($staff_pass, $staff_pass2)) {

							$staff_hash_pass = password_hash($staff_pass, PASSWORD_BCRYPT);

							$sql = "SELECT staff_id FROM mst_staff WHERE staff_id = ? LIMIT 0, 1";
							if ($res = sql($sql, $_SESSION['id'])) {

								$sql = "UPDATE mst_staff SET staff_pass = ? WHERE staff_id = ?";
								$par = [$staff_hash_pass, $_SESSION['id']];
								if (sql($sql, $par) !== false) {

									include_once('../../config/mailer.php');

									$mail1_subj = '【パスワード変更】'.$site_name;

									$mail1_text = "この度はcar_repair予約システムのご利用ありがとうございます。。\n";
									$mail1_text .= "パスワード変更を行いました。\n";
									$mail1_text .= "--------------------------------------------------------------------------------\n";
									$mail1_text .= "このメールはシステムより自動送信されています。\n";
									$mail1_text .= "本メールに心当たりのない場合はお手数ですが破棄してください。\n";
									$mail1_text .= "何かご不明な点がございましたら、お気軽にお問い合わせください。\n";
									$mail1_text .= "https://example.com/contact.php\n";

									send_mailer($staff_mail, $mail1_subj, $mail1_text);

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
			else $mess = 'パスワードの再設定に失敗しました。権限がありません。';

		}
		else $mess = 'データの取得に失敗しました。画面を更新してもう一度送信してください。';

	}
	else $mess = 'パスワードの再設定に失敗しました。ログインしてください。';

}
else $mess = 'データの受信に失敗しました。';

$json = ['result' => $result, 'data' => $data, 'mess' => $mess];

echo json_encode($json);

?>
