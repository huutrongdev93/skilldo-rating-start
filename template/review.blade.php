<div class="review-comments js_review_comments" data-id="{{ $object->id }}" data-type="{{ $type }}">
    {!! Admin::loading() !!}
    <div class="review-comments_heading">
        <p class="review_heading__title">{{ trans('review.rating.title') }}</p>
        <p class="review_heading__close js_review_btn__close"><i class="fal fa-times"></i></p>
    </div>
    <div class="review-comments_sort">
        <ul>
            <li><span>{{ trans('review.rating.sort') }}:</span></li>
            <li class="star active" data-sort=""><span>{{ trans('review.rating.sort.all') }}</span></li>
            @for($i =1; $i <= 5; $i++)
            <li class="star" data-sort="{{ $i }}-star"><span>{{ $i }}</span><i class="fas fa-star" aria-hidden="true" style="color:#ccc;"></i></li>
            @endfor
        </ul>
    </div>
    <div class="review-comments_content" id="js_review_comments_list"></div>
    <div class="review-comments_pagination" id="js_review_comments_pagination"></div>
    <div class="review-comments_more"><button class="btn btn-theme btn-effect-default js_review_btn__more">{{ trans('review.rating.view.all') }}</button></div>
</div>

<div class="modal micromodal-slide rating_star_modal__reply review-form" id="js_rating_star_modal__reply" aria-hidden="true">
    <div class="modal__overlay" tabindex="-1" data-micromodal-close>
        <div class="modal__container" role="dialog" aria-modal="true" aria-labelledby="modal-1-title">
            <header class="modal__header">
                <h2 class="modal__title">{{ trans('review.rating.reply.title') }}</h2>
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
                            <textarea name="rating_star_message" class="form-control" rows="5" required placeholder="{{ trans('template.rating.message.placeholder') }}"></textarea>
                        </div>
                        @if(!Auth::check())
                        <div class="form-group col-md-6">
                            <input name="rating_star_name" value="{{ $form['name'] }}" type="text" class="form-control" placeholder="{{ trans('template.rating.name.placeholder') }}" required @if(Auth::check()) {{'readonly'}} @endif>
                        </div>
                        <div class="form-group col-md-6">
                            <input name="rating_star_email" value="{{ $form['email'] }}" type="email" class="form-control" placeholder="{{ trans('template.rating.email.placeholder') }}" required @if(Auth::check()) {{'readonly'}} @endif>
                        </div>
                        @endif
                        <div class="form-group col-md-12 mt-2">
                            <button type="submit" class="btn btn-theme btn-effect-default d-block w-100">{{ trans('general.send')  }}</button>
                        </div>
                    </div>
                </form>
            </div>
            {!! Admin::loading() !!}
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
				this.isMobile   = '{{ Device::isMobile() }}';
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


				request.post(ajax, data).then(function(response) {

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

						$('#js_review_comments_list').html(response.data.review);

						$('#js_review_comments_pagination').html(response.data.pagination);
					}
					else {
						SkilldoMessage.response(response);
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

				request.post(ajax, data).then(function(response) {});

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

				request.post(ajax, data).then(function(response) {
					loading.hide();
					SkilldoMessage.response(response);
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