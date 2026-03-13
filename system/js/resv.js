$(function() {

	const shape = $('select[name="rep_shape_id"]');
	const parts = $('.parts_block');
	let id = shape.val();

	shape.on('change', function() {
		id = shape.val();
		parts.children('.parts').hide();
		if (id > 0) {
			parts.children('.parts[data-shape_id="' + id + '"]').show();
		}
	})
	.triggerHandler('change');

	const resv_form = $('#resv_form');

	$('.new_resv_span').on('click', function(e) {

		e.preventDefault();

		const formData = new FormData(resv_form[0]);
		const id = parseInt($(this).data('id'));

		resv_form.children('.war_text').text('').hide();
		pop_load.fadeIn(100);
		$('#main').scrollTop(0);

		$.ajax({
			url: './api/set-new-resv.php',
			type: 'post',
			dataType: 'json',
			data: formData,
			async: true,
			processData: false,
			contentType: false,
			timeout: 10000
		}).
		done(function(res) {

			if (res['result'] == 'success') {
				location.href = './?resv-span=' + id;
			}

			if (res['mess']) resv_form.children('.war_text').text(res['mess']).show();
			pop_load.fadeOut();

		}).
		fail(function() {
			resv_form.children('.war_text').text('送信に失敗しました。').show();
			pop_load.fadeOut();
		});

	});

	$('.span_dele').on('click', function(e) {

		e.preventDefault();

		const csrf_token = resv_form.children('input[name=csrf_token]').val();
		const id = $(this).data('id');
		const date = $(this).data('date');
		const time1 = $(this).data('time1');
		const time2 = $(this).data('time2');

		if (! window.confirm('日程を削除します。よろしいですか？')) return false;

		resv_form.children('.war_text').text('').hide();
		pop_load.fadeIn(100);
		$('#main').scrollTop(0);

		$.ajax({
			url: './api/set-staff-cale.php',
			type: 'post',
			dataType: 'json',
			data: {
				csrf_token: csrf_token,
				dele: 1,
				staff_id: id,
				staff_cale_date: date,
				staff_cale_time1: time1,
				staff_cale_time2: time2
			},
			async: true,
			timeout: 10000
		}).
		done(function(res) {

			if (res['result'] == 'success') {
				setTimeout(function() {
					location.reload();
				}, 1000);
			}

			if (res['mess']) resv_form.children('.war_text').text(res['mess']).show();
			pop_load.fadeOut();

		}).
		fail(function() {
			resv_form.children('.war_text').text('送信に失敗しました。').show();
			pop_load.fadeOut();
		});

	});

	resv_form.on('submit', function(e) {

		e.preventDefault();

		const formData = new FormData(resv_form[0]);
		const data_new = parseInt(resv_form.find('[name=new]').val());

		const resv_status = $('input[name="new_resv_status"]');
		let send_mail = false;
		resv_status.each(function() {
			if ($(this).is(':checked')) {
				send_mail = true;
			}
		});
		if (send_mail) {
			if (! window.confirm('メールを送信します。よろしいですか？')) return false;
		}

		resv_form.children('.war_text').text('').hide();
		pop_load.fadeIn(100);
		$('#main').scrollTop(0);
		formData.append('new', data_new);

		$.ajax({
			url: './api/set-resv.php',
			type: 'post',
			dataType: 'json',
			data: formData,
			async: true,
			processData: false,
			contentType: false,
			timeout: 10000,
		}).
		done(function(res) {
			resv_form.children('.war_text').text(res['mess']).show();
			if (res['result'] === 'success') {
				setTimeout(function() {
					if (data_new) {
						let id = parseInt(res['data']['id']);
						if (id) location.replace('./resv.php?id=' + id);
						else location.replace('./resv-list.php');
					}
					else {
						location.reload();
					}
				}, 1000);
			}
			if (res['mess']) resv_form.children('.war_text').text(res['mess']).show();
			pop_load.fadeOut();
		}).
		// fail(function() {
		// 	resv_form.children('.war_text').text('送信に失敗しました。').show();
		// 	pop_load.fadeOut();
		// });
		fail(function(jqXHR, textStatus, errorThrown) {
			const errorMessage = jqXHR.responseJSON?.mess || '送信に失敗しました。';
			resv_form.children('.war_text').text(errorMessage).show();
			pop_load.fadeOut();
			console.error('エラー詳細:', textStatus, errorThrown);
		});

	});

});
