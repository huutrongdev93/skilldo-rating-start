<?php
Class Rating_Star_Product {
    function __construct() {
        add_action('product_detail_tabs', 'Rating_Star_Product::form', 30);
        add_action('product_detail_info', 'Rating_Star_Product::detail', 6);
        add_action('product_object_info', 'Rating_Star_Product::object', RatingStar::config('item_position') );
    }
    static function object($object): void {

        $rating_star_data = Product::getMeta($object->id, 'rating_star', true);

        $total_star     = (isset($rating_star_data['star'])) ? $rating_star_data['star'] : 0;

        $total_number_review    = (isset($rating_star_data['count'])) ? $rating_star_data['count'] : 0;

        if($total_number_review != 0) {
            $total_star = round($total_star/$total_number_review);
        }
        else {
            $total_star = 5;
        }

        self::template($total_number_review, $total_star);
    }
    static function detail($object): void {
        $rating_star_data = Product::getMeta($object->id, 'rating_star', true);

        $total_star     = (isset($rating_star_data['star'])) ? $rating_star_data['star'] : 0;

        $total_number_review    = (isset($rating_star_data['count'])) ? $rating_star_data['count'] : 0;

        if( $total_number_review != 0 ) $total_star = round($total_star/$total_number_review);
        ?>
        <div class="skd-product-reviews-star skd-product-detail-reviews-star" style="text-align:left;color:var(--star-color);font-size:13px; margin-bottom: 10px;">
            <div class="product-reviews__inner" style="display: inline-block; text-align: left">
                <?php for( $i = 0; $i < 5; $i++ ) {?>
                    <span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 32 32">
                            <path fill="none" fill-rule="evenodd" stroke="var(--star-color)" stroke-width="1.5" d="M16 1.695l-4.204 8.518-9.401 1.366 6.802 6.631-1.605 9.363L16 23.153l8.408 4.42-1.605-9.363 6.802-6.63-9.4-1.367L16 1.695z"></path>
                        </svg>
                    </span>
                <?php } ?>
                <div style="width: 100%;">
                    <?php for( $i = 0; $i < $total_star; $i++ ) {?>
                        <span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 32 32">
                                <path fill="var(--star-color)" fill-rule="evenodd" stroke="var(--star-color)" stroke-width="1.5" d="M16 1.695l-4.204 8.518-9.401 1.366 6.802 6.631-1.605 9.363L16 23.153l8.408 4.42-1.605-9.363 6.802-6.63-9.4-1.367L16 1.695z"></path>
                            </svg>
                        </span>
                    <?php } ?>
                </div>
                <span class="star-count">( <?php echo $total_number_review;?> <?php echo __('đánh giá', 'rating_rate');?> )</span>
            </div>
        </div>
        <?php
    }
    static function form($object): void {

        $rating_star_data = Product::getMeta($object->id, 'rating_star', true);

        $data = [
            'type'   => 'products',
            'objectName' => 'sản phẩm',
            'object' => $object,
            'config' => RatingStar::config(),
            'star'   => (isset($rating_star_data['star'])) ? $rating_star_data['star'] : 0,
            'count'  => (isset($rating_star_data['count'])) ? $rating_star_data['count'] : 0,
            'form'   => [
                'name' => '',
                'email' => '',
            ]
        ];

        if($data['count'] != 0) $data['star'] = round($data['star']/$data['count']);

        Plugin::partial(RATING_STAR_NAME, RatingStar::config('template'), $data);

        Plugin::partial(RATING_STAR_NAME, 'review', $data);
    }
    static function template($total_count, $total_star): void {
        ?>
        <div class="skd-product-reviews-star" style="text-align:var(--star-align)!important;">
            <div class="product-reviews__inner" style="display: inline-block; text-align: left">
                <?php for( $i = 0; $i < 5; $i++ ) {?>
                <span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 32 32">
                        <path fill="none" fill-rule="evenodd" stroke="var(--star-color)" stroke-width="1.5" d="M16 1.695l-4.204 8.518-9.401 1.366 6.802 6.631-1.605 9.363L16 23.153l8.408 4.42-1.605-9.363 6.802-6.63-9.4-1.367L16 1.695z"></path>
                    </svg>
                </span>
                <?php } ?>
                <div style="width: 100%;">
                    <?php for( $i = 0; $i < $total_star; $i++ ) {?>
                        <span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 32 32">
                            <path fill="var(--star-color)" fill-rule="evenodd" stroke="var(--star-color)" stroke-width="1.5" d="M16 1.695l-4.204 8.518-9.401 1.366 6.802 6.631-1.605 9.363L16 23.153l8.408 4.42-1.605-9.363 6.802-6.63-9.4-1.367L16 1.695z"></path>
                        </svg>
                    </span>
                    <?php } ?>
                </div>
            </div>
        </div>
        <?php
    }
    static function get($args = null) {
        return Product::get($args);
    }
    static function getsData($listId) {
        return Product::gets(Qr::set()->whereIn('id', $listId)->select('id', 'title', 'slug'));
    }
}
if(RatingStar::config('product_enable') == 1) {
    new Rating_Star_Product();
}