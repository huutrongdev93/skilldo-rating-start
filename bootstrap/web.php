<?php
use RatingStar\Modules\Web\RatingStarPost;
use RatingStar\Modules\Web\RatingStarProduct;
use RatingStar\Services\AssetsService;
/*
|--------------------------------------------------------------------------
|  Web Assets
|--------------------------------------------------------------------------
| web - đăng ký các file css, js
| webVariable - đăng ký các biến toàn cục css
*/

add_action('theme_custom_assets', [AssetsService::class, 'web'], 30, 2);
add_filter('theme_head_style_variable',[AssetsService::class, 'webVariable'], 30);

/*
|--------------------------------------------------------------------------
|  Bài viết
|--------------------------------------------------------------------------
| Thêm đánh giá vào bài viết
*/
add_action('view_post_detail_after', [RatingStarPost::class, 'form'], 99);


/*
|--------------------------------------------------------------------------
|  Sản phẩm
|--------------------------------------------------------------------------
| Thêm đánh giá vào sản phẩm
*/
add_action('product_detail_tabs', [RatingStarProduct::class, 'form'], 30);
add_action('product_detail_info', [RatingStarProduct::class, 'detail'], 6);
add_action('product_object_info', [RatingStarProduct::class, 'object'], config('rating-star::theme.item_position') ?? 45);