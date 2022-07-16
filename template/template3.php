<div style="display: none;" class="review-post-star-ratings review-valign-bottom review-align-right" data-id="<?php echo $object->id;?>">
    <div class="review-stars">
        <div class="review-stars-inactive">
            <div class="review-star" data-star="1">
                <div class="review-icon" style="width: 25px; height: 25px;"></div>
            </div>
            <div class="review-star" data-star="2">
                <div class="review-icon" style="width: 25px; height: 25px;"></div>
            </div>
            <div class="review-star" data-star="3">
                <div class="review-icon" style="width: 25px; height: 25px;"></div>
            </div>
            <div class="review-star" data-star="4">
                <div class="review-icon" style="width: 25px; height: 25px;"></div>
            </div>
            <div class="review-star" data-star="5">
                <div class="review-icon" style="width: 25px; height: 25px;"></div>
            </div>
        </div>
        <div class="review-stars-active" style="width: 135px;">
            <div class="review-star" style="text-align:var(--star-align)!important;color:var(--star-color);font-size:13px;">
                <?php for( $i = 0; $i < 5; $i++ ) {?>
                    <span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 32 32">
                            <path fill="none" fill-rule="evenodd" stroke="#FFB500" stroke-width="1.5" d="M16 1.695l-4.204 8.518-9.401 1.366 6.802 6.631-1.605 9.363L16 23.153l8.408 4.42-1.605-9.363 6.802-6.63-9.4-1.367L16 1.695z"></path>
                        </svg>
                    </span>
                <?php } ?>
            </div>
            <div class="review-star-bg">
                <?php for( $i = 0; $i < $star; $i++ ) {?>
                    <span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 32 32">
                            <path fill="#FDD835" fill-rule="evenodd" stroke="#FFB500" stroke-width="1.5" d="M16 1.695l-4.204 8.518-9.401 1.366 6.802 6.631-1.605 9.363L16 23.153l8.408 4.42-1.605-9.363 6.802-6.63-9.4-1.367L16 1.695z"></path>
                        </svg>
                    </span>
                <?php } ?>
            </div>
        </div>
    </div>
    <style>
        .review-stars-active .review-star {
            position: relative;
            z-index: 1;
            display: flex
        }
        .review-stars-active .review-star span {
            display: inline-block;
            vertical-align: middle;
            line-height: 12px;
            height: 25px; width: 25px;
        }
        .review-stars-active svg {
            height: 25px; width: 25px;
        }
        .review-stars-active .review-star-bg {
            white-space: nowrap;
            overflow: hidden;
            position: absolute;
            top: 0px;
            left: 0px;
            display: flex
        }
    </style>
    <div class="review-legend">
        <strong class="review-score"><?php echo $star;?></strong><span class="review-muted">/</span><strong>5</strong>
        <span class="review-muted">( </span><strong class="review-count"><?php echo $count;?></strong> <span class="review-muted">bình chọn</span><span class="review-muted"> )</span>
    </div>
</div>
<script type="text/javascript" defer>
    $(function(){
        let review = false;
        $('.review-post-star-ratings .review-stars-inactive .review-star').click(function(){
            if(review === true) {
                show_message('Bạn đã đánh giá, không thể đánh giá lại', 'error');
                return false;
            }
            review = true;
            let box = $(this).closest('.review-post-star-ratings');
            let data = {
                'action'        : 'Rating_Star_Ajax::reviewSave',
                'object_type'   : '<?php echo $type;?>',
                'object_id'     : box.attr('data-id'),
                'rating'        : $(this).attr('data-star'),
            };
            $.post(ajax, data, function(){}, 'json').done(function(response){
                show_message(response.message, response.status);
            });
            return false;
        });
    });
</script>