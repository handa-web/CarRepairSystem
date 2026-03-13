$(function() {

	const staff_form = $('#staff_form');

	staff_form.on('submit', function(e) {

		e.preventDefault();

		const formData = new FormData(staff_form[0]);
		const data_new = parseInt(staff_form.find('[name=new]').val());
		const data_dele = staff_form.find('[name=dele]').prop('checked');

		if (data_dele) {
			if (! window.confirm('データを削除します。よろしいですか？')) return false;
		}

		staff_form.children('.war_text').text('').hide();
		pop_load.fadeIn(100);
		$('#main').scrollTop(0);

		$.ajax({
			url: './api/set-staff.php',
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
				setTimeout(function() {
					if (data_new) {
						let id = parseInt(res['data']['id']);
						if (id) location.replace('./staff.php?id=' + id);
						else location.replace('./staff-list.php');
					}
					else if (data_dele) {
						location.replace('./staff-list.php');
					}
					else {
						location.reload();
					}
				}, 1000);
			}

			if (res['mess']) staff_form.children('.war_text').text(res['mess']).show();
			pop_load.fadeOut();

		}).
		fail(function(jqXHR, textStatus, errorThrown) {
			console.error('AJAX request failed:', textStatus, errorThrown);
			console.error('Response:', jqXHR.responseText);
			staff_form.children('.war_text').text('送信に失敗しました。').show();
			pop_load.fadeOut();
		});

	});

		// 認証メール確認
		const staff_repass_form = $('#staff_repass_form');
		staff_repass_form.on('submit', function(e) {
	
			e.preventDefault();
	
			const formData = new FormData(staff_repass_form[0]);
	
			staff_repass_form.children('.war_text').text('').hide();
			pop_load.fadeIn(100);
			$('#main').scrollTop(0);
	
			$.ajax({
				url: './api/set-staff-repass.php',
				type: 'post',
				dataType: 'json',
				data: formData,
				async: true,
				contentType: false,
				processData: false,
			}).
			done(function(res) {
	
				staff_repass_form.children('.war_text').text(res['mess']).show();
	
				if (res['result'] === 'success') {
					setTimeout(function() {
						location.replace('./');
					}, 1000);
				}
	
				if (res['mess']) staff_repass_form.children('.war_text').text(res['mess']).show();
				pop_load.fadeOut();
	
			}).
			fail(function() {
				staff_repass_form.children('.war_text').text('送信に失敗しました。').show();
				pop_load.fadeOut();
			});
			// fail(function(jqXHR, textStatus, errorThrown) {
			// 	const errorMessage = jqXHR.responseJSON?.mess || '送信に失敗しました。';
			// 	staff_repass_form.children('.war_text').text(errorMessage).show();
			// 	pop_load.fadeOut();
			// 	console.error('エラー詳細:', textStatus, errorThrown);
			// });
	
		});

});
