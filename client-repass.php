<?php

include_once('./in-init.php');

$dat = [];
$sql = "SELECT * FROM mst_client WHERE client_id = ? LIMIT 0, 1";
if ($res = sql($sql, $_SESSION['id'])) $dat = $res[0];

$csrf_token = getCsrfToken();

?>
<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>パスワードリセット | car_repair予約システム</title>
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
			<li><a href="./client.php">お客様情報</a></li>
			<li>パスワードリセット</li>
		</ol>
	</nav>

	<form id="repass_form">
		<input type="hidden" name="csrf_token" value="<?=h($csrf_token);?>">
		<p class="war_text" style="display: none;"></p>
		<div class="edit_list">
			<div class="item">
				<p class="title">会社名</p>
				<p class="data"><?=h($dat['client_name']);?></p>
			</div>
			<div class="item">
				<p class="title">現在のパスワード</p>
				<div class="data in"><input type="password" name="client_pass" class="on" pattern="^(?=.*[a-zA-Z0-9@$!%*?&]).{8,}$" title="大文字、小文字、数字、特殊文字のいずれかを含む8文字以上のパスワードを入力してください。" required></div>
			</div>
			<div class="item">
				<p class="title">新しいパスワード</p>
				<div class="data in"><input type="password" name="client_new_pass" class="on" pattern="^(?=.*[a-zA-Z0-9@$!%*?&]).{8,}$" title="大文字、小文字、数字、特殊文字のいずれかを含む8文字以上のパスワードを入力してください。" required></div>
				<p class="hint">※大文字、小文字、数字、特殊文字のいずれかを含む8文字以上のパスワードを入力してください。</p>
			</div>
			<div class="item">
				<p class="title">新しいパスワード（確認用）</p>
				<div class="data in"><input type="password" name="client_new_pass2" class="on" pattern="^(?=.*[a-zA-Z0-9@$!%*?&]).{8,}$" title="大文字、小文字、数字、特殊文字のいずれかを含む8文字以上のパスワードを入力してください。" required></div>
			</div>
		</div>

		<ul class="button_list">
			<li><a href="./client.php" class="button back">戻る</a></li>
			<li><button type="submit" class="button edit">更新</button></li>
		</ul>

	</form>

</main>

</div>

<?php include_once('./in-footer.php'); ?>
</body>
</html>
