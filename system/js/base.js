const pop_load = $('#pop_load');

$(function() {

	const header_height = $('#header').height();

	pop_load.fadeIn(100);

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

		if (! confirm('ログアウトしますか？')) return false;

		const formData = new FormData(logout_form[0]);

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

	// const edit_mode = $('.edit_mode').find('input[type=checkbox]');
	// const edit_butt = $('.edit_mode').closest('form').find('button[type=submit]');

	// if (edit_mode.length) {

	// 	edit_butt.prop('disabled', edit_mode.prop('checked'));
	// 	const edit_text = $('form .edit_list').find('input[readonly], textarea[readonly]');
	// 	const edit_option = $('form .edit_list').find('option[disabled]');
	// 	const edit_check = $('form .edit_list').find('input[type=checkbox], input[type=radio]');
	// 	const edit_date_list = $('form .edit_list .date_list');
	// 	const edit_span_butt = $('form .edit_list .span_butt');

	// 	edit_mode.on('change', function() {
	// 		if ($(this).prop('checked')) {
	// 			edit_butt.prop('disabled', false);
	// 			edit_text.prop('readonly', false).addClass('on');
	// 			edit_option.prop('disabled', false).closest('select').addClass('on');
	// 			edit_check.prop('disabled', false);
	// 			edit_date_list.addClass('on');
	// 			edit_span_butt.prop('disabled', false);
	// 		}
	// 		else {
	// 			edit_butt.prop('disabled', true);
	// 			edit_text.prop('readonly', true).removeClass('on');
	// 			edit_option.prop('disabled', true).closest('select').removeClass('on');
	// 			edit_check.prop('disabled', true);
	// 			edit_date_list.removeClass('on');
	// 			edit_span_butt.prop('disabled', true);
	// 		}
	// 	}).triggerHandler('change');

	// }
	// else {
	// 	edit_butt.prop('disabled', true);
	// }

	$(window).on('pageshow', function() {
		$('.list_tbl').on("scroll", function () {
			const scrollLeft = $(this).scrollLeft();
			if (scrollLeft > 0) {
				if (! $(this).hasClass('scroll')) $(this).addClass('scroll');
			}
			else {
				$(this).removeClass('scroll');
			}
		});
	});

	pop_load.fadeOut();

});
