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

        if( $total_number_review != 0 ) $total_star = round($total_star/$total_number_review);

        static::template($total_number_review, $total_star);
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
}
if(RatingStar::config('product_enable') == 1) {
    new Rating_Star_Product();
}
Class Rating_Star_Admin_Product {
    static public function randomRatingStar($id, $module) {

        if($module == 'products' && RatingStar::config('auto_enable') == 1) {

            $ratings = RatingStar::random();

            if(have_posts($ratings)) {

                foreach ($ratings as $rating) {

                    $rating['object_id'] = $id;

                    $rating['status'] = 'public';

                    $rating['type'] = 'auto';

                    $error = RatingStar::insert($rating);

                    if(!is_skd_error($error)) {

                        $model = model('rating_star');

                        $model->update(['created' => date('Y-m-d H:i:s', time() - rand(0, 30)*24*rand(50, 60)*rand(0, 60))], Qr::set($error));

                        if(RatingStar::config('has_approving') == 0) {

                            $rating_star_product = Product::getMeta($id, 'rating_star', true);

                            if(!have_posts($rating_star_product)) {

                                $rating_star_product = ['count' => 0, 'star'  => 0];
                            }

                            $rating_star_product['count'] += 1;

                            $rating_star_product['star']  += $rating['star'];

                            Product::updateMeta($id, 'rating_star', $rating_star_product);
                        }
                    }
                }
            }
        }
    }
    static public function addColumnTitle($item) {

        $rating_star_data = Product::getMeta($item->id, 'rating_star', true);

        $total_star     = (isset($rating_star_data['star'])) ? $rating_star_data['star'] : 0;

        $total_number_review    = (isset($rating_star_data['count'])) ? $rating_star_data['count'] : 0;

        if( $total_number_review != 0 ) $total_star = round($total_star/$total_number_review);
        ?>
        <div class="skd-product-detail-reviews-star" style="text-align:left;color:#fd9a42; margin:5px 0; font-size:10px;">
            <a href="<?php echo Url::admin('plugins?page=rating-star&object='.$item->id.'&type=product');?>">
                <?php for( $i = 0; $i < $total_star; $i++ ) {?>
                    <i class="fas fa-star" aria-hidden="true" style="color:#fd9a42; font-weight: bold;"></i>&nbsp
                <?php } ?>
                <?php for( $i = 0; $i < (5 - $total_star); $i++ ) {?>
                    <i class="fas fa-star" aria-hidden="true" style="color:#ccc;"></i>&nbsp;
                <?php } ?>
                ( <?php echo $total_number_review;?> <?php echo __('đánh giá', 'rating_rate');?> )
            </a>
        </div>
        <?php
    }
    static public function delete($productID) {

        if(is_numeric($productID)) $productID = [$productID];

        $rating_star = RatingStar::gets(Qr::set('object_type', 'products')->whereIn('object_id', $productID));

        if(have_posts($rating_star)) {
            $listID = [];
            foreach ($rating_star as $item) {
                $listID[] = $item->id;
                Metadata::delete($item->object_type, $item->object_id, 'rating_star');
            }
            //Delete comments
            model('rating_star')->delete(Qr::set('object_type', 'comment')->whereIn('parent_id', $listID));
            model('rating_star')->delete(Qr::set()->whereIn('id', $listID));
        }
    }
}
add_action('save_object_add', 'Rating_Star_Admin_Product::randomRatingStar', 10, 2);
add_action('admin_product_table_column_title', 'Rating_Star_Admin_Product::addColumnTitle', 10);
add_action('delete_product_success', 'Rating_Star_Admin_Product::delete', 10);
add_action('delete_products_list_success', 'Rating_Star_Admin_Product::delete', 10);