<?php
Class AdminRatingStar {
    static public function navigation() {
        if(Auth::hasCap('rating_star')) {

            $module = rating_star::module();

            $count = RatingStar::count(Qr::set('is_read',0));

            AdminMenu::add('rating-star', 'Đánh giá sao', 'plugins?page=rating-star', [
                'icon' => '<img src="'.RATING_STAR_PATH.'/assets/images/rating-star.png">',
                'callback' => 'AdminRatingStar::page',
                'position' => 'theme',
                'count' => $count
            ]);

            foreach ($module as $moduleKey => $item) {
                AdminMenu::addSub('rating-star', $moduleKey, $item['name'], 'plugins?page=rating-star&object_type='.$moduleKey, [
                    'callback' => 'AdminRatingStar::page',
                ]);
            }
        }
    }
    static public function page() {

        $page   = (int)Request::get('p');

        $star   = (int)Request::get('star');

        $type = Request::get('type');

        $object_type = Request::get('object_type');

        if(empty($object_type)) $object_type = 'products';

        $module = Rating_star::module($object_type);

        if(!empty($module)) {

            $object_id = Request::get('object');

            $url = Url::admin('plugins?page=rating-star&p={page}');

            $limit = 10;

            $args = Qr::set()->whereIn('status', ['public', 'hidden']);

            if (!empty($type)) {
                $args->where('type', $type);
            }
            else {
                $args->where('type', 'handmade');
            }

            if (!empty($star)) {
                $args->where('star', $star);
                $url .= '&star=' . $star;
            }

            if (!empty($object_type)) {
                $args->where('object_type', $object_type);
                $url .= '&type=' . $object_type;
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
    static public function actionBarButton($module) {
        if(Template::isClass('plugins') && Request::get('page') == 'rating-star') {
            echo '<div class="pull-right">'; do_action('action_bar_plugin_rating_star_right', $module); echo '</div>';
        }
    }
    static public function actionBarButtonRight($module) {
        if(Request::get('view') == '' || Request::get('view') == 'list') {
            ?>
            <button class="btn-icon btn-red delete" data-table="rating_star"><?php echo Admin::icon('delete');?> Xóa đánh giá</button>
            <a href="<?php echo Url::admin('system/rating-star');?>" class="btn btn-blue"><i class="fad fa-cog"></i> Cấu Hình</a>
            <?php
        }
    }
    static public function actionDelete($res, $table, $id) {
        if(is_numeric($id))         $res = RatingStar::delete($id);
        else if(have_posts($id))    $res = RatingStar::deleteList($id);
        return $res;
    }
}

add_action('init', 'AdminRatingStar::navigation', 10);
add_action('action_bar_before', 'AdminRatingStar::actionBarButton', 10 );
add_action('action_bar_plugin_rating_star_right', 'AdminRatingStar::actionBarButtonRight', 10 );
add_filter('delete_object_rating_star', 'AdminRatingStar::actionDelete', 1, 3);

Class AdminRatingStarSetting {
    static function register($tabs) {
        $tabs['rating-star']   = [
            'label'       => 'Đánh giá sao',
            'description' => 'Quản lý cấu hình đánh giá bài viết, sản phẩm',
            'callback'    => 'AdminRatingStarSetting::render',
            'icon'        => '<i class="fa-solid fa-stars"></i>',
        ];
        return $tabs;
    }
    static public function render($ci, $tab) {
        do_action('admin_system_rating_start_html', $tab);
    }
    static public function renderDefault($tab): void {
        $form = new FormBuilder();
        $form
            ->add('rating_star_setting[product_enable]', 'switch', [
                'label' => 'Bật / Tắt đánh giá sản phẩm',
                'note' => 'Bật tùy chọn này khi sử dụng đánh giá sao trong sản phẩm.'], RatingStar::config('product_enable'))
            ->add('rating_star_setting[post_enable]', 'switch', [
                'label' => 'Bật / Tắt đánh giá bài viết',
                'note' => 'Bật tùy chọn này khi sử dụng đánh giá sao trong bài viết.'], RatingStar::config('post_enable'))

            ->add('rating_star_setting[has_approving]', 'switch', [
                'label' => 'Duyệt đánh giá',
                'note' => 'Cấu hình cho phép chủ cửa hàng duyệt các đánh giá sản phẩm trước khi cho hiển thị.'], RatingStar::config('has_approving'))

            ->add('rating_star_setting[color][star]', 'color', [
                'label' => 'Màu biểu tượng sao',], RatingStar::config('color.star'))

            ->add('rating_star_setting[illegal_message]', 'textarea', [
                'label' => 'Từ khóa không cho phép',
                'note' => 'Mỗi từ khóa cách nhau bằng dầu ","'
            ], RatingStar::config('illegal_message'));

        Admin::partial('function/system/html/default', [
            'title'       => 'Cấu hình chung',
            'description' => 'Quản lý màu sắc hệ thống admin',
            'form'        => $form
        ]);
    }
    static public function renderProduct($tab): void {
        Plugin::partial('rating-star', 'admin/views/html-setting-product');
    }
    static public function renderAuto($tab): void {
        $form = new FormBuilder();
        $form
            ->add('rating_star_setting[auto_enable]', 'switch', ['label' => 'Bật / tắt tự động tạo đánh giá', 'note' => 'Tự động tạo đánh giá khi tạo mới sản phẩm' ], RatingStar::config('auto_enable'))
            ->add('rating_star_setting[auto_min_number]', 'number', ['label' => 'Số đánh giá nhỏ nhất tạo ra', 'min' => 0,
                'after' => '<div class="col-md-6"><div class="form-group group">', 'before'=> '</div></div>'
            ], RatingStar::config('auto_min_number'))

            ->add('rating_star_setting[auto_max_number]', 'number', ['label' => 'Số đánh giá lớn nhất tạo ra', 'min' => 3, 'max' => 10,
                'after' => '<div class="col-md-6"><div class="form-group group">', 'before'=> '</div></div>'
            ], RatingStar::config('auto_max_number'))

            ->add('rating_star_setting[auto_percent_5]', 'number', ['label' => 'Tỉ lệ ra 5 sao', 'min' => 0, 'max' => 100,
                'after' => '<div class="col-md-4"><div class="form-group group">', 'before'=> '</div></div>'
            ], RatingStar::config('auto_percent_5'))

            ->add('rating_star_setting[auto_percent_4]', 'number', ['label' => 'Tỉ lệ ra 4 sao', 'min' => 0, 'max' => 100,
                'after' => '<div class="col-md-4"><div class="form-group group">', 'before'=> '</div></div>'
            ], RatingStar::config('auto_percent_4'))

            ->add('rating_star_setting[auto_percent_3]', 'number', ['label' => 'Tỉ lệ ra 3 sao', 'min' => 0, 'max' => 100,
                'after' => '<div class="col-md-4"><div class="form-group group">', 'before'=> '</div></div>'
            ], RatingStar::config('auto_percent_3'));

        Admin::partial('function/system/html/default', [
            'title'       => 'Cấu hình tự động đánh giá',
            'description' => 'Quản lý hệ thống tự động đánh giá sản phẩm',
            'form'        => $form
        ]);
    }
    static public function save($result, $data) {

        if(isset($data['rating_star_setting'])) {

            $data = $data['rating_star_setting'];

            $rating['product_enable']  = Str::clear($data['product_enable']);
            $rating['post_enable']     = Str::clear($data['post_enable']);
            $rating['has_approving']   = Str::clear($data['has_approving']);
            $rating['color']           = add_magic_quotes($data['color']);
            $rating['illegal_message'] = trim(Str::clear($data['illegal_message']));
            $rating['illegal_message'] = trim($rating['illegal_message'], ',');

            $rating['item_align']       = Str::clear($data['item_align']);
            $rating['item_position']    = (int)Str::clear($data['item_position']);
            $rating['template']         = Str::clear($data['template']);
            $rating['reply']            = Str::clear($data['reply']);
            $rating['auto_enable']      = Str::clear($data['auto_enable']);
            $rating['auto_min_number']  = (int)Str::clear($data['auto_min_number']);
            $rating['auto_max_number']  = (int)Str::clear($data['auto_max_number']);
            $rating['auto_percent_5']   = (int)Str::clear($data['auto_percent_5']);
            $rating['auto_percent_4']   = (int)Str::clear($data['auto_percent_4']);
            $rating['auto_percent_3']   = (int)Str::clear($data['auto_percent_3']);

            if ($rating['auto_min_number'] > $rating['auto_max_number']) {
                $result['message'] = 'Số đánh giá nhỏ nhất tạo ra không thể lớn hơn số đánh giá lớn nhất.';
                $result['status'] = 'error';
                return $result;
            }

            Option::update('rating_star_setting', $rating);
        }

        return $result;
    }
}

add_filter('skd_system_tab' , 'AdminRatingStarSetting::register', 20);
add_action('admin_system_rating_start_html' , 'AdminRatingStarSetting::renderDefault', 10);
add_action('admin_system_rating_start_html' , 'AdminRatingStarSetting::renderProduct', 20);
add_action('admin_system_rating_start_html' , 'AdminRatingStarSetting::renderAuto', 30);
add_filter('admin_system_rating_star_save','AdminRatingStarSetting::save',10,2);