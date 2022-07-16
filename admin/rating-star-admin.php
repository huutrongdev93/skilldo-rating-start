<?php
Class Rating_Star_Admin {
    static public function navigation() {
        if(Auth::hasCap('rating_star')) {

            $module = rating_star::module();

            $count = RatingStar::count(Qr::set('is_read',0));

            AdminMenu::add('rating-star', 'Đánh giá sao', 'plugins?page=rating-star', [
                'icon' => '<img src="'.RATING_STAR_PATH.'/assets/images/rating-star.png">',
                'callback' => 'Rating_Star_Admin::page',
                'position' => 'theme',
                'count' => $count
            ]);

            foreach ($module as $moduleKey => $item) {
                AdminMenu::addSub('rating-star', $moduleKey, $item['name'], 'plugins?page=rating-star&type='.$moduleKey, [
                    'callback' => 'Rating_Star_Admin::page',
                ]);
            }
        }
    }
    static public function page() {
        $views = Request::get('view');
        if($views == 'setting') {
            include RATING_STAR_PATH.'/admin/views/html-setting.php';
        }
        else {

            $page   = (int)Request::get('p');

            $star   = (int)Request::get('star');

            $type = Request::get('type');

            if(empty($type)) $type = 'products';

            $module = Rating_star::module($type);

            if(!empty($module)) {

                $object_id = Request::get('object');

                $status = Request::get('status');

                $url = Url::admin('plugins?page=rating-star&p={page}');

                $limit = 10;

                $args = Qr::set()->whereIn('status', ['public', 'hidden']);

                if ($status == 'auto') {
                    $args->removeWhere('status');
                    $args->where('status', 'auto');
                }

                if (!empty($star)) {
                    $args->where('star', $star);
                    $url .= '&star=' . $star;
                }

                if (!empty($type)) {
                    $args->where('object_type', $type);
                    $url .= '&type=' . $type;
                }

                if (!empty($object_id)) {
                    $args->where('object_id', $object_id);
                    $url .= '&object=' . $object_id;
                }

                $count = RatingStar::count($args);

                $config = [
                    'currentPage' => ($page != 0) ? $page : 1, // Trang hiện tại
                    'totalRecords' => $count, // Tổng số record
                    'limit' => $limit,
                    'url' => $url,
                ];

                $pagination = new Pagination($config);

                $args->limit($limit)->offset($pagination->offset());

                $rating_stars = RatingStar::gets($args);

                if (have_posts($rating_stars)) {

                    foreach ($rating_stars as $rating_star) {

                        $object = $module['class']::get($rating_star->object_id);

                        $rating_star->title = '';

                        $rating_star->slug = '';

                        if (have_posts($object)) {

                            $rating_star->title = (isset($object->name)) ? $object->name : $object->title;

                            $rating_star->slug = (isset($object->slug)) ? $object->slug : '';
                        }
                    }
                }

                model('rating_star')->update(['is_read' => 1], Qr::set('is_read', 0, 'object_type', $type));

                include RATING_STAR_PATH . '/admin/views/html-index.php';
            }
        }
    }
    static public function actionBarButton($module) {
        if(Template::isClass('plugins') && Request::get('page') == 'rating-star') {
            echo '<div class="pull-right">'; do_action('action_bar_plugin_rating_star_right', $module); echo '</div>';
        }
    }
    static public function actionBarButtonRight($module) {
        if(Request::get('view') == '' || Request::get('view') == 'list') {
            ?>
            <button class="btn-icon btn-red delete" data-table="rating_star"><?php echo Admin::icon('delete');?> Xóa đánh giá</button>
            <a href="<?php echo admin_url('plugins?page=rating-star&view=setting');?>" class="btn btn-blue"><i class="fad fa-cog"></i> Cấu Hình</a>
            <?php
        }
        if(Request::get('view') == 'setting') {
            ?>
            <button form="rating_star_setting_form" type="submit" class="btn btn-green"><?php echo Admin::icon('save');?> Lưu</button>
            <a href="<?php echo admin_url('plugins?page=rating-star');?>" class="btn btn-blue"><?php echo Admin::icon('back');?> Danh sách đánh giá</a>
            <?php
        }
    }
    static public function actionDelete($res, $table, $id) {
        if(is_numeric($id))         $res = RatingStar::delete($id);
        else if(have_posts($id))    $res = RatingStar::deleteList($id);
        return $res;
    }
}

add_action('init', 'Rating_Star_Admin::navigation', 10);
add_action( 'action_bar_before', 'Rating_Star_Admin::actionBarButton', 10 );
add_action( 'action_bar_plugin_rating_star_right', 'Rating_Star_Admin::actionBarButtonRight', 10 );
add_filter('delete_object_rating_star', 'Rating_Star_Admin::actionDelete', 1, 3);