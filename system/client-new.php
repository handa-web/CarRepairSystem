<?php

include_once('./in-init.php');

$csrf_token = getCsrfToken();

?>
<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>顧客情報 新規登録 | car_repair予約システム</title>
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
			<li>新規登録</li>
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
				<p class="title">メールアドレス</p>
				<p class="data in"><input type="email" name="client_mail" value="" class="on" required></p>
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
			<div class="item">
				<p class="title">電話番号</p>
				<p class="data in"><input type="tel" name="client_tel" value="" class="on"></p>
			</div>
			<div class="item">
				<p class="title">メモ（任意）</p>
				<p class="data in"><textarea name="client_memo" class="on"></textarea></p>
			</div>
			<div class="item">
				<div class="title">一時停止</div>
				<div class="data">
					<label><input type="checkbox" name="client_stop" value="1" class="on" checked>一時停止にする</label>
				</div>
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
<script src="./js/client.js" defer></script>
</body>
</html>
