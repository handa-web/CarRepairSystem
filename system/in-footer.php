<?php
if (stristr($_SERVER['PHP_SELF'], basename(__FILE__))) {
	header('Location: ./');
	exit;
}
?>
<footer id="footer">
	<div class="inner">
		<p><small>©<?=h(date('Y'));?> <?=h($_SESSION['shop']['shop_name']);?></small></p>
	</div>
</footer>
<div id="pop_load">
	<img src="./img/pop-load.svg" alt="" width="100" height="100">
</div>
<script src="./js/jquery-3.7.1.min.js"></script>
<script src="./js/base.js"></script>
