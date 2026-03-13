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

$mess = '';

if ($_POST) {

	session_name('car_repair_RESV');
	session_start();

	include_once('../config/library.php');

	$csrf_token = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '';

	if (checkCsrfToken($csrf_token)) {

		include_once('../config/db-connect.php');
		include_once('../in-functions.php');

		$in_date = isset($_POST['in_date']) ? (string)$_POST['in_date'] : '';
		$out_date = isset($_POST['out_date']) ? (string)$_POST['out_date'] : '';
		$size_id = isset($_POST['size']) ? (int)$_POST['size'] : 0;
		$shape_id = isset($_POST['shape']) ? (int)$_POST['shape'] : 0;
		$parts_id_list = isset($_POST['parts']) ? (array)$_POST['parts'] : [];
		$parts_id = implode(',', $parts_id_list);
		$level_id = isset($_POST['level']) ? (int)$_POST['level'] : 0;
		$client_id = isset($_POST['client_id']) ? (int)$_POST['client_id'] : 0;
		$resv_client_name = isset($_POST['resv_client_name']) ? (string)$_POST['resv_client_name'] : '';
		$resv_client_mail = isset($_POST['resv_client_mail']) ? (string)$_POST['resv_client_mail'] : '';
		$resv_client_tel = isset($_POST['resv_client_tel']) ? str_replace('-', '', (string)$_POST['resv_client_tel']) : '';
		$resv_car_number = isset($_POST['resv_car_number']) ? (string)$_POST['resv_car_number'] : '';
		$resv_text = '';

		$new_span = isset($_POST['new_span']) ? (array)$_POST['new_span'] : [];

		$time_list = [
			['start' => '09:00', 'end' => '11:00'],
			['start' => '11:00', 'end' => '13:00'],
			['start' => '14:00', 'end' => '16:00'],
			['start' => '16:00', 'end' => '18:00'],
		];

		if (isset($_SESSION['id']) && $_SESSION['id'] == $client_id) {

			if ($size_id && $shape_id && $parts_id_list && $level_id) {

				if ($resv_car_number) {

					if (checkDateTime($in_date, 'Y-m-d') && checkDateTime($out_date, 'Y-m-d')) {

						$sql = "SELECT * FROM mst_client WHERE client_id = ? LIMIT 0, 1";
						if ($res = sql($sql, $client_id)) {

							if (! $res[0]['client_stop']) {

								$client_name = $res[0]['client_name'];

								$sql = "SELECT * FROM mst_shop WHERE shop_id = 1 AND shop_stop = 0 LIMIT 0, 1";
								if ($res = sql($sql)) {

									if (! $res[0]['shop_resv_stop']) {

										$shop = $res[0];

										$rep_size_name = '';
										$sql = "SELECT * FROM mst_rep_size WHERE rep_size_id = ? LIMIT 0, 1";
										if ($res = sql($sql, $size_id)) $rep_size_name = $res[0]['rep_size_name'];

										$rep_shape_name = '';
										$sql = "SELECT * FROM mst_rep_shape WHERE rep_shape_id = ? LIMIT 0, 1";
										if ($res = sql($sql, $shape_id)) $rep_shape_name = $res[0]['rep_shape_name'];

										$rep_parts_name = '';
										$rep_parts_name_list = [];
										foreach ($parts_id_list as $id) {
											$sql = "SELECT * FROM mst_rep_parts WHERE rep_parts_id = ? LIMIT 0, 1";
											if ($res = sql($sql, $id)) $rep_parts_name_list[] = $res[0]['rep_parts_name'];
										}

										$rep_level_name = '';
										$sql = "SELECT * FROM mst_rep_level WHERE rep_level_id = ? LIMIT 0, 1";
										if ($res = sql($sql, $level_id)) $rep_level_name = $res[0]['rep_level_name'];
					
										$db->beginTransaction();
					
										$resv_time = new DateTime();
										$resv_time_text = $resv_time->format('Y年n月j日').'（'.$week_list[(int)$resv_time->format('w')].'）'.$resv_time->format('G時i分');

										$resv_code = uniqid();

										$resv_img = [];
										$img_path = '../system/set-img/';
										for ($i = 1; $i <= 10; $i++) {
											@unlink($img_path.'resv-'.$resv_code.'-'.$i.'.jpg');
											$resv_img[$i] = set_image($_FILES['resv_img'.$i], $img_path, 'resv-'.$resv_code.'-'.$i);
										}

										$sql = "INSERT INTO dat_resv VALUES (null, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, '', 1, ?, null, null, null, null, ?)";
										$par = [$in_date, $out_date, $size_id, $shape_id, $parts_id, $level_id, $client_id, $resv_client_name, $resv_client_mail, $resv_client_tel, $resv_car_number, $resv_img[1], $resv_img[2], $resv_img[3],$resv_img[4], $resv_img[5], $resv_img[6],$resv_img[7], $resv_img[8], $resv_img[9],$resv_img[10], $resv_text, $resv_time->format('Y-m-d H:i:s'), $resv_code];
										if ($res = sql($sql, $par)) {

											$resv_id = $res;

											if ($set_mess = setStaffCale($new_span, $resv_id, 0)) {
												$mess = $set_mess;
											}

											if (! $mess) {

												include_once('../config/mailer.php');

												$in_date_text = createDateTime($in_date)->format('Y年n月j日').'（'.$week_list[(int)createDateTime($in_date)->format('w')].'）';
												$out_date_text = createDateTime($out_date)->format('Y年n月j日').'（'.$week_list[(int)createDateTime($out_date)->format('w')].'）';

												$mail1_subj = '【予約受付】'.$site_name;

												$mail1_text = "ご予約いただき誠にありがとうございます。\n";
												$mail1_text .= "以下のご予約を受付いたしましたのでご確認ください。\n";
												$mail1_text .= "--------------------------------------------------------------------------------\n";
												$mail1_text .= "【ご予約内容】\n";
												$mail1_text .= "受付日：".$resv_time_text."\n";
												$mail1_text .= "予約ID：".$resv_id."\n";
												$mail1_text .= "車両ナンバー：".$resv_car_number."\n";
												$mail1_text .= "入庫日：".$in_date_text."\n";
												$mail1_text .= "納車予定日：".$out_date_text."\n";
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
													$mail1_text .= $shop['shop_resv_mail_status1']."\n";
													$mail1_text .= "--------------------------------------------------------------------------------\n";
												}
												$mail1_text .= "【ご注意】\n";
												$mail1_text .= "予約履歴にてご予約の確認・キャンセルができます。\n";
												$mail1_text .= "https://example.com/resv.php?id=".$resv_id."\n";
												$mail1_text .= "--------------------------------------------------------------------------------\n";
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

												$resv_mail_id = 1;
												$sql = "INSERT INTO dat_resv_mail VALUES (?, ?, 1, ?, ?, ?, ?, ?)";
												$par = [$resv_id, $resv_mail_id, $mail1_subj, $mail1_text, $resv_client_mail, $resv_mail_ok, $resv_time->format('Y-m-d H:i:s')];
												$res = sql($sql, $par);

												$mail2_subj = '【新規予約】'.$site_name;

												$mail2_text = "新規予約がありましたのでご確認ください。\n";
												$mail2_text .= "https://example.com/system/resv.php?id=".$resv_id."\n";
												$mail2_text .= "以下のメールが送信されました。\n";
												$mail2_text .= "================================================================================\n";
												$mail2_text .= $mail1_text;
												$mail2_text .= "================================================================================\n";
												$mail2_text .= "api set-resv-new";

												for ($i = 1; $i <= 3; $i++) {
													if ($shop['shop_mail'.$i]) {
														send_mailer($shop['shop_mail'.$i], $mail2_subj, $mail2_text);
														sleep(1);
													}
												}

											}

										}
										else $mess = 'ご予約に失敗しました。';

										if (! $mess) {
											$db->commit();
											$data['id'] = $resv_id;
											$result = 'success';
										}
										else $db->rollBack();

									}
									else $mess = 'ご予約に失敗しました。現在、予約の新規受付を停止しております。';

								}
								else $mess = 'ご予約に失敗しました。店舗情報の取得に失敗しました。';

							}
							else $mess = 'ご予約に失敗しました。お客様情報は停止されています。';

						}
						else $mess = 'ご予約に失敗しました。お客様情報の情報が登録されていません。';

					}
					else $mess = 'ご予約に失敗しました。日付が正しくありません。';

				}
				else $mess = 'ご予約に失敗しました。車両番号が正しくありません。';

			}
			else $mess = 'ご予約に失敗しました。データが正しくありません。';

		}
		else $mess = 'ご予約に失敗しました。ログインし直してください。';

	}
	else $mess = 'データの取得に失敗しました。画面を更新してもう一度送信してください。';

}
else $mess = 'データの受信に失敗しました。';

$json = ['result' => $result, 'data' => $data, 'mess' => $mess];

echo json_encode($json);

?>
