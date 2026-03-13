<?php

include_once('./in-init.php');

$_SESSION['back'] = $now_url;

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

<main id="main">

	<nav class="bread">
		<ol>
			<li><a href="./">TOP</a></li>
			<li>システム</li>
		</ol>
	</nav>

	<form id="logout_form">
		<input type="hidden" name="csrf_token" value="<?=h($csrf_token);?>">

		<p class="war_text" style="display: none"></p>

		<div class="list_tbl">
			<table>
				<thead>
					<tr>
						<th class="name">ページ名</th>
						<th>詳細</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<th class="name"><a href="./staff-list.php">スタッフ一覧</a></th>
						<td>店舗スタッフの登録・修正・削除</td>
					</tr>
					<tr>
						<th><a href="./setting.php">店舗設定</a></th>
						<td>店舗情報の修正・メールテンプレート修正</td>
					</tr>
				</tbody>
			</table>
		</div>

		<div class="list_tbl">
			<table>
				<thead>
					<tr>
						<th class="name">ページ名</th>
						<th>詳細</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<th class="name"><a href="./rep-size-list.php">修理分類一覧</a></th>
						<td>修理分類の登録・修正・削除</td>
					</tr>
					<tr>
						<th class="name"><a href="./rep-shape-list.php">修理型一覧</a></th>
						<td>修理型の登録・修正・削除</td>
					</tr>
					<tr>
						<th class="name"><a href="./rep-parts-list.php">修理箇所一覧</a></th>
						<td>修理箇所の登録・修正・削除</td>
					</tr>
					<tr>
						<th class="name"><a href="./rep-level-list.php">修理レベル一覧</a></th>
						<td>修理レベルの登録・修正・削除</td>
					</tr>
				</tbody>
			</table>
		</div>

		<ul class="button_list">
			<li><a href="./" class="button back">戻る</a></li>
			<li>
				<form id="logout_form">
					<input type="hidden" name="csrf_token" value="<?=h($csrf_token);?>">
					<button type="submit" class="button submit">ログアウト</button>
				</form>
			</li>
		</ul>

	</form>

</main>

<?php include_once('./in-footer.php'); ?>
</body>
</html>
