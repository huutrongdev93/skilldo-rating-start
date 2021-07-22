<div class="review-item js_review_item" data-id="<?php echo $review->id;?>" data-name="<?php echo $review->name;?>" data-message="<?php echo $review->message;?>">
    <div class="review-comment">
        <div class="review-comment__user">
            <div class="review-comment__user-inner">
                <div class="review-comment__user-avatar">
                    <div class="avatar" data-name="<?php echo $review->name;?>"><span><?php echo rating_star::getKeyName($review->name);?></span></div>
                </div>
                <div>
                    <div class="review-comment__user-name"><?php echo $review->name;?></div>
                    <div class="review-comment__user-date"><?php echo $review->email;?></div>
                </div>
            </div>
            <div class="review-comment__user-info"><img src="https://salt.tikicdn.com/ts/upload/84/41/b2/8c371b639b0d5f511b44bc20e9051210.png"> Đã nhận:&nbsp; <span class="rc-like-total"><?php echo $review->like;?></span> &nbsp;Lượt thích</div>
        </div>
        <div style="flex-grow: 1;">
            <div class="review-comment__rating-title">
                <div class="review-comment__rating">
                    <?php Rating_star_product::template(1,$review->star, rating_star::config('color.star.detail'));?>
                </div>
                <a class="review-comment__title" href="javascript:void(0)"><?php echo rating_star::starLabel($review->star);?></a>
            </div>
            <div class="review-comment__seller-name-attributes">
                <div class="review-comment__seller-name">Đã mua hàng <i class="fal fa-check"></i> từ shop</div>
            </div>
            <div class="review-comment__content"><?php echo $review->message;?></div>
            <div class="review-comment__images">
                <?php
                $files = Metadata::get('rating_star', $review->id, 'attach', true);
                if(have_posts($files)) {
                    ?>
                    <div class="attach-images">
                        <?php foreach ($files as $path => $item) { ?>
                            <div class="images"><a href="<?php echo Url::base($path);?>" data-fancybox="group"><?php Template::img(Url::base($path));?></a></div>
                        <?php } ?>
                    </div>
                    <?php
                }
                ?>
            </div>
            <div class="review-comment__created-date">
                <span>Nhận xét vào <?php echo rating_star::timeElapsed($review->created);?> trước</span>
            </div>
            <span data-view-id="pdp_product_review_like_button" class="review-comment__thank" data-id="<?php echo $review->id;?>" data-total="<?php echo $review->like;?>">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20">
                    <g fill="none" fill-rule="evenodd">
                        <path d="M0 0H20V20H0z"></path>
                        <path fill="#0d5cb6" fill-rule="nonzero" d="M14.252 17.063c.465 0 .863-.056 1.195-.167.443-.143.8-.387 1.071-.73.271-.343.429-.747.473-1.212.022-.254.006-.503-.05-.747.277-.443.404-.908.382-1.395-.01-.132-.038-.265-.083-.398.266-.398.393-.819.382-1.262 0-.166-.028-.332-.083-.498.155-.232.266-.481.332-.747l.067-.083v-.73l-.034-.083v-.05c-.022-.033-.033-.055-.033-.066-.166-.642-.531-1.069-1.096-1.279-.265-.088-.542-.133-.83-.133h-2.888c.044-.298.083-.525.116-.68.144-.742.116-1.4-.083-1.976-.078-.221-.21-.586-.399-1.096l-.149-.398c-.177-.443-.476-.753-.896-.93-.321-.144-.648-.216-.98-.216-.376 0-.742.095-1.096.283-.564.287-.84.747-.83 1.378.011.254.017.453.017.597.01.454.022.797.033 1.03 0 .055-.011.105-.033.149-.033.066-.091.172-.174.315l-.191.332c-.388.676-.681 1.174-.88 1.495-.133.199-.313.365-.54.498-.227.132-.423.215-.59.249l-.248.05H4.258c-.332 0-.614.116-.847.348-.232.233-.349.515-.349.847v6.11c0 .331.117.613.35.846.232.232.514.349.846.349h9.994zm0-1.196h-6.94l.017-6.441c.51-.244.908-.587 1.195-1.03V8.38c.21-.332.504-.836.88-1.51l.017-.017c.022-.034.1-.166.232-.399.011-.011.034-.044.067-.1.033-.055.055-.094.066-.116.155-.265.221-.548.2-.846-.012-.244-.023-.56-.034-.947v-.63c-.01-.067 0-.122.033-.167.022-.044.072-.088.15-.132.177-.089.354-.133.531-.133.166 0 .338.039.515.116.11.044.193.127.249.249.077.232.127.365.15.398.165.454.292.808.38 1.063.134.387.145.841.034 1.361-.033.188-.072.426-.116.714l-.232 1.395h4.3c.143 0 .287.022.431.066.166.067.277.216.332.448.011 0 .02.011.025.034.005.022.008.038.008.05v.232l-.033.133c-.033.121-.083.238-.15.348l-.315.465.15.531c.022.067.033.139.033.216.01.188-.05.37-.183.548l-.299.465.15.531c.01.055.022.105.033.15.011.22-.055.442-.2.664l-.265.415.1.415v.05c.033.143.044.282.033.414v.017c-.022.221-.094.404-.216.548-.122.155-.288.271-.498.349-.21.066-.487.1-.83.1zm-8.135 0h-1.86v-6.11h1.86v6.11z"></path>
                    </g>
                </svg>
                <span>Like </span>
            </span>
            <?php if($reply == true) {?>
            <span data-view-id="pdp_product_review_reply_button" class="review-comment__reply" data-reply-id="<?php echo $review->id;?>">Gửi trả lời</span>
            <?php } ?>
            <?php if(have_posts($review->reply)) {?>
            <div class="review-comment__sub-comments">
                <?php foreach ($review->reply as $comment) {  ?>
                <div class="review-sub-comment">
                    <div class="review-sub-comment__avatar-thumb">
                        <div class="avatar">
                            <img src="http://affiliate.dominhhai.com/wp-content/uploads/2021/03/14708156_1861294444099532_8999849426570778930_n.jpg">
                        </div>
                    </div>
                    <div class="review-sub-comment__inner">
                        <div class="review-sub-comment__avatar">
                            <div class="review-sub-comment__avatar-name"><?php echo $comment->name;?></div>
                            <span class="review-sub-comment__check-icon"></span>
                            <div class="review-sub-comment__avatar-date"><?php echo rating_star::timeElapsed($comment->created);?></time></div>
                        </div>
                        <div class="review-sub-comment__content">
                            <div><span><?php echo $comment->message;?></span></div>
                        </div>
                    </div>
                </div>
                <?php } ?>
            </div>
            <?php } ?>
        </div>
    </div>
</div>