<?php
Class Rating_Star_Admin {
    static public function navigation() {
        if(Auth::hasCap('rating_star')) {
            $count = rating_star::count(['where' => ['is_read' => 0]]);
            AdminMenu::add('rating-star', 'Đánh giá sao', 'plugins?page=rating-star', [
                'icon' => '<img src="'.RATING_STAR_PATH.'/assets/images/rating-star.png">',
                'callback' => 'Rating_Star_Admin::page',
                'position' => 'theme',
                'count' => $count
            ]);
        }
    }
    static public function page() {
        $views = InputBuilder::get('view');
        if($views == 'setting') {
            include RATING_STAR_PATH.'/admin/views/html-setting.php';
        }
        else {

            $page = (int)InputBuilder::Get('p');

            $star = (int)InputBuilder::Get('star');

            $type = InputBuilder::Get('type');

            if(empty($type)) $type = 'product';

            $object_id = InputBuilder::Get('object');

            $url = Url::admin('plugins?page=rating-star&p={page}');

            $review_in_page = 10;

            $args = [
                'where'  => [],
                'params' => []
            ];

            if(!empty($star)) {
                $args['where']['star'] = $star;
                $url .= '&star='.$star;
            }

            if(!empty($type)) {
                $args['where']['object_type'] = $type;
                $url .= '&type='.$type;
            }

            if(!empty($object_id)) {
                $args['where']['object_id'] = $object_id;
                $url .= '&object='.$object_id;
            }

            $count = rating_star::count($args);

            $config  = array (
                'current_page'  => ($page != 0) ? $page : 1, // Trang hiện tại
                'total_rows'    => $count, // Tổng số record
                'number'		=> $review_in_page,
                'url'           => $url,
            );

            $pagination = new paging($config);

            $args['params']['limit'] = $review_in_page;

            $args['params']['start'] = $pagination->getoffset();

            $rating_stars = rating_star::gets($args);

            get_model('plugins')->settable('rating_star')->update_where(['is_read' => 1], ['is_read' => 0]);

            include RATING_STAR_PATH.'/admin/views/html-index.php';
        }
    }
    static public function actionBarButton($module) {
        if(Template::isClass('plugins') && InputBuilder::get('page') == 'rating-star') {
            echo '<div class="pull-right">'; do_action('action_bar_plugin_rating_star_right', $module); echo '</div>';
        }
    }
    static public function actionBarButtonRight($module) {
        if(InputBuilder::get('view') == '' || InputBuilder::get('view') == 'list') {
            ?>
            <button class="btn-icon btn-red delete" data-table="rating_star"><?php echo Admin::icon('delete');?> Xóa đánh giá</button>
            <a href="<?php echo admin_url('plugins?page=rating-star&view=setting');?>" class="btn btn-blue"><i class="fad fa-cog"></i> Cấu Hình</a>
            <?php
        }
        if(InputBuilder::get('view') == 'setting') {
            ?>
            <a href="<?php echo admin_url('plugins?page=rating-star');?>" class="btn btn-blue"><?php echo Admin::icon('back');?> Danh sách đánh giá</a>
            <button form="rating_star_setting_form" type="submit" class="btn btn-green"><?php echo Admin::icon('save');?> Lưu</button><?php
        }
    }
    static public function actionDelete($res, $table, $id) {
        if(is_numeric($id))         $res = rating_star::delete($id);
        else if(have_posts($id))    $res = rating_star::deleteList($id);
        return $res;
    }
}

add_action('init', 'Rating_Star_Admin::navigation', 10);
add_action( 'action_bar_before', 'Rating_Star_Admin::actionBarButton', 10 );
add_action( 'action_bar_plugin_rating_star_right', 'Rating_Star_Admin::actionBarButtonRight', 10 );
add_filter('delete_object_rating_star', 'Rating_Star_Admin::actionDelete', 1, 3);

Class Rating_Star_Admin_Product {
    static public function randomRatingStar($id, $module) {

        if($module == 'products' && rating_star::config('auto_enable') == 1) {

            $ratings = rating_star::random();

            if(have_posts($ratings)) {

                foreach ($ratings as $rating) {

                    $rating['object_id'] = $id;

                    $error = rating_star::insert($rating);

                    if(!is_skd_error($error)) {

                        $model = get_model()->settable('rating_star');

                        $model->update_where(['created' => date('Y-m-d H:i:s', time() - rand(0, 30)*24*rand(50, 60)*rand(0, 60))], ['id' => $error]);

                        if(rating_star::config('has_approving') == 0) {

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
}

add_action('save_object_add', 'Rating_Star_Admin_Product::randomRatingStar', 10, 2);
add_action('admin_product_table_column_title', 'Rating_Star_Admin_Product::addColumnTitle', 10);