<?php
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
        $form = form();
        $form
            ->switch('rating_star_setting[product_enable]', [
                'label' => 'Bật / Tắt đánh giá sản phẩm',
                'note' => 'Bật tùy chọn này khi sử dụng đánh giá sao trong sản phẩm.',
                'start' => 4
            ], $config['product_enable']);

        $form->switch('rating_star_setting[post_enable]', [
            'label' => 'Bật / Tắt đánh giá bài viết',
            'note' => 'Bật tùy chọn này khi sử dụng đánh giá sao trong bài viết.',
            'start' => 4
        ], $config['post_enable']);

        $form->switch('rating_star_setting[has_approving]', [
            'label' => 'Duyệt đánh giá',
            'note' => 'Cấu hình cho phép chủ cửa hàng duyệt các đánh giá sản phẩm trước khi cho hiển thị.',
            'start' => 4
        ], $config['has_approving']);

        $form->textarea('rating_star_setting[illegal_message]', [
            'label' => 'Từ khóa không cho phép',
            'note' => 'Mỗi từ khóa cách nhau bằng dầu ","'
        ], $config['illegal_message']);

        Admin::view('components/system-default', [
            'title'       => 'Cấu hình chung',
            'description' => 'Quản lý màu sắc hệ thống admin',
            'form'        => $form
        ]);
    }

    static public function renderAuto($config): void {
        $form = form();
        $form->switch('rating_star_setting[auto_enable]', [
            'label' => 'Bật / tắt tự động tạo đánh giá',
            'note'  => 'Tự động tạo đánh giá khi tạo mới sản phẩm',
            'start' => 4
        ], $config['auto_enable']);

        $form->number('rating_star_setting[auto_min_number]', [
            'label' => 'Số đánh giá nhỏ nhất tạo ra',
            'min' => 0,
            'start' => 4
        ], $config['auto_min_number']);

        $form->number('rating_star_setting[auto_max_number]', [
            'label' => 'Số đánh giá lớn nhất tạo ra',
            'min' => 3,
            'max' => 10,
            'start' => 4
        ], $config['auto_max_number']);

        $form->number('rating_star_setting[auto_percent_5]', [
            'label' => 'Tỉ lệ ra 5 sao',
            'min' => 0,
            'max' => 100,
            'start' => 4
        ], $config['auto_percent_5']);

        $form->number('rating_star_setting[auto_percent_4]', [
            'label' => 'Tỉ lệ ra 4 sao',
            'min' => 0,
            'max' => 100,
            'start' => 4
        ], $config['auto_percent_4']);

        $form->number('rating_star_setting[auto_percent_3]', [
            'label' => 'Tỉ lệ ra 3 sao',
            'min' => 0,
            'max' => 100,
            'start' => 4
        ], $config['auto_percent_3']);

        Admin::view('components/system-default', [
            'title'       => 'Cấu hình tự động đánh giá',
            'description' => 'Quản lý hệ thống tự động đánh giá sản phẩm',
            'form'        => $form
        ]);
    }

    static public function renderAutoData($config): void {

        $dataAuto = file_get_contents(RATING_STAR_PATH.'/assets/auto-data.json');

        Admin::view('components/system-default', [
            'title'       => 'Dữ liệu tự động đánh giá',
            'description' => 'Quản lý dữ liệu sẽ tự động đánh giá',
            'form'        => Plugin::partial('rating-star', 'admin/setting-data', ['config' => $config, 'dataAuto' => $dataAuto])
        ]);
    }

    static public function save(SkillDo\Http\Request $request) {

        if($request->has('rating_star_setting')) {

            $dataSetting = $request->input('rating_star_setting');

            $rating['product_enable']  = Str::clear($dataSetting['product_enable']);
            $rating['post_enable']     = Str::clear($dataSetting['post_enable']);
            $rating['has_approving']   = Str::clear($dataSetting['has_approving']);
            $rating['illegal_message'] = trim(Str::clear($dataSetting['illegal_message']));
            $rating['illegal_message'] = trim($rating['illegal_message'], ',');
            //$rating['reply']           = Str::clear($dataSetting['reply']);
            $rating['auto_enable']     = Str::clear($dataSetting['auto_enable']);
            $rating['auto_min_number'] = (int)Str::clear($dataSetting['auto_min_number']);
            $rating['auto_max_number'] = (int)Str::clear($dataSetting['auto_max_number']);
            $rating['auto_percent_5']  = (int)Str::clear($dataSetting['auto_percent_5']);
            $rating['auto_percent_4']  = (int)Str::clear($dataSetting['auto_percent_4']);
            $rating['auto_percent_3']  = (int)Str::clear($dataSetting['auto_percent_3']);

            if ($rating['auto_min_number'] > $rating['auto_max_number']) {
                response()->error('Số đánh giá nhỏ nhất tạo ra không thể lớn hơn số đánh giá lớn nhất.');
            }

            $rating['autoDataType'] = Str::clear($dataSetting['autoDataType']);

            if($rating['autoDataType'] == 'handmade') {

                if (empty($request->input('handmade'))) {
                    response()->error('Không được bỏ trống dữ liệu đánh giá mẫu');
                }

                $dataAuto = $request->input('handmade');

                $count = 0;

                foreach ($dataAuto as $key => $item) {

                    $item['id'] = $key;

                    if(empty($item['name'])) {
                        response()->error('Không được để trống giá trị tên khách hàng');
                    }
                    $item['name'] = trim(Str::clear($item['name']));

                    if(empty($item['message'])) {
                        response()->error('Không được để trống giá trị nhận xét của khách hàng');
                    }
                    $item['message'] = trim(Str::clear($item['message']));

                    $dataAuto[$key] = $item;

                    $count++;
                }

                if($count < 30) {
                    response()->error('Vui lòng nhập ích nhất 30 mẫu đánh giá');
                }

                file_put_contents(RATING_STAR_PATH.'/assets/auto-data.json', json_encode($dataAuto));
            }

            Option::update('rating_star_setting', $rating);
        }
    }
}

add_filter('skd_system_tab' , 'AdminRatingStarSetting::register', 20);
add_action('admin_system_rating_start_html', 'AdminRatingStarSetting::renderDefault', 10);
add_action('admin_system_rating_start_html', 'AdminRatingStarSetting::renderAuto', 30);
add_action('admin_system_rating_start_html', 'AdminRatingStarSetting::renderAutoData', 40);
add_action('admin_system_rating_star_save', 'AdminRatingStarSetting::save',10);