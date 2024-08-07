<div class="review-item js_review_item" data-id="{{ $review->id }}" data-name="{{ $review->name }}" data-message="{{ $review->message }}">
    <div class="review-comment">
        <div class="rvc_user">
            <div class="rvc_user-avatar">
                <div class="avatar" data-name="{{ $review->name }}"><span>{{ rating_star::getKeyName($review->name) }}</span></div>
            </div>
            <div class="rvc_user-info">
	            <div class="rvc_user-name">
                    <span>{{ $review->name }}</span>
                    @if($type == 'products')
			            <div class="rvc_user-seller"><i class="fal fa-check"></i> {{ trans('review.rating.seller') }}</div>
                    @endif
	            </div>
	            <div class="rvc_star">
                    @php Rating_star_product::template(1,$review->star, RatingStar::config('color.star.detail')) @endphp
	            </div>
            </div>
        </div>
        <div class="rvc_content">
	        <div class="rvc_content_inner">
		        <p class="rvc_title">{{ RatingStar::starLabel($review->star) }}</p>
		        <div class="rvc_message">{{ $review->message }}</div>
		        <div class="rvc_images">
                    @php
                    $files = Metadata::get('rating_star', $review->id, 'attach', true);
                    @endphp

                    @if(have_posts($files)) 
				        <div class="attach-images">
                            @foreach ($files as $path => $item)
						        <div class="images"><a href="{{ Url::base($path) }}" data-fancybox="group">{!! Template::img(Url::base($path)) !!}</a></div>
						     @endforeach
				        </div>
                    @endif
		        </div>
		        <div class="rvc_time">
			        <span>{{ rating_star::timeElapsed($review->created) }} trước</span>
		        </div>
	        </div>
	        <span class="rvc_like js_rvc_btn_like" data-id="{{ $review->id }}" data-total="{{ $review->like }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20">
                    <g fill="none" fill-rule="evenodd">
                        <path d="M0 0H20V20H0z"></path>
                        <path fill="#0d5cb6" fill-rule="nonzero" d="M14.252 17.063c.465 0 .863-.056 1.195-.167.443-.143.8-.387 1.071-.73.271-.343.429-.747.473-1.212.022-.254.006-.503-.05-.747.277-.443.404-.908.382-1.395-.01-.132-.038-.265-.083-.398.266-.398.393-.819.382-1.262 0-.166-.028-.332-.083-.498.155-.232.266-.481.332-.747l.067-.083v-.73l-.034-.083v-.05c-.022-.033-.033-.055-.033-.066-.166-.642-.531-1.069-1.096-1.279-.265-.088-.542-.133-.83-.133h-2.888c.044-.298.083-.525.116-.68.144-.742.116-1.4-.083-1.976-.078-.221-.21-.586-.399-1.096l-.149-.398c-.177-.443-.476-.753-.896-.93-.321-.144-.648-.216-.98-.216-.376 0-.742.095-1.096.283-.564.287-.84.747-.83 1.378.011.254.017.453.017.597.01.454.022.797.033 1.03 0 .055-.011.105-.033.149-.033.066-.091.172-.174.315l-.191.332c-.388.676-.681 1.174-.88 1.495-.133.199-.313.365-.54.498-.227.132-.423.215-.59.249l-.248.05H4.258c-.332 0-.614.116-.847.348-.232.233-.349.515-.349.847v6.11c0 .331.117.613.35.846.232.232.514.349.846.349h9.994zm0-1.196h-6.94l.017-6.441c.51-.244.908-.587 1.195-1.03V8.38c.21-.332.504-.836.88-1.51l.017-.017c.022-.034.1-.166.232-.399.011-.011.034-.044.067-.1.033-.055.055-.094.066-.116.155-.265.221-.548.2-.846-.012-.244-.023-.56-.034-.947v-.63c-.01-.067 0-.122.033-.167.022-.044.072-.088.15-.132.177-.089.354-.133.531-.133.166 0 .338.039.515.116.11.044.193.127.249.249.077.232.127.365.15.398.165.454.292.808.38 1.063.134.387.145.841.034 1.361-.033.188-.072.426-.116.714l-.232 1.395h4.3c.143 0 .287.022.431.066.166.067.277.216.332.448.011 0 .02.011.025.034.005.022.008.038.008.05v.232l-.033.133c-.033.121-.083.238-.15.348l-.315.465.15.531c.022.067.033.139.033.216.01.188-.05.37-.183.548l-.299.465.15.531c.01.055.022.105.033.15.011.22-.055.442-.2.664l-.265.415.1.415v.05c.033.143.044.282.033.414v.017c-.022.221-.094.404-.216.548-.122.155-.288.271-.498.349-.21.066-.487.1-.83.1zm-8.135 0h-1.86v-6.11h1.86v6.11z"></path>
                    </g>
                </svg>
                <span>Like </span>
		        <span class="rc-like-total js_like_total">{{ $review->like }}</span>
            </span>
            @if($reply == true)
		        <span class="rvc_reply js_rvc_btn_reply" data-reply-id="{{ $review->id }}">
			        {{ trans('review.rating.reply.send') }}
		        </span>
            @endif
            @if(have_posts($review->reply))
		        <div class="review-comment__sub-comments">
                    @foreach ($review->reply as $comment)
				        <div class="review-sub-comment">
					        <div class="review-sub-comment__avatar-thumb">
						        <div class="avatar" data-name="{{ $review->name }}"><span>{{ rating_star::getKeyName($comment->name) }}</span></div>
					        </div>
					        <div class="review-sub-comment__inner">
						        <div class="review-sub-comment__avatar">
							        <div class="review-sub-comment__avatar-name">{{ $comment->name }}</div>
							        <span class="review-sub-comment__check-icon"></span>
							        <div class="review-sub-comment__avatar-date">{{ rating_star::timeElapsed($comment->created) }}</time></div>
						        </div>
						        <div class="review-sub-comment__content">
							        <div><span>{{ $comment->message }}</span></div>
						        </div>
					        </div>
				        </div>
                    @endforeach
		        </div>
            @endif
        </div>
    </div>
</div>