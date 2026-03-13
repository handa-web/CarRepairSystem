<?php

if (stristr($_SERVER['PHP_SELF'], basename(__FILE__))) {
	header('Location: ./');
	exit;
}

session_name('car_repair_RESV');
session_start();

if (! isset($_SESSION['id']) || ! $_SESSION['id']) {
	header('Location: ./login.php');
	exit;
}

if (! isset($_SESSION['back'])) {
	$_SESSION['back'] = './';
}
if (! isset($_SESSION['status_list'])) {
	$_SESSION['status_list'] = [1 => '受付済', 2 => '修正済', 3 => '確定済', 4 => '完了済', 5 => 'キャンセル済'];
}
if (! isset($_SESSION['week_list'])) {
	$_SESSION['week_list'] = ['日', '月', '火', '水', '木', '金', '土'];
}
if (! isset($_SESSION['resv_data'])) {
	$_SESSION['resv_data'] = [];
}

include_once('./config/library.php');
include_once('./config/db-connect.php');

$now_url = '.'.$_SERVER['REQUEST_URI'];
if (strpos($now_url, 'index.php') !== false) $now_url = str_replace('index.php', '', $now_url);

$mess = '';
$jump = '';

?>
