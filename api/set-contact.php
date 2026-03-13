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

		$cont_company = isset($_POST['cont_company']) ? (string)$_POST['cont_company'] : '';
		$cont_staff = isset($_POST['cont_staff']) ? (string)$_POST['cont_staff'] : '';
		$cont_tel = isset($_POST['cont_tel']) ? str_replace('-', '', (string)$_POST['cont_tel']) : '';
		$cont_mail = isset($_POST['cont_mail']) ? (string)$_POST['cont_mail'] : '';
		$cont_text = isset($_POST['cont_text']) ? (string)$_POST['cont_text'] : '';

		$sql = "SELECT * FROM mst_shop WHERE shop_id = 1 AND shop_stop = 0 LIMIT 0, 1";
		if ($res = sql($sql)) {

			if ($cont_company && $cont_staff && $cont_tel && $cont_mail && $cont_text) {

				include_once('../config/mailer.php');

				$mail1_subj = '【お問い合わせ】'.$site_name;

				$mail1_text = "お問い合わせありがとうございます。。\n";
				$mail1_text .= "以下の内容で受け付けました。\n";
				$mail1_text .= "--------------------------------------------------------------------------------\n";
				$mail1_text .= "会社名：".$cont_company."\n";
				$mail1_text .= "メール：".$cont_mail."\n";
				$mail1_text .= "電話番号：".$cont_tel."\n";
				$mail1_text .= "担当者名：".$cont_staff."\n";
				$mail1_text .= "お問い合わせ内容：\n".$cont_text."\n";
				$mail1_text .= "--------------------------------------------------------------------------------\n";
				$mail1_text .= "このメールはシステムより自動送信されています。\n";
				$mail1_text .= "本メールに心当たりのない場合はお手数ですが破棄してください。\n";

				send_mailer($cont_mail, $mail1_subj, $mail1_text);

				$result = 'success';
				$mess = 'お問い合わせメールを送信しています。';

				for ($i = 1; $i <= 3; $i++) {
					if ($shop['shop_mail'.$i]) {
						send_mailer($shop['shop_mail'.$i], $mail2_subj, $mail2_text);
						sleep(1);
					}
				}

			}
			else $mess = 'お問い合わせメールの送信に失敗しました。必須項目を入力してください。';

		}
		else $mess = 'ご予約に失敗しました。店舗情報の取得に失敗しました。';

	}
	else $mess = 'データの取得に失敗しました。画面を更新してもう一度送信してください。';

}
else $mess = 'データの受信に失敗しました。';

$json = ['result' => $result, 'data' => $data, 'mess' => $mess];

echo json_encode($json);

?>
