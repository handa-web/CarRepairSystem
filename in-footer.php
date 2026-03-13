<?php
if (stristr($_SERVER['PHP_SELF'], basename(__FILE__))) {
	header('Location: ./');
	exit;
}
?>
<footer id="footer">
	<div class="inner">
		<ul class="footer_list">
			<li><a href="./term.php">利用規約</a></li>
			<li><a href="./policy.php">プライバシーポリシー</a></li>
			<li><a href="./contact.php">お問い合わせ</a></li>
		</ul>
	</div>
</footer>

<div id="pop_load">
	<img src="./img/pop-load.svg" alt="" width="100" height="100">
</div>

<div id="pop_modal" style="display: none;">
	<div class="modal_box">
		<button type="button" class="close"></button>
		<p class="title"></p>
		<p class="text"></p>
		<p class="button_list">
			<button type="button" class="button solve">実行する</button>
		</p>
	</div>
	<div class="overlay"></div>
</div>

<script src="./js/jquery-3.7.1.min.js"></script>
<script src="./js/base.js"></script>
