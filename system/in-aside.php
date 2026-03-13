<?php
if (stristr($_SERVER['PHP_SELF'], basename(__FILE__))) {
	header('Location: ./');
	exit;
}
if (! isset($now_url)) $now_url = '';
if (isset($_SESSION['id']) && $_SESSION['id']) {
?>
<aside id="aside">
	<nav id="nav">
		<div class="inner">
			<ul class="list">
				<li><a href="./" class="<?=h(( ($now_url === './') || (strpos($now_url, './?') !== false) ) ? 'on' : '');?>">TOP</a></li>
				<li><a href="./resv-list.php" class="<?=h((strpos($now_url, './resv') !== false)? 'on' : '');?>">予約管理</a></li>
				<li><a href="./plan-list.php" class="<?=h((strpos($now_url, './plan') !== false)? 'on' : '');?>">予定管理</a></li>
				<li><a href="./client-list.php" class="<?=h((strpos($now_url, './client') !== false)? 'on' : '');?>">顧客管理</a></li>
				<li><a href="./staff-list.php" class="<?=h((strpos($now_url, './staff') !== false)? 'on' : '');?>">スタッフ管理</a></li>
				<li><a href="./setting.php" class="<?=h((strpos($now_url, './setting') !== false)? 'on' : '');?>">設定</a></li>
			</ul>
		</div>
	</nav>
</aside>
<?php } ?>
