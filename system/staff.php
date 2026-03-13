<?php

include_once('./in-init.php');

$staff_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$dat = [];
$sql = "SELECT * FROM mst_staff WHERE staff_id = ? LIMIT 0, 1";
$par = [$staff_id];
if ($res = sql($sql, $par)) $dat = $res[0];
else $mess = 'データの取得に失敗しました。';

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
			<li>詳細</li>
		</ol>
	</nav>

	<?php if ($mess) { ?>
	<p class="mess"><?=h($mess);?></p>
	<?php } ?>

	<form id="staff_form">

		<?php if ($dat) { ?>
		<input type="hidden" name="csrf_token" value="<?=h($csrf_token);?>">
		<input type="hidden" name="staff_id" value="<?=h($dat['staff_id']);?>">
		<input type="hidden" name="edit" value="1">

		<p class="war_text" style="display: none;"></p>

		<div class="edit_list">
			<div class="item">
				<p class="title">スタッフ名</p>
				<p class="data in"><input type="text" name="staff_name" value="<?=h($dat['staff_name']);?>" class="on" required></p>
			</div>
			<div class="item">
				<p class="title">メールアドレス</p>
				<p class="data in"><input type="email" name="staff_mail" value="<?=h($dat['staff_mail']);?>" class="on" required></p>
			</div>
			<div class="item">
				<p class="title">パスワード</p>
				<p class="data"><a href="./staff-repass.php?id=<?=h($staff_id);?>" class="link">※パスワードの変更はこちら</a></p>
			</div>
			<div class="item">
				<p class="title">電話番号（任意）</p>
				<p class="data in"><input type="tel" name="staff_tel" value="<?=h($dat['staff_tel']);?>" class="on"></p>
			</div>
			<div class="item">
				<p class="title">メモ（任意）</p>
				<p class="data in"><textarea name="staff_memo" class="on"></textarea></p>
			</div>
			<div class="item">
				<p class="title">管理者権限</p>
				<p class="data"><?=h($dat['staff_admin']? '権限あり' : '権限なし');?></p>
			</div>
			<div class="item">
				<div class="title">一時停止</div>
				<div class="data">
					<label><input type="checkbox" name="staff_stop" value="1" <?=h($dat['staff_stop'] ? 'checked' : '');?>>一時停止にする</label>
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
<script src="./js/staff.js"></script>
</body>
</html>
