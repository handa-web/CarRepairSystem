<?php

session_name('car_repair_RESV');
session_start();

if (isset($_SESSION['id']) and $_SESSION['id']) {
	header('Location: ./');
	exit;
}

include_once('./config/library.php');
include_once('./config/db-connect.php');

$mess = '';

$client_mail = isset($_GET['mail']) ? urldecode($_GET['mail']) : '';
$client_check_time = isset($_GET['cd']) ? (int)$_GET['cd'] : 0;

if ($client_mail && $client_check_time) {

	$sql = "SELECT client_id FROM mst_client WHERE client_mail = ? AND client_check_time = ? LIMIT 0, 1";
	if ($res = sql($sql, [$client_mail, $client_check_time])) {

		$limit_time = $client_check_time + (86400 * 3); // 72時間後
		$time = (new DateTime())->getTimestamp();

		if ($limit_time < $time) { // 72時間以内
			$mess = '認証コードの有効期限が切れています。';
		}

	}
	else $mess = 'メールアドレスまたは確認コードが不正です。';

}
else {
	header('Location: ./');
	exit;
}

$csrf_token = getCsrfToken();

?>
<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>お客様情報 新規登録 | car_repair予約システム</title>
	<!-- css -->
	<link rel="stylesheet" href="./css/destyle.css">
	<link rel="stylesheet" href="./css/base.css">
	<!-- other -->
	<meta name="robots" content="noindex, nofollow">
	<link rel="icon" href="./favicon.ico">
</head>
<body>
<?php include_once('./in-header.php'); ?>

<div class="main_wrap">

<?php include_once('./in-aside.php') ?>

<main id="main">

	<nav class="bread">
		<ol>
			<li><a href="./">TOP</a></li>
			<li>新規登録</li>
		</ol>
	</nav>

	<?php if ($mess) { ?>
	<p class="mess"><?=h($mess);?></p>
	<?php } else { ?>

	<form id="client_form">
		<input type="hidden" name="csrf_token" value="<?=h($csrf_token);?>">
		<input type="hidden" name="new" value="1">
		<input type="hidden" name="client_check_time" value="<?=h($client_check_time);?>">

		<p class="war_text" style="display: none;"></p>

		<div class="edit_list">
			<div class="item">
				<p class="title">メールアドレス</p>
				<p class="data in"><input type="email" name="client_mail" value="<?=h($client_mail);?>" required readonly></p>
			</div>
			<div class="item">
				<p class="title">会社名</p>
				<p class="data in"><input type="text" name="client_name" value="" class="on" required></p>
			</div>
			<div class="item">
				<p class="title">郵便番号（任意）</p>
				<p class="data in"><input type="text" name="client_zip" maxlength="9" value="〒" class="on"></p>
			</div>
			<div class="item">
				<p class="title">住所（任意）</p>
				<p class="data in"><input type="text" name="client_addr" value="" class="on"></p>
			</div>
			<div class="item">
				<p class="title">担当者名</p>
				<p class="data in"><input type="text" name="client_staff" value="" class="on" required></p>
			</div>
			<div class="item">
				<p class="title">電話番号</p>
				<p class="data in"><input type="tel" name="client_tel" value="" class="on"></p>
			</div>
			<div class="item">
				<p class="title">パスワード</p>
				<p class="data in"><input type="password" name="client_pass" class="on" pattern="^(?=.*[a-zA-Z0-9@$!%*?&]).{8,}$" title="大文字、小文字、数字、特殊文字のいずれかを含む8文字以上のパスワードを入力してください。" required></p>
				<p class="hint">※大文字、小文字、数字、特殊文字のいずれかを含む8文字以上のパスワードを入力してください。</p>
			</div>
			<div class="item">
				<p class="title">パスワード(確認用)</p>
				<p class="data in"><input type="password" name="client_pass2" class="on" pattern="^(?=.*[a-zA-Z0-9@$!%*?&]).{8,}$" title="大文字、小文字、数字、特殊文字のいずれかを含む8文字以上のパスワードを入力してください。" required></p>
			</div>
		</div>

		<ul class="button_list">
			<li><a href="<?=h($_SESSION['back']);?>" class="button back">戻る</a></li>
			<li><button type="submit" class="button submit">新規登録</button></li>
		</ul>

	</form>

	<?php } ?>

</main>

</div>

<?php include_once('./in-footer.php'); ?>
</body>
</html>
