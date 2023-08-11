<?php
Class Rating_Star_Admin_Product {
    static function randomRatingStar($id, $module): void
    {
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
    static function addColumnTitle($item): void
    {

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
    static function delete($productID): void
    {
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
    static function buttonAction($actionList) {
        if (Auth::hasCap('product_edit')) {
            $actionList['productAddReviews'] = [
                'icon' => '<i class="fa-duotone fa-stars"></i>',
                'label' => 'Tạo đánh giá',
                'class' => 'js_btn_product_add_review',
            ];
        }
        return $actionList;
    }

}
add_action('save_object_add', 'Rating_Star_Admin_Product::randomRatingStar', 10, 2);
add_action('admin_product_table_column_title', 'Rating_Star_Admin_Product::addColumnTitle', 10);
add_action('delete_product_success', 'Rating_Star_Admin_Product::delete', 10);
add_action('delete_products_list_success', 'Rating_Star_Admin_Product::delete', 10);
add_filter('admin_table_action_product_list_button', 'Rating_Star_Admin_Product::buttonAction');