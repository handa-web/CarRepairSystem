<?php

include_once('./in-init.php');

$dat = [];
$sql = "SELECT * FROM mst_client WHERE client_id = ? LIMIT 0, 1";
if ($res = sql($sql, $_SESSION['id'])) $dat = $res[0];
else $mess = 'お客様情報の取得に失敗しました。';

if ($dat) {
	// $dat['client_zip']の先頭に「〒」、３文字目の次に「-」を追加
	if (preg_match('/^(\d{3})(\d{4})$/', $dat['client_zip'], $matches)) {
		$dat['client_zip'] = '〒' . $matches[1] . '-' . $matches[2];
	}
}

$csrf_token = getCsrfToken();

?>
<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>お客様情報 | car_repair予約システム</title>
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
			<li>お客様情報</li>
		</ol>
	</nav>

	<?php if ($mess) { ?>
	<p class="mess"><?=h($mess);?></p>
	<?php } ?>

	<?php if ($dat) { ?>
	<form id="client_form">
		<input type="hidden" name="csrf_token" value="<?=h($csrf_token);?>">
		<input type="hidden" name="edit" value="1">

		<p class="war_text" style="display: none;"></p>

		<h2 class="list_title">お客様情報</h2>
		<div class="edit_list">
			<div class="item">
				<p class="title">会社名</p>
				<p class="data in"><input type="text" name="client_name" maxlength="100" value="<?=h($dat['client_name']);?>" class="on"></p>
			</div>
			<div class="item">
				<p class="title">郵便番号</p>
				<p class="data in"><input type="text" name="client_zip" maxlength="9" value="<?=h($dat['client_zip']);?>" class="on"></p>
			</div>
			<div class="item">
				<p class="title">住所</p>
				<p class="data in"><input type="text" name="client_addr" maxlength="255" value="<?=h($dat['client_addr']);?>" class="on"></p>
			</div>
			<div class="item">
				<p class="title">担当者名</p>
				<p class="data in"><input type="text" name="client_staff" maxlength="100" value="<?=h($dat['client_staff']);?>" class="on"></p>
			</div>
			<div class="item">
				<p class="title">メールアドレス</p>
				<p class="data in"><input type="email" name="client_mail" maxlength="255" value="<?=h($dat['client_mail']);?>" class="on"></p>
			</div>
			<div class="item">
				<p class="title">電話番号</p>
				<p class="data in"><input type="tel" name="client_tel" maxlength="255" value="<?=h($dat['client_tel']);?>" class="on"></p>
			</div>
			<div class="item">
				<p class="title">パスワード</p>
				<p class="data"><a href="./client-repass.php" class="link">※パスワードの変更はこちら</a></p>
			</div>
		</div>

	</form>
	<?php } ?>

	<ul class="button_list">
		<li><a href="./" class="button back">戻る</a></li>
		<?php if ($dat) { ?>
		<li><button type="submit" form="client_form" class="button edit">更新</button></li>
		<?php } ?>
		<li>
			<form id="logout_form">
				<input type="hidden" name="csrf_token" value="<?=h($csrf_token);?>">
				<button type="submit" class="button">ログアウト</button>
			</form>
		</li>
	</ul>

</main>

</div>

<?php include_once('./in-footer.php'); ?>
</body>
</html>
