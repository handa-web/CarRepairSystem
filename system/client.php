<?php

include_once('./in-init.php');

$client_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$dat = [];
$sql = "SELECT * FROM mst_client WHERE client_id = ? LIMIT 0, 1";
$par = [$client_id];
if ($res = sql($sql, $par)) $dat = $res[0];
else $mess = 'データの取得に失敗しました。';

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

	<nav class="bread">
		<ol>
			<li><a href="./">TOP</a></li>
			<li><a href="./client-list.php">顧客管理</a></li>
			<li>詳細</li>
		</ol>
	</nav>

	<?php if ($mess) { ?>
	<p class="mess"><?=h($mess);?></p>
	<?php } ?>

	<form id="client_form">

		<?php if ($dat) { ?>
		<input type="hidden" name="csrf_token" value="<?=h($csrf_token);?>">
		<input type="hidden" name="client_id" value="<?=h($dat['client_id']);?>">
		<input type="hidden" name="edit" value="1">

		<p class="war_text" style="display: none;"></p>

		<div class="edit_list">
			<div class="item">
				<p class="title">会社名</p>
				<p class="data in"><input type="text" name="client_name" value="<?=h($dat['client_name']);?>" class="on"></p>
			</div>
			<div class="item">
				<p class="title">郵便番号（任意）</p>
				<p class="data in"><input type="text" name="client_zip" maxlength="9" value="<?=h($dat['client_zip']);?>" class="on"></p>
			</div>
			<div class="item">
				<p class="title">住所（任意）</p>
				<p class="data in"><input type="text" name="client_addr" value="" class="on"></p>
			</div>
			<div class="item">
				<p class="title">担当者名</p>
				<p class="data in"><input type="text" name="client_staff" value="<?=h($dat['client_staff']);?>" class="on"></p>
			</div>
			<div class="item">
				<p class="title">メールアドレス</p>
				<p class="data in"><input type="email" name="client_mail" value="<?=h($dat['client_mail']);?>" class="on"></p>
			</div>
			<div class="item">
				<p class="title">電話番号</p>
				<p class="data in"><input type="tel" name="client_tel" value="<?=h($dat['client_tel']);?>" class="on"></p>
			</div>
			<div class="item">
				<p class="title">メモ</p>
				<p class="data in"><textarea name="client_memo" class="on"><?=h($dat['client_memo']);?></textarea></p>
			</div>
			<div class="item">
				<p class="title">登録日</p>
				<p class="data"><?=h($dat['client_new_date']);?></p>
			</div>
			<div class="item">
				<div class="title">停止</div>
				<div class="data">
					<label><input type="checkbox" name="client_stop" value="1" <?=h($dat['client_stop'] ? 'checked' : '');?>>停止する</label>
				</div>
			</div>
			<div class="item">
				<div class="title">削除</div>
				<div class="data">
					<label><input type="checkbox" name="dele" value="1">削除する</label>
				</div>
			</div>
		</div>

		<ul class="button_list">
			<li><a href="<?=h($_SESSION['back']);?>" class="button back">戻る</a></li>
			<li><button type="submit" class="button edit">更新</button></li>
		</ul>

		<?php } else { ?>

		<ul class="button_list">
			<li><a href="<?=h($_SESSION['back']);?>" class="button back">戻る</a></li>
		</ul>

		<?php } ?>

	</form>

</main>

</div>

<?php include_once('./in-footer.php'); ?>
<script src="./js/client.js"></script>
</body>
</html>
