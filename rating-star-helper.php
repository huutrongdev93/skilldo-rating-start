<?php
Class Rating_Star_Product {

    function __construct() {
        add_action('product_detail_tabs', 'Rating_Star_Product::form', 30);
        add_action('product_detail_info', 'Rating_Star_Product::detail', 6);
        add_action('product_object_info', 'Rating_Star_Product::object', rating_star::config('item_position') );
    }

    static public function object($object) {

        $rating_star_data = Product::getMeta($object->id, 'rating_star', true);

        $total_star     = (isset($rating_star_data['star'])) ? $rating_star_data['star'] : 0;

        $total_number_review    = (isset($rating_star_data['count'])) ? $rating_star_data['count'] : 0;

        if( $total_number_review != 0 ) $total_star = round($total_star/$total_number_review);

        static::template($total_number_review, $total_star);

        return true;
    }

    static public function detail($object) {

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

        return true;
    }

    static public function form($object) {

        $rating_star_data = Product::getMeta($object->id, 'rating_star', true);

        $data = [
            'object' => $object,
            'config' => rating_star::config(),
            'star'   => (isset($rating_star_data['star'])) ? $rating_star_data['star'] : 0,
            'count'  => (isset($rating_star_data['count'])) ? $rating_star_data['count'] : 0,
            'form'   => [
                'name' => '',
                'email' => '',
            ]
        ];

        if($data['count'] != 0) $data['star'] = round($data['star']/$data['count']);

        Plugin::partial(RATING_STAR_NAME, 'product/'.rating_star::config('template'), $data);

        Plugin::partial(RATING_STAR_NAME, 'rating-star-product-review', $data);

        return true;
    }

    static public  function template($total_count, $total_star) {
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
}

Class Rating_star_post {

    function __construct() {
        add_action('the_content', 'Rating_star_post::form', 99);
    }

    static public function object($object) {

        $rating_star_data = Posts::getMeta($object->id, 'rating_star', true);

        $total_star     = (isset($rating_star_data['star'])) ? $rating_star_data['star'] : 0;

        $total_number_review    = (isset($rating_star_data['count'])) ? $rating_star_data['count'] : 0;

        if( $total_number_review != 0 ) $total_star = round($total_star/$total_number_review);

        static::template($total_number_review, $total_star);

        return true;
    }

    static public function form($content) {

        if(Template::isPage('post_detail')) {

            $object = get_object_current();

            $rating_star_data = Posts::getMeta($object->id, 'rating_star', true);

            $data = [
                'object' => $object,
                'config' => rating_star::config(),
                'star'   => (isset($rating_star_data['star'])) ? $rating_star_data['star'] : 0,
                'count'  => (isset($rating_star_data['count'])) ? $rating_star_data['count'] : 0,
            ];

            if($data['count'] != 0) $data['star'] = round($data['star']/$data['count']);

            ob_start();

            Plugin::partial(RATING_STAR_NAME, 'rating-star-post-form', $data);

            $content .= ob_get_contents();

            ob_end_clean();
        }

        return $content;
    }

    static public  function template($total_count, $total_star) {
        ?>
        <div class="skd-product-reviews-star" style="color: var(--star-color);margin-bottom:10px;height: 11px;font-size:13px;">
            <?php if($total_star != 0) { ?>
                <?php for( $i = 0; $i < $total_star; $i++ ) {?>
                    <i class="fa fa-star" aria-hidden="true" style="color:var(--star-color); font-weight: bold;"></i>&nbsp;
                <?php } ?>
                <?php for( $i = 0; $i < (5 - $total_star); $i++ ) {?>
                    <i class="fas fa-star" aria-hidden="true" style="color:#ccc;"></i>&nbsp;
                <?php } ?>
            <?php } ?>
        </div>
        <?php
    }
}
