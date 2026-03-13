<?php

include_once('./in-init.php');

$staff_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$dat = [];
if ($_SESSION['admin'] ||  (! $_SESSION['admin'] && $_SESSION['id'] == $staff_id)) {

	$sql = "SELECT * FROM mst_staff WHERE staff_id = ? LIMIT 0, 1";
	$par = [$staff_id];
	if ($res = sql($sql, $par)) {
		$dat = $res[0];
	}
	else $mess = 'データの取得に失敗しました。';

}
else $mess = '権限がありません。';

$csrf_token = getCsrfToken();

?>
<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>test</title>
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

	<?php if ($mess) { ?>

	<p class="war_text"><?=h($mess);?></p>

	<?php } else { ?>

	<form id="staff_repass_form" class="staff_repass_form">
		<input type="hidden" name="csrf_token" value="<?=h($csrf_token);?>">
		<input type="hidden" name="staff_id" value="<?=h($dat['staff_id']);?>">
		<input type="hidden" name="staff_mail" value="<?=h($dat['staff_mail']);?>">

		<p class="staff_repass_title">パスワードリセット</p>

		<p class="war_text" style="display: none;"></p>

		<div class="edit_list">
			<div class="item">
				<div class="title">スタッフ名</div>
				<div class="data"><?=h($dat['staff_name']);?></div>
			</div>
			<div class="item">
				<div class="title">メールアドレス</div>
				<div class="data"><?=h($dat['staff_mail']);?></div>
			</div>
			<div class="item">
				<div class="title">新しいパスワード</div>
				<div class="data in"><input type="password" name="staff_pass" pattern="^(?=.*[a-zA-Z0-9@$!%*?&]){8,}$" title="8文字以上、大文字、小文字、数字、特殊文字のいずれか1文字以上含んでください" class="on"></div>
				<p class="hint">※大文字、小文字、数字、特殊文字のいずれかを含む8文字以上のパスワードを入力してください。</p>
			</div>
			<div class="item">
				<div class="title">新しいパスワード（確認用）</div>
				<div class="data in"><input type="password" name="staff_pass2" pattern="^(?=.*[a-zA-Z0-9@$!%*?&]){8,}$" title="8文字以上、大文字、小文字、数字、特殊文字のいずれか1文字以上含んでください" class="on"></div>
			</div>
		</div>
		<p class="staff_repass_button"><button type="submit" class="button">送信する</button></p>
		<p class="new_button"><a href="./staff.php?id=<?=h($staff_id);?>" class="button back">戻る</a></p>
	</form>

	<?php } ?>

</main>

</div>

<?php include_once('./in-footer.php'); ?>
<script src="./js/staff.js"></script>
</body>
</html>
