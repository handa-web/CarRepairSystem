<?php

include_once('./in-init.php');

$dat = [];
$sql = "SELECT * FROM mst_shop LIMIT 0, 1";
if ($res = sql($sql)) $dat = $res[0];
else $mess = 'データの取得に失敗しました。';

if ($dat) {
	// $dat['client_zip']の先頭に「〒」、３文字目の次に「-」を追加
	if (preg_match('/^(\d{3})(\d{4})$/', $dat['shop_zip'], $matches)) {
		$dat['shop_zip'] = '〒' . $matches[1] . '-' . $matches[2];
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
			<li>設定</li>
		</ol>
	</nav>

	<?php if ($mess) { ?>
	<p class="mess"><?=h($mess);?></p>
	<?php } ?>

	<?php if ($dat) { ?>
	<form id="shop_form">
		<input type="hidden" name="csrf_token" value="<?=h($csrf_token);?>">
		<input type="hidden" name="edit" value="1">

		<p class="war_text" style="display: none;"></p>

		<h2 class="list_title">各種設定</h2>
		<div class="edit_list">
			<div class="item">
				<div class="title">システム名</div>
				<div class="data in"><input type="text" name="shop_name" value="<?=h($dat['shop_name']);?>" class="on"></div>
			</div>
			<div class="item">
				<div class="title">郵便番号（任意）</div>
				<div class="data in"><input type="text" name="shop_zip" value="<?=h($dat['shop_zip']);?>" class="on"></div>
			</div>
			<div class="item">
				<div class="title">住所（任意）</div>
				<div class="data in"><input type="text" name="shop_addr" value="<?=h($dat['shop_addr']);?>" class="on"></div>
			</div>
			<div class="item">
				<div class="title">電話番号（任意）</div>
				<div class="data in"><input type="tel" name="shop_tel" value="<?=h($dat['shop_tel']);?>" class="on"></div>
			</div>
			<div class="item">
				<div class="title">FAX（任意）</div>
				<div class="data in"><input type="text" name="shop_fax" value="<?=h($dat['shop_fax']);?>" class="on"></div>
			</div>
			<div class="item">
				<div class="title">代表者名（任意）</div>
				<div class="data in"><input type="text" name="shop_staff" value="<?=h($dat['shop_staff']);?>" class="on"></div>
			</div>
		</div>

		<h2 class="list_title">メール受信設定</h2>
		<div class="edit_list">
			<div class="item">
				<div class="title">受信メールアドレス</div>
				<div class="data in"><?=h($_SESSION['shop']['shop_mail1']);?></div>
			</div>
			<div class="item">
				<div class="title">受信メールアドレス（任意）</div>
				<div class="data in"><input type="email" name="shop_mail2" value="<?=h($_SESSION['shop']['shop_mail2']);?>" maxlength="255" class="on"></div>
			</div>
			<div class="item">
				<div class="title">受信メールアドレス（任意）</div>
				<div class="data in"><input type="email" name="shop_mail3" value="<?=h($_SESSION['shop']['shop_mail3']);?>" maxlength="255" class="on"></div>
			</div>
		</div>

		<h2 class="list_title">メールテンプレート設定</h2>
		<div class="edit_list">
			<div class="item">
				<div class="title">予約受付メール（任意）</div>
				<div class="data in"><textarea name="shop_resv_mail_status1" rows="4" class="on"><?=h($_SESSION['shop']['shop_resv_mail_status1']);?></textarea></div>
			</div>
			<div class="item">
				<div class="title">予約修正メール任意）</div>
				<div class="data in"><textarea name="shop_resv_mail_status2" rows="4" class="on"><?=h($_SESSION['shop']['shop_resv_mail_status2']);?></textarea></div>
			</div>
			<div class="item">
				<div class="title">予約確定メール（任意）</div>
				<div class="data in"><textarea name="shop_resv_mail_status3" rows="4" class="on"><?=h($_SESSION['shop']['shop_resv_mail_status3']);?></textarea></div>
			</div>
			<div class="item">
				<div class="title">予約完了メール（任意）</div>
				<div class="data in"><textarea name="shop_resv_mail_status4" rows="4" class="on"><?=h($_SESSION['shop']['shop_resv_mail_status4']);?></textarea></div>
			</div>
			<div class="item">
				<div class="title">予約キャンセルメール（任意）</div>
				<div class="data in"><textarea name="shop_resv_mail_status5" rows="4" class="on"><?=h($_SESSION['shop']['shop_resv_mail_status5']);?></textarea></div>
			</div>
		</div>

		<h2 class="list_title">その他設定</h2>
		<div class="edit_list">
			<div class="item">
				<div class="title">予約停止</div>
				<div class="data">
					<label><input type="checkbox" name="shop_resv_stop" value="1" <?=h($dat['shop_resv_stop'] ? 'checked' : '');?>>新規予約を停止する</label>
				</div>
			</div>
		</div>

	</form>

	<ul class="button_list">
		<li><a href="<?=h($_SESSION['back']);?>" class="button back">戻る</a></li>
		<li><button type="submit" form="shop_form" class="button edit">更新</button></li>
		<li>
			<form id="logout_form">
				<input type="hidden" name="csrf_token" value="<?=h($csrf_token);?>">
				<button type="submit" class="button">ログアウト</button>
			</form>
		</li>
	</ul>

	<?php } else { ?>

	<ul class="button_list">
		<li><a href="<?=h($_SESSION['back']);?>" class="button back">戻る</a></li>
		<li>
			<form id="logout_form">
				<input type="hidden" name="csrf_token" value="<?=h($csrf_token);?>">
				<button type="submit" class="button">ログアウト</button>
			</form>
		</li>
	</ul>

	<?php } ?>

</main>

</div>

<?php include_once('./in-footer.php'); ?>
<script src="./js/setting.js"></script>
</body>
</html>
