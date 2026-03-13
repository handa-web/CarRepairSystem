<?php
include_once('./in-init.php');

$dat = [];
$sql = "SELECT * FROM mst_client WHERE client_id = ? LIMIT 0, 1";
if ($res = sql($sql, $_SESSION['id'])) $dat = $res[0];

?>
<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>TOPページ | car_repair予約システム</title>
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

<?php if ($dat['client_name']) { ?>
<p class="suc_text">ログイン中： <span><?=h($dat['client_name']);?> 様</span></p>
<?php } ?>

<ul class="index_list">
	<li><a href="./rep-sele.php" class="repair"><img src="./img/icon-repair.svg" alt="" width="40" height="40"><span>修理のご予約</span></a></li>
	<li><a href="./resv-list.php" class="resv"><img src="./img/icon-list.svg" alt="" width="40" height="40"><span>予約履歴</span></a></li>
	<li><a href="./client.php" class="client"><img src="./img/icon-client.svg" alt="" width="32" height="32"><span>お客様情報</span></a></li>
</ul>

</main>

</div>

<?php include_once('./in-footer.php'); ?>
</body>
</html>
