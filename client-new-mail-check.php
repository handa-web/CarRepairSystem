<?php

session_name('car_repair_RESV');
session_start();

include_once('./config/library.php');

$mess = '';

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

	<form id="client_mail_check_form">
		<input type="hidden" name="csrf_token" value="<?=h($csrf_token);?>">

		<p class="war_text" style="<?=h($mess ? 'display: none;' : '');?>"><?=h($mess);?></p>

		<div class="edit_list">
			<div class="item">
				<p class="title">メールアドレス</p>
				<p class="data in"><input type="email" name="client_mail" value="" class="on" required></p>
			</div>
		</div>

		<ul class="button_list">
			<li><a href="<?=h($_SESSION['back']);?>" class="button back">戻る</a></li>
			<li><button type="submit" name="new" value="1" class="button new">新規登録</button></li>
		</ul>

	</form>

</main>

</div>

<?php include_once('./in-footer.php'); ?>
</body>
</html>
