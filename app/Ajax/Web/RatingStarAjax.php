<?php
namespace RatingStar\Ajax\Web;

use RatingStar\Models\RatingStar;
use RatingStar\Supports\RatingStarHelper;
use SkillDo\Cms\Support\Admin;
use SkillDo\Cms\Support\Metadata;
use SkillDo\Cms\Support\Pagination;
use SkillDo\Http\Request;
use SkillDo\Support\Auth;
use Illuminate\Support\Str;
use SkillDo\Validate\Rule;

class RatingStarAjax
{
    static function reviewLoad(Request $request): void
    {
        $id     =  (int)$request->input('object_id');

        $page   =  (int)$request->input('page');

        $type   =  $request->input('object_type');

        $sort   =  $request->input('sort');

        $review_in_page = 3;

        $query = RatingStar::where('object_id', $id)->where('object_type', $type)->whereIn('status', ['public', 'auto']);

        if(!empty($sort)) 
        {
            if($sort == '1-star') $query->where('star', 1);
            if($sort == '2-star') $query->where('star', 2);
            if($sort == '3-star') $query->where('star', 3);
            if($sort == '4-star') $query->where('star', 4);
            if($sort == '5-star') $query->where('star', 5);
        }
        else 
        {
            $query->orderBy('star', 'desc')->orderBy('created', 'desc');
        }

        $count = (clone $query)->count();

        if($count > 0)
        {
            $config  = [
                'currentPage'   => ($page != 0) ? $page : 1, // Trang hiện tại
                'totalRecords'  => $count, // Tổng số record
                'limit'		    => $review_in_page,
                'url'           => '#review{page}',
            ];

            $pagination = new Pagination($config);

            $query->limit($review_in_page)->offset($pagination->offset());

            $reviews = $query->get();

            $resultReview = '';

            $reply = true;

            if (config('rating-star::config.reply') == 'login' && !Auth::check()) $reply = false;

            if (config('rating-star::config.reply') == 'admin' && (!Auth::check() || !Auth::hasCap('loggin_admin'))) $reply = false;

            foreach ($reviews as $review)
            {
                $review->reply = RatingStar::where('parent_id', $review->id)->where('object_type', 'comment')->get();

                $resultReview .= view('rating-star::review-item', [
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
                'review' => Admin::alert('warning', trans('rating-star::messages.review.empty')),
            ]);
        }

        response()->error(trans('ajax.load.error'));
    }

    static function reviewSave(Request $request): void
    {
        if($request->isMethod('post')) {

            if($request->hasFile('attach'))
            {
                $validate = $request->validate([
                    'attach.*' => Rule::make('File')->file(['jpeg', 'jpg', 'png'], ['max' => '2MB']),
                ]);

                if ($validate->fails())
                {
                    response()->error($validate->errors());
                }
            }

            $id     = (int)$request->input('object_id');

            $type   = $request->input('object_type');

            $module = RatingStarHelper::module($type);

            if(empty($module))
            {
                response()->error(trans('rating-star::messages.review.module.notFound'));
            }

            $object = $module['class']::whereKey($id)->first();

            if(!hasItems($object))
            {
                response()->error(trans('rating-star::messages.review.object.notFound'));
            }

            $rating = [];

            $rating['object_id']    = $id;

            $rating['object_type']  = $type;

            $rating['star']         = (int)$request->input('rating');

            $rating['message']      = $request->input('rating_star_message');

            if(Auth::check())
            {
                $user_current       = Auth::user();
                $rating['name']     = $user_current->firstname.' '.$user_current->lastname;
                $rating['email']    = $user_current->email;
                $rating['user_id']  = $user_current->id;
            }
            else
            {
                $rating['name']     = $request->input('rating_star_name');

                $rating['email']    = $request->input('rating_star_email');

                if($type == 'post' && empty($rating['name']))
                {
                    $rating['name']    = 'guest';

                    $rating['email']   = 'guest_no_isset@empty';

                    $rating['message'] =  'Đánh giá bài viết '.RatingStarHelper::starLabel($rating['star']);
                }
            }

            if(empty($rating['email']) && !empty($request->input('email')))
            {
                $rating['email'] = trim($request->input('email'));
            }

            if($type == 'post' && empty($rating['message']))
            {
                $rating['message'] =  'Đánh giá bài viết '.RatingStarHelper::starLabel($rating['star']);
            }

            if(empty($rating['name']))
            {
                response()->error(trans('rating-star::messages.review.name.empty'));
            }

            if(empty($rating['email']))
            {
                response()->error(trans('rating-star::messages.review.email.empty'));
            }

            if(isset($rating['message']) && strlen($rating['message']) < 10)
            {
                response()->error(trans('rating-star::messages.review.message.empty'));
            }

            if(!empty($illegal_message))
            {
                $illegal_message = explode(',', config('rating-star::config.illegal_message'));

                if (hasItems($illegal_message))
                {
                    foreach ($illegal_message as $illegal)
                    {
                        $illegal = trim($illegal);
                        if (!empty($illegal) && str_contains($rating['message'], $illegal))
                        {
                            response()->error(trans('rating-star::messages.review.message.illegal'));
                        }
                    }
                }
            }

            if($rating['star'] <= 0 || $rating['star'] > 5)
            {
                response()->error(trans('rating-star::messages.review.star.illegal'));
            }

            $has_approving  = config('rating-star::config.has_approving');

            if($has_approving == 1) $rating['status'] = 'pending';

            $errors = apply_filters('rating_star_save_error', [], $rating);

            if(is_skd_error($errors))
            {
                response()->error($errors);
            }

            $id = RatingStar::create($rating);

            if(!is_skd_error($id))
            {
                if($request->hasFile('attach'))
                {
                    if (!file_exists('uploads/rating-star'))
                    {
                        mkdir('uploads/rating-star', 0777, true);
                    }

                    $validate = $request->validate([
                        'attach.*' => Rule::make('File')->file(['jpeg','jpg','png'], ['max' => '2MB']),
                    ]);

                    if ($validate->fails())
                    {
                        response()->error($validate->errors());
                    }

                    $attaches = $request->file('attach');

                    $images = [];

                    foreach ($attaches as $file)
                    {
                        $path = $file->store('rating-star');
                        $images['uploads/'.$path] = str_replace('rating-star/', '', $path);
                    }

                    if(hasItems($images))
                    {
                        Metadata::update('rating_star', $id, 'attach', $images);
                    }
                }

                if($has_approving == 0)
                {
                    $rating_star_product = [];

                    $rating_star_product['count'] = RatingStar::where('object_type', $type)->where('object_id', $rating['object_id'])->amount();

                    $rating_star_product['star']  = RatingStar::where('object_type', $type)->where('object_id', $rating['object_id'])->sum('star');

                    Metadata::update($type, $rating['object_id'], 'rating_star', $rating_star_product);
                }

                do_action('rating_star_save_success', $id, $rating);

                response()->success(trans('rating-star::messages.review.success'));
            }
        }

        response()->error(trans('ajax.save.error'));
    }

    static function reviewReply(Request $request): void
    {
        $data = $request->input();

        $id = (int)$request->input('id');

        $rating_star = RatingStar::get($id);

        if(hasItems($rating_star))
        {
            $rating = [];

            if(Auth::check())
            {
                $user_current = Auth::user();
                $rating['name']           = $user_current->firstname.' '.$user_current->lastname;
                $rating['email']          = $user_current->email;
                $rating['user_id']        = $user_current->id;
            }
            else
            {
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

            $rating['message']  = Str::clear($data['rating_star_message']);

            if(strlen($rating['message']) < 10)
            {
                response()->error(trans('rating-star::messages.review.message.empty'));
            }

            if(!empty($illegal_message))
            {
                $illegal_message = explode(',', config('rating-star::config.illegal_message'));
                if (hasItems($illegal_message))
                {
                    foreach ($illegal_message as $illegal)
                    {
                        $illegal = trim($illegal);

                        if (!empty($illegal) && str_contains($rating['message'], $illegal))
                        {
                            response()->error(trans('rating-star::messages.review.message.illegal'));
                        }
                    }
                }
            }

            $rating['object_id']    = $rating_star->object_id;

            $rating['object_type']  = 'comment';

            $rating['status']       = 'public';

            $rating['star']         = 0;

            $rating['parent_id']    = $rating_star->id;

            $id = RatingStar::create($rating);

            if(!is_skd_error($id))
            {
                response()->success(trans('rating-star::rating.reply.success'));
            }
        }

        response()->error(trans('ajax.save.error'));
    }

    static function reviewLike(Request $request): void
    {
        $id = (int)$request->input('id');

        $rating_star = RatingStar::get($id);

        if(hasItems($rating_star))
        {
            $rating['id']    = $id;

            $rating['like']  = $rating_star->like + 1;

            $id = RatingStar::create($rating, $rating_star);

            if(!is_skd_error($id))
            {
                response()->error(trans('ajax.save.success'));
            }
        }

        response()->error(trans('ajax.save.error'));
    }
}