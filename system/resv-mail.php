<?php

include_once('./in-init.php');

$resv_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$sql = "SELECT * FROM dat_resv ";
$sql .= "INNER JOIN dat_resv_mail USING(resv_id) ";
$sql .= "WHERE resv_id = ? ORDER BY resv_mail_time DESC";
if (! $list = sql($sql, $resv_id)) {
	if ($list === false) $mess = 'データの取得に失敗しました。';
	else $mess = 'データが登録されていません。';
}

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
			<li><a href="./resv-list.php">予約管理</a></li>
			<li><a href="./resv.php?id=<?=h($resv_id);?>">詳細</a></li>
			<li>メール履歴</li>
		</ol>
	</nav>

	<?php if ($mess) { ?>
	<p class="mess"><?=h($mess);?></p>
	<?php } ?>

	<?php if ($list) { ?>
	<p class="list_title">送信メール一覧</p>
	<?php foreach ($list as $dat) { ?>
	<div class="edit_list">
		<div class="item">
		<div class="title">送信日時</div>
		<div class="data"><?=h(formatDateTime($dat['resv_mail_time']));?><span>（<?=h($dat['resv_mail_ok'] ? '送信成功' : '送信失敗');?>）</span></div>
		</div>
		<div class="item">
			<div class="title">件名</div>
			<div class="data"><?=h($dat['resv_mail_subj']);?></div>
		</div>
		<div class="item">
			<div class="title">メール内容</div>
			<div class="data"><?=nl2br(h($dat['resv_mail_text']));?></div>
		</div>
	</div>
	<?php } } ?>

	<ul class="button_list">
		<li><a href="./resv.php?id=<?=h($resv_id);?>" class="button back">戻る</a></li>
	</ul>

</main>

</div>

<?php include_once('./in-footer.php'); ?>
</body>
</html>
