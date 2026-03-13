<?php

// 処理
// result : success / error
// data : 配列
// mess : 文字列

header('Content-Type: application/json; charset=utf-8');
header('Content_Language: ja');

ini_set('display_errors', 0);

$week_list = ['日', '月', '火', '水', '木', '金', '土'];
$status_list = [1 => '受付', 2 => '修正', 3 => '確定', 4 => '完了', 5 => 'キャンセル'
];
$result = 'error';
$data = [];
$mess = '';

if ($_POST) {

	session_name('car_repair_RESV-SYSTEM_from_2024');
	session_start();

	include_once('../../config/library.php');
	include_once('../../config/db-connect.php');
	include_once('../../in-functions.php');

	$csrf_token = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '';

	$new = isset($_POST['new']) ? (int)$_POST['new'] : 0;
	$edit = isset($_POST['edit']) ? (int)$_POST['edit'] : 0;
	$dele = isset($_POST['dele']) ? (int)$_POST['dele'] : 0;

	$resv_id = isset($_POST['resv_id']) ? (int)$_POST['resv_id'] : 0;
	$in_date = isset($_POST['in_date']) ? (string)$_POST['in_date'] : '';
	$out_date = isset($_POST['out_date']) ? (string)$_POST['out_date'] : '';
	$rep_size_id = isset($_POST['rep_size_id']) ? (int)$_POST['rep_size_id'] : 0;
	$rep_shape_id = isset($_POST['rep_shape_id']) ? (int)$_POST['rep_shape_id'] : 0;
	$rep_parts_id = isset($_POST['rep_parts_id']) ? (array)$_POST['rep_parts_id'] : [];
	$rep_parts_id_list = implode(',', $rep_parts_id);
	$rep_level_id = isset($_POST['rep_level_id']) ? (int)$_POST['rep_level_id'] : 0;
	$resv_span = isset($_POST['resv_span']) ? (array)$_POST['resv_span'] : [];
	$client_id = isset($_POST['client_id']) ? (int)$_POST['client_id'] : 0;
	$resv_client_name = isset($_POST['resv_client_name']) ? (string)$_POST['resv_client_name'] : '';
	$resv_client_mail = isset($_POST['resv_client_mail']) ? (string)$_POST['resv_client_mail'] : '';
	$resv_client_tel = isset($_POST['resv_client_tel']) ? (string)$_POST['resv_client_tel'] : '';
	$resv_car_number = isset($_POST['resv_car_number']) ? (string)$_POST['resv_car_number'] : '';
	$resv_text = isset($_POST['resv_text']) ? (string)$_POST['resv_text'] : '';
	$resv_memo = isset($_POST['resv_memo']) ? (string)$_POST['resv_memo'] : '';
	$resv_code = isset($_POST['resv_code']) ? (string)$_POST['resv_code'] : '';

	$resv_img = [];
	for ($i = 1; $i <= 10; $i++) {
		if (isset($_FILES['resv_img'.$i])) $resv_img[$i] = $_FILES['resv_img'.$i];
		else $resv_img[$i] = [];
	}

	$resv_status = isset($_POST['resv_status']) ? (int)$_POST['resv_status'] : 0;
	$new_resv_status = isset($_POST['new_resv_status']) ? (int)$_POST['new_resv_status'] : 0;
	$resv_status1_time = (isset($_POST['resv_status1_time']) && $_POST['resv_status1_time']) ? (string)$_POST['resv_status1_time'] : Null;
	$resv_status2_time = (isset($_POST['resv_status2_time']) && $_POST['resv_status2_time']) ? (string)$_POST['resv_status2_time'] : Null;
	$resv_status3_time = (isset($_POST['resv_status3_time']) && $_POST['resv_status3_time']) ? (string)$_POST['resv_status3_time'] : Null;
	$resv_status4_time = (isset($_POST['resv_status4_time']) && $_POST['resv_status4_time']) ? (string)$_POST['resv_status4_time'] : Null;
	$resv_status5_time = (isset($_POST['resv_status5_time']) && $_POST['resv_status5_time']) ? (string)$_POST['resv_status5_time'] : Null;
	$resv_mail_text = isset($_POST['resv_mail_text'.$new_resv_status]) ? (string)$_POST['resv_mail_text'.$new_resv_status] : '';

	$date = new DateTimeImmutable();
	$today = $date->format('Y-m-d');
	$code = uniqid();

	if (checkCsrfToken($csrf_token)) {

		$rep_size_name = '';
		$sql = "SELECT * FROM mst_rep_size WHERE rep_size_id = ? AND rep_size_stop = 0 LIMIT 0, 1";
		if ($res = sql($sql, $rep_size_id)) $rep_size_name = $res[0]['rep_size_name'];

		$rep_shape_name = '';
		$sql = "SELECT * FROM mst_rep_shape WHERE rep_shape_id = ? AND rep_shape_stop = 0 LIMIT 0, 1";
		if ($res = sql($sql, $rep_shape_id)) $rep_shape_name = $res[0]['rep_shape_name'];

		$rep_parts_name = '';
		$rep_parts_name_list = [];
		$sql = "SELECT * FROM mst_rep_parts WHERE rep_parts_stop = 0";
		if ($res = sql($sql)) {
			foreach ($res as $row) {
				if (in_array($row['rep_parts_id'], $rep_parts_id)) $rep_parts_name_list[] = $row['rep_parts_name'];
			}
			$rep_parts_name = implode(',', $rep_parts_name_list);
		}

		$rep_level_name = '';
		$sql = "SELECT * FROM mst_rep_level WHERE rep_level_id = ? AND rep_level_stop = 0 LIMIT 0, 1";
		if ($res = sql($sql, $rep_level_id)) $rep_level_name = $res[0]['rep_level_name'];

		$resv_time = new DateTime();
		$resv_time_text = $resv_time->format('Y年n月j日').'（'.$week_list[(int)$resv_time->format('w')].'）'.$resv_time->format('G時i分');

		if ($new) {

			if ($client_id && $resv_client_name && $resv_client_mail && $resv_client_tel && $in_date && $out_date && $resv_car_number) {

				$db->beginTransaction();

				$resv_code = uniqid();

				$resv_img = [];
				$img_path = '../set-img/';
				for ($i = 1; $i <= 10; $i++) {
					@unlink($img_path.'resv-'.$resv_code.'-'.$i.'.jpg');
					$resv_img[$i] = set_image($_FILES['resv_img'.$i], $img_path, 'resv-'.$resv_code.'-'.$i);
				}

				$sql = "INSERT INTO dat_resv VALUES (null, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, ?, null, null, null, null, ?)";
				$par = [$in_date, $out_date, $rep_size_id, $rep_shape_id, $rep_parts_id_list, $rep_level_id, $client_id, $resv_client_name, $resv_client_mail, $resv_client_tel, $resv_car_number, $resv_img[1], $resv_img[2], $resv_img[3],$resv_img[4], $resv_img[5], $resv_img[6],$resv_img[7], $resv_img[8], $resv_img[9],$resv_img[10], $resv_text, $resv_memo, $resv_time->format('Y-m-d H:i:s'), $resv_code];
				if ($res = sql($sql, $par)) {

					$resv_id = $res;

				}
				else $mess = 'データの登録に失敗しました。';

				if (! $mess) {
					$db->commit();
					$result = 'success';
					$data = ['id' => $resv_id];
					$mess = 'データを登録しています。';
					unset($_SESSION['new_resv'], $_SESSION['new_span']);
				}
				else $db->rollBack();

			}
			else $mess = 'データの登録に失敗しました。必須項目を入力してください。';

		}
		elseif ($edit) {

			if ($resv_id && $client_id && $resv_client_name && $resv_client_mail && $resv_client_tel && $in_date && $out_date && $resv_car_number && $resv_code) {

				$db->beginTransaction();

				if ($resv_status < $new_resv_status) {
					$resv_status = $new_resv_status;
					switch ($resv_status) {
						case 2:
							$resv_status2_time = $resv_time->format('Y-m-d H:i:s');
							break;
						case 3:
							$resv_status3_time = $resv_time->format('Y-m-d H:i:s');
							break;
						case 4:
							$resv_status4_time = $resv_time->format('Y-m-d H:i:s');
							break;
						case 5:
							$resv_status5_time = $resv_time->format('Y-m-d H:i:s');
							break;
					}
				}

				$resv_img = [];
				$img_path = '../set-img/';
				for ($i = 1; $i <= 10; $i++) {
					@unlink($img_path.'resv-'.$resv_code.'-'.$i.'.jpg');
					$resv_img[$i] = set_image($_FILES['resv_img'.$i], $img_path, 'resv-'.$resv_code.'-'.$i);
				}

				$sql = "UPDATE dat_resv SET resv_in_date = ?, resv_out_date = ?, rep_size_id = ?, rep_shape_id = ?, rep_parts_id_list = ?, rep_level_id = ?, client_id = ?, resv_client_name = ?, resv_client_mail = ?, resv_client_tel = ?, resv_car_number = ?, resv_img1 = ?, resv_img2 = ?, resv_img3 = ?, resv_img4 = ?, resv_img5 = ?, resv_img6 = ?, resv_img7 = ?, resv_img8 = ?, resv_img9 = ?, resv_img10 = ?, resv_text = ?, resv_memo = ?, resv_status = ?, resv_status2_time = ?, resv_status3_time = ?, resv_status4_time = ?, resv_status5_time = ? WHERE resv_id = ?";
				$par = [$in_date, $out_date, $rep_size_id, $rep_shape_id, $rep_parts_id_list, $rep_level_id, $client_id, $resv_client_name, $resv_client_mail, $resv_client_tel, $resv_car_number, $resv_img[1], $resv_img[2], $resv_img[3],$resv_img[4], $resv_img[5], $resv_img[6],$resv_img[7], $resv_img[8], $resv_img[9],$resv_img[10], $resv_text, $resv_memo, $resv_status, $resv_status2_time, $resv_status3_time, $resv_status4_time, $resv_status5_time, $resv_id];
				if ($res = sql($sql, $par)) {

				}
				else $mess = 'データの更新に失敗しました。';

				$client_name = '';
				$sql = "SELECT * FROM mst_client WHERE client_id = ? LIMIT 0, 1";
				if ($res = sql($sql, $client_id)) {
					$client_name = $res[0]['client_name'];
				}
				else $mess = 'データの取得に失敗しました。';

				if (! $mess) {

					$db->commit();
					$result = 'success';
					$mess = 'データを更新しています。';
					unset($_SESSION['new_resv'], $_SESSION['new_span']);

					if ($new_resv_status) {

						$sql = "SELECT * FROM mst_shop WHERE shop_id = 1 AND shop_stop = 0 LIMIT 0, 1";
						if ($res = sql($sql)) $shop = $res[0];
	
						include_once('../../config/mailer.php');
	
						$mail_subj = '【予約'.$status_list[$resv_status].'】'.$site_name;
	
						$mail1_text = "ご予約いただき誠にありがとうございます。\n";
						$mail1_text .= "以下のご予約を".$status_list[$resv_status]."いたしましたのでご確認ください。\n";
						$mail1_text .= "--------------------------------------------------------------------------------\n";
						$mail1_text .= "【ご予約内容】\n";
						$mail1_text .= "受付日：".formatDateTime($resv_status1_time)."\n";
						$mail1_text .= "予約ID：".$resv_id."\n";
						$mail1_text .= "車両ナンバー：".$resv_car_number."\n";
						$mail1_text .= "入庫日：".formatDateTime($in_date)."\n";
						$mail1_text .= "納車予定日：".formatDateTime($out_date)."\n";
						if ($resv_status < 4) {
							$mail1_text .= "※納車予定日は、部品取り寄せの遅延等により遅れる場合がございます。\n";
						}
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
						if ($resv_mail_text) {
							$mail1_text .= "【店舗メッセージ】\n";
							$mail1_text .= $resv_mail_text."\n";
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
	
						if (send_mailer($resv_client_mail, $mail_subj, $mail1_text)) $resv_mail_ok = 1;
						else $resv_mail_ok = 0;
	
						$sql = "SELECT max(resv_mail_id) AS mid FROM dat_resv_mail WHERE resv_id = ? LIMIT 0, 1";
						if ($res = sql($sql, [$resv_id])) {
							$resv_mail_id = (int)$res[0]['mid'] + 1;
							$sql = "INSERT INTO dat_resv_mail VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
							$par = [$resv_id, $resv_mail_id, $resv_status, $mail_subj, $mail1_text, $resv_client_mail, $resv_mail_ok, $resv_time->format('Y-m-d H:i:s')];
							$res = sql($sql, $par);
						}
	
						$mail2_text = "";
						$mail2_text .= "以下のメールが送信されました。\n";
						$mail2_text .= "================================================================================\n";
						$mail2_text .= $mail1_text;
						$mail2_text .= "================================================================================\n";
						$mail2_text .= "api";
	
						for ($i = 1; $i <= 3; $i++) {
							if ($shop['shop_mail'.$i]) {
								send_mailer($shop['shop_mail'.$i], $mail_subj, $mail2_text);
								sleep(1);
							}
						}

					}

				}
				else $db->rollBack();

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
