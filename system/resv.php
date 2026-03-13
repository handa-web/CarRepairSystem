<?php

include_once('./in-init.php');

$resv_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$edit = isset($_GET['edit']) ? (int)$_GET['edit'] : 0;

$dat = [];
$sql = "SELECT * FROM dat_resv ";
$sql .= "LEFT JOIN mst_client USING(client_id) ";
$sql .= "WHERE resv_id = ? LIMIT 0, 1";
if ($res = sql($sql, $resv_id)) {

	$dat = $res[0];

	$rep_parts_id_list = explode(',', $dat['rep_parts_id_list']);

	$resv_img = [];
	for ($i = 1; $i <= 10; $i++) {
		$resv_img[$i] = '';
		if ($dat['resv_img'.$i]) {
			$resv_img[$i] = './set-img/resv-'.h($dat['resv_code']).'-'.h($i).'.jpg?'.time();
		}
	}

}
elseif ($res !== false) $mess = 'データが見つかりませんでした。';
else $mess = 'データの取得に失敗しました。';

$cale_list = [];
if ($dat) {

	$sql = "SELECT * FROM dat_staff_cale LEFT JOIN mst_staff USING (staff_id) WHERE resv_id = ? ORDER BY staff_cale_date, staff_cale_time1";
	if ($res = sql($sql, $resv_id)) {
		foreach ($res as $row) {
			$cale_list[] = $row;
		}
	}

	$rep_size_list = [];
	$sql = "SELECT * FROM mst_rep_size WHERE rep_size_stop = 0";
	if ($res = sql($sql)) $rep_size_list = $res;

	$rep_shape_list = [];
	$sql = "SELECT * FROM mst_rep_shape WHERE rep_shape_stop = 0";
	if ($res = sql($sql)) $rep_shape_list = $res;

	$rep_parts_list = [];
	$sql = "SELECT * FROM mst_rep_parts WHERE rep_parts_stop = 0";
	if ($res = sql($sql)) $rep_parts_list = $res;

	$rep_level_list = [];
	$sql = "SELECT * FROM mst_rep_level WHERE rep_level_stop = 0";
	if ($res = sql($sql)) $rep_level_list = $res;

	$client_list = [];
	$sql = "SELECT * FROM mst_client WHERE client_stop = 0";
	if ($res = sql($sql)) {
		foreach ($res as $row) {
			$client_list[(int)$row['client_id']] = $row['client_name'];
		}
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
			<li><a href="./resv-list.php">予約管理</a></li>
			<li>詳細</li>
		</ol>
	</nav>

	<?php if ($mess) { ?>
	<p class="mess"><?=h($mess);?></p>
	<?php } ?>

	<?php if ($dat) { ?>
	<form id="resv_form">
		<input type="hidden" name="csrf_token" value="<?=h($csrf_token);?>">
		<input type="hidden" name="edit" value="1">
		<input type="hidden" name="resv_id" value="<?=h($resv_id);?>">
		<input type="hidden" name="resv_code" value="<?=h($dat['resv_code']);?>">
		<input type="hidden" name="resv_status" value="<?=h($dat['resv_status']);?>">
		<input type="hidden" name="resv_status1_time" value="<?=h($dat['resv_status1_time']);?>">
		<input type="hidden" name="resv_status2_time" value="<?=h($dat['resv_status2_time']);?>">
		<input type="hidden" name="resv_status3_time" value="<?=h($dat['resv_status3_time']);?>">
		<input type="hidden" name="resv_status4_time" value="<?=h($dat['resv_status4_time']);?>">
		<input type="hidden" name="resv_status5_time" value="<?=h($dat['resv_status5_time']);?>">

		<p class="war_text" style="display: none;"></p>

		<p class="list_title">予約内容確認</p>
		<div class="edit_list">
			<div class="item">
				<div class="title">予約ID</div>
				<div class="data"><?=h($dat['resv_id']);?></div>
			</div>
			<div class="item">
				<div class="title">車両ナンバー（数字４桁）</div>
				<div class="data in"><input type="text" name="resv_car_number" value="<?=h(isset($dat['resv_car_number']) ? $dat['resv_car_number'] : '');?>" class="on" maxlength="4" required></div>
			</div>
			<div class="item">
				<div class="title">予約状況</div>
				<div class="data"><?=h($_SESSION['status_list'][$dat['resv_status']]);?></div>
			</div>
			<div class="item">
				<div class="title">入庫日</div>
				<div class="data in"><input type="date" name="in_date" value="<?=h($dat['resv_in_date']);?>" class="on"></div>
			</div>
			<div class="item">
				<div class="title">納車予定日</div>
				<div class="data in"><input type="date" name="out_date" value="<?=h($dat['resv_out_date']);?>" class="on"></div>
			</div>
			<div class="item">
				<div class="title">修理日程</div>
				<div class="data date_list">
					<?php foreach ($cale_list as $row) { ?>
					<div class="row">
						<p>
							<span><?=h($row['staff_name']);?>　<?=h(formatDateTime($row['staff_cale_date']. ' '.$row['staff_cale_time1']));?> ~ <?=h(formatDateTime($row['staff_cale_time2']));?></span>
							<button type="button" class="button dele span_butt span_dele" data-id="<?=h($row['staff_id']);?>" data-date="<?=h($row['staff_cale_date']);?>" data-time1="<?=h($row['staff_cale_time1']);?>" data-time2="<?=h($row['staff_cale_time2']);?>">削除</button>
						</p>
					</div>
					<?php } ?>
					<button type="button" class="button new span_butt new_resv_span" data-id="<?=h($resv_id);?>">追加</button>
				</div>
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
				<div class="data">
					<select name="rep_size_id" required>
						<option value="0">選択してください</option>
						<?php foreach ($rep_size_list as $row) {?>
						<option value="<?=h($row['rep_size_id']);?>" <?=h(($row['rep_size_id'] == $dat['rep_size_id']) ? 'selected' : '');?>><?=h($row['rep_size_name']);?></option>
						<?php } ?>
					</select>
				</div>
			</div>
			<div class="item">
				<div class="title">型選択</div>
				<div class="data">
					<select name="rep_shape_id" required>
						<option value="0">選択してください</option>
						<?php foreach ($rep_shape_list as $row) {?>
						<option value="<?=h($row['rep_shape_id']);?>" <?=h(($row['rep_shape_id'] == $dat['rep_shape_id']) ? 'selected' : '');?>><?=h($row['rep_shape_name']);?></option>
						<?php } ?>
					</select>
				</div>
			</div>
			<div class="item">
				<div class="title">箇所選択</div>
				<div class="data parts_list">
					<div class="parts_block">
						<div class="parts" data-shape_id="1" style="display: none;">
							<?php
							foreach ($rep_parts_list as $row) {
								$src = '';
								if ($row['rep_parts_id'] == 1) $src = '../img/parts-t-front.png';
								elseif ($row['rep_parts_id'] == 2) $src = '../img/parts-t-left.png';
								elseif ($row['rep_parts_id'] == 3) $src = '../img/parts-t-right.png';
								elseif ($row['rep_parts_id'] == 4) $src = '../img/parts-t-roof.png';
								elseif ($row['rep_parts_id'] == 5) $src = '../img/parts-t-rear.png';
							?>
							<label>
								<img src="<?=h($src);?>" alt="">
								<span><input type="checkbox" name="rep_parts_id[]" value="<?=h($row['rep_parts_id']);?>" class="parts_check" data-parts_id="1" <?=h(in_array($row['rep_parts_id'], $rep_parts_id_list) ? 'checked' : '');?>><?=h($row['rep_parts_name']);?></span>
							</label>
							<?php } ?>
						</div>
						<div class="parts" data-shape_id="2" style="display: none;">
							<?php
							foreach ($rep_parts_list as $row) {
								$src = '';
								if ($row['rep_parts_id'] == 1) $src = '../img/parts-c-front.png';
								elseif ($row['rep_parts_id'] == 2) $src = '../img/parts-c-left.png';
								elseif ($row['rep_parts_id'] == 3) $src = '../img/parts-c-right.png';
								elseif ($row['rep_parts_id'] == 4) $src = '../img/parts-c-roof.png';
								elseif ($row['rep_parts_id'] == 5) $src = '../img/parts-c-rear.png';
							?>
							<label>
								<img src="<?=h($src);?>" alt="">
								<span><input type="checkbox" name="rep_parts_id[]" value="<?=h($row['rep_parts_id']);?>" class="parts_check" data-parts_id="1" <?=h(in_array($row['rep_parts_id'], $rep_parts_id_list) ? 'checked' : '');?>><?=h($row['rep_parts_name']);?></span>
							</label>
							<?php } ?>
						</div>
						<div class="parts" data-shape_id="3" style="display: none;">
							<?php
							foreach ($rep_parts_list as $row) {
								$src = '';
								if ($row['rep_parts_id'] == 1) $src = '../img/parts-w-front.png';
								elseif ($row['rep_parts_id'] == 2) $src = '../img/parts-w-left.png';
								elseif ($row['rep_parts_id'] == 3) $src = '../img/parts-w-right.png';
								elseif ($row['rep_parts_id'] == 4) $src = '../img/parts-w-roof.png';
								elseif ($row['rep_parts_id'] == 5) $src = '../img/parts-w-rear.png';
							?>
							<label>
								<img src="<?=h($src);?>" alt="">
								<span><input type="checkbox" name="rep_parts_id[]" value="<?=h($row['rep_parts_id']);?>" class="parts_check" data-parts_id="1" <?=h(in_array($row['rep_parts_id'], $rep_parts_id_list) ? 'checked' : '');?>><?=h($row['rep_parts_name']);?></span>
							</label>
							<?php } ?>
						</div>
					</div>
				</div>
			</div>
			<div class="item">
				<div class="title">レベル選択</div>
				<div class="data">
					<select name="rep_level_id" required>
						<option value="0">選択してください</option>
						<?php foreach ($rep_level_list as $row) {?>
						<option value="<?=h($row['rep_level_id']);?>" <?=h(($row['rep_level_id'] == $dat['rep_level_id']) ? 'selected' : '');?>><?=h($row['rep_level_name']);?></option>
						<?php } ?>
					</select>
				</div>
			</div>
		</div>

		<p class="list_title">予約者情報</p>
		<div class="edit_list">
			<div class="item">
				<div class="title">会社名</div>
				<div class="data">
					<select name="client_id" required>
						<?php foreach ($client_list as $id => $name) { ?>
						<option value="<?=h($id);?>" <?=h(($id == $dat['client_id']) ? 'selected' : '');?>><?=h($name);?></option>
						<?php } ?>
					</select>
				</div>
			</div>
			<div class="item">
				<div class="title">メールアドレス</div>
				<div class="data in"><input type="text" name="resv_client_mail" value="<?=h(isset($dat['resv_client_mail']) ? $dat['resv_client_mail'] : '');?>" class="on" required></div>
			</div>
			<div class="item">
				<div class="title">予約者名</div>
				<div class="data in"><input type="text" name="resv_client_name" value="<?=h(isset($dat['resv_client_name']) ? $dat['resv_client_name'] : '');?>" class="on" required></div>
			</div>
			<div class="item">
				<div class="title">電話番号</div>
				<div class="data in"><input type="text" name="resv_client_tel" value="<?=h(isset($dat['resv_client_tel']) ? $dat['resv_client_tel'] : '');?>" class="on" required></div>
			</div>
			<div class="item">
				<div class="title">画像追加</div>
				<div class="data in">
					<div class="all_button_list">
						<button type="button" id="img_add_all" class="button">まとめて<span>画像追加</span></button>
						<button type="button" id="img_delete_all" class="button file_dele">すべて削除</button>
					</div>
					<ul id="img_list" class="img_list">
						<li style="display:none;"><input type="file" id="multi_img_input" multiple accept="image/jpeg,image/png"></li>
						<?php for ($i = 1; $i <= 10; $i++) { ?>
						<li><img src="<?=h($resv_img[$i]);?>" alt="" class="file_img" style="<?=h($resv_img[$i] ? '' : 'display: none;')?>"><input type="file" name="resv_img<?=h($i);?>"><button type="button" class="button file">画像選択</button><button type="button" class="button file_dele" style="<?=h($resv_img[$i] ? '' : 'display:none');?>">削除</button></li>
						<?php } ?>
					</ul>
					<p>※最大10枚まで</p>
					<p>※2M以下のjpgまたはpng</p>
				</div>
			</div>
			<div class="item">
				<div class="title">備考（任意）</div>
				<div class="data in"><textarea name="resv_text" rows="5" class="on"><?=h($dat['resv_text']);?></textarea></div>
			</div>
			<div class="item">
				<div class="title">メモ（任意）</div>
				<div class="data in"><textarea name="resv_memo" rows="5" class="on"><?=h($dat['resv_memo']);?></textarea></div>
			</div>
		</div>

		<p class="list_title">予約処理</p>
		<div class="edit_list">
			<div class="item">
				<div class="title">予約受付</div>
				<div class="data"><?=h(formatDateTime($dat['resv_status1_time']));?></div>
			</div>
			<div class="item">
				<div class="title">予約修正</div>
				<div class="data">
					<?php if ($dat['resv_status'] < 3) { ?>
					<label><input type="checkbox" name="new_resv_status" value="2">予約修正メール送信</label>
					<textarea name="resv_mail_text2" rows="3"><?=h($_SESSION['shop']['shop_resv_mail_status2']);?></textarea>
					<?php } else { ?>
					<?=h(formatDateTime($dat['resv_status2_time']));?> ※変更不可
					<?php } ?>
				</div>
			</div>
			<div class="item">
				<div class="title">予約確定</div>
				<div class="data">
					<?php if ($dat['resv_status'] < 4) {?>
					<label><input type="checkbox" name="new_resv_status" value="3">予約確定メール送信</label>
					<textarea name="resv_mail_text3" rows="3"><?=h($_SESSION['shop']['shop_resv_mail_status3']);?></textarea>
					<?php } else { ?>
					<?=h(formatDateTime($dat['resv_status3_time']));?> ※変更不可
					<?php } ?>
				</div>
			</div>
			<div class="item">
				<div class="title">予約完了</div>
				<div class="data">
					<?php if ($dat['resv_status'] < 4) {?>
					<label><input type="checkbox" name="new_resv_status" value="4">予約完了メール送信</label>
					<textarea name="resv_mail_text4" rows="3"><?=h($_SESSION['shop']['shop_resv_mail_status4']);?></textarea>
					<?php } else { ?>
					<?=h($dat['resv_status4_time'] ? formatDateTime($dat['resv_status4_time']) : '');?> ※変更不可
					<?php } ?>
				</div>
			</div>
			<div class="item">
				<div class="title">予約キャンセル</div>
				<div class="data">
					<?php if ($dat['resv_status'] < 4) { ?>
					<label><input type="checkbox" name="new_resv_status" value="5">予約キャンセルメール送信</label>
					<textarea name="resv_mail_text5" rows="3"><?=h($_SESSION['shop']['shop_resv_mail_status5']);?></textarea>
					<?php } else { ?>
					<?=h($dat['resv_status5_time'] ? formatDateTime($dat['resv_status5_time']) : '');?> ※変更不可
					<?php } ?>
				</div>
			</div>
		</div>

	</form>

	<?php } ?>

	<ul class="button_list">
		<li><a href="<?=h($_SESSION['back']);?>" class="button back">戻る</a></li>
		<?php if ($dat) { ?>
		<li><a href="./resv-mail.php?id=<?=h($resv_id);?>" class="button">メール履歴</a></li>
		<li><button type="submit" form="resv_form" class="button edit" <?=h(($dat['resv_status'] < 4) ? '' : 'disabled');?>>更新</button></li>
		<?php } ?>
	</ul>

</main>

</div>

<?php include_once('./in-footer.php'); ?>
<script src="./js/resv.js"></script>
<script>
$(function() {
	    // new_resv_status チェックボックスの排他制御
    const resv_status = $('input[name="new_resv_status"]');
    resv_status.on('change', function() {
        if ($(this).is(':checked')) {
            resv_status.not(this).prop('checked', false);
        }
    });

    // まとめて追加
    $('#img_add_all').on('click', function() {
        $('#multi_img_input').trigger('click');
    });

    // まとめて追加 input change
    $('#multi_img_input').on('change', function(e) {
		const files = e.target.files;
		if (!files.length) return;
		let fileInputs = $('#img_list input[type="file"]').not('#multi_img_input');
		let imgTags = $('#img_list .file_img');
		let deleBtns = $('#img_list .file_dele');
		let idx = 0;
		fileInputs.each(function(i) {
			if (idx >= files.length) return false;
			// すでに画像がセットされている場合はスキップ（inputの値 or imgのsrcが空でない場合）
			let img = imgTags.eq(i);
			// src属性が空でない（デフォルト画像でない）場合はスキップ
			if ($(this).val() || (img.attr('src') && img.attr('src').indexOf('set-img/resv-') !== -1 && img.attr('src').indexOf('?') !== -1)) return;
			const file = files[idx];
			let dt = new DataTransfer();
			dt.items.add(file);
			this.files = dt.files;
			// プレビュー表示
			let delBtn = deleBtns.eq(i);
			let reader = new FileReader();
			reader.onload = function(e) {
				img.attr('src', e.target.result).show();
				delBtn.show();
			};
			reader.readAsDataURL(file);
			idx++;
		});
		$(this).val('');
    });

    // すべて削除
    $('#img_delete_all').on('click', function() {
        $('#img_list .file_img').hide().attr('src', '');
        $('#img_list input[type="file"]').not('#multi_img_input').val('');
        $('#img_list .file_dele').hide();
    });

});
</script>
</body>
</html>
