<?php

session_name('car_repair_RESV-SYSTEM_from_2024');
session_start();

if (isset($_SESSION['id']) and $_SESSION['id']) {
	header('Location: ./');
	exit;
}

include_once('../config/library.php');
include_once('../config/db-connect.php');

if (! (isset($_SESSION['shop']) && $_SESSION['shop'])) {
	$sql = "SELECT * FROM mst_shop WHERE shop_id = 1 LIMIT 0, 1";
	if ($res = sql($sql)) {
		$_SESSION['shop'] = $res[0];
	}
}

$mess = '';

$csrf_token = getCsrfToken();

?>
<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>SYSTEM</title>
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

	<form id="login_form" class="login_form">
		<input type="hidden" name="csrf_token" value="<?=h($csrf_token);?>">
		<p class="login_title">管理画面 ログイン</p>
		<p class="war_text" style="display: none"></p>
		<div class="edit_list">
			<div class="item">
				<div class="title">メールアドレス</div>
				<div class="data in"><input type="email" name="staff_mail" class="on" maxlength="255" required></div>
			</div>
			<div class="item">
				<div class="title">パスワード</div>
				<div class="data in"><input type="password" name="staff_pass" class="on" pattern="^(?=.*[a-zA-Z0-9@$!%*?&]).{8,}$" title="大文字、小文字、数字、特殊文字のいずれかを含む8文字以上のパスワードを入力してください。" required></div>
			</div>
		</div>
		<p class="login_button"><button type="submit" class="button">ログイン</button></p>
		<!-- <p class="login_link"><a href="" class="link">※パスワードをお忘れの方はこちら</a></p> -->
	</form>

</main>

</div>

<?php include_once('./in-footer.php'); ?>
</body>
</html>
