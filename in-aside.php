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
				<li><a href="./rep-sele.php" class="<?=h((strpos($now_url, './rep') !== false)? 'on' : '');?>">修理のご予約</a></li>
				<li><a href="./resv-list.php" class="<?=h((strpos($now_url, './resv') !== false)? 'on' : '');?>">予約履歴</a></li>
				<li><a href="./client.php" class="<?=h((strpos($now_url, './client') !== false)? 'on' : '');?>">お客様情報</a></li>
				<li><a href="./contact.php" class="<?=h((strpos($now_url, './contact') !== false)? 'on' : '');?>">お問い合わせ</a></li>
			</ul>
		</div>
	</nav>
</aside>
<?php } ?>
