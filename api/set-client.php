<?php

// 処理
// result : success / error
// data : 配列
// mess : 文字列

header('Content-Type: application/json; charset=utf-8');

$result = 'error';
$data = [];
$mess = '';

ini_set('display_errors', 0);

if ($_POST) {

	session_name('car_repair_RESV');
	session_start();

	$csrf_token = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '';

	include_once('../config/library.php');

	if (checkCsrfToken($csrf_token)) {

		$new = isset($_POST['new']) ? (int)$_POST['new'] : 0;
		$edit = isset($_POST['edit']) ? (int)$_POST['edit'] : 0;
		$list = isset($_POST['list']) ? (int)$_POST['list'] : 0;
		$dele = isset($_POST['dele']) ? (int)$_POST['dele'] : 0;

		$client_id = isset($_SESSION['id']) ? (int)$_SESSION['id'] : 0;
		$client_name = isset($_POST['client_name']) ? (string)$_POST['client_name'] : '';
		$client_zip = isset($_POST['client_zip']) ? str_replace('-', '', str_replace('〒', '', (string)$_POST['client_zip'])) : '';
		$client_addr = isset($_POST['client_addr']) ? (string)$_POST['client_addr'] : '';
		$client_staff = isset($_POST['client_staff']) ? (string)$_POST['client_staff'] : '';
		$client_mail = isset($_POST['client_mail']) ? (string)$_POST['client_mail'] : '';
		$client_tel = isset($_POST['client_tel']) ? str_replace('-', '', (string)$_POST['client_tel']) : '';
		$client_pass = isset($_POST['client_pass']) ? (string)$_POST['client_pass'] : '';
		$client_pass2 = isset($_POST['client_pass2']) ? (string)$_POST['client_pass2'] : '';
		$client_memo = isset($_POST['client_memo']) ? (string)$_POST['client_memo'] : '';
		$client_check_time = isset($_POST['client_check_time']) ? (int)$_POST['client_check_time'] : 0;
		$client_stop = isset($_POST['client_stop']) ? (int)$_POST['client_stop'] : 0;

		$date = (new DateTime())->format('Y-m-d');

		include_once('../config/db-connect.php');

		if ($new) {

			$sql = "SELECT client_id FROM mst_client WHERE client_mail = ? AND client_check_time = ?";
			$par = [$client_mail, $client_check_time];
			if ($res = sql($sql, $par)) {

				$client_id = (int)$res[0]['client_id'];

				if ($client_name && $client_staff && $client_mail && $client_tel && $client_pass && $client_pass2 && $client_check_time) {

					if (hash_equals($client_pass, $client_pass2)) {

						$client_hash_pass = password_hash($client_pass, PASSWORD_BCRYPT);

						$sql = "UPDATE mst_client SET client_name = ?, client_zip = ?, client_addr = ?, client_staff = ?, client_tel = ?, client_pass = ?, client_new_date = ?, client_check_time = 0, client_stop = 0 WHERE client_id = ?";
						$par = [$client_name, $client_zip, $client_addr, $client_staff, $client_tel, $client_hash_pass, $date, $client_id];
						if ($res = sql($sql, $par)) {

							include_once('../config/mailer.php');

							$mail1_subj = '【新規登録】'.$site_name;

							$mail1_text = "この度はcar_repair予約システムのご利用ありがとうございます。。\n";
							$mail1_text .= "以下の内容で新規登録を行いました。\n";
							$mail1_text .= "--------------------------------------------------------------------------------\n";
							$mail1_text .= "【お客様情報】\n";
							$mail1_text .= "会社名：".$client_name."\n";
							if ($client_zip) $mail1_text .= "郵便番号：".$client_zip."\n";
							if ($client_addr) $mail1_text .= "住所：".$client_addr."\n";
							$mail1_text .= "担当者名：".$client_staff."\n";
							$mail1_text .= "メール：".$client_mail."\n";
							$mail1_text .= "電話番号：".$client_tel."\n";
							$mail1_text .= "--------------------------------------------------------------------------------\n";
							$mail1_text .= "このメールはシステムより自動送信されています。\n";
							$mail1_text .= "本メールに心当たりのない場合はお手数ですが破棄してください。\n";
							$mail1_text .= "何かご不明な点がございましたら、お気軽にお問い合わせください。\n";
							$mail1_text .= "https://example.com/contact.php\n";

							send_mailer($client_mail, $mail1_subj, $mail1_text);

							$result = 'success';
							$mess = 'ログインしています。';

							$_SESSION['id'] = $client_id;

						}
						else $mess = 'データの登録に失敗しました。';

					}
					else $mess = 'データの登録に失敗しました。パスワードが一致しません。';

				}
				else $mess = 'データの登録に失敗しました。必須項目を入力してください。';

			}
			else $mess = "データの取得に失敗しました。";

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
