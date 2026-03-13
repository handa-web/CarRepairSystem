<?php

include_once('./in-init.php');

$_SESSION['back'] = $now_url;

$now_page = (isset($_GET['page'])) ? (int)$_GET['page'] : 1;
$page_max = 0;
$inview = 10;
$set_inview = ($now_page - 1) * $inview;
$count = 0;

$sql = "SELECT COUNT(*) AS count FROM dat_resv ";
$sql .= "INNER JOIN mst_client USING(client_id) ";
$sql .= "INNER JOIN (SELECT *, MIN(staff_cale_date) FROM dat_staff_cale GROUP BY resv_id) AS cale USING(resv_id) ";
$sql .= "WHERE client_id = ? ";
$sql .= "ORDER BY resv_status = 5, staff_cale_date DESC, resv_status1_time DESC";
$par = [$_SESSION['id']];
if ($res = sql($sql, $par)) {

	$count = $res[0]['count'];
	$page_max = ceil($count / $inview);

	$sql = "SELECT * FROM dat_resv ";
	$sql .= "INNER JOIN mst_client USING(client_id) ";
	$sql .= "INNER JOIN (SELECT *, MIN(staff_cale_date) AS rece_date FROM dat_staff_cale GROUP BY resv_id) AS cale USING(resv_id) ";
	$sql .= "WHERE client_id = ? ";
	$sql .= "ORDER BY resv_status = 5, staff_cale_date DESC, resv_status1_time DESC ";
	$sql .= "LIMIT {$set_inview}, {$inview}";
	if (! $list = sql($sql, $par)) {
		if ($list === false) $mess = 'データの取得に失敗しました。';
		else $mess = 'データが登録されていません。';
	}

}

?>
<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>予約履歴 | car_repair予約システム</title>
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
			<li>予約管理</li>
		</ol>
	</nav>

	<?php if ($mess) { ?>
	<p class="mess"><?=h($mess);?></p>
	<?php } ?>

	<?php if ($list) { ?>
	<div class="list_tbl">
		<table>
			<thead>
				<tr>
					<th class="id fixed">予約ID</th>
					<th class="status">予約状況</th>
					<th class="date">入庫日</th>
					<th class="date">納車予定日</th>
					<th class="name">予約者名</th>
					<th>備考</th>
					<th class="code">予約コード</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($list as $dat) { ?>
				<tr class="<?=h(($dat['resv_status'] > 3) ? 'stop' : '');?>">
					<th class="id fixed link"><a href="./resv.php?id=<?=h($dat['resv_id']);?>"><?=h($dat['resv_id']);?></a></th>
					<td class="status"><?=h($_SESSION['status_list'][$dat['resv_status']]);?></td>
					<td class="date"><?=h(formatDateTime($dat['resv_in_date']));?></td>
					<td class="date"><?=h(formatDateTime($dat['resv_out_date']));?></td>
					<td class="name"><?=h($dat['resv_client_name']);?></td>
					<td class="auto"><?=h($dat['resv_text']);?></td>
					<td class="code"><?=h($dat['resv_code']);?></td>
				</tr>
				<?php } ?>
			</tbody>
		</table>
	</div>

	<form action="./resv-list.php" method="get">
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

	<?php } ?>

	<ul class="button_list">
		<li><a href="./" class="button back">戻る</a></li>
		<li><a href="./resv-new.php" class="button new">新規登録</a></li>
	</ul>

</main>

</div>

<?php include_once('./in-footer.php'); ?>
</body>
</html>
