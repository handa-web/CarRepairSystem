$(function() {

	const shop_form = $('#shop_form');

	shop_form.on('submit', function(e) {

		e.preventDefault();

		const formData = new FormData(shop_form[0]);
		const shop_resv_stop = shop_form.find('[name=shop_resv_stop]').prop('checked');
		const shop_stop = shop_form.find('[name=shop_stop]').prop('checked');

		if (shop_resv_stop) {
			if (! window.confirm('予約の新規受付を停止します。よろしいですか？')) return false;
		}

		if (shop_stop) {
			if (! window.confirm('システムを停止します。よろしいですか？')) return false;
		}

		shop_form.children('.war_text').text('').hide();
		pop_load.fadeIn(100);
		$('#main').scrollTop(0);

		$.ajax({
			url: './api/set-setting.php',
			type: 'post',
			dataType: 'json',
			data: formData,
			async: true,
			contentType: false,
			processData: false,
			timeout: 10000
		}).
		done(function(res) {

			if (res['result'] === 'success') {
				location.reload();
			}

			if (res['mess']) shop_form.children('.war_text').text(res['mess']).show();
			pop_load.fadeOut();

		}).
		fail(function() {
			shop_form.children('.war_text').text('送信に失敗しました。').show();
			pop_load.fadeOut();
		});

	});

});
