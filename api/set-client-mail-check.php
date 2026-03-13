<?php

// 処理
// result : success / error
// data : 配列
// mess : 文字列

header('Content-Type: application/json; charset=utf-8');

ini_set('display_errors', 0);

$result = 'error';
$data = [];
$mess = '';

if ($_POST) {

	session_name('car_repair_RESV');
	session_start();

	$csrf_token = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '';

	include_once('../config/library.php');

	if (checkCsrfToken($csrf_token)) {

		$client_name = '';
		$client_zip = '';
		$client_addr = '';
		$client_staff = '';
		$client_mail = isset($_POST['client_mail']) ? (string)$_POST['client_mail'] : '';
		$client_tel = '';
		$client_pass = '';
		$client_memo = '';
		$client_check_time = (new DateTime())->getTimestamp();
		$client_stop = 1;

		$date = Null;

		include_once('../config/db-connect.php');

		if ($client_mail) {

			$client_hash_pass = '';
			
			$sql = "SELECT * FROM mst_client WHERE client_mail = ? LIMIT 0, 1";
			if (! $res = sql($sql, $client_mail)) {

				$sql = "INSERT INTO mst_client VALUES(null, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
				$par = [$client_name, $client_zip, $client_addr, $client_staff, $client_mail, $client_hash_pass, $client_tel, $client_memo, $date, $client_check_time, $client_stop];
				if ($res = sql($sql, $par)) {

					include_once('../config/mailer.php');

					$limit_time = $client_check_time + (86400 * 3); // 72時間後

					$mail1_subj = '【メール認証】'.$site_name;

					$mail1_text = "この度はcar_repair予約システムのご利用いただき、誠にありがとうございます。\n";
					$mail1_text = "\n";
					$mail1_text .= "まだ新規登録は完了しておりません。\n";
					$mail1_text .= "72時間以内に、以下ＵＲＬからお客様情報を入力して新規登録を行ってください。\n";
					$mail1_text .= "--------------------------------------------------------------------------------\n";
					$mail1_text .= "https://example.com/client-new.php?mail=".urlencode($client_mail)."&cd=".$client_check_time."\n";
					$mail1_text .= "有効期限：".formatDateTime('@'.$limit_time)."まで\n";
					$mail1_text .= "--------------------------------------------------------------------------------\n";
					$mail1_text .= "※有効期限を過ぎた場合は別のメールアドレスでご登録いただくか、以下URLからお問い合わせください。\n";
					$mail1_text .= "https://example.com/contact.php\n";
					$mail1_text .= "--------------------------------------------------------------------------------\n";
					$mail1_text .= "このメールはシステムより自動送信されています。\n";
					$mail1_text .= "本メールに心当たりのない場合はお手数ですが破棄してください。\n";

					send_mailer($client_mail, $mail1_subj, $mail1_text);

					$result = 'success';

				}
				else $mess = '認証メールの送信に失敗しました。';

			}
			else $mess = '認証メールの送信に失敗しました。認証メールはすでに送信済みです。';

		}
		else $mess = '認証メールの送信に失敗しました。メールアドレスが未入力です。';

	}
	else $mess = 'データの取得に失敗しました。画面を更新してもう一度送信してください。';

}
else $mess = 'データの受信に失敗しました。';

$json = ['result' => $result, 'data' => $data, 'mess' => $mess];

echo json_encode($json);

?>
