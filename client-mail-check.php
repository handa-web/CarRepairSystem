<?php

session_name('car_repair_RESV');
session_start();

if (isset($_SESSION['id']) and $_SESSION['id']) {
	header('Location: ./');
	exit;
}

include_once('./config/library.php');

$mess = '';

if (isset($_GET['success'])) {
	$mess = "認証メールを送信しました。メール内のURLから新規登録を完了してください。";
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

	<form id="client_mail_check_form" class="client_mail_check_form">
		<input type="hidden" name="csrf_token" value="<?=h($csrf_token);?>">

		<p class="client_mail_check_title">新規登録 メール認証</p>

		<?php if ($mess) { ?>

		<p class="suc_text"><?=h($mess);?></p>

		<?php } else { ?>

		<p class="war_text" style="display: none;"></p>

		<div class="edit_list">
			<div class="item">
				<div class="title">メールアドレス</div>
				<div class="data in"><input type="email" name="client_mail" class="on" maxlength="255" required></div>
			</div>
		</div>

		<p class="client_mail_check_button"><button type="submit" class="button">メール送信</button></p>

		<?php } ?>

		<p class="new_button"><a href="./login.php" class="button back">ログイン画面へ</a></p>

	</form>

</main>

</div>

<?php include_once('./in-footer.php'); ?>
</body>
</html>
