const pop_load = $('#pop_load');

$(function() {

	const header_height = $('#header').height();

	pop_load.fadeIn(100);

	// 画面遷移時
	$(window).on('beforeunload', function() {
		pop_load.fadeIn(100);
		setTimeout(function() {
			pop_load.fadeOut();
		}, 5000);
	});

	// 画像選択
	$('.button.file').on('click', function() {
		$(this).siblings('input[type=file]').click();
	});

	// 画像キャンセル
	$('.button.file_dele').on('click', function() {
		$(this).siblings('input[type=file]').val('')
		.end().siblings('img').hide()
		.end().hide();
	});

	// 画像表示
	$('input[type=file]').on('change', function(e) {
		const img = $(this).siblings('img');
		const file = e.target.files[0];
		if (file) {
			var reader = new FileReader();  // FileReaderオブジェクトを作成
			reader.onload = function(e) {
				img.attr('src', e.target.result).show();  // 画像を表示
			}
			reader.readAsDataURL(file);  // 画像ファイルをData URLとして読み込む
			$(this).siblings('.file_dele').show();
		}
		if (img.is(':visible')) {
			$(this).siblings('.file_dele').show();
		}
	}).triggerHandler('change');

	// スムーススクロール
	$('a[href^="#"]').on('click', function() {
		let href = '#' + $(this).attr('href').split('#')[1];
		let target = $(href == '#' || href == '' ? 'html' : href);
		let position = target.offset().top - header_height - 16;
		let time = 500;
		$('body,html').animate({scrollTop: position}, time);
	});

	//ハンバーガー
	const hamburger = $('#hamburger');
	const nav = $('#nav');
	hamburger.on('click', function() {
		if (window.matchMedia('(max-width: 767px)').matches) {
			if (! $(this).hasClass('on')) {
				$(this).addClass('on');
				nav.addClass('on');
				$('body').css('overflow', 'hidden');
			}
			else {
				$(this).removeClass('on');
				nav.removeClass('on');
				$('body').css('overflow', '');
			}
		}
	});
	nav.on('click', function() {
		if (window.matchMedia('(max-width: 767px)').matches) {
			hamburger.removeClass('on');
			$(this).removeClass('on');
			$('body').css('overflow', '');
		}
	});

	// ログイン
	const login_form = $('#login_form');
	login_form.on('submit', function(e) {

		e.preventDefault();

		const formData = new FormData(login_form[0]);

		login_form.children('.war_text').text('').hide();
		pop_load.fadeIn(100);
		$('#main').scrollTop(0);

		$.ajax({
			url: './api/set-login.php',
			type: 'post',
			dataType: 'json',
			data: formData,
			async: true,
			processData: false,
			contentType: false
		}).
		done(function(res) {
			if (res['result'] === 'success') {
				location.replace('./');
			}
			else {
				login_form.children('.war_text').text(res['mess']).show();
				pop_load.fadeOut();
			}
		}).
		fail(function() {
			login_form.children('.war_text').text('送信に失敗しました。').show();
			pop_load.fadeOut();
		});

	});

	// ログアウト
	const logout_form = $('#logout_form');
	logout_form.on('submit', function(e) {

		e.preventDefault();

		const formData = new FormData(logout_form[0]);

		logout_form.children('.war_text').text('').hide();
		pop_load.fadeIn(100);
		$('#main').scrollTop(0);

		$.ajax({
			url: './api/set-logout.php',
			type: 'post',
			dataType: 'json',
			data: formData,
			async: true,
			processData: false,
			contentType: false
		}).
		done(function(res) {
			if (res['result'] === 'success') {
				location.replace('./');
			}
			else {
				logout_form.children('.war_text').text(res['mess']).show();
				pop_load.fadeOut();
			}
		}).
		fail(function() {
			logout_form.children('.war_text').text('送信に失敗しました。').show();
			pop_load.fadeOut();
		});
	});

	// パスワードリセット
	const repass_form = $('#repass_form');
	repass_form.on('submit', function(e) {

		e.preventDefault();

		const formData = new FormData(repass_form[0]);

		repass_form.children('.war_text').text('').hide();
		pop_load.fadeIn(100);
		$('#main').scrollTop(0);

		$.ajax({
			url: './api/set-repass.php',
			type: 'post',
			dataType: 'json',
			data: formData,
			async: true,
			processData: false,
			contentType: false
		}).
		done(function(res) {
			if (res['result'] === 'success') {
				location.replace('./');
			}
			else {
				repass_form.children('.war_text').text(res['mess']).show();
				pop_load.fadeOut();
			}
		}).
		fail(function() {
			repass_form.children('.war_text').text('送信に失敗しました。').show();
			pop_load.fadeOut();
		});
	});

	// 編集モード
	// const edit_mode = $('.edit_mode').find('input[type=checkbox]');
	// if (edit_mode.length) {

	// 	const edit_butt = edit_mode.closest('form').find('button[type=submit]');
	// 	const edit_text = $('form .edit_list').find('input[readonly], textarea[readonly]');
	// 	const edit_option = $('form .edit_list').find('option[disabled]');

	// 	edit_butt.prop('disabled', ! edit_mode.is(':checked'));

	// 	edit_mode.on('change', function() {

	// 		if ($(this).prop('checked')) {
	// 			edit_butt.prop('disabled', false);
	// 			edit_text.prop('readonly', false).addClass('on');
	// 			edit_option.prop('disabled', false).closest('select').addClass('on');
	// 		}
	// 		else {
	// 			edit_butt.prop('disabled', true);
	// 			edit_text.prop('readonly', true).removeClass('on');
	// 			edit_option.prop('disabled', true).closest('select').removeClass('on');
	// 		}

	// 	});

	// }

	// 予約メール送信
	const resv_form = $('#resv_form')
	resv_form.on('submit', function(e) {

		e.preventDefault();

		const formData = new FormData(resv_form[0]);

		resv_form.children('.war_text').text('').hide();
		pop_load.fadeIn(100);
		$('#main').scrollTop(0);

		$.ajax({
			url: './api/set-resv-new.php',
			type: 'post',
			dataType: 'json',
			data: formData,
			async: true,
			processData: false,
			contentType: false
		}).
		done(function(res) {
			if (res['result'] === 'success') {
				const id = parseInt(res['data']['id']);
				location.replace('./send-mail.php?id=' + id);
			}
			else {
				resv_form.children('.war_text').text(res['mess']).show();
				pop_load.fadeOut();
			}
		}).
		fail(function() {
			resv_form.children('.war_text').text('送信に失敗しました。').show();
			pop_load.fadeOut();
		});

	});

	// 予約更新メール送信
	const resv_edit_form = $('#resv_edit_form')
	resv_edit_form.on('submit', function(e) {

		e.preventDefault();

		const formData = new FormData(resv_edit_form[0]);
		const cancel = resv_edit_form.find('[name=cancel]');

		if (cancel.is(':checked')) {
			if (! confirm('この予約をキャンセルします。よろしいですか？')) return false;
		}

		resv_edit_form.children('.war_text').text('').hide();
		pop_load.fadeIn(100);
		$('#main').scrollTop(0);

		$.ajax({
			url: './api/set-resv-edit.php',
			type: 'post',
			dataType: 'json',
			data: formData,
			async: true,
			processData: false,
			contentType: false
		}).
		done(function(res) {
			if (res['result'] === 'success') {
				setTimeout(function() {
					location.reload();
				}, 1000);
			}
			resv_edit_form.children('.war_text').text(res['mess']).show();
			pop_load.fadeOut();
		}).
		// fail(function() {
		// 	resv_edit_form.children('.war_text').text('送信に失敗しました。').show();
		// 	pop_load.fadeOut();
		// });
		fail(function(jqXHR, textStatus, errorThrown) {
			const errorMessage = jqXHR.responseJSON?.mess || '送信に失敗しました。';
			resv_edit_form.children('.war_text').text(errorMessage).show();
			pop_load.fadeOut();
			console.error('エラー詳細:', textStatus, errorThrown);
		});

	});

	// お問い合わせメール送信
	const contact_form = $('#contact_form')
	contact_form.on('submit', function(e) {

		e.preventDefault();

		const formData = new FormData(contact_form[0]);

		contact_form.children('.war_text').text('').hide();
		pop_load.fadeIn(100);
		$('#main').scrollTop(0);

		$.ajax({
			url: './api/set-contact.php',
			type: 'post',
			dataType: 'json',
			data: formData,
			async: true,
			processData: false,
			contentType: false
		}).
		done(function(res) {
			if (res['result'] === 'success') {
				location.replace('./send-mail.php');
			}
			else {
				contact_form.children('.war_text').text(res['mess']).show();
				pop_load.fadeOut();
			}
		}).
		fail(function() {
			contact_form.children('.war_text').text('送信に失敗しました。').show();
			pop_load.fadeOut();
		});

	});

	// 顧客情報
	const client_form = $('#client_form');
	client_form.on('submit', function(e) {

		e.preventDefault();

		const formData = new FormData(client_form[0]);
		const data_new = parseInt(client_form.find('[name=new]').val());

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
					location.reload();
				}, 1000);
			}

			if (res['mess']) client_form.children('.war_text').text(res['mess']).show();
			pop_load.fadeOut();

		}).
		// fail(function() {
		// 	client_form.children('.war_text').text('送信に失敗しました。').show();
		// 	pop_load.fadeOut();
		// });
		fail(function(jqXHR, textStatus, errorThrown) {
			const errorMessage = jqXHR.responseJSON?.mess || '送信に失敗しました。';
			client_form.children('.war_text').text(errorMessage).show();
			pop_load.fadeOut();
			console.error('エラー詳細:', textStatus, errorThrown);
		});

	});

	// 認証メール確認
	const client_mail_check_form = $('#client_mail_check_form');
	client_mail_check_form.on('submit', function(e) {

		e.preventDefault();

		const formData = new FormData(client_mail_check_form[0]);

		client_mail_check_form.children('.war_text').text('').hide();
		pop_load.fadeIn(100);
		$('#main').scrollTop(0);

		$.ajax({
			url: './api/set-client-mail-check.php',
			type: 'post',
			dataType: 'json',
			data: formData,
			async: true,
			contentType: false,
			processData: false,
		}).
		done(function(res) {

			if (res['result'] === 'success') {
				location.replace('./client-mail-check.php?success');
			}

			if (res['mess']) client_mail_check_form.children('.war_text').text(res['mess']).show();
			pop_load.fadeOut();

		}).
		fail(function() {
			client_mail_check_form.children('.war_text').text('送信に失敗しました。').show();
			pop_load.fadeOut();
		});

	});

	pop_load.fadeOut();

});
