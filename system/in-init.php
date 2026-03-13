<?php

if (stristr($_SERVER['PHP_SELF'], basename(__FILE__))) {
	header('Location: ./');
	exit;
}

session_name('car_repair_RESV-SYSTEM_from_2024');
session_start();

include_once('../config/library.php');
include_once('../config/db-connect.php');

if (! (isset($_SESSION['id']) && $_SESSION['id'])) {
	header('Location: ./login.php');
	exit;
}

if (! (isset($_SESSION['shop']) && $_SESSION['shop'])) {
	$sql = "SELECT * FROM mst_shop WHERE shop_id = 1 LIMIT 0, 1";
	if ($res = sql($sql)) {
		$_SESSION['shop'] = $res[0];
	}
}

if (! isset($_SESSION['back'])) {
	$_SESSION['back'] = './';
}
if (! (isset($_SESSION['new_resv']) && $_SESSION['new_resv'])) {
	$_SESSION['new_resv'] = [
		'resv_id' => 0,
		'rep_size_id' => 0,
		'rep_shape_id' => 0,
		'rep_parts_id_list' => [],
		'rep_level_id' => 0,
		'client_id' => 0,
		'resv_client_name' => '',
		'resv_client_mail' => '',
		'resv_client_tel' => '',
		'resv_text' => '',
		'resv_memo' => '',
	];
}
if (! (isset($_SESSION['new_plan']) && $_SESSION['new_plan'])) {
	$_SESSION['new_plan'] = [
		'plan_id' => 0,
		'plan_name' => '',
		'plan_memo' => '',
	];
}
if (! (isset($_SESSION['new_span']) && $_SESSION['new_span'])) {
	$_SESSION['new_span'] = [
		'staff_id' => 0,
		'date_time1' => '',
		'date_time2' => '',
		'resv_span' => 0,
		'plan_span' => 0,
	];
}
if (! isset($_SESSION['status_list'])) {
	$_SESSION['status_list'] = [1 => '受付済', 2 => '修正済', 3 => '確定済', 4 => '完了済', 5 => 'キャンセル済'];
}
if (! isset($_SESSION['week_list'])) {
	$_SESSION['week_list'] = ['日', '月', '火', '水', '木', '金', '土'];
}

if (! isset($_SESSION['repair_time_list'])) {
	$_SESSION['repair_time_list'] = [];
	for ($i = 30; $i <= 720; $i += 30) {
		$h = sprintf('%02d', floor($i / 60));
		$m = ($i % 60 == 0) ? '00' : '30';
		$time = $h.':'.$m.':00';
		$text = formatTime($time);
		$_SESSION['repair_time_list'][] = ['time' => $time, 'text' => $text];
	}
}

$mess = '';
$jump = '';

$now_url = str_replace('/system', '.', $_SERVER['REQUEST_URI']);
if (strpos($now_url, 'index.php') !== false) $now_url = str_replace('index.php', '', $now_url);
?>
