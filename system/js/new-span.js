$(function() {

	$('.set_data').each(function() {
		const width = 'calc(' + $(this).data('minutes') / 60 * 100 + '% + ' + ($(this).data('minutes') / 60 - 1) / 2 + 'px)';
		$(this).css('width', width);
	});

	const base_form = $('.base form');
	base_form.on('submit', function(e) {

		e.preventDefault();

		const formData = new FormData($(this)[0]);
		const resv_span = parseInt(base_form.find('input[name="resv_span"]').val());
		const plan_span = parseInt(base_form.find('input[name="plan_span"]').val());

		if ($(this).hasClass('set_off')) return false;
		if (resv_span <= 0 && plan_span <= 0) return false;

		base_form.children('.war_text').text('').hide();
		pop_load.fadeIn(100);
		$('#main').scrollTop(0);

		$.ajax({
			url: './api/set-new-span.php',
			type: 'post',
			dataType: 'json',
			data: formData,
			async: true,
			processData: false,
			contentType: false
		}).
		done(function(res) {

			if (res['result'] == 'success') {
				location.reload();
			}

			if (res['mess']) base_form.children('.war_text').text(res['mess']).show();
			pop_load.fadeOut();

		}).
		fail(function() {
			base_form.children('.war_text').text('送信に失敗しました。').show();
			pop_load.fadeOut();
		});

	});

	const set_form = $('#set_form');
	set_form.on('submit', function(e) {

		e.preventDefault();

		const formData = new FormData(set_form[0]);
		const resv_id = parseInt(set_form.find('input[name="resv_id"]').val());
		const plan_id = parseInt(set_form.find('input[name="plan_id"]').val());

		set_form.children('.war_text').text('').hide();
		pop_load.fadeIn(100);
		$('#main').scrollTop(0);

		$.ajax({
			url: './api/set-staff-cale.php',
			type: 'post',
			dataType: 'json',
			data: formData,
			async: true,
			processData: false,
			contentType: false
		}).
		done(function(res) {

			if (res['result'] == 'success') {
				setTimeout(function() {
					if (resv_id > 0) {
						location.href = './resv.php?id=' + resv_id;
					}
					else if (plan_id > 0) {
						location.href = './plan.php?id=' + plan_id;
					}
				}, 1000);
			}

			if (res['mess']) set_form.children('.war_text').text(res['mess']).show();
			pop_load.fadeOut();

		}).
		fail(function() {
			set_form.children('.war_text').text('送信に失敗しました。').show();
			pop_load.fadeOut();
		});

	});

	set_form.on('reset', function(e) {

		e.preventDefault();

		const csrf_token = set_form.children('input[name=csrf_token]').val();
		const reset = 1;

		set_form.children('.war_text').text('').hide();
		pop_load.fadeIn(100);
		$('#main').scrollTop(0);

		$.ajax({
			url: './api/set-new-span.php',
			type: 'post',
			dataType: 'json',
			data: {
				csrf_token: csrf_token,
				reset: reset
			},
			async: true,
		}).
		done(function(res) {

			if (res['result'] == 'success') {
				location.reload();
			}

			if (res['mess']) set_form.children('.war_text').text(res['mess']).show();
			pop_load.fadeOut();

		}).
		fail(function() {
			set_form.children('.war_text').text('送信に失敗しました。').show();
			pop_load.fadeOut();
		});

	});

});
