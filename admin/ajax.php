<?php
Class Rating_Star_Admin_Ajax {

    static function load(\SkillDo\Http\Request $request, $model): void
    {
        if($request->isMethod('post')) {

            $page   = $request->input('page');

            $limit  = $request->input('limit');

            $status = $request->input('status');

            $star   = (int)$request->input('star');

            $type   = $request->input('type');

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

            $result = [
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

                    $result['items'] .= Plugin::partial(RATING_STAR_NAME, 'admin/item', ['item' => $object]);
                }
            }

            $result['items']    = base64_encode($result['items']);

            response()->success(trans('ajax.load.success'), $result);
        }

        response()->error(trans('ajax.load.error'));
    }
    static function commentLoad(\SkillDo\Http\Request $request, $model): void
    {
        if($request->isMethod('post')) {

            $id = (int)$request->input('id');

            $rating_star = RatingStar::get($id);

            if (have_posts($rating_star)) {

                $comments = RatingStar::gets(Qr::set('parent_id', $id)->where('object_type', 'comment'));

                foreach ($comments as $comment) {
                    $comment->avatar = rating_star::getKeyName($comment->name);
                }

                response()->success(trans('ajax.load.success'), [
                    'items' => $comments
                ]);
            }
        }

        response()->error(trans('ajax.load.error'));
    }
    static function commentAdd(\SkillDo\Http\Request $request, $model): bool
    {
        if($request->isMethod('post')) {

            $data = $request->input('comment');

            $id   = (int)$request->input('parentId');

            $ratingStar = RatingStar::get($id);

            if (have_posts($ratingStar)) {

                if(empty($data['name'])) {
                    response()->error(trans('Tên người trả lời không được để trống'));
                }

                if(empty($data['email'])) {
                    response()->error(trans('Email người trả lời không được để trống'));
                }

                if (empty($data['content'])) {
                    response()->error(trans('Không được để trống câu trả lời của bạn'));
                }

                if (strlen($data['content']) < 10) {
                    response()->error(trans('Nội dung trả lời quá ngắn'));
                }

                $rating['name'] = Str::clear($data['name']);

                $rating['email'] = Str::clear($data['email']);

                $rating['message']      = Str::clear($data['content']);

                $rating['object_id']    = $ratingStar->object_id;

                $rating['object_type']  = 'comment';

                $rating['status']       = 'public';

                $rating['star']         = 0;

                $rating['parent_id']    = $ratingStar->id;

                $id = RatingStar::insert($rating);

                if (!is_skd_error($id)) {

                    response()->success(trans('Đăng câu trả lời thành công'), [
                        'item' => [
                            'id'     => $id,
                            'avatar' => rating_star::getKeyName($rating['name']),
                            'created'=> date('Y-m-d H:i:s'),
                            ...$rating,
                        ]
                    ]);
                }
            }
        }

        response()->error(trans('ajax.add.error'));
    }
    static function commentEdit(\SkillDo\Http\Request $request, $model): bool
    {
        if($request->isMethod('post')) {

            $data       = $request->input('comment');

            $id         = (int)$request->input('id');

            $ratingStar = RatingStar::get(Qr::set($id)->where('object_type', 'comment'));

            if (have_posts($ratingStar)) {

                if(empty($data['name'])) {
                    response()->error(trans('Tên người trả lời không được để trống'));
                }

                if(empty($data['email'])) {
                    response()->error(trans('Email người trả lời không được để trống'));
                }

                if (empty($data['content'])) {
                    response()->error(trans('Không được để trống câu trả lời của bạn'));
                }

                if (strlen($data['content']) < 10) {
                    response()->error(trans('Nội dung trả lời quá ngắn'));
                }

                $ratingStar->name         = Str::clear($data['name']);

                $ratingStar->email        = Str::clear($data['email']);

                $ratingStar->message      = Str::clear($data['content']);

                $id = RatingStar::insert((array)$ratingStar);

                if (!is_skd_error($id)) {

                    $ratingStar->avatar = rating_star::getKeyName($ratingStar->name);

                    $ratingStar->isActive = 'active';

                    response()->success(trans('Cập nhật câu trả lời thành công'), [
                        'item' => $ratingStar
                    ]);
                }
            }
        }

        response()->error(trans('ajax.save.error'));
    }
    static function commentDelete(\SkillDo\Http\Request $request, $model): void
    {
        if($request->isMethod('post')) {

            $id = (int)$request->input('id');

            $rating_star = RatingStar::get($id);

            if (have_posts($rating_star)) {

                if (RatingStar::delete($id) != 0) {

                    response()->success(trans('ajax.delete.success'));
                }
            }
        }

        response()->error(trans('ajax.delete.error'));
    }
    static function status(\SkillDo\Http\Request $request, $model): bool
    {
        if($request->isMethod('post')) {

            $id = (int)$request->input('id');

            $ratingStar = RatingStar::get($id);

            if (have_posts($ratingStar)) {

                $status = $request->input('status');

                if(empty($status)) {
                    response()->error(trans('Trạng thái cần cập nhật không được để trống'));
                }

                if($ratingStar->status == $status) {
                    response()->error(trans('Trạng thái đánh giá không thay đổi'));
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
                    response()->error(trans('Trạng thái đánh giá không đúng định dạng'));
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

                    response()->success(trans('ajax.save.success'));
                }
            }
        }

        response()->error(trans('ajax.save.error'));
    }
    static function save(\SkillDo\Http\Request $request, $model): bool
    {
        if($request->isMethod('post')) {

            $data       = $request->input('review');

            $id         = (int)$request->input('id');

            $ratingStar = RatingStar::get($id);

            if (have_posts($ratingStar)) {

                $ratingStarUpdate = ['id' => $ratingStar->id];

                if(empty($data['name'])) {
                    response()->error(trans('Tên người trả lời không được để trống'));
                }
                $ratingStarUpdate['name']         = Str::clear($data['name']);

                if(empty($data['email'])) {
                    response()->error(trans('Email người trả lời không được để trống'));
                }
                $ratingStarUpdate['email']        = Str::clear($data['email']);

                if(!empty($data['phone'])) {
                    $ratingStarUpdate['phone']        = Str::clear($data['phone']);
                }

                if (empty($data['content'])) {
                    response()->error(trans('Không được để trống câu trả lời của bạn'));
                }

                if (strlen($data['content']) < 10) {
                    response()->error(trans('Nội dung trả lời quá ngắn'));
                }

                $ratingStarUpdate['message']      = Str::clear($data['content']);

                if (empty($data['star']) || $data['star'] < 1 || $data['star'] > 5) {
                    response()->error(trans('Điểm đánh giá không được nhỏ hơn 1 hoặc lớn hơn 5'));
                }
                $ratingStarUpdate['star']  = Str::clear($data['star']);

                if(empty($data['status'])) {
                    response()->error(trans('Trạng thái cần cập nhật không được để trống'));
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

                    response()->success(trans('ajax.save.success'), [
                        'item' => Plugin::partial(RATING_STAR_NAME, 'admin/item', ['item' => $ratingStar])
                    ]);
                }
            }
        }

        response()->error(trans('ajax.save.error'));
    }
    static function delete(\SkillDo\Http\Request $request, $model): bool
    {
        if($request->isMethod('post')) {

            $id         = (int)$request->input('id');

            $ratingStar = RatingStar::get($id);

            if (have_posts($ratingStar)) {

                RatingStar::delete($id);

                response()->success(trans('ajax.delete.success'));
            }
        }

        response()->error(trans('ajax.delete.error'));
    }
    static function randomReview(\SkillDo\Http\Request $request, $model): bool
    {
        if($request->isMethod('post')) {

            $data = $request->input('data');

            if(!have_posts($data)) {
                response()->error(trans('Không có sản phẩm nào được chọn'));
            }

            foreach ($data as $productId) {
                Rating_Star_Admin_Product::randomRatingStar($productId, 'products');
            }

            response()->success(trans('Thêm đánh giá cho sản phẩm thành công'));
        }

        response()->error(trans('ajax.delete.error'));
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