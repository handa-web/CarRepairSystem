<?php

include_once('./in-init.php');

$dat = [];
$sql = "SELECT * FROM mst_client WHERE client_id = ? AND client_stop = 0 LIMIT 0, 1";
if ($res = sql($sql, $_SESSION['id'])) $dat = $res[0];

$csrf_token = getCsrfToken();

?>
<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>お問い合わせ | car_repair予約システム</title>
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
			<li>お問い合わせ</li>
		</ol>
	</nav>

	<form id="contact_form">
		<input type="hidden" name="csrf_token" value="<?=h($csrf_token);?>">
		<input type="hidden" name="client_id" value="<?=h($client_id);?>">
		<p class="war_text" style="display: none;"></p>

		<div class="edit_list">
			<div class="item">
				<div class="title">会社名</div>
				<div class="data in"><input type="text" name="cont_company" maxlength="255" class="on" value="<?=h(isset($dat['client_name']) ? $dat['client_name'] : '');?>" required></div>
			</div>
			<div class="item">
				<div class="title">メールアドレス</div>
				<div class="data in"><input type="email" name="cont_mail" maxlength="255" class="on" value="<?=h(isset($dat['client_mail']) ? $dat['client_mail'] : '');?>" required></div>
			</div>
			<div class="item">
				<div class="title">電話番号</div>
				<div class="data in"><input type="tel" name="cont_tel" maxlength="13" class="on" value="<?=h(isset($dat['client_tel']) ? $dat['client_tel'] : '');?>" required></div>
			</div>
			<div class="item">
				<div class="title">担当者名</div>
				<div class="data in"><input type="tel" name="cont_staff" maxlength="255" class="on" value="<?=h(isset($dat['client_staff']) ? $dat['client_staff'] : '');?>" required></div>
			</div>
			<div class="item">
				<div class="title">お問い合わせ内容</div>
				<div class="data in"><textarea name="cont_text" cols="30" rows="10" class="on" required></textarea></div>
			</div>
		</div>

		<ul class="button_list">
			<li><a href="./" class="button back">戻る</a></li>
			<li><button type="submit" class="button submit">送信する</button></li>
		</ul>

	</form>

</main>

</div>

<?php include_once('./in-footer.php'); ?>
</body>
</html>
