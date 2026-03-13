$(function() {

	const client_form = $('#client_form');

	client_form.on('submit', function(e) {

		e.preventDefault();

		const formData = new FormData(client_form[0]);
		const data_new = parseInt(client_form.find('[name=new]').val());
		const data_dele = client_form.find('[name=dele]').prop('checked');

		if (data_dele) {
			if (! window.confirm('この顧客に関わる予約データも削除されます。よろしいですか？')) return false;
		}

		client_form.children('.war_text').text('').hide();
		pop_load.fadeIn(100);
		$('#main').scrollTop(0);

		$.ajax({
			url: './api/set-client.php',
			type: 'post',
			dataType: 'json',
			data: formData,
			async: true,
			contentType: false,
			processData: false,
		}).
		done(function(res) {

			client_form.children('.war_text').text(res['mess']).show();

			if (res['result'] === 'success') {
				setTimeout(function() {
					if (data_new) {
						let id = parseInt(res['data']['id']);
						if (id) location.replace('./client.php?id=' + id);
						else location.replace('./client-list.php');
					}
					else if (data_dele) {
						location.replace('./client-list.php');
					}
					else {
						location.reload();
					}
				}, 1000);
			}

			if (res['mess']) client_form.children('.war_text').text(res['mess']).show();
			pop_load.fadeOut();

		}).
		fail(function(jqXHR, textStatus, errorThrown) {
			console.error('AJAX request failed:', textStatus, errorThrown);
			console.error('Response:', jqXHR.responseText);
			client_form.children('.war_text').text('送信に失敗しました。').show();
			pop_load.fadeOut();
		});

	});

});
