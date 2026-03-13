$(function() {

	const repair_form = $('#repair_form');

	repair_form.on('submit', function(e) {

		e.preventDefault();

		const formData = new FormData(repair_form[0]);
		const data_new = parseInt(repair_form.find('[name=new]').val());
		const data_dele = repair_form.find('[name=dele]').prop('checked');

		if (data_dele) {
			if (! window.confirm('データを削除します。よろしいですか？')) return false;
		}

		repair_form.children('.war_text').text('').hide();
		pop_load.fadeIn(100);
		$('#main').scrollTop(0);

		$.ajax({
			url: './api/set-repair.php',
			type: 'post',
			dataType: 'json',
			data: formData,
			async: true,
			processData: false,
			contentType: false,
			timeout: 10000
		}).
		done(function(res) {

			repair_form.children('.war_text').text(res['mess']).show();

			if (res['result'] === 'success') {
				setTimeout(function() {
					if (data_new) {
						let id = parseInt(res['data']['id']);
						if (id) location.replace('./repair.php?id=' + id);
						else location.replace('./repair-list.php');
					}
					else if (data_dele) {
						location.replace('./repair-list.php');
					}
					else {
						location.reload();
					}
				}, 1000);
			}

			if (res['mess']) repair_form.children('.war_text').text(res['mess']).show();
			pop_load.fadeOut();

		}).
		fail(function() {
			repair_form.children('.war_text').text('送信に失敗しました。').show();
			pop_load.fadeOut();
		});

	});

});
