<?php
Class Rating_Star_Admin_Ajax {
    static function load($ci, $model): void
    {
        $result['status'] 	= 'error';

        $result['message'] 	= 'Load dữ liệu không thành công';

        if(Request::post()) {

            $page   = Request::post('page');

            $limit  = Request::post('limit');

            $status = Request::post('status');

            $star   = (int)Request::post('star');

            $type   = Request::post('type');

            $args = Qr::set();

            if (!empty($star)) {
                $args->where('star', $star);
            }

            if (!empty($status)) {
                $args->where('status', $status);
            }

            if (!empty($type)) {
                if($type == 'auto') {
                    $args->where('type', $type);
                }
                else {
                    $args->where('object_type', $type)->where('type', 'handmade');
                }
            }
            else {
                $args->where('type', 'handmade')->where('object_type', '<>', 'comment');
            }

            $total = RatingStar::count($args);

            # [Pagination]
            $url = '#{page}';

            $pagination = pagination($total, $url, $limit, $page);

            # [Data]
            $args->limit($limit)->offset($pagination->offset())->orderByDesc('created');

            $objects = RatingStar::gets($args);

            $result['data'] = [
                'items' => '',
                'pagination' => base64_encode($pagination->frontend())
            ];

            if(have_posts($objects)) {

                $modules = Rating_Star::module();

                foreach ($objects as $object) {
                    if(isset($modules[$object->object_type])) {
                        $modules[$object->object_type]['listId'][$object->object_id] = $object->object_id;
                    }
                }

                foreach ($modules as $key => $module) {
                    if(!empty($module['listId'])) {
                        $modules[$key]['listData'] = $module['class']::getsData($module['listId']);
                    }
                }

                foreach ($objects as $object) {

                    $object->title = '';

                    $object->slug = '';

                    if(!empty($modules[$object->object_type]['listData'])) {
                        foreach ($modules[$object->object_type]['listData'] as $objData) {
                            if($objData->id == $object->object_id) {
                                $object->title = $objData->title;
                                $object->slug  = $objData->slug;
                                break;
                            }
                        }
                    }

                    $object->reply = RatingStar::count(Qr::set('parent_id', $object->id)->where('object_type', 'comment'));

                    $result['data']['items'] .= Plugin::partial(RATING_STAR_NAME, 'admin/item', ['item' => $object], true);
                }
            }

            $result['data']['items']    = base64_encode($result['data']['items']);
            $result['status'] 	        = 'success';
            $result['message'] 	        = 'Load dữ liệu thành công';
        }

        echo json_encode($result);
    }
    static function commentLoad($ci, $model): void
    {

        $result['status'] = 'error';

        $result['message'] = 'Lưu dữ liệu không thành công.';

        if (Request::post()) {

            $id = (int)Request::post('id');

            $rating_star = RatingStar::get($id);

            if (have_posts($rating_star)) {

                $comments = RatingStar::gets(Qr::set('parent_id', $id)->where('object_type', 'comment'));

                foreach ($comments as $comment) {
                    $comment->avatar = rating_star::getKeyName($comment->name);
                }

                $result['data'] = [
                    'items' => $comments
                ];

                $result['status'] = 'success';

                $result['message'] = 'Load dữ liệu thành công.';
            }
        }

        echo json_encode($result);
    }
    static function commentAdd($ci, $model): bool
    {

        $result['status'] = 'error';

        $result['message'] = 'Lưu dữ liệu không thành công';

        if (Request::post()) {

            $data = Request::post('comment');

            $id   = (int)Request::post('parentId');

            $ratingStar = RatingStar::get($id);

            if (have_posts($ratingStar)) {

                if(empty($data['name'])) {
                    $result['message'] = 'Tên người trả lời không được để trống';
                    echo json_encode($result);
                    return false;
                }

                $rating['name']         = Str::clear($data['name']);

                if(empty($data['email'])) {
                    $result['message'] = 'Email người trả lời không được để trống';
                    echo json_encode($result);
                    return false;
                }

                $rating['email']        = Str::clear($data['email']);

                if (empty($data['content'])) {
                    $result['message'] = 'Không được để trống câu trả lời của bạn.';
                    echo json_encode($result);
                    return false;
                }

                if (strlen($data['content']) < 10) {
                    $result['message'] = 'Nội dung trả lời quá ngắn.';
                    echo json_encode($result);
                    return false;
                }

                $rating['message']      = Str::clear($data['content']);

                $rating['object_id']    = $ratingStar->object_id;

                $rating['object_type']  = 'comment';

                $rating['status']       = 'public';

                $rating['star']         = 0;

                $rating['parent_id']    = $ratingStar->id;

                $id = RatingStar::insert($rating);

                if (!is_skd_error($id)) {

                    $result['data'] = [
                        'item' => [
                            'id'     => $id,
                            'avatar' => rating_star::getKeyName($rating['name']),
                            'created'=> date('Y-m-d H:i:s'),
                            ...$rating,
                        ]
                    ];

                    $result['status'] = 'success';

                    $result['message'] = 'Đăng câu trả lời thành công.';
                }
            }
        }

        echo json_encode($result);

        return true;
    }
    static function commentEdit($ci, $model): bool
    {

        $result['status'] = 'error';

        $result['message'] = 'Lưu dữ liệu không thành công';

        if (Request::post()) {

            $data       = Request::post('comment');

            $id         = (int)Request::post('id');

            $ratingStar = RatingStar::get(Qr::set($id)->where('object_type', 'comment'));

            if (have_posts($ratingStar)) {

                if(empty($data['name'])) {
                    $result['message'] = 'Tên người trả lời không được để trống';
                    echo json_encode($result);
                    return false;
                }

                $ratingStar->name         = Str::clear($data['name']);

                if(empty($data['email'])) {
                    $result['message'] = 'Email người trả lời không được để trống';
                    echo json_encode($result);
                    return false;
                }

                $ratingStar->email        = Str::clear($data['email']);

                if (empty($data['content'])) {
                    $result['message'] = 'Không được để trống câu trả lời của bạn.';
                    echo json_encode($result);
                    return false;
                }

                if (strlen($data['content']) < 10) {
                    $result['message'] = 'Nội dung trả lời quá ngắn.';
                    echo json_encode($result);
                    return false;
                }

                $ratingStar->message      = Str::clear($data['content']);

                $id = RatingStar::insert((array)$ratingStar);

                if (!is_skd_error($id)) {

                    $ratingStar->avatar = rating_star::getKeyName($ratingStar->name);

                    $ratingStar->isActive = 'active';

                    $result['data'] = [
                        'item' => $ratingStar
                    ];

                    $result['status'] = 'success';

                    $result['message'] = 'Cập nhật câu trả lời thành công.';
                }
            }
        }

        echo json_encode($result);

        return true;
    }
    static function commentDelete($ci, $model): void
    {

        $result['status'] = 'error';

        $result['message'] = 'Lưu dữ liệu không thành công.';

        if (Request::post()) {

            $id = (int)Request::post('id');

            $rating_star = RatingStar::get($id);

            if (have_posts($rating_star)) {

                if (RatingStar::delete($id) != 0) {

                    $result['message'] = 'Xóa dữ liệu thành công';

                    $result['status'] = 'success';
                }
            }
        }
        echo json_encode($result);
    }
    static function status($ci, $model): bool
    {
        $result['status'] = 'error';

        $result['message'] = 'Lưu dữ liệu không thành công.';

        if (Request::post()) {

            $id = (int)Request::post('id');

            $ratingStar = RatingStar::get($id);

            if (have_posts($ratingStar)) {

                $status = Request::post('status');

                if(empty($status)) {
                    $result['message'] = 'Trạng thái cần cập nhật không được để trống';
                    echo json_encode($result);
                    return false;
                }

                if($ratingStar->status == $status) {
                    $result['message'] = 'Trạng thái đánh giá không thay đổi';
                    echo json_encode($result);
                    return false;
                }

                $result['data'] = [
                    'status' => ''
                ];

                if ($status == 'hidden') {

                    $result['data']['status'] = '<span class="badge badge-red">Tạm ẩn</span>';

                    $rating_star_product = Metadata::get($ratingStar->object_type, $ratingStar->object_id, 'rating_star', true);

                    if (!have_posts($rating_star_product)) {
                        $rating_star_product = ['count' => 0, 'star' => 0];
                    } else {
                        $rating_star_product['count'] = $rating_star_product['count'] - 1;
                        $rating_star_product['star'] = $rating_star_product['star'] - $ratingStar->star;
                    }
                }

                if ($status == 'public') {
                    $result['data']['status'] = '<span class="badge badge-green">Hiển thị</span>';
                    $rating_star_product = Metadata::get($ratingStar->object_type, $ratingStar->object_id, 'rating_star', true);
                    if (!have_posts($rating_star_product)) {
                        $rating_star_product = array('count' => 0, 'star' => 0);
                    }
                    $rating_star_product['count'] += 1;
                    $rating_star_product['star'] += $ratingStar->star;
                }

                if ($status == 'pending') {
                    $result['data']['status'] = '<span class="badge badge-yellow">Đợi duyệt</span>';
                    if($ratingStar->status == 'public') {
                        $rating_star_product = Metadata::get($ratingStar->object_type, $ratingStar->object_id, 'rating_star', true);
                        if (!have_posts($rating_star_product)) {
                            $rating_star_product = ['count' => 0, 'star' => 0];
                        } else {
                            $rating_star_product['count'] = $rating_star_product['count'] - 1;
                            $rating_star_product['star'] = $rating_star_product['star'] - $ratingStar->star;
                        }
                    }
                }

                if(empty($result['data']['status'])) {
                    $result['message'] = 'Trạng thái đánh giá không đúng định dạng';
                    echo json_encode($result);
                    return false;
                }

                $ratingStar->status = $status;

                if (!is_skd_error(RatingStar::insert((array)$ratingStar))) {

                    if(isset($rating_star_product)) {
                        Metadata::update(
                            $ratingStar->object_type,
                            $ratingStar->object_id,
                            'rating_star',
                            $rating_star_product
                        );
                    }

                    $result['message'] = 'Cập nhật dữ liệu thành công';

                    $result['status'] = 'success';
                }
            }
        }

        echo json_encode($result);

        return false;
    }
    static function save($ci, $model): bool
    {
        $result['status'] = 'error';

        $result['message'] = 'Lưu dữ liệu không thành công';

        if (Request::post()) {

            $data       = Request::post('review');

            $id         = (int)Request::post('id');

            $ratingStar = RatingStar::get($id);

            if (have_posts($ratingStar)) {

                $ratingStarUpdate = ['id' => $ratingStar->id];

                if(empty($data['name'])) {
                    $result['message'] = 'Tên người trả lời không được để trống';
                    echo json_encode($result);
                    return false;
                }
                $ratingStarUpdate['name']         = Str::clear($data['name']);

                if(empty($data['email'])) {
                    $result['message'] = 'Email người trả lời không được để trống';
                    echo json_encode($result);
                    return false;
                }
                $ratingStarUpdate['email']        = Str::clear($data['email']);

                if(!empty($data['phone'])) {
                    $ratingStarUpdate['phone']        = Str::clear($data['phone']);
                }

                if (empty($data['content'])) {
                    $result['message'] = 'Không được để trống câu trả lời của bạn.';
                    echo json_encode($result);
                    return false;
                }

                if (strlen($data['content']) < 10) {
                    $result['message'] = 'Nội dung trả lời quá ngắn.';
                    echo json_encode($result);
                    return false;
                }

                $ratingStarUpdate['message']      = Str::clear($data['content']);

                if (empty($data['star']) || $data['star'] < 1 || $data['star'] > 5) {
                    $result['message'] = 'Điểm đánh giá không được nhỏ hơn 1 hoặc lớn hơn 5';
                    echo json_encode($result);
                    return false;
                }
                $ratingStarUpdate['star']  = Str::clear($data['star']);

                if(empty($data['status'])) {
                    $result['message'] = 'Trạng thái cần cập nhật không được để trống';
                    echo json_encode($result);
                    return false;
                }
                $ratingStarUpdate['status']  = Str::clear($data['status']);

                $ratingStarTotal = Metadata::get($ratingStar->object_type, $ratingStar->object_id, 'rating_star', true);

                if (!have_posts($ratingStarTotal)) {
                    $ratingStarTotal = ['count' => 0, 'star' => 0];
                }

                if($data['star'] != $ratingStar->star && $ratingStar->status == 'public') {
                    $ratingStarTotal['star'] -= $ratingStar->star;
                    $ratingStarTotal['star'] += $data['star'];
                }

                if($ratingStar->status == 'public' && $data['status'] != 'public') {
                    $ratingStarTotal['count'] -= 1;
                    $ratingStarTotal['star'] -= $data['star'];
                }

                $id = RatingStar::insert($ratingStarUpdate);

                if (!is_skd_error($id)) {

                    if(isset($ratingStarTotal)) {
                        Metadata::update(
                            $ratingStar->object_type,
                            $ratingStar->object_id,
                            'rating_star',
                            $ratingStarTotal
                        );
                    }
                    $ratingStar = RatingStar::get($id);
                    $ratingStar->title = '';
                    $ratingStar->slug = '';
                    if($ratingStar->object_type == 'post') {
                        $post = Posts::get(Qr::set($ratingStar->object_id)->select('id', 'title', 'slug'));
                        if(have_posts($post)) {
                            $ratingStar->title = $post->title;
                            $ratingStar->slug  = $post->slug;
                        }
                    }
                    if($ratingStar->object_type == 'products') {
                        $post = Product::get(Qr::set($ratingStar->object_id)->select('id', 'title', 'slug'));
                        if(have_posts($post)) {
                            $ratingStar->title = $post->title;
                            $ratingStar->slug  = $post->slug;
                        }
                    }
                    $ratingStar->reply = RatingStar::count(Qr::set('parent_id', $ratingStar->id)->where('object_type', 'comment'));

                    $result['data'] = [
                        'item' => Plugin::partial(RATING_STAR_NAME, 'admin/item', ['item' => $ratingStar], true)
                    ];

                    $result['status'] = 'success';

                    $result['message'] = 'Cập nhật câu trả lời thành công.';
                }
            }
        }

        echo json_encode($result);

        return true;
    }
    static function delete($ci, $model): bool
    {
        $result['status'] = 'error';

        $result['message'] = 'Xóa dữ liệu không thành công';

        if (Request::post()) {

            $id         = (int)Request::post('id');

            $ratingStar = RatingStar::get($id);

            if (have_posts($ratingStar)) {
                RatingStar::delete($id);
                $result['status'] = 'success';
                $result['message'] = 'xóa dữ liệu thành công';
            }
        }

        echo json_encode($result);

        return true;
    }
    static function randomReview($ci, $model): bool
    {
        $result['status'] = 'error';

        $result['message'] = 'Xóa dữ liệu không thành công';

        if (Request::post()) {

            $data = Request::post('data');

            if(!have_posts($data)) {
                $result['message'] = 'Không có sản phẩm nào được chọn';
                echo json_encode($result);
                return true;
            }

            foreach ($data as $productId) {
                Rating_Star_Admin_Product::randomRatingStar($productId, 'products');
            }

            $result['status'] = 'success';

            $result['message'] = 'Thêm đánh giá cho sản phẩm thành công';
        }

        echo json_encode($result);

        return true;
    }

}
Ajax::admin('Rating_Star_Admin_Ajax::load');
Ajax::admin('Rating_Star_Admin_Ajax::commentLoad');
Ajax::admin('Rating_Star_Admin_Ajax::commentAdd');
Ajax::admin('Rating_Star_Admin_Ajax::commentEdit');
Ajax::admin('Rating_Star_Admin_Ajax::commentDelete');
Ajax::admin('Rating_Star_Admin_Ajax::status');
Ajax::admin('Rating_Star_Admin_Ajax::save');
Ajax::admin('Rating_Star_Admin_Ajax::delete');
Ajax::admin('Rating_Star_Admin_Ajax::randomReview');