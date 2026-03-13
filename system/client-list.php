<?php

include_once('./in-init.php');

$_SESSION['back'] = $now_url;

$now_page = (isset($_GET['page'])) ? (int)$_GET['page'] : 1;
$page_max = 0;
$inview = 10;
$set_inview = ($now_page - 1) * $inview;
$count = 0;

$list = [];
$sql = "SELECT COUNT(*) AS count FROM mst_client ORDER BY client_stop, client_id";
if ($res = sql($sql)) {

	$count = $res[0]['count'];
	$page_max = ceil($count / $inview);
	$sql = "SELECT * FROM mst_client ORDER BY client_stop, client_id LIMIT {$set_inview}, {$inview}";
	if (! $list = sql($sql)) {
		if ($list === false) $mess = 'データの取得に失敗しました。';
		else $mess = 'データが登録されていません。';
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
			<li>顧客管理</li>
		</ol>
	</nav>

	<?php if ($mess) { ?>
	<p class="mess"><?=h($mess);?></p>
	<?php } ?>

	<?php if ($list) { ?>

	<form id="client_form">
		<input type="hidden" name="csrf_token" value="<?=h($csrf_token);?>">
		<input type="hidden" name="list" value="1">

		<p class="war_text" style="display: none;"></p>

		<div class="list_tbl">
			<table>
				<thead>
					<tr>
						<th class="name fixed">会社名</th>
						<th class="name">担当者名</th>
						<th class="mail">メールアドレス</th>
						<th class="tel">電話番号</th>
						<th class="auto">メモ</th>
						<th class="date">登録日</th>
						<th class="check">停止</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($list as $dat) { ?>
					<tr class="<?=h($dat['client_stop'] ? 'stop' : '');?>">
						<th class="name fixed">
							<input type="hidden" name="id_list[]" value="<?=h($dat['client_id']);?>">
							<a href="./client.php?id=<?=h($dat['client_id']);?>"><?=h($dat['client_name']);?></a>
						</th>
						<td class="name"><?=h($dat['client_staff']);?></td>
						<td class="mail"><?=h($dat['client_mail']);?></td>
						<td class="tel"><?=h($dat['client_tel']);?></td>
						<td class="auto"><?=h($dat['client_memo']);?></td>
						<td class="date"><?=h($dat['client_new_date']);?></td>
						<td class="check in"><label><input type="checkbox" name="stop[<?=h($dat['client_id']);?>]" value="1" <?=h($dat['client_stop'] ? 'checked' : '');?>></label></td>
					</tr>
					<?php } ?>
				</tbody>
			</table>
		</div>
	</form>

	<form action="./client-list.php" method="get">
		<div class="pagination">
			<?php
			$page_len = 1;
			echo '<button type="submit" name="page" class="page_butt" value="'.h($now_page - 1).'"'.(($now_page <= 1) ? ' disabled' : '').'>← 前へ</button>';
			echo '<button type="submit" name="page" class="count_num'.(($now_page == 1) ? ' on' : '').'" value="1">1</button>';
			if ($page_max > 1) {
				$start_page = max($now_page - $page_len, 2);
				$end_page = min($now_page + $page_len, $page_max - 1);
				if ($start_page <= $end_page) {
					if ($start_page > 2) echo '<p class="dotted">…</p>';
					for ($i = $start_page; $i <= $end_page; $i++) {
						echo '<button type="submit" name="page" class="count_num'.(($now_page == $i) ? ' on' : '').'" value="'.h($i).'">'.h($i).'</button>';
					}
					if ($end_page < $page_max - 1) echo '<p class="dotted">…</p>';
				}
				echo '<button type="submit" name="page" class="count_num'.(($now_page == $page_max) ? ' on' : '').'" value="'.h($page_max).'">'.h($page_max).'</button>';
			}
			echo '<button type="submit" name="page" class="page_butt" value="'.h($now_page + 1).'"'.(($now_page >= $page_max) ? ' disabled' : '').'>次へ →</button>';
			?>
		</div>
	</form>

	<ul class="button_list">
		<li><a href="./" class="button back">戻る</a></li>
		<li><a href="./client-new.php" class="button new">新規登録</a></li>
		<li><button type="submit" form="client_form" class="button edit">更新</button></li>
	</ul>

	<?php } else { ?>

	<ul class="button_list">
		<li><a href="./" class="button back">戻る</a></li>
		<li><a href="./client-new.php" class="button new">新規登録</a></li>
	</ul>

	<?php } ?>

</main>

</div>

<?php include_once('./in-footer.php'); ?>
<script src="./js/client.js" defer></script>
</body>
</html>
