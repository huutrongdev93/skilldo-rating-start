<?php
include 'ajax.php';
include 'products.php';

Class AdminRatingStar {
    static public function navigation(): void
    {
        if(Auth::hasCap('rating_star')) {
            $count = RatingStar::count(Qr::set('is_read',0));
            AdminMenu::add('rating-star', 'Đánh giá', 'plugins?page=rating-star', [
                'icon' => '<img src="'.RATING_STAR_PATH.'/assets/images/rating-star.png">',
                'callback' => 'AdminRatingStar::page',
                'position' => 'theme',
                'count' => $count
            ]);
        }
    }
    static public function page(): void
    {
        model('rating_star')->update(['is_read' => 1], Qr::set('is_read', 0));
        include RATING_STAR_PATH . '/admin/views/html-index.php';
    }
    static public function actionBarButton($module): void
    {
        if(Template::isClass('plugins') && Request::get('page') == 'rating-star') {
            echo '<div class="pull-right">'; do_action('action_bar_plugin_rating_star_right', $module); echo '</div>';
        }
    }
    static public function actionBarButtonRight($module): void
    {
        if(Request::get('view') == '' || Request::get('view') == 'list') {
            ?>
            <a href="<?php echo Url::admin('system/rating-star');?>" class="btn btn-blue"><i class="fad fa-cog"></i> Cấu Hình</a>
            <?php
        }
    }
}

add_action('init', 'AdminRatingStar::navigation', 10);
add_action('action_bar_before', 'AdminRatingStar::actionBarButton', 10 );
add_action('action_bar_plugin_rating_star_right', 'AdminRatingStar::actionBarButtonRight', 10 );

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
    static public function render($ci, $tab): void
    {
        $config = RatingStar::config();
        do_action('admin_system_rating_start_html', $config, $tab);
    }
    static public function renderDefault($config): void {
        $form = new FormBuilder();
        $form
            ->add('rating_star_setting[product_enable]', 'switch', [
                'label' => 'Bật / Tắt đánh giá sản phẩm',
                'note' => 'Bật tùy chọn này khi sử dụng đánh giá sao trong sản phẩm.',
	            'start' => 4
            ], $config['product_enable'])
            ->add('rating_star_setting[post_enable]', 'switch', [
                'label' => 'Bật / Tắt đánh giá bài viết',
                'note' => 'Bật tùy chọn này khi sử dụng đánh giá sao trong bài viết.',
                'start' => 4
            ], $config['post_enable'])

            ->add('rating_star_setting[has_approving]', 'switch', [
                'label' => 'Duyệt đánh giá',
                'note' => 'Cấu hình cho phép chủ cửa hàng duyệt các đánh giá sản phẩm trước khi cho hiển thị.',
                'start' => 4
            ], $config['has_approving'])

            ->add('rating_star_setting[color][star]', 'color', [
                'label' => 'Màu biểu tượng sao',], $config['color']['star'])

            ->add('rating_star_setting[illegal_message]', 'textarea', [
                'label' => 'Từ khóa không cho phép',
                'note' => 'Mỗi từ khóa cách nhau bằng dầu ","'
            ], $config['illegal_message']);

        Admin::partial('function/system/html/default', [
            'title'       => 'Cấu hình chung',
            'description' => 'Quản lý màu sắc hệ thống admin',
            'form'        => $form
        ]);
    }
    static public function renderProduct($config): void {
        Plugin::partial('rating-star', 'admin/views/html-setting-product', ['config' => $config]);
    }
    static public function renderAuto($config): void {
        $form = new FormBuilder();
        $form
            ->add('rating_star_setting[auto_enable]', 'switch', [
				'label' => 'Bật / tắt tự động tạo đánh giá',
	            'note'  => 'Tự động tạo đánh giá khi tạo mới sản phẩm',
                'start' => 4
            ], $config['auto_enable'])
            ->add('rating_star_setting[auto_min_number]', 'number', [
				'label' => 'Số đánh giá nhỏ nhất tạo ra', 'min' => 0,
                'start' => 4
            ], $config['auto_min_number'])

            ->add('rating_star_setting[auto_max_number]', 'number', [
				'label' => 'Số đánh giá lớn nhất tạo ra', 'min' => 3, 'max' => 10,
                'start' => 4
            ], $config['auto_max_number'])

            ->add('rating_star_setting[auto_percent_5]', 'number', ['label' => 'Tỉ lệ ra 5 sao', 'min' => 0, 'max' => 100,
                'after' => '<div class="col-md-4"><div class="form-group group">', 'before'=> '</div></div>'
            ], $config['auto_percent_5'])

            ->add('rating_star_setting[auto_percent_4]', 'number', ['label' => 'Tỉ lệ ra 4 sao', 'min' => 0, 'max' => 100,
                'after' => '<div class="col-md-4"><div class="form-group group">', 'before'=> '</div></div>'
            ], $config['auto_percent_4'])

            ->add('rating_star_setting[auto_percent_3]', 'number', ['label' => 'Tỉ lệ ra 3 sao', 'min' => 0, 'max' => 100,
                'after' => '<div class="col-md-4"><div class="form-group group">', 'before'=> '</div></div>'
            ], $config['auto_percent_3']);

        Admin::partial('function/system/html/default', [
            'title'       => 'Cấu hình tự động đánh giá',
            'description' => 'Quản lý hệ thống tự động đánh giá sản phẩm',
            'form'        => $form
        ]);
    }
    static public function renderAutoData($config): void {
		$dataAuto = file_get_contents(RATING_STAR_PATH.'/assets/auto-data.json');
        Plugin::partial('rating-star', 'admin/views/html-setting-data', ['config' => $config, 'dataAuto' => $dataAuto]);
    }
    static public function save($result, $data) {

        if(isset($data['rating_star_setting'])) {

            $dataSetting = $data['rating_star_setting'];

            $rating['product_enable']  = Str::clear($dataSetting['product_enable']);
            $rating['post_enable']     = Str::clear($dataSetting['post_enable']);
            $rating['has_approving']   = Str::clear($dataSetting['has_approving']);
            $rating['color']           = add_magic_quotes($dataSetting['color']);
            $rating['illegal_message'] = trim(Str::clear($dataSetting['illegal_message']));
            $rating['illegal_message'] = trim($rating['illegal_message'], ',');
            $rating['item_align']      = Str::clear($dataSetting['item_align']);
            $rating['item_position']   = (int)Str::clear($dataSetting['item_position']);
            $rating['template']        = Str::clear($dataSetting['template']);
            $rating['reply']           = Str::clear($dataSetting['reply']);
            $rating['auto_enable']     = Str::clear($dataSetting['auto_enable']);
            $rating['auto_min_number'] = (int)Str::clear($dataSetting['auto_min_number']);
            $rating['auto_max_number'] = (int)Str::clear($dataSetting['auto_max_number']);
            $rating['auto_percent_5']  = (int)Str::clear($dataSetting['auto_percent_5']);
            $rating['auto_percent_4']  = (int)Str::clear($dataSetting['auto_percent_4']);
            $rating['auto_percent_3']  = (int)Str::clear($dataSetting['auto_percent_3']);

            if ($rating['auto_min_number'] > $rating['auto_max_number']) {
                $result['message'] = 'Số đánh giá nhỏ nhất tạo ra không thể lớn hơn số đánh giá lớn nhất.';
                $result['status'] = 'error';
                return $result;
            }

            $rating['autoDataType'] = Str::clear($dataSetting['autoDataType']);

			if($rating['autoDataType'] == 'handmade') {

                if (empty($data['handmade'])) {
                    $result['message'] = 'Không được bỏ trống dữ liệu đánh giá mẫu';
                    $result['status']  = 'error';
                    return $result;
                }

				$dataAuto = $data['handmade'];

				$count = 0;

                foreach ($dataAuto as $key => $item) {

                    $item['id'] = $key;

                    if(empty($item['name'])) {
						$result['status'] = 'error';
                        $result['message']  = 'Không được để trống giá trị tên khách hàng';
                        return $result;
                    }
                    $item['name'] = trim(Str::clear($item['name']));

                    if(empty($item['message'])) {
						$result['status'] = 'error';
                        $result['message']  = 'Không được để trống giá trị nhận xét của khách hàng';
                        return $result;
                    }
                    $item['message'] = trim(Str::clear($item['message']));

                    $dataAuto[$key] = $item;

                    $count++;
                }

				if($count < 30) {
                    $result['message'] = 'Vui lòng nhập ích nhất 30 mẫu đánh giá';
                    $result['status']  = 'error';
                    return $result;
				}

                file_put_contents(RATING_STAR_PATH.'/assets/auto-data.json', json_encode($dataAuto));
			}

            Option::update('rating_star_setting', $rating);
        }

        return $result;
    }
}

add_filter('skd_system_tab' , 'AdminRatingStarSetting::register', 20);
add_action('admin_system_rating_start_html', 'AdminRatingStarSetting::renderDefault', 10);
add_action('admin_system_rating_start_html', 'AdminRatingStarSetting::renderProduct', 20);
add_action('admin_system_rating_start_html', 'AdminRatingStarSetting::renderAuto', 30);
add_action('admin_system_rating_start_html', 'AdminRatingStarSetting::renderAutoData', 40);
add_filter('admin_system_rating_star_save', 'AdminRatingStarSetting::save',10,2);