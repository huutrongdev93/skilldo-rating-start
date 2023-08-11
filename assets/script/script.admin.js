$(function () {
	$(document).on('click', '.js_btn_product_add_review', function () {

		let changeProductList = []; let i = 0;

		$('.select:checked').each(function () { changeProductList[i++] = $(this).val(); });

		if(typeof changeProductList == 'undefined' || changeProductList.length === 0) {
			show_message('Bạn chưa chọn sản phẩm nào', 'error');
			return false;
		}

		let button = $(this);

		button.attr('disable', true);

		button.html('<i class="fa-duotone fa-spinner-third fa-spin"></i> Đang tạo đánh giá...');

		let data = {
			'action': 'Rating_Star_Admin_Ajax::randomReview',
			'data'  : changeProductList,
		};

		$.post(ajax, data, function () {}, 'json').done(function (data) {
			show_message(data.message, data.status);
			if (data.status === 'success') {
				location.reload();
			}
		});

		return false;
	})
})