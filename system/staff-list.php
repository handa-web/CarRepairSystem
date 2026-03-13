<?php

include_once('./in-init.php');

$_SESSION['back'] = $now_url;

$sql = "SELECT * FROM mst_staff ORDER BY staff_stop, staff_sort, staff_id";
if (! $list = sql($sql)) {
	if ($list === false) $mess = 'データの取得に失敗しました。';
	else $mess = 'データが登録されていません。';
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
			<li>スタッフ一覧</li>
		</ol>
	</nav>

	<?php if ($mess) { ?>
	<p class="mess"><?=h($mess);?></p>
	<?php } ?>

	<?php if ($list) { ?>
	<form id="staff_form">
		<input type="hidden" name="csrf_token" value="<?=h($csrf_token);?>">
		<input type="hidden" name="list" value="1">

		<p class="war_text" style="display: none;"></p>

		<div class="list_tbl">
			<table>
				<thead>
					<tr>
						<th class="name fixed">スタッフ名</th>
						<th class="mail">メールアドレス</th>
						<th class="tel">電話番号</th>
						<th class="auto">メモ</th>
						<th class="sort">並び順</th>
						<th class="check">停止</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($list as $dat) { ?>
					<tr class="<?=h($dat['staff_stop'] ? 'stop' : '');?>">
						<th class="name fixed link">
							<input type="hidden" name="id_list[]" value="<?=h($dat['staff_id']);?>">
							<a href="./staff.php?id=<?=h($dat['staff_id']);?>"><?=h($dat['staff_name']);?></a>
						</th>
						<td class="mail link"><a href="mailto:<?=h($dat['staff_mail']);?>"><?=h($dat['staff_mail']);?></a></td>
						<td class="tel link"><a href="tel:<?=h($dat['staff_tel']);?>"><?=h($dat['staff_tel']);?></a></td>
						<td class="auto"><?=h($dat['staff_memo']);?></td>
						<td class="sort in"><input type="number" min="0" max="999" step="1" name="sort[<?=h($dat['staff_id']);?>]" value="<?=h($dat['staff_sort']);?>"></td>
						<td class="check in"><label><input type="checkbox" name="stop[<?=h($dat['staff_id']);?>]" value="1" <?=h($dat['staff_stop'] ? 'checked' : '');?>></label></td>
					</tr>
					<?php } ?>
				</tbody>
			</table>
		</div>

		<ul class="button_list">
			<li><a href="./setting-list.php" class="button back">戻る</a></li>
			<li><a href="./staff-new.php" class="button new">新規登録</a></li>
			<?php if ($list) { ?>
			<li><button type="submit" class="button edit">更新</button></li>
			<?php } ?>
		</ul>

	</form>

	<?php } else { ?>

	<ul class="button_list">
		<li><a href="./setting-list.php" class="button back">戻る</a></li>
		<li><a href="./staff-new.php" class="button new">新規登録</a></li>
	</ul>

	<?php } ?>

</main>

</div>

<?php include_once('./in-footer.php'); ?>
<script src="./js/staff.js"></script>
</body>
</html>
