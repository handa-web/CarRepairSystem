<?php
if (stristr($_SERVER['PHP_SELF'], basename(__FILE__))) {
	header('Location: ./');
	exit;
}
?>
<header id="header">
	<div class="inner">
		<h1><a href="./">car_repair 予約システム</a></h1>
		<?php if (isset($_SESSION['id']) && $_SESSION['id']) { ?>
		<button type="button" id="hamburger">
			<span class="line"></span>
			<span class="text">Menu</span>
		</button>
		<?php } ?>
	</div>
</header>
