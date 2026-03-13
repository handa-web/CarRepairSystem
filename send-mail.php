<?php

include_once('./in-init.php');

$resv_id = (isset($_GET['id'])) ? (int)$_GET['id'] : 0;

?>
<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>メール送信完了 | car_repair予約システム</title>
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

	<p class="suc_text">メール送信完了</p>

	<div class="edit_list">
		<div class="item">
			<div class="title">自動メールが送信されましたのでご確認ください。</div>
			<div class="data">
			メールが届かない場合は以下をご確認ください。<br>
			・「@example.com」アドレスが受信可能か<br>
			・メールアドレスに間違いがないか
			</div>
		</div>
	</div>

	<ul class="button_list">
		<li><a href="./" class="button">HOME</a></li>
		<?php if ($resv_id) { ?>
		<li><a href="./resv.php?id=<?=h($resv_id);?>" class="button submit">予約確認</a></li>
		<?php } ?>
	</ul>

</main>

</div>

<?php include_once('./in-footer.php'); ?>
</body>
</html>
