<?php

// 処理
// result : success / error
// data : 配列
// mess : 文字列

header('Content-Type: application/json; charset=utf-8');

$week_list = ['日', '月', '火', '水', '木', '金', '土'];
$result = 'error';
$data = [];
$mess = '';

if ($_POST) {

	session_name('car_repair_RESV');
	session_start();

	include_once('../config/library.php');
	include_once('../config/db-connect.php');

	$csrf_token = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '';

	$cancel = isset($_POST['cancel']) ? (int)$_POST['cancel'] : 0;

	$client_id = isset($_POST['client_id']) ? (int)$_POST['client_id'] : 0;
	$resv_id = isset($_POST['resv_id']) ? (int)$_POST['resv_id'] : 0;
	$resv_code = isset($_POST['resv_code']) ? (string)$_POST['resv_code'] : '';

	if (checkCsrfToken($csrf_token)) {

		if (isset($_SESSION['id']) && $_SESSION['id'] == $client_id) {

			$resv_time = new DateTime();
			$resv_time_text = $resv_time->format('Y年n月j日').'（'.$week_list[(int)$resv_time->format('w')].'）'.$resv_time->format('G時i分');

			$db->beginTransaction();

			if ($cancel) {

				$sql = "SELECT * FROM dat_resv INNER JOIN mst_client USING (client_id) WHERE resv_id = ? AND client_id = ? LIMIT 0, 1";
				if ($res = sql($sql, [$resv_id, $_SESSION['id']])) {

					$dat = $res[0];

					if ($resv_status < 3) {

						$sql = "UPDATE dat_resv SET resv_status = 5, resv_status5_time = ? WHERE resv_id = ? AND client_id = ?";
						$par = [$resv_time->format('Y-m-d H:i:s'), $resv_id, $_SESSION['id']];
						if (! $res = sql($sql, $par)) $mess = '予約のキャンセルに失敗しました。';

					}
					elseif ($resv_status == 3) {
						$mess = '予約のキャンセルに失敗しました。予約確定後はキャンセルできません。';
					}
					elseif ($resv_status == 4) {
						$mess = '予約のキャンセルに失敗しました。予約完了後はキャンセルできません。';
					}
					elseif ($resv_status == 5) {
						$mess = '予約のキャンセルに失敗しました。すでにキャンセル済みです。';
					}

				}
				else $mess = 'データの取得に失敗しました。';

			}

			if (! $mess) {

				$resv_img = [];
				$img_path = '../system/set-img/';
				for ($i = 1; $i <= 10; $i++) {
					@unlink($img_path.'resv-'.$resv_code.'-'.$i.'.jpg');
					$resv_img[$i] = set_image($_FILES['resv_img'.$i], $img_path, 'resv-'.$resv_code.'-'.$i);
				}

				$sql = "UPDATE dat_resv SET resv_img1 = ?, resv_img2 = ?, resv_img3 = ?, resv_img4 = ?, resv_img5 = ?, resv_img6 = ?, resv_img7 = ?, resv_img8 = ?, resv_img9 = ?, resv_img10 = ?  WHERE resv_id = ? AND client_id = ?";
				$par = [$resv_img[1], $resv_img[2], $resv_img[3],$resv_img[4], $resv_img[5], $resv_img[6],$resv_img[7], $resv_img[8], $resv_img[9],$resv_img[10], $resv_id, $_SESSION['id']];
				if (! $res = sql($sql, $par)) $mess = 'データの更新に失敗しました。';

			}

			if (! $mess) {
				$db->commit();
				$result = 'success';
				$mess = 'データを更新しています。';

				if ($cancel) {

					$resv_id = $dat['resv_id'];
					$resv_code = $dat['resv_code'];
					$client_name = $dat['client_name'];
					$resv_client_name = $dat['resv_client_name'];
					$resv_client_mail = $dat['resv_client_mail'];
					$resv_client_tel = $dat['resv_client_tel'];
					$resv_text = $dat['resv_text'];
					$resv_status = $dat['resv_status'];

					$rep_size_name = '';
					$sql = "SELECT * FROM mst_rep_size WHERE rep_size_id = ? LIMIT 0, 1";
					if ($res = sql($sql, $dat['rep_size_id'])) $rep_size_name = $res[0]['rep_size_name'];

					$rep_shape_name = '';
					$sql = "SELECT * FROM mst_rep_shape WHERE rep_shape_id = ? LIMIT 0, 1";
					if ($res = sql($sql, $dat['rep_shape_id'])) $rep_shape_name = $res[0]['rep_shape_name'];

					$rep_parts_name_list = [];
					foreach (explode(',',$dat['rep_parts_id_list']) as $id) {
						$sql = "SELECT * FROM mst_rep_parts WHERE rep_parts_id = ? LIMIT 0, 1";
						if ($res = sql($sql, $id)) $rep_parts_name_list[] = $res[0]['rep_parts_name'];
					}

					$rep_level_name = '';
					$sql = "SELECT * FROM mst_rep_level WHERE rep_level_id = ? LIMIT 0, 1";
					if ($res = sql($sql, $dat['rep_level_id'])) $rep_level_name = $res[0]['rep_level_name'];

					$shop = [];
					$sql = "SELECT * FROM mst_shop WHERE shop_id = 1 AND shop_stop = 0 LIMIT 0, 1";
					if ($res = sql($sql)) $shop = $res[0];

					include_once('../config/mailer.php');

					$mail1_subj = '【予約キャンセル】'.$site_name;

					$mail1_text = "ご予約いただき誠にありがとうございます。\n";
					$mail1_text .= "以下のご予約をキャンセルいたしましたのでご確認ください。\n";
					$mail1_text .= "--------------------------------------------------------------------------------\n";
					$mail1_text .= "【ご予約内容】\n";
					$mail1_text .= "受付日：".formatDateTime($dat['resv_status1_time'])."\n";
					$mail1_text .= "予約ID：".$resv_id."\n";
					$mail1_text .= "車両ナンバー：".$dat['resv_car_number']."\n";
					$mail1_text .= "入庫日：".formatDateTime($dat['resv_in_date'])."\n";
					$mail1_text .= "納車予定日：".formatDateTime($dat['resv_out_date'])."\n";
					$mail1_text .= "※納車予定日は、部品取り寄せの遅延等により遅れる場合がございます。\n";
					$mail1_text .= "予約番号：".$resv_code."\n";
					$mail1_text .= "--------------------------------------------------------------------------------\n";
					$mail1_text .= "【修理内容】\n";
					$mail1_text .= "分類：".$rep_size_name."\n";
					$mail1_text .= "形状：".$rep_shape_name."\n";
					$mail1_text .= "部位：".implode(', ', $rep_parts_name_list)."\n";
					$mail1_text .= "レベル：".$rep_level_name."\n";
					$mail1_text .= "--------------------------------------------------------------------------------\n";
					$mail1_text .= "【予約者情報】\n";
					$mail1_text .= "会社名：".$client_name."\n";
					$mail1_text .= "お名前：".$resv_client_name."\n";
					$mail1_text .= "メール：".$resv_client_mail."\n";
					$mail1_text .= "電話番号：".$resv_client_tel."\n";
					if ($resv_text) {
						$mail1_text .= "備考：\n";
						$mail1_text .= $resv_text."\n";
					}
					$mail1_text .= "--------------------------------------------------------------------------------\n";
					if ($shop['shop_resv_mail_status1']) {
						$mail1_text .= "【店舗メッセージ】\n";
						$mail1_text .= $shop['shop_resv_mail_status5']."\n";
						$mail1_text .= "--------------------------------------------------------------------------------\n";
					}
					$mail1_text .= "このメールはシステムより自動送信されています。\n";
					$mail1_text .= "本メールに心当たりのない場合はお手数ですが破棄してください。\n";
					$mail1_text .= "何かご不明な点がございましたら、お気軽にお問い合わせください。\n";
					$mail1_text .= "https://example.com/contact.php\n";
					$mail1_text .= "--------------------------------------------------------------------------------\n";
					$mail1_text .= "【店舗情報】.\n";
					$mail1_text .= "店舗名：".$shop['shop_name']."\n";
					$mail1_text .= "電話番号：".$shop['shop_tel']."\n";

					if (send_mailer($resv_client_mail, $mail1_subj, $mail1_text)) $resv_mail_ok = 1;
					else $resv_mail_ok = 0;

					$sql = "SELECT max(resv_mail_id) AS mid FROM dat_resv_mail WHERE resv_id = ? AND resv_mail_id = 1 LIMIT 0, 1";
					if ($res = sql($sql, [$resv_id])) {
						$resv_mail_id = (int)$res[0]['mid'] + 1;
						$sql = "INSERT INTO dat_resv_mail VALUES (?, ?, 5, ?, ?, ?, ?, ?)";
						$par = [$resv_id, $resv_mail_id, $mail1_subj, $mail1_text, $resv_client_mail, $resv_mail_ok, $resv_time->format('Y-m-d H:i:s')];
						$res = sql($sql, $par);
					}

					$mail2_subj = '【予約キャンセル】'.$site_name;

					$mail2_text .= "以下のメールが送信されました。\n";
					$mail2_text .= "================================================================================\n";
					$mail2_text .= $mail1_text;
					$mail2_text .= "================================================================================\n";
					$mail2_text .= "api";

					for ($i = 1; $i <= 3; $i++) {
						if ($shop['shop_mail'.$i]) {
							send_mailer($shop['shop_mail'.$i], $mail2_subj, $mail2_text);
							sleep(1);
						}
					}

				}
			}
			else $db->rollBack();

		}
		else $mess = 'データの更新に失敗しました。ログインし直してください。';

	}
	else $mess = 'データの取得に失敗しました。画面を更新してもう一度送信してください。';

}
else $mess = 'データの受信に失敗しました。';

$json = ['result' => $result, 'data' => $data, 'mess' => $mess];

echo json_encode($json);

?>
