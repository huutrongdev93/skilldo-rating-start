<?php

use JetBrains\PhpStorm\NoReturn;
use SkillDo\Validate\Rule;

Class Rating_Star_Ajax {
    #[NoReturn]
    static function reviewLoad(SkillDo\Http\Request $request, $model): void
    {
        if($request->isMethod('post')) {

            $id     =  (int)$request->input('object_id');

            $page     =  (int)$request->input('page');

            $type   =  $request->input('object_type');

            $sort   =  $request->input('sort');

            $review_in_page = 3;

            $args = Qr::set('object_id', $id)->where('object_type', $type)->whereIn('status', ['public', 'auto']);

            if(!empty($sort)) {
                if($sort == '1-star') $args->where('star', 1);
                if($sort == '2-star') $args->where('star', 2);
                if($sort == '3-star') $args->where('star', 3);
                if($sort == '4-star') $args->where('star', 4);
                if($sort == '5-star') $args->where('star', 5);
            }
            else {
                $args->orderBy('star', 'desc')->orderBy('created', 'desc');
            }

            $count = RatingStar::count($args);

            if($count > 0) {

                $config  = [
                    'currentPage'   => ($page != 0) ? $page : 1, // Trang hiện tại
                    'totalRecords'  => $count, // Tổng số record
                    'limit'		    => $review_in_page,
                    'url'           => '#review{page}',
                ];

                $pagination = new Pagination($config);

                $args->limit($review_in_page)->offset($pagination->offset());

                $reviews = RatingStar::gets($args);

                $resultReview = '';

                $reply = true;

                if (RatingStar::config('reply') == 'login' && !Auth::check()) $reply = false;

                if (RatingStar::config('reply') == 'admin' && (!Auth::check() || !Auth::hasCap('loggin_admin'))) $reply = false;

                foreach ($reviews as $review) {

                    $review->reply = RatingStar::gets(Qr::set('parent_id', $review->id)->where('object_type', 'comment'));

                    $resultReview .= Plugin::partial(RATING_STAR_NAME, 'review-item', [
                        'type'  => $type,
                        'review' => $review,
                        'reply' => $reply
                    ]);
                }

                response()->success(trans('ajax.load.success'), [
                    'pagination' => $pagination->frontend(),
                    'review' => $resultReview,
                ]);
            }
            else {

                response()->success(trans('ajax.load.success'), [
                    'pagination' => '',
                    'review' => Admin::alert('warning', trans('messages.rating.review.empty')),
                ]);
            }
        }

        response()->error(trans('ajax.load.error'));
    }

    #[NoReturn]
    static function reviewSave(SkillDo\Http\Request $request, $model): void
    {
        if($request->isMethod('post')) {

            $id     = (int)$request->input('object_id');

            $type   = $request->input('object_type');

            $module = Rating_Star::module($type);

            if(empty($module)) {
                response()->error(trans('messages.rating.review.module.notFound'));
            }

            $object = $module['class']::get($id);

            if(!have_posts($object)) {
                response()->error(trans('messages.rating.review.object.notFound'));
            }

            $rating = [];

            $rating['object_id']    = $id;

            $rating['object_type']  = $type;

            $rating['star']         = (int)$request->input('rating');

            $rating['message']      = $request->input('rating_star_message');

            if(Auth::check()) {
                $user_current       = Auth::user();
                $rating['name']     = $user_current->firstname.' '.$user_current->lastname;
                $rating['email']    = $user_current->email;
                $rating['user_id']  = $user_current->id;
            }
            else {
                $rating['name']     = $request->input('rating_star_name');
                $rating['email']    = $request->input('rating_star_email');
                if($type == 'post' && empty($rating['name'])) {
                    $rating['name']    = 'guest';
                    $rating['email']   = 'guest_no_isset@empty';
                    $rating['message'] =  'Đánh giá bài viết '.RatingStar::starLabel($rating['star']);
                }
            }

            if(empty($rating['email']) && !empty($request->input('email'))) {
                $rating['email'] = trim($request->input('email'));
            }

            if($type == 'post' && empty($rating['message'])) {
                $rating['message'] =  'Đánh giá bài viết '.RatingStar::starLabel($rating['star']);
            }

            if(empty($rating['name'])) {
                response()->error(trans('messages.rating.review.name.empty'));
            }

            if(empty($rating['email'])) {
                response()->error(trans('messages.rating.review.email.empty'));
            }

            if(isset($rating['message']) && strlen($rating['message']) < 10) {
                response()->error(trans('messages.rating.review.message.empty'));
            }

            if(!empty($illegal_message)) {
                $illegal_message = explode(',', RatingStar::config('illegal_message'));
                if (have_posts($illegal_message)) {
                    foreach ($illegal_message as $illegal) {
                        $illegal = trim($illegal);
                        if (!empty($illegal) && str_contains($rating['message'], $illegal)) {
                            response()->error(trans('messages.rating.review.message.illegal'));
                        }
                    }
                }
            }

            if($rating['star'] <= 0 || $rating['star'] > 5) {
                response()->error(trans('messages.rating.review.star.illegal'));
            }

            $has_approving  = RatingStar::config('has_approving');

            if($has_approving == 1) $rating['status'] = 'pending';

            $errors = apply_filters('rating_star_save_error', [], $rating);

            if(is_skd_error($errors)) {
                response()->error($errors);
            }

            $id = RatingStar::insert($rating);

            if(!is_skd_error($id)) {

                if($request->hasFile('attach')) {

                    if (!file_exists('uploads/rating-star')) {
                        mkdir('uploads/rating-star', 0777, true);
                    }

                    $validate = $request->validate([
                        'attach.*' => Rule::make('File')->file(['jpeg','jpg','png'], ['max' => '2MB']),
                    ]);

                    if ($validate->fails()) {
                        response()->error($validate->errors());
                    }

                    $attaches = $request->file('attach');

                    $images = [];

                    foreach ($attaches as $key => $file) {
                        $path = $file->store('rating-star');
                        $images['uploads/'.$path] = str_replace('rating-star/', '', $path);
                    }

                    if(have_posts($images)) {
                        Metadata::update('rating_star', $id, 'attach', $images);
                    }
                }

                if($has_approving == 0) {

                    $rating_star_product = [];

                    $rating_star_product['count'] = RatingStar::where('object_type', $type)->where('object_id', $rating['object_id'])->amount();

                    $rating_star_product['star']  = RatingStar::where('object_type', $type)->where('object_id', $rating['object_id'])->sum('star');

                    Metadata::update($type, $rating['object_id'], 'rating_star', $rating_star_product);
                }

                do_action('rating_star_save_success', $id, $rating);

                response()->success(trans('messages.rating.review.success'));
            }
        }

        response()->error(trans('ajax.save.error'));
    }

    #[NoReturn]
    static function reviewReply(SkillDo\Http\Request $request, $model): void
    {

        if($request->isMethod('post')) {

            $data = $request->input();

            $id = (int)$request->input('id');

            $rating_star = RatingStar::get($id);

            if(have_posts($rating_star)) {

                $rating = [];

                if(Auth::check()) {
                    $user_current = Auth::user();
                    $rating['name']           = $user_current->firstname.' '.$user_current->lastname;
                    $rating['email']          = $user_current->email;
                    $rating['user_id']        = $user_current->id;
                }
                else {

                    $validate = $request->validate([
                        'rating_star_name' => Rule::make('Tên')->notEmpty(),
                        'rating_star_email' => Rule::make('Email')->notEmpty()->email(),
                    ]);

                    if ($validate->fails()) {
                        response()->error($validate->errors());
                    }

                    $rating['name'] = $request->input('rating_star_name');

                    $rating['email'] = $request->input('rating_star_email');
                }

                $rating['message']     = Str::clear($data['rating_star_message']);

                if(strlen($rating['message']) < 10) {
                    response()->error(trans('messages.rating.review.message.empty'));
                }

                if(!empty($illegal_message)) {
                    $illegal_message = explode(',', RatingStar::config('illegal_message'));
                    if (have_posts($illegal_message)) {
                        foreach ($illegal_message as $illegal) {
                            $illegal = trim($illegal);
                            if (!empty($illegal) && str_contains($rating['message'], $illegal)) {
                                response()->error(trans('messages.rating.review.message.illegal'));
                            }
                        }
                    }
                }

                $rating['object_id']    = $rating_star->object_id;

                $rating['object_type']  = 'comment';

                $rating['status']       = 'public';

                $rating['star']         = 0;

                $rating['parent_id']    = $rating_star->id;

                $id = RatingStar::insert($rating);

                if(!is_skd_error($id)) {

                    response()->success(trans('rating.reply.success'));
                }
            }
        }

        response()->error(trans('ajax.save.error'));
    }

    #[NoReturn]
    static function reviewLike(SkillDo\Http\Request $request, $model): void
    {
        if($request->isMethod('post')) {

            $id = (int)$request->input('id');

            $rating_star = RatingStar::get($id);

            if(have_posts($rating_star)) {

                $rating['id']    = $id;

                $rating['like']  = $rating_star->like + 1;

                $id = RatingStar::insert($rating, $rating_star);

                if(!is_skd_error($id)) {

                    response()->error(trans('ajax.save.success'));
                }
            }
        }

        response()->error(trans('ajax.save.error'));
    }
}

Ajax::client('Rating_Star_Ajax::reviewLoad');
Ajax::client('Rating_Star_Ajax::reviewSave');
Ajax::client('Rating_Star_Ajax::reviewReply');
Ajax::client('Rating_Star_Ajax::reviewLike');
