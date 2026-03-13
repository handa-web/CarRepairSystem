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
			<li><a href="./staff-list.php">スタッフ一覧</a></li>
			<li>新規登録</li>
		</ol>
	</nav>

	<?php if ($mess) { ?>
	<p class="mess"><?=h($mess);?></p>
	<?php } ?>

	<form id="staff_form">
		<input type="hidden" name="csrf_token" value="<?=h($csrf_token);?>">
		<input type="hidden" name="new" value="1">

		<p class="war_text" style="display: none;"></p>

		<div class="edit_list">
			<div class="item">
				<p class="title">スタッフ名</p>
				<p class="data in"><input type="text" name="staff_name" value="" class="on" required></p>
			</div>
			<div class="item">
				<p class="title">メールアドレス</p>
				<p class="data in"><input type="email" name="staff_mail" value="" class="on" required></p>
			</div>
			<div class="item">
				<p class="title">パスワード</p>
				<p class="data in"><input type="password" name="staff_pass" class="on" pattern="^(?=.*[a-zA-Z0-9@$!%*?&]).{8,}$" title="大文字、小文字、数字、特殊文字のいずれかを含む8文字以上のパスワードを入力してください。" required></p>
				<p class="hint">※大文字、小文字、数字、特殊文字のいずれかを含む8文字以上のパスワードを入力してください。</p>
			</div>
			<div class="item">
				<p class="title">パスワード(確認用)</p>
				<p class="data in"><input type="password" name="staff_pass2" class="on" pattern="^(?=.*[a-zA-Z0-9@$!%*?&]).{8,}$" title="大文字、小文字、数字、特殊文字のいずれかを含む8文字以上のパスワードを入力してください。" required></p>
			</div>
			<div class="item">
				<p class="title">電話番号（任意）</p>
				<p class="data in"><input type="tel" name="staff_tel" value="" class="on"></p>
			</div>
			<div class="item">
				<p class="title">メモ（任意）</p>
				<p class="data in"><textarea name="staff_memo" class="on"></textarea></p>
			</div>
			<div class="item">
				<div class="title">一時停止</div>
				<div class="data">
					<label><input type="checkbox" name="staff_stop" value="1" class="on" checked>一時停止にする</label>
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
<script src="./js/staff.js"></script>
</body>
</html>
