$(document).ready(function () {
	$(document.body).on('click', '.js-submit-confirm', function (event) {
		event.preventDefault();
		var $form = $(this).closest('form');
		var $el = $(this);
		var text = $el.data('confirm-message') ? $el.data('confirm-message') : 'Kamu tidak bisa mengembalikan ini!';

		swal({
			title: 'Apa kamu yakin?',
			text: text,
			type: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#e35b5a',
			cancelButtonColor: '#44b6ae',
			confirmButtonText: 'Ya!'
		}).then((result) => {
			if (result.value) {
				$form.submit();
			}
		});
	});
	$(document.body).on('click', '.js-mail-confirm', function (event) {
		event.preventDefault();
		var $form = $(this).closest('form');
		var $el = $(this);
		var text = $el.data('confirm-message') ? $el.data('confirm-message') : 'Apa kamu yakin ?';

		swal({
			title: 'Konfirmasi',
			text: text,
			type: 'info',
			showCancelButton: true,
			confirmButtonColor: '#e35b5a',
			cancelButtonColor: '#44b6ae',
			confirmButtonText: 'Ya, kirim email!'
		}).then((result) => {
			if (result.value) {
				$form.submit();
			}
		});
	});
});
