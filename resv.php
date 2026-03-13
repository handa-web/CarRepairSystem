<?php

include_once('./in-init.php');

$resv_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$dat = [];
$cale_list = [];
$sql = "SELECT * FROM dat_resv INNER JOIN mst_client USING(client_id) ";
$sql .= "WHERE client_id = ? AND resv_id = ? LIMIT 0, 1";
$par = [$_SESSION['id'], $resv_id];
if ($res = sql($sql, $par)) {

	$dat = $res[0];

	$rep_parts_id_list = explode(',', $dat['rep_parts_id_list']);

	$rep_size_list = [];
	$sql = "SELECT * FROM mst_rep_size";
	if ($res = sql($sql)) {
		foreach ($res as $row) {
			$add_text = $row['rep_size_dele'] ? '【削除済】' : ($row['rep_size_stop'] ? '【停止中】' : '');
			$rep_size_list[(int)$row['rep_size_id']] = $row['rep_size_name'].$add_text;
		}
	}

	$rep_shape_list = [];
	$sql = "SELECT * FROM mst_rep_shape";
	if ($res = sql($sql)) {
		foreach ($res as $row) {
			$add_text = $row['rep_shape_dele'] ? '【削除済】' : ($row['rep_shape_stop'] ? '【停止中】' : '');
			$rep_shape_list[(int)$row['rep_shape_id']] = $row['rep_shape_name'].$add_text;
		}
	}

	$rep_parts_list = [];
	$sql = "SELECT * FROM mst_rep_parts";
	if ($res = sql($sql)) {
		foreach ($res as $row) {
			$add_text = $row['rep_parts_dele'] ? '【削除済】' : ($row['rep_parts_stop'] ? '【停止中】' : '');
			$rep_parts_list[(int)$row['rep_parts_id']] = $row['rep_parts_name'].$add_text;
		}
	}

	$rep_level_list = [];
	$sql = "SELECT * FROM mst_rep_level";
	if ($res = sql($sql)) {
		foreach ($res as $row) {
			$add_text = $row['rep_level_dele'] ? '【削除済】' : ($row['rep_level_stop'] ? '【停止中】' : '');
			$rep_level_list[(int)$row['rep_level_id']] = $row['rep_level_name'].$add_text;
		}
	}

	$resv_img = [];
	for ($i = 1; $i <= 10; $i++) {
		$resv_img[$i] = '';
		if ($dat['resv_img'.$i]) {
			$resv_img[$i] = './system/set-img/resv-'.h($dat['resv_code']).'-'.h($i).'.jpg?'.time();
		}
	}

}
elseif ($res !== false) $mess = 'データが見つかりませんでした。';
else $mess = 'データの取得に失敗しました。';

$csrf_token = getCsrfToken();

?>
<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>予約情報 | car_repair予約システム</title>
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
			<li><a href="./resv-list.php">予約履歴</a></li>
			<li>予約情報</li>
		</ol>
	</nav>

	<form id="resv_edit_form">
		<input type="hidden" name="csrf_token" value="<?=h($csrf_token);?>">
		<input type="hidden" name="resv_id" value="<?=h($resv_id);?>">
		<input type="hidden" name="resv_code" value="<?=h($dat['resv_code']);?>">
		<input type="hidden" name="client_id" value="<?=h($dat['client_id']);?>">
		<input type="hidden" name="resv_status" value="<?=h($dat['resv_status']);?>">

		<p class="war_text" style="display: none;"></p>

		<p class="list_title">予約内容確認</p>
		<div class="edit_list">
			<div class="item">
				<div class="title">予約ID</div>
				<div class="data"><?=h($dat['resv_id']);?></div>
			</div>
			<div class="item">
				<div class="title">車両ナンバー（数字４桁）</div>
				<div class="data"><?=h($dat['resv_car_number']);?></div>
			</div>
			<div class="item">
				<div class="title">予約状況</div>
				<div class="data"><?=h($_SESSION['status_list'][$dat['resv_status']]);?></div>
			</div>
			<div class="item">
				<div class="title">入庫日</div>
				<div class="data"><?=h(formatDateTime($dat['resv_in_date']));?></div>
			</div>
			<div class="item">
				<div class="title">納車予定日</div>
				<div class="data"><?=h(formatDateTime($dat['resv_out_date']));?></div>
			</div>
			<div class="item">
				<div class="title">予約番号</div>
				<div class="data"><?=h($dat['resv_code']);?></div>
			</div>
		</div>

		<p class="list_title"> 修理内容確認</p>
		<div class="edit_list">
			<div class="item">
				<div class="title">分類選択</div>
				<div class="data"><?=h(isset($rep_size_list[$dat['rep_size_id']]) ? $rep_size_list[$dat['rep_size_id']] : '');?></div>
			</div>
			<div class="item">
				<div class="title">型選択</div>
				<div class="data"><?=h(isset($rep_shape_list[$dat['rep_shape_id']]) ? $rep_shape_list[$dat['rep_shape_id']] : '');?></div>
			</div>
			<div class="item">
				<div class="title">箇所選択</div>
				<div class="data parts_list">
					<div class="parts_block">
						<div class="parts">
							<?php
							foreach ($rep_parts_list as $id => $name) {
								$shape = 't';
								if ($dat['rep_shape_id'] == 2) $shape = 'c';
								elseif ($dat['rep_shape_id'] == 3) $shape = 'w';
								$src = '';
								if ($id == 1) $src = './img/parts-'.$shape.'-front.png';
								elseif ($id == 2) $src = './img/parts-'.$shape.'-left.png';
								elseif ($id == 3) $src = './img/parts-'.$shape.'-right.png';
								elseif ($id == 4) $src = './img/parts-'.$shape.'-roof.png';
								elseif ($id == 5) $src = './img/parts-'.$shape.'-rear.png';
							?>
							<label>
								<?php if ($src) { ?>
								<img src="<?=h($src);?>" alt="">
								<?php } ?>
								<span><input type="checkbox" name="parts[]" value="1" disabled <?=h(in_array($id, $rep_parts_id_list) ? 'checked' : '');?>><?=h($name);?></span>
							</label>
							<?php } ?>
						</div>
					</div>
				</div>
			</div>
			<div class="item">
				<div class="title">レベル選択</div>
				<div class="data"><?=h(isset($rep_level_list[$dat['rep_level_id']]) ? $rep_level_list[$dat['rep_level_id']] : '');?></div>
			</div>
		</div>

		<p class="list_title">予約者情報</p>
		<div class="edit_list">
			<div class="item">
				<div class="title">会社名</div>
				<div class="data"><?=h($dat['client_name']);?></div>
			</div>
			<div class="item">
				<div class="title">予約者名</div>
				<div class="data"><?=h($dat['resv_client_name']);?></div>
			</div>
			<div class="item">
				<div class="title">メールアドレス</div>
				<div class="data"><?=h($dat['resv_client_mail']);?></div>
			</div>
			<div class="item">
				<div class="title">電話番号</div>
				<div class="data"><?=h($dat['resv_client_tel']);?></div>
			</div>
		</div>

		<p class="list_title">更新可能項目</p>
		<div class="edit_list">
			<?php if ($dat['resv_status'] < 3) { ?>
			<div class="item">
				<div class="title">画像追加</div>
				<div class="data in">
					<ul id="img_list" class="img_list">
						<?php for ($i = 1; $i <= 10; $i++) { ?>
						<li><img src="<?=h($resv_img[$i]);?>" alt="" class="file_img" style="<?=h($resv_img[$i] ? '' : 'display: none;')?>"><input type="file" name="resv_img<?=h($i);?>"><button type="button" class="button file">画像選択</button><button type="button" class="button file_dele" style="<?=h($resv_img[$i] ? '' : 'display:none');?>">削除</button></li>
						<?php } ?>
					</ul>
					<p class="hint">※2M以下のjpgまたはpng</p>
				</div>
			</div>
			<div class="item">
				<div class="title">備考</div>
				<div class="data in"><textarea name="resv_text" rows="5" class="on"><?=h($dat['resv_text']);?></textarea></div>
			</div>
			<div class="item">
				<div class="title">キャンセル</div>
				<div class="data">
					<label><input type="checkbox" name="cancel" value="1" <?=h(($dat['resv_status'] < 3) ? '' : 'disabled');?> <?=h(($dat['resv_status'] == 5) ? 'checked' : '');?>>予約をキャンセルする</label>
				</div>
				<p class="hint">※予約確定後のキャンセルは、<a href="./contact.php" class="link">お問い合わせフォーム</a>にてお問い合わせください。</p>
			</div>
			<?php } else { ?>
			<p class="hint">※この予約は「<?=h($_SESSION['status_list'][$dat['resv_status']])?>」のため更新できません。</p>
			<?php } ?>
		</div>

		<ul class="button_list">
			<li><a href="./" class="button back">戻る</a></li>
			<li><a href="./resv-mail.php?id=<?=h($resv_id);?>" class="button">メール履歴</a></li>
			<li><button type="submit" form="resv_edit_form" class="button submit" <?=h(($dat['resv_status'] < 3) ? '' : 'disabled');?>>更新</button></li>
		</ul>

	</form>

</main>

</div>

<?php include_once('./in-footer.php'); ?>
</body>
</html>
