<?php

include_once('./in-init.php');

if ($dat = $_POST) {

	$dat['client_mail'] = (string)$dat['client_mail'];

	$mail1_subj = '【新規登録申請のご案内】'.$shop_name;
	$mail1_text .= "以下URLから会員登録をしてください。\n";
	$mail1_text .= "https://example.com/client-new.php \n";
	$mail1_text .= "================================================================================\n";
	$mail1_text .= "このメールはシステムより自動送信されています。\n";
	$mail1_text .= "本メールに心当たりのない場合はお手数ですが破棄してください。\n";
	$mail1_text .= "================================================================================\n";
	$mail1_text .= "【店舗情報】.\n";
	$mail1_text .= "店舗名：".$shop['shop_name']."\n";
	$mail1_text .= "電話番号：".$shop['shop_tel']."\n";
	$mail1_text .= "================================================================================\n";
	$mail1_text .= "【送信日時】.\n";
	$mail1_text .= $mail_time_text."\n";

	if (send_mailer($dat['client_mail'], $mail1_subj, $mail1_text)) $mail_ok = 1;
	else $mail_ok = 0;

	if ($mail_ok) {

		$mess = 'メールを送信しました。';

		$mail_text = "新規登録申請メールを送信しました。\n";
		$mail_text .= "================================================================================\n";
		$mail_text .= $mail1_text;
		$mail_text .= "================================================================================\n";
		$mail_text .= "system client-entry";

		for ($i = 1; $i <= 3; $i++) {
			if ($shop['shop_mail'.$i]) {
				send_mailer($shop['shop_mail'.$i], $mail1_subj, $mail_text);
				sleep(1);
			}
		}

	}
	else {
		$mess = 'メールの送信に失敗しました。';
	}

}
else {

}

$csrf_token = getCsrfToken();

?>
<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>顧客情報 新規登録のご案内 | car_repair予約システム</title>
	<!-- css -->
	<link rel="stylesheet" href="./css/destyle.css">
	<link rel="stylesheet" href="./css/base.css">
	<!-- other -->
	<meta name="robots" content="noindex, nofollow">
	<link rel="icon" href="../favicon.ico">
</head>
<body>
<?php include_once('./in-header.php'); ?>

<div class="main_wrap">

<?php include_once('./in-aside.php') ?>

<main id="main">

	<nav class="bread">
		<ol>
			<li><a href="./">TOP</a></li>
			<li><a href="./client-list.php">顧客一覧</a></li>
			<li>新規登録申請</li>
		</ol>
	</nav>

	<?php if ($mess) { ?>
	<p class="mess"><?=h($mess);?></p>
	<?php } ?>

	<form id="client_form">
		<input type="hidden" name="csrf_token" value="<?=h($csrf_token);?>">
		<input type="hidden" name="new" value="1">

		<p class="war_text" style="display: none;"></p>

		<div class="edit_list">
			<div class="item">
				<p class="title">メールアドレス</p>
				<p class="data in"><input type="email" name="client_mail" value="" class="on" required></p>
			</div>
		</div>

		<ul class="button_list">
			<li><a href="<?=h($_SESSION['back']);?>" class="button back">戻る</a></li>
			<li><button type="submit" name="new" value="1" class="button new">メール送信</button></li>
		</ul>

	</form>

</main>

</div>

<?php include_once('./in-footer.php'); ?>
<script src="./js/client.js" defer></script>
</body>
</html>
