<div class="review-comments js_review_comments">
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
    <div class="review-comments_content"></div>
    <div class="review-comments_pagination"></div>
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
                        <div class="form-group col-md-12">
                            <button type="submit" class="btn btn-theme btn-effect-default d-block"><?php echo __('Gửi');?></button>
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
        let page = 1;
        let sort = '';
        let object_id = '<?php echo $object->id;?>';
        let type = '<?php echo $type;?>';
        let review_id = 0;
        let liked = {};
        let isMobile = '<?php echo Device::isMobile();?>';
        function rating_star_review_load() {
            $('.review-comments .loading').show();
            $.ajaxSetup({
                beforeSend: function(xhr, settings) {
                    if (settings.data.indexOf('csrf_test_name') === -1) {
                        settings.data += '&csrf_test_name=' + encodeURIComponent(getCookie('csrf_cookie_name'));
                    }
                }
            });
            let data = {
                'action': 'Rating_Star_Ajax::reviewLoad',
                'page'  : page,
                'object_id' : object_id,
                'object_type' : type,
                'sort' : sort
            };
            $.post(ajax, data, function () { }, 'json').done(function (response) {
                $('.review-comments .loading').hide();
                if (response.status === 'success') {
                    if(isMobile == 1) {
                        if(page === 1 && response.pagination === '') {
                            $('.js_review_btn__more').hide();
                        }
                        else {
                            $('.js_review_btn__more').show();
                        }
                    }

                    $('.review-comments_content').html(response.review);
                    $('.review-comments_pagination').html(response.pagination);
                }
                else {
                    show_message(response.message, response.status);
                }
            });
        }
        rating_star_review_load();
        $(document).on('click', '.review-comments_pagination .pagination-item', function () {
            page = $(this).attr('data-page-number');
            rating_star_review_load();
            $('html,body').animate({
                scrollTop: $(".review-comments_sort").offset().top - 100
            }, 'slow');
            return false;
        });
        $(document).on('click', '.review-comments_sort ul li.star', function () {
            page = 1;
            sort = $(this).attr('data-sort');
            $('.review-comments_sort ul li.star').removeClass('active');
            $(this).addClass('active');
            rating_star_review_load();
            $('html,body').animate({
                scrollTop: $(".review-comments_sort").offset().top - 100
            }, 'slow');
            return false;
        });
        $(document).on('click', '.review-comment__thank', function() {

            let item = $(this).closest('.js_review_item');

            let total = parseInt($(this).attr('data-total')) + 1;

            review_id = item.data('id');

            if(typeof liked[review_id] != 'undefined') {
                return false;
            }
            else {
                liked[review_id] = review_id;
            }

            item.find('.review-comment__user-info .rc-like-total').html(total);

            $(this).attr('data-total', total);

            $(this).addClass('review-comment__thank--active');

            let data = {
                'action' : 'Rating_Star_Ajax::reviewLike',
                'id' : review_id
            };

            $.post(ajax, data, function () {}, 'json').done(function (response) {});

            return false;
        });
        $(document).on('click', '.review-comment__reply', function () {
            let item = $(this).closest('.js_review_item');
            review_id = item.data('id');
            $('.js_review__reply_name').html(item.data('name'));
            $('.js_review__reply_content').html(item.data('message'));
            MicroModal.show('js_rating_star_modal__reply');
        });
        $(document).on('click', '.js_review_btn__more', function () {
            $('.js_review_comments').addClass('active');
        });
        $(document).on('click', '.js_review_btn__close', function () {
            $('.js_review_comments').removeClass('active');
        });
        $(document).on('submit', '#js_review_form__reply', function(){
            let self = $(this);

            let data = self.serializeJSON();

            let loading = self.find('.loading');

            data.action = 'Rating_Star_Ajax::reviewReply';

            data.id = review_id;

            loading.show();

            $.post(ajax, data, function () { }, 'json').done(function (response) {
                loading.hide();
                show_message(response.message, response.status);
                if (response.status === 'success') {
                    MicroModal.close('js_rating_star_modal__reply');
                    self.trigger('reset');
                }
            });

            return false;
        });
    });
</script>