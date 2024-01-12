<div class="review-comments js_review_comments" data-id="<?php echo $object->id;?>" data-type="<?php echo $type;?>">
    <?php echo Admin::loading();?>
    <div class="review-comments_heading">
        <p class="review_heading__title">ĐÁNH GIÁ SẢN PHẨM</p>
        <p class="review_heading__close js_review_btn__close"><i class="fal fa-times"></i></p>
    </div>
    <div class="review-comments_sort">
        <ul>
            <li><span><?php echo __('Lọc theo', 'rating_sort_label');?>:</span></li>
            <li class="star active" data-sort=""><span><?php echo __('Tất cả', 'rating_sort_all');?></span></li>
            <?php for($i =1; $i <= 5; $i++) {?>
            <li class="star" data-sort="<?php echo $i;?>-star"><span><?php echo $i;?></span><i class="fas fa-star" aria-hidden="true" style="color:#ccc;"></i></li>
            <?php } ?>
        </ul>
    </div>
    <div class="review-comments_content" id="js_review_comments_list"></div>
    <div class="review-comments_pagination" id="js_review_comments_pagination"></div>
    <div class="review-comments_more"><button class="btn btn-theme btn-effect-default js_review_btn__more">Xem tất cả đánh giá</button></div>
</div>

<div class="modal micromodal-slide rating_star_modal__reply review-form" id="js_rating_star_modal__reply" aria-hidden="true">
    <div class="modal__overlay" tabindex="-1" data-micromodal-close>
        <div class="modal__container" role="dialog" aria-modal="true" aria-labelledby="modal-1-title">
            <header class="modal__header">
                <h2 class="modal__title">Phản hồi đánh giá</h2>
                <button class="modal__close" aria-label="Close modal" data-micromodal-close></button>
            </header>
            <div class="modal__content js_rating_star_modal__reply">
                <div class="write-review__reply--to">
                    <div class="review-comment__user-name js_review__reply_name"></div>
                    <div class="review-comment__content js_review__reply_content"></div>
                </div>
                <form method="post" id="js_review_form__reply" autocomplete="off">
                    <div class="row">
                        <div class="form-group col-md-12">
                            <textarea name="rating_star_message" class="form-control" rows="5" required placeholder="<?php echo __('Hãy chia sẻ những điều bạn thích về sản phẩm này nhé', 'rating_placeholder_message');?>"></textarea>
                        </div>
                        <?php if(!Auth::check()) {?>
                        <div class="form-group col-md-6">
                            <input name="rating_star_name" value="<?php echo $form['name'];?>" type="text" class="form-control" placeholder="<?php echo __('Họ tên của bạn', 'rating_placeholder_name');?>" required <?php if(Auth::check()) echo 'readonly';?>>
                        </div>
                        <div class="form-group col-md-6">
                            <input name="rating_star_email" value="<?php echo $form['email'];?>" type="email" class="form-control" placeholder="<?php echo __('Email của bạn', 'rating_placeholder_email');?>" required <?php if(Auth::check()) echo 'readonly';?>>
                        </div>
                        <?php } ?>
                        <div class="form-group col-md-12 mt-2">
                            <button type="submit" class="btn btn-theme btn-effect-default d-block w-100"><?php echo __('Gửi');?></button>
                        </div>
                    </div>
                </form>
            </div>
            <?php echo Admin::loading();?>
        </div>
    </div>
</div>

<script defer>
    $(function(){
		class RatingStartReviewHandle {
			constructor() {
				this.page       = 1;
				this.sort       = null;
				this.reviewDiv  = $('.js_review_comments')
				this.objectId   = this.reviewDiv.data('id')
				this.type       = this.reviewDiv.data('type')
				this.loading    = this.reviewDiv.find('.loading')
				this.reviewId   = 0
				this.liked       = {}
				this.isMobile   = '<?php echo Device::isMobile();?>';
				this.load();
			}
			load() {
				let self = this;
				this.loading.show();
				$.ajaxSetup({
					beforeSend: function(xhr, settings) {
						if (settings.data.indexOf('csrf_test_name') === -1) {
							settings.data += '&csrf_test_name=' + encodeURIComponent(getCookie('csrf_cookie_name'));
						}
					}
				});
				let data = {
					'action'        : 'Rating_Star_Ajax::reviewLoad',
					'page'          : this.page,
					'object_id'     : this.objectId,
					'object_type'   : this.type,
					'sort'          : this.sort
				};
				$.post(ajax, data, function () {}, 'json').done(function (response) {

					self.loading.hide();

					if (response.status === 'success') {

						if(self.isMobile == 1) {
							if(self.page === 1 && response.pagination === '') {
								$('.js_review_btn__more').hide();
							}
							else {
								$('.js_review_btn__more').show();
							}
						}

						$('#js_review_comments_list').html(response.review);

						$('#js_review_comments_pagination').html(response.pagination);
					}
					else {
						show_message(response.message, response.status);
					}
				});
			}
			pagination(element) {
				this.page = element.attr('data-page-number');
				this.load();
				$('html,body').animate({
					scrollTop: $(".review-comments_sort").offset().top - 100
				}, 'slow');
				return false;
			}
			sortComment(element) {
				this.page = 1
				this.sort = element.attr('data-sort');
				$('.review-comments_sort ul li.star').removeClass('active');
				element.addClass('active');
				this.load();
				$('html,body').animate({
					scrollTop: $(".review-comments_sort").offset().top - 100
				}, 'slow');
				return false;
			}
			like(element) {

				let item = element.closest('.js_review_item');

				let total = parseInt(element.attr('data-total')) + 1;

				this.reviewId = item.data('id');

				if(typeof this.liked[this.reviewId] != 'undefined') {
					return false;
				}
				else {
					this.liked[this.reviewId] = this.reviewId;
				}

				item.find('.js_like_total').html(total);

				element.attr('data-total', total);

				element.addClass('rvc_like--active');

				let data = {
					'action' : 'Rating_Star_Ajax::reviewLike',
					'id' : this.reviewId
				};

				$.post(ajax, data, function () {}, 'json').done(function (response) {});

				return false;
			}
			clickReply(element) {
				let item = element.closest('.js_review_item');
				this.reviewId = item.data('id');
				$('.js_review__reply_name').html(item.data('name'));
				$('.js_review__reply_content').html(item.data('message'));
				MicroModal.show('js_rating_star_modal__reply');
			}
			reply(element) {

				let data = element.serializeJSON();

				let loading = element.find('.loading');

				data.action = 'Rating_Star_Ajax::reviewReply';

				data.id = this.reviewId;

				loading.show();

				$.post(ajax, data, function () {}, 'json').done(function (response) {
					loading.hide();
					show_message(response.message, response.status);
					if (response.status === 'success') {
						MicroModal.close('js_rating_star_modal__reply');
						element.trigger('reset');
					}
				});

				return false;
			}
		}

		const ratingStartReview = new RatingStartReviewHandle();

	    $(document)
		    .on('click', '#js_review_comments_pagination .pagination-item', function () {
			    return ratingStartReview.pagination($(this))
		    })
		    .on('click', '.review-comments_sort ul li.star', function () {
			    return ratingStartReview.sortComment($(this))
		    })
		    .on('click', '.js_rvc_btn_like', function () {
			    return ratingStartReview.like($(this))
		    })
		    .on('click', '.js_rvc_btn_reply', function () {
			    return ratingStartReview.clickReply($(this))
            })
		    .on('click', '.js_review_btn__more', function () {
			    ratingStartReview.reviewDiv.addClass('active');
		    })
		    .on('click', '.js_review_btn__close', function () {
			    ratingStartReview.reviewDiv.removeClass('active');
		    })
		    .on('submit', '#js_review_form__reply', function () {
			    return ratingStartReview.reply($(this))
		    })
    });
</script>