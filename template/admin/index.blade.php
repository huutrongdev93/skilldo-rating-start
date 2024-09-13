<div class="col-md-12">
    <div class="ui-title-bar__group">
        <h1 class="ui-title-bar__title">Danh sách đánh giá</h1>
        <p style="margin-top: 10px; margin-left: 1px; color: #8c8c8c">Đánh giá sản phẩm, bài viết</p>
    </div>
    <div class="box">
        <div class="box-heading">
            <div class="box-heading-left"></div>
            <div class="box-heading-right">
                <form class="search-box d-flex gap-2 align-items-center" id="js_rating_star_form_search">
	                <div class="form-group mb-0">
		                <select name="type" class="form-control">
			                <option value="">Tất cả</option>
			                @foreach (rating_star::module() as $item)
				                <option value="{{ $item['key']}}">{{ $item['name']}}</option>
			                @endforeach
			                <option value="auto">Đánh giá tự động</option>
		                </select>
	                </div>
                    <div class="form-group mb-0">
                        <select name="star" class="form-control">
                            <option value="0">Tất cả review</option>
                            <option value="1">1 Sao</option>
                            <option value="2">2 Sao</option>
                            <option value="3">3 Sao</option>
                            <option value="4">4 Sao</option>
                            <option value="5">5 Sao</option>
                        </select>
                    </div>
                    <button type="submit" class="btn"><i class="fad fa-search"></i></button>
                </form>
            </div>
        </div>
        <!-- .box-content -->
        <div class="box-content">
	        <div class="tabs" id="js_rating_star_tabs_status">
		        <button class="btn-tab tab-item js_rating_star_tab_status p-2 active" type="button" data-status="">Tất cả</button>
		        <button class="btn-tab tab-item js_rating_star_tab_status p-2 ms-3" type="button" data-status="pending">Chưa duyệt</button>
		        <button class="btn-tab tab-item js_rating_star_tab_status p-2 ms-3" type="button" data-status="public">Đã duyệt</button>
		        <button class="btn-tab tab-item js_rating_star_tab_status p-2 ms-3" type="button" data-status="hidden">Tạm ẩn</button>
	        </div>
	        <div class="table-responsive table-">
	            <table class="display table table-rating table-striped media-table" id="js_rating_star_table_result">
	                <thead>
	                    <tr>
	                        <th class="manage-column">Đánh giá</th>
	                        <th class="manage-column text-center">Điểm</th>
	                        <th class="manage-column">Trạng thái</th>
	                        <th class="manage-column">Hình ảnh</th>
	                        <th class="manage-column">Trả lời</th>
	                        <th class="manage-column text-end">#</th>
	                    </tr>
	                </thead>
	                <tbody></tbody>
	            </table>
	        </div>
            <div class="paging">
                <div class="pull-left" style="padding-top:20px;"></div>
                <div class="pull-right" id="js_rating_star_pagination"></div>
            </div>
        </div>
        <!-- /.box-content -->
    </div>
</div>

<div class="modal fade" id="js_rating_star_reply_modal">
    <div class="modal-dialog modal-lg modal-dialog-centered">
	    <div class="modal-content p-3">
		    <div class="row">
			    <div class="col-md-8">
				    <p class="heading">Trả lời cho đánh giá</p>
				    <div id="js_rating_star_review"></div>
				    <div id="js_rating_star_reply_list"></div>
			    </div>
			    <div class="col-md-4">
				    <p class="heading js_rating_star_reply_form_heading">Tạo trả lời mới</p>
				    <form action="" id="js_rating_star_reply_form">
					    <div class="form-group">
						    <label for="">Tên người trả lời</label>
						    <input name="comment[name]" value="" class="js_reply_input_name form-control" required>
					    </div>
					    <div class="form-group">
						    <label for="">Email</label>
						    <input name="comment[email]" value="" class="js_reply_input_email form-control" required>
					    </div>
					    <div class="form-group">
						    <label for="">Câu trả lời</label>
						    <textarea name="comment[content]" cols="40" rows="5" class="js_reply_input_content form-control" required></textarea>
					    </div>
					    <div class="form-group">
						    <button class="btn btn-blue btn-block w-100 m-0 js_reply_submit">Tạo trả lời</button>
						    <p class="created-reply text-center mt-2 js_reply_btn_back_to_add" style="display: none">Tạo câu trả lời</p>
					    </div>
				    </form>
			    </div>
		    </div>
	    </div>

    </div><!-- /.modal-dialog -->
</div>

<div class="modal fade" id="js_rating_star_edit_modal">
	<div class="modal-dialog">
		<div class="modal-content p-3">
			<p class="heading js_rating_star_reply_form_heading">Chỉnh sửa đánh giá</p>
			<form action="" id="js_rating_star_edit_form">
				<div class="form-group">
					<label for="">Tên người đánh giá</label>
					<input name="review[name]" value="" class="js_review_input_name form-control" required>
				</div>
				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label for="">Email</label>
							<input name="review[email]" value="" class="js_review_input_email form-control" required>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label for="">Số điện thoại</label>
							<input name="review[phone]" value="" class="js_review_input_phone form-control">
						</div>
					</div>
				</div>

				<div class="form-group">
					<label for="" class="mb-2">Đánh giá</label>
					<div class="d-flex gap-2">
						<label class="mb-2 w-100 form-check" style="font-weight: normal;">
							<input type="radio" name="review[star]" value="1" class="form-check-input js_review_input_star"> 1 Sao
						</label>
						<label class="mb-2 w-100 form-check" style="font-weight: normal;">
							<input type="radio" name="review[star]" value="2" class="form-check-input js_review_input_star"> 2 Sao
						</label>
						<label class="mb-2 w-100 form-check" style="font-weight: normal;">
							<input type="radio" name="review[star]" value="3" class="form-check-input js_review_input_star"> 3 Sao
						</label>
						<label class="mb-2 w-100 form-check" style="font-weight: normal;">
							<input type="radio" name="review[star]" value="4" class="form-check-input js_review_input_star"> 4 Sao
						</label>
						<label class="mb-2 w-100 form-check" style="font-weight: normal;">
							<input type="radio" name="review[star]" value="5" class="form-check-input js_review_input_star"> 5 Sao
						</label>
					</div>
				</div>

				<div class="form-group">
					<label for="" class="mb-2">Trạng thái</label>
					<div class="">
						<label class="mb-2 w-100 form-check" style="font-weight: normal;">
							<input type="radio" name="review[status]" value="pending" class="form-check-input js_review_input_status"> Đợi duyệt
						</label>
						<label class="mb-2 w-100 form-check" style="font-weight: normal;">
							<input type="radio" name="review[status]" value="public" class="form-check-input js_review_input_status"> Hiển thị
						</label>
						<label class="mb-2 w-100 form-check" style="font-weight: normal;">
							<input type="radio" name="review[status]" value="hidden" class="form-check-input js_review_input_status"> Tạm ẩn
						</label>
					</div>
				</div>

				<div class="form-group">
					<label for="">Nội dung đánh giá</label>
					<textarea name="review[content]" cols="40" rows="5" class="js_review_input_content form-control" required></textarea>
				</div>
				<div class="form-group">
					<button class="btn btn-blue btn-block w-100 m-0">Cập nhật đánh giá</button>
				</div>
			</form>
		</div>
	</div>
</div>

<div class="modal fade" id="js_rating_star_modal_confirm">
	<div class="modal-dialog modal-dialog-centered" style="max-width:420px;">
		<div class="modal-content">
			<div class="modal-body">
				<p class="js_rating_star_modal_confirm__heading" style="font-weight: bold; font-size: 20px;">Xóa dữ liệu</p>
				<p class="js_rating_star_modal_confirm__description mb-0">Bạn muốn xóa trường dữ liệu này ?</p>
			</div>
			<div class="modal-footer" style="border: none;">
				<div style="display: flex;align-items:center; justify-content:space-between;">
					<button class="btn btn-white" data-bs-dismiss="modal"><i class="fal fa-times"></i> Đóng</button>
					<button id="js_rating_star_modal_confirm_btn__save" class="btn btn-red" type="button">Đồng ý</button>
				</div>
			</div>
		</div>
	</div>
</div>

<style>
    .rating-star-item .attach-images {
        display: flex; gap:2px;
    }
    .rating-star-item .attach-images .images {
        border-radius: 5px;
	    height: 40px;
        overflow: hidden;
    }
    .rating-star-item .attach-images img {
        width: 100%; height: 100%; object-fit: cover;
    }
</style>

<script id="reply_item_template" type="text/x-custom-template">
	<div class="js_reply_item reply-item d-flex gap-2 mt-2 ${isActive}">
		<div class="reply-avatar">
			<div class="avatar"><span>${avatar}</span></div>
		</div>
		<div class="reply-content">
			<div class="reply-user d-flex gap-3">
				<span class="name">${name}</span>
				<span class="time">${created}</span>
			</div>
			<p class="reply-comment mt-1 mb-1">${message}</p>
			<div class="reply-action">
				<button class="btn btn-blue btn-blue-bg p-1 ps-2 pe-2 js_reply_btn_edit" data-id="${id}">{!! Admin::icon('edit') !!}</button>
				<button class="btn btn-red btn-red-bg p-1 ps-2 pe-2 js_reply_btn_delete" data-id="${id}">{!! Admin::icon('delete') !!}</button>
			</div>
		</div>
	</div>
</script>

<script type="text/javascript">
    $(function(){
	    class RatingStarTableHandle {

		    constructor() {
			    this.reviewModalHandle = new bootstrap.Modal('#js_rating_star_edit_modal')
			    this.reviewModal = $('#js_rating_star_edit_modal')

				this.id     = 0;
				this.item   = undefined;
			    this.replyModalHandle = new bootstrap.Modal('#js_rating_star_reply_modal')
			    this.replyModal = $('#js_rating_star_reply_modal')
			    this.replyList  = {
				    items : [],
				    get(id) {
					    let objIndex = this.items.findIndex((item => item.id == id*1));
					    if(objIndex == -1) return null;
					    return this.items[objIndex]
				    },
				    add(object) {
					    let objIndex = this.items.findIndex((item => item.id == object.id));
					    if(objIndex == -1) {
						    this.items.unshift(object);
					    }
					    return this.items;
				    },
				    update(object) {
					    let objIndex = this.items.findIndex((item => item.id == object.id));
					    this.items[objIndex] = {...this.items[objIndex], ...object};
					    return this.items;
				    },
				    delete(id) {
					    id = id*1
					    this.items = this.items.filter(function(item) {
						    return item.id !== id
					    })
				    },
			    };
				this.replyId    = 0;

			    this.modalConfirmHandle  = new bootstrap.Modal('#js_rating_star_modal_confirm')
			    this.modalConfirm        = $('#js_rating_star_modal_confirm');
			    this.modalConfirmBtn     = $('#js_rating_star_modal_confirm_btn__save');
			    this.modalConfirmHeading = '';
			    this.modalConfirmDes     = '';
			    this.modalAction        = null;
			    this.btnClick        = null;

			    this.searchForm = $('#js_rating_star_form_search');
			    this.searchData = {};
			    this.pagingNumber   = { limit: 20, page: 1 }
			    this.status         = $('#js_rating_star_tabs_status .active').attr('data-status');
			    this.table          = $('#js_rating_star_table_result');
			    this.pagination     = $('#js_rating_star_pagination');
			    this.load();
		    }

		    load(element) {

			    $('.loading').show();

			    this.table.find('tbody').html('');

			    let data = {
				    'action'    : 'Rating_Star_Admin_Ajax::load',
				    'limit'     : this.pagingNumber.limit,
				    'page'      : this.pagingNumber.page,
				    'status'    : this.status,
				    ...this.searchData
			    }

			    let self = this;

			    request.post(ajax, data).then(function(response) {

				    $('.loading').hide();

				    if (response.status === 'error') SkilldoMessage.response(response);

				    if (response.status === 'success') {

					    self.pagingNumber.page++;

					    response.data.items = decodeURIComponent(atob(response.data.items).split('').map(function (c) {
						    return '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2);
					    }).join(''));

					    response.data.pagination = decodeURIComponent(atob(response.data.pagination).split('').map(function (c) {
						    return '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2);
					    }).join(''));

					    self.table.find('tbody').html(response.data.items);

					    self.pagination.html(response.data.pagination);
				    }
			    });
		    }

		    clickSearch(element) {
			    this.searchData = element.serializeJSON();
			    this.pagingNumber.page = 1;
			    this.load();
			    return false;
		    }

		    clickPagination(element) {

			    this.pagingNumber.page = element.data('page-number');

			    this.load();

			    return false;
		    }

		    statusLoad(element) {

			    $('.tab-item').removeClass('active');

			    element.closest('.tab-item').addClass('active');

			    this.status = element.attr('data-status');

			    this.pagingNumber.page = 1;

			    this.load();

			    return false;
		    }

			showReply(element) {

				let self        = this;

				this.id   = $(element).attr('data-id');

				this.item = $(element).closest('.js_rating_star_table_item');

				this.replyModal.find('#js_rating_star_review').html(this.item.find('.js_rating_star_table_review').html());

				this.replyModal.find('#js_rating_star_reply_list').html('');

				this.replyModalHandle.show();

				this.setModalReplyAdd();

				let data = {
					action : 'Rating_Star_Admin_Ajax::commentLoad',
					id     : this.id
				};

				self.replyList.items = [];

				request.post(ajax, data).then(function(response) {
					if(response.status === 'success') {
						for (const [key, obj] of Object.entries(response.data.items)) {
							self.replyList.add({
								id      : obj.id,
								name    : obj.name,
								email   : obj.email,
								message : obj.message,
							});

							let items = [obj];

							self.replyModal.find('#js_rating_star_reply_list').append(items.map(function(item) {
								return $('#reply_item_template').html().split(/\$\{(.+?)\}/g).map(render(item)).join('');
							}));
						}
					}
				});

				return false;
			}

			saveReply(element) {
				if(this.replyId == 0) {
					return this.addReply(element)
				}
				return this.editReply(element)
			}

			addReply(element) {

				let self        = this;

				let loader = SkilldoUtil.buttonLoading(element.find('button.js_reply_submit'))

				let data          = element.serializeJSON();

				data.parentId     = this.id;

				data.action       = 'Rating_Star_Admin_Ajax::commentAdd';

				loader.start()

				request.post(ajax, data).then(function(response) {

					SkilldoMessage.response(response);

					loader.stop()

					if(response.status === 'success') {

						self.replyList.add({
							id      : response.data.item.id,
							name    : response.data.item.name,
							email   : response.data.item.email,
							message : response.data.item.message,
						});

						let items = [response.data.item];

						self.replyModal.find('#js_rating_star_reply_list').append(items.map(function(item) {
							return $('#reply_item_template').html().split(/\$\{(.+?)\}/g).map(render(item)).join('');
						}));

						$('#js_rating_star_reply_form').trigger('reset');
					}
				});

				return false;
			}

		    editReply(element) {

			    let self        = this;

				let loader = SkilldoUtil.buttonLoading(element.find('button.js_reply_submit'))

			    let data          = element.serializeJSON();

			    data.parentId     = this.id;

			    data.id           = this.replyId;

			    data.action       = 'Rating_Star_Admin_Ajax::commentEdit';

				loader.start()

			    request.post(ajax, data).then(function(response) {

					loader.stop()

				    SkilldoMessage.response(response);

				    if(response.status === 'success') {

					    self.replyList.update({
						    id      : response.data.item.id,
						    name    : response.data.item.name,
						    email   : response.data.item.email,
						    message : response.data.item.message,
					    });

						self.item.replaceWith($('#reply_item_template').html().split(/\$\{(.+?)\}/g).map(render(response.data.item)).join(''))

					    self.item = self.replyModal.find('.js_reply_item.active');
					}
			    });

			    return false;
		    }

		    deleteReply(element) {

				let loader = SkilldoUtil.buttonLoading(element)

			    let self = this;

			    let data = {
				    action : 'Rating_Star_Admin_Ajax::commentDelete',
				    id     : this.replyId
			    };

				loader.start()

			    request.post(ajax, data).then(function(response) {

					loader.stop()

				    SkilldoMessage.response(response);

				    if(response.status === 'success') {

					    self.replyList.delete(self.replyId);

					    self.btnClick.closest('.js_reply_item').remove();

					    self.setModalReplyAdd();

						self.modalConfirmHandle.hide();
				    }
			    });

			    return false;
		    }

			clickEditReply(element) {

				let self  = this;

				this.replyId = $(element).attr('data-id');

				this.item    = $(element).closest('.js_reply_item');

				let reply = this.replyList.get(this.replyId);

				if(reply == null) {
					SkilldoMessage.error('Không tìm thấy thông tin trả lời');
					return false;
				}

				this.replyModal.find('.js_rating_star_reply_form_heading').html('Chỉnh sửa trả lời')
				this.replyModal.find('.js_reply_input_name').val(reply.name)
				this.replyModal.find('.js_reply_input_email').val(reply.email)
				this.replyModal.find('.js_reply_input_content').val(reply.message)
				this.replyModal.find('.js_reply_submit').html('Cập nhật câu trả lời')
				this.replyModal.find('.js_reply_btn_back_to_add').show();

				$('.js_reply_item').removeClass('active');

				this.item.addClass('active');

				return false;
			}

			setModalReplyAdd(element) {
				this.replyId = 0;
				this.replyModal.find('.js_rating_star_reply_form_heading').html('Tạo trả lời mới')
				this.replyModal.find('.js_reply_input_name').val('')
				this.replyModal.find('.js_reply_input_email').val('')
				this.replyModal.find('.js_reply_input_content').val('')
				this.replyModal.find('.js_reply_submit').html('Tạo câu trả lời')
				this.replyModal.find('.js_reply_btn_back_to_add').hide();
				$('.js_reply_item').removeClass('active');
			}

		    modalConfirmShow() {
			    this.modalConfirm.find('.js_rating_star_modal_confirm__heading').html(this.modalConfirmHeading)
			    this.modalConfirm.find('.js_rating_star_modal_confirm__description').html(this.modalConfirmDes)
			    this.modalConfirmHandle.show();
			    this.modalConfirmBtn.removeAttr('readonly');
			    this.modalConfirmBtn.removeAttr('disabled');
			    this.modalConfirmBtn.html('Xác nhận');
		    }

		    clickDeleteReply(element) {
			    this.modalConfirmHeading = 'Xác nhận xoá';
			    this.modalConfirmDes     = 'Bạn có chắc chắn muốn xoá. Điều này là không thể khôi phục.';
			    this.modalAction         = 'deleteReply';
			    this.replyId = element.attr('data-id');
			    this.btnClick = element;
			    this.modalConfirmShow();
				return false;
		    }

		    clickModalConfirm(element) {
			    if(this.modalAction == 'deleteReply') {
				    return this.deleteReply(element);
			    }
			    if(this.modalAction == 'delete') {
				    return this.delete(element);
			    }
			    return false;
		    }

			clickPublic(element) {

				let self = this;

				this.id  = element.attr('data-id');

				this.item = element.closest('.js_rating_star_table_item');

				let data = {
					action : 'Rating_Star_Admin_Ajax::status',
					id     : this.id,
					status :'public',
				};

				request.post(ajax, data).then(function(response) {
					if(response.status == 'success') {
						let itemData = JSON.parse(self.item.attr('data-item'));
						itemData.status = 'public';
						self.item.attr('data-item', JSON.stringify(itemData));
						self.item.find('.js_column_status').html(response.data.status);
						element.remove();
					}
				});

				return false;
			}

			clickEdit(element) {

				this.id = element.attr('data-id');

				this.item = element.closest('.js_rating_star_table_item');

				let review = JSON.parse(this.item.attr('data-item'));

				this.reviewModal.find('.js_review_input_name').val(review.name);

				this.reviewModal.find('.js_review_input_email').val(review.email);

				this.reviewModal.find('.js_review_input_phone').val(review.phone);

				this.reviewModal.find('.js_review_input_content').val(review.message);

				this.reviewModal.find('.js_review_input_star[value="'+review.star+'"]').prop('checked', true);

				this.reviewModal.find('.js_review_input_status[value="'+review.status+'"]').prop('checked', true);

				this.reviewModalHandle.show();

				return false;
			}

		    clickDelete(element) {
			    this.modalConfirmHeading = 'Xác nhận xoá';
			    this.modalConfirmDes     = 'Bạn có chắc chắn muốn xoá. Điều này là không thể khôi phục.';
			    this.modalAction         = 'delete';
			    this.id = element.attr('data-id');
			    this.item = element.closest('.js_rating_star_table_item');
			    this.btnClick = element;
			    this.modalConfirmShow();
			    return false;
		    }

			save(element) {
				let self    = this;

				let data    = element.serializeJSON();

				data.id     = this.id;

				data.action       = 'Rating_Star_Admin_Ajax::save';

				request.post(ajax, data).then(function(response) {

					SkilldoMessage.response(response);

					if(response.status === 'success') {
						self.item.replaceWith(response.data.item)
						self.reviewModalHandle.hide()
					}
				});

				return false;
			}

		    delete(element) {

				let loader = SkilldoUtil.buttonLoading(element)

			    let self    = this;

			    let data    = {
				    action  : 'Rating_Star_Admin_Ajax::delete',
				    id      : this.id,
			    }

				loader.start()

			    request.post(ajax, data).then(function(response) {

					loader.stop()

				    SkilldoMessage.response(response);

				    if(response.status === 'success') {
					    self.item.remove();
					    self.reviewModalHandle.hide()
						self.modalConfirmHandle.hide();
				    }
			    });

			    return false;
		    }
	    }

	    let table = new RatingStarTableHandle();

	    $(document)
		    .on('click', '#js_rating_star_pagination .pagination-item', function () {
			    return table.clickPagination($(this))
		    })
		    .on('submit', '#js_rating_star_form_search', function () {
			    return table.clickSearch($(this))
		    })
		    .on('click', '.js_rating_star_tab_status', function () {
				return table.statusLoad($(this))
			})
		    .on('click', '.js_rating_star_btn_reply', function () {
			    return table.showReply($(this))
		    })
		    .on('click', '.js_rating_star_btn_status', function () {
			    return table.clickPublic($(this))
		    })
		    .on('click', '.js_rating_star_btn_edit', function () {
			    return table.clickEdit($(this))
		    })
		    .on('click', '.js_rating_star_btn_delete', function () {
			    return table.clickDelete($(this))
		    })
		    .on('submit', '#js_rating_star_reply_form', function () {
			    return table.saveReply($(this))
		    })
		    .on('click', '.js_reply_btn_edit', function () {
			    return table.clickEditReply($(this))
		    })
		    .on('click', '.js_reply_btn_delete', function () {
			    return table.clickDeleteReply($(this))
		    })
		    .on('click', '.js_reply_btn_back_to_add', function () {
			    return table.setModalReplyAdd($(this))
		    })
		    .on('click', '#js_rating_star_modal_confirm_btn__save', function () {
			    return table.clickModalConfirm($(this))
		    })
		    .on('submit', '#js_rating_star_edit_form', function () {
			    return table.save($(this))
		    })

    })
</script>