$(function() {

	const plan_form = $('#plan_form');

	$('.new_plan_span').on('click', function(e) {

		e.preventDefault();

		const formData = new FormData(plan_form[0]);
		const id = parseInt($(this).data('id'));

		plan_form.children('.war_text').text('').hide();
		pop_load.fadeIn(100);
		$('#main').scrollTop(0);

		$.ajax({
			url: './api/set-new-plan.php',
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
				location.href = './?plan-span=' + id;
			}

			if (res['mess']) plan_form.children('.war_text').text(res['mess']).show();
			pop_load.fadeOut();

		}).
		fail(function() {
			plan_form.children('.war_text').text('送信に失敗しました。').show();
			pop_load.fadeOut();
		});

	});

	$('.span_dele').on('click', function(e) {

		e.preventDefault();

		const csrf_token = plan_form.children('input[name=csrf_token]').val();
		const id = $(this).data('id');
		const date = $(this).data('date');
		const time1 = $(this).data('time1');
		const time2 = $(this).data('time2');

		if (! window.confirm('日程を削除します。よろしいですか？')) return false;

		plan_form.children('.war_text').text('').hide();
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

			if (res['mess']) plan_form.children('.war_text').text(res['mess']).show();
			pop_load.fadeOut();

		}).
		fail(function() {
			plan_form.children('.war_text').text('送信に失敗しました。').show();
			pop_load.fadeOut();
		});

	});

	plan_form.on('submit', function(e) {

		e.preventDefault();

		const formData = new FormData(plan_form[0]);
		const data_new = parseInt(plan_form.find('[name=new]').val());
		const data_dele = (plan_form.find('[name=dele]').length) ? plan_form.find('[name=dele]').prop('checked') : false;

		if (data_dele) {
			if (! window.confirm('データを削除します。よろしいですか？')) return false;
		}

		plan_form.children('.war_text').text('').hide();
		pop_load.fadeIn(100);
		$('#main').scrollTop(0);
		formData.append('new', data_new);
		formData.append('dele', data_dele);

		$.ajax({
			url: './api/set-plan.php',
			type: 'post',
			dataType: 'json',
			data: formData,
			async: true,
			processData: false,
			contentType: false,
			timeout: 10000,
		}).
		done(function(res) {
			plan_form.children('.war_text').text(res['mess']).show();
			if (res['result'] === 'success') {
				setTimeout(function() {
					if (data_new) {
						let id = parseInt(res['data']['id']);
						if (id) location.replace('./plan.php?id=' + id);
						else location.replace('./plan-list.php');
					}
					else if (data_dele) {
						location.replace('./plan-list.php');
					}
					else {
						location.reload();
					}
				}, 1000);
			}
			if (res['mess']) plan_form.children('.war_text').text(res['mess']).show();
			pop_load.fadeOut();
		}).
		// fail(function() {
		// 	plan_form.children('.war_text').text('送信に失敗しました。').show();
		// 	pop_load.fadeOut();
		// });
		fail(function(jqXHR, textStatus, errorThrown) {
			const errorMessage = jqXHR.responseJSON?.mess || '送信に失敗しました。';
			plan_form.children('.war_text').text(errorMessage).show();
			pop_load.fadeOut();
			console.error('エラー詳細:', textStatus, errorThrown);
		});

	});

});
