<?php
namespace RatingStar\Ajax\Admin;

use Ecommerce\Models\Product;
use RatingStar\Models\RatingStar;
use RatingStar\Modules\Admin\RatingStarProducts;
use RatingStar\Supports\RatingStarHelper;
use SkillDo\Cms\Models\Post;
use SkillDo\Cms\Support\Metadata;
use SkillDo\Http\Request;
use Illuminate\Support\Str;
use SkillDo\Validate\Rule;

class RatingStarAjax
{
    static function load(Request $request): void
    {
        $page   = $request->input('page');

        $limit  = $request->input('limit');

        $status = $request->input('status');

        $star   = (int)$request->input('star');

        $type   = $request->input('type');

        $query = RatingStar::query();

        if (!empty($star)) 
        {
            $query->where('star', $star);
        }

        if (!empty($status)) 
        {
            $query->where('status', $status);
        }

        if (!empty($type)) 
        {
            if($type == 'auto') 
            {
                $query->where('type', $type);
            }
            else 
            {
                $query->where('object_type', $type)->where('type', 'handmade');
            }
        }
        else 
        {
            $query->where('type', 'handmade')->where('object_type', '<>', 'comment');
        }

        $total = (clone $query)->count();

        # [Pagination]
        $url = '#{page}';

        $pagination = pagination($total, $url, $limit, $page);

        # [Data]
        $query->limit($limit)->offset($pagination->offset())->orderByDesc('created');

        $objects = $query->get();

        $result = [
            'items' => '',
            'pagination' => base64_encode($pagination->frontend())
        ];

        if(hasItems($objects)) {

            $modules = RatingStarHelper::module();

            foreach ($objects as $object) 
            {
                if(isset($modules[$object->object_type])) 
                {
                    $modules[$object->object_type]['listId'][$object->object_id] = $object->object_id;
                }
            }

            foreach ($modules as $key => $module) 
            {
                if(!empty($module['listId'])) 
                {
                    $modules[$key]['listData'] = $module['class']::whereKey($module['listId'])->get();
                }
            }

            foreach ($objects as $object) 
            {
                $object->title = '';

                $object->slug = '';

                if(!empty($modules[$object->object_type]['listData'])) 
                {
                    foreach ($modules[$object->object_type]['listData'] as $objData) 
                    {
                        if($objData->id == $object->object_id)
                        {
                            $object->title = $objData->title;
                            $object->slug  = $objData->slug;
                            break;
                        }
                    }
                }

                $object->reply = RatingStar::where('parent_id', $object->id)->where('object_type', 'comment')->count();

                $result['items'] .= view('rating-star::admin/item', ['item' => $object]);
            }
        }

        $result['items']    = base64_encode($result['items']);

        response()->success(trans('ajax.load.success'), $result);
    }

    static function commentLoad(Request $request): void
    {
        $id = (int)$request->input('id');

        $rating_star = RatingStar::find($id);

        if(hasItems($rating_star))
        {
            $comments = RatingStar::where('parent_id', $id)->where('object_type', 'comment')->get();

            foreach ($comments as $comment)
            {
                $comment->avatar = RatingStarHelper::getKeyName($comment->name);
            }

            response()->success(trans('ajax.load.success'), [
                'items' => $comments
            ]);
        }

        response()->error(trans('ajax.load.error'));
    }

    static function commentAdd(Request $request): void
    {
        $validate = $request->validate([
            'comment.name' => Rule::make('Tên người trả lời')->notEmpty(),
            'comment.email' => Rule::make('Email người trả lời')->notEmpty()->email(),
            'comment.content' => Rule::make('câu trả lời của bạn')->notEmpty()->string()->min(10),
            'comment.star' => Rule::make('Điểm đánh giá')->notEmpty()->integer()->min(1)->max(5),
            'comment.status' => Rule::make('Trạng thái cần cập nhật')->notEmpty(),
            'parentId' => Rule::make('id đánh giá')->notEmpty()->integer(),
        ]);

        if ($validate->fails())
        {
            response()->error($validate->errors());
        }

        $data = $request->input('comment');

        $id   = (int)$request->input('parentId');

        $ratingStar = RatingStar::find($id);

        if (hasItems($ratingStar))
        {
            $rating['name'] = Str::clear($data['name']);

            $rating['email'] = Str::clear($data['email']);

            $rating['message']      = Str::clear($data['content']);

            $rating['object_id']    = $ratingStar->object_id;

            $rating['object_type']  = 'comment';

            $rating['status']       = 'public';

            $rating['star']         = 0;

            $rating['parent_id']    = $ratingStar->id;

            $id = RatingStar::create($rating);

            if (!is_skd_error($id))
            {
                response()->success(trans('Đăng câu trả lời thành công'), [
                    'item' => [
                        'id'     => $id,
                        'avatar' => RatingStarHelper::getKeyName($rating['name']),
                        'created'=> date('Y-m-d H:i:s'),
                        ...$rating,
                    ]
                ]);
            }
        }

        response()->error(trans('ajax.add.error'));
    }

    static function commentEdit(Request $request): void
    {
        $validate = $request->validate([
            'comment.name' => Rule::make('Tên người trả lời')->notEmpty(),
            'comment.email' => Rule::make('Email người trả lời')->notEmpty()->email(),
            'comment.content' => Rule::make('câu trả lời của bạn')->notEmpty()->string()->min(10),
            'comment.star' => Rule::make('Điểm đánh giá')->notEmpty()->integer()->min(1)->max(5),
            'comment.status' => Rule::make('Trạng thái cần cập nhật')->notEmpty(),
            'id' => Rule::make('id câu trả lời')->notEmpty()->integer(),
        ]);

        if ($validate->fails())
        {
            response()->error($validate->errors());
        }

        $data       = $request->input('comment');

        $id         = (int)$request->input('id');

        $ratingStar = RatingStar::whereKey($id)->where('object_type', 'comment')->first();

        if (hasItems($ratingStar))
        {
            $ratingStar->name         = Str::clear($data['name']);

            $ratingStar->email        = Str::clear($data['email']);

            $ratingStar->message      = Str::clear($data['content']);

            $ratingStar->save();

            $ratingStar->avatar = RatingStarHelper::getKeyName($ratingStar->name);

            $ratingStar->isActive = 'active';

            response()->success(trans('Cập nhật câu trả lời thành công'), [
                'item' => $ratingStar
            ]);
        }

        response()->error(trans('ajax.save.error'));
    }

    static function commentDelete(Request $request): void
    {
        $id = (int)$request->input('id');

        $rating_star = RatingStar::find($id);

        if (hasItems($rating_star))
        {
            if (RatingStar::whereKey($id)->delete() != 0)
            {
                response()->success(trans('ajax.delete.success'));
            }
        }

        response()->error(trans('ajax.delete.error'));
    }

    static function status(Request $request): void
    {
        $id = (int)$request->input('id');

        $ratingStar = RatingStar::find($id);

        if (hasItems($ratingStar)) {

            $status = $request->input('status');

            if(empty($status))
            {
                response()->error(trans('Trạng thái cần cập nhật không được để trống'));
            }

            if($ratingStar->status == $status)
            {
                response()->error(trans('Trạng thái đánh giá không thay đổi'));
            }

            $result['data'] = [
                'status' => ''
            ];

            if ($status == 'hidden')
            {
                $result['data']['status'] = '<span class="badge badge-red">Tạm ẩn</span>';

                $rating_star_product = Metadata::get($ratingStar->object_type, $ratingStar->object_id, 'rating_star', true);

                if (!hasItems($rating_star_product))
                {
                    $rating_star_product = ['count' => 0, 'star' => 0];
                }
                else
                {
                    $rating_star_product['count'] = $rating_star_product['count'] - 1;
                    $rating_star_product['star'] = $rating_star_product['star'] - $ratingStar->star;
                }
            }

            if ($status == 'public')
            {
                $result['data']['status'] = '<span class="badge badge-green">Hiển thị</span>';
                $rating_star_product = Metadata::get($ratingStar->object_type, $ratingStar->object_id, 'rating_star', true);
                if (!hasItems($rating_star_product))
                {
                    $rating_star_product = array('count' => 0, 'star' => 0);
                }
                $rating_star_product['count'] += 1;
                $rating_star_product['star'] += $ratingStar->star;
            }

            if ($status == 'pending')
            {
                $result['data']['status'] = '<span class="badge badge-yellow">Đợi duyệt</span>';
                if($ratingStar->status == 'public')
                {
                    $rating_star_product = Metadata::get($ratingStar->object_type, $ratingStar->object_id, 'rating_star', true);
                    if (!hasItems($rating_star_product))
                    {
                        $rating_star_product = ['count' => 0, 'star' => 0];
                    }
                    else
                    {
                        $rating_star_product['count'] = $rating_star_product['count'] - 1;
                        $rating_star_product['star'] = $rating_star_product['star'] - $ratingStar->star;
                    }
                }
            }

            if(empty($result['data']['status']))
            {
                response()->error(trans('Trạng thái đánh giá không đúng định dạng'));
            }

            $ratingStar->status = $status;

            $ratingStar->save();

            if(isset($rating_star_product))
            {
                Metadata::update(
                    $ratingStar->object_type,
                    $ratingStar->object_id,
                    'rating_star',
                    $rating_star_product
                );
            }

            response()->success(trans('ajax.save.success'));
        }

        response()->error(trans('ajax.save.error'));
    }

    static function save(Request $request): void
    {
        $validate = $request->validate([
            'review.name' => Rule::make('Tên người trả lời')->notEmpty(),
            'review.email' => Rule::make('Email người trả lời')->notEmpty()->email(),
            'review.content' => Rule::make('câu trả lời của bạn')->notEmpty()->string()->min(10),
            'review.star' => Rule::make('Điểm đánh giá')->notEmpty()->integer()->min(1)->max(5),
            'review.status' => Rule::make('Trạng thái cần cập nhật')->notEmpty(),
        ]);

        if ($validate->fails())
        {
            response()->error($validate->errors());
        }

        $data       = $request->input('review');

        $id         = (int)$request->input('id');

        $ratingStar = RatingStar::find($id);

        if (hasItems($ratingStar))
        {
            $ratingStar->name = Str::clear($data['name']);

            $ratingStar->email = Str::clear($data['email']);

            if(!empty($data['phone']))
            {
                $ratingStar->phone = Str::clear($data['phone']);
            }

            $ratingStar->message = Str::clear($data['content']);

            $ratingStarTotal = Metadata::get($ratingStar->object_type, $ratingStar->object_id, 'rating_star', true);

            if (!hasItems($ratingStarTotal))
            {
                $ratingStarTotal = ['count' => 0, 'star' => 0];
            }

            if($data['star'] != $ratingStar->star && $ratingStar->status == 'public')
            {
                $ratingStarTotal['star'] -= $ratingStar->star;
                $ratingStarTotal['star'] += $data['star'];
            }

            if($ratingStar->status == 'public' && $data['status'] != 'public')
            {
                $ratingStarTotal['count'] -= 1;
                $ratingStarTotal['star'] -= $data['star'];
            }

            $ratingStar->star  = Str::clear($data['star']);

            $ratingStar->status  = Str::clear($data['status']);

            $ratingStar->save();

            if(isset($ratingStarTotal))
            {
                Metadata::update(
                    $ratingStar->object_type,
                    $ratingStar->object_id,
                    'rating_star',
                    $ratingStarTotal
                );
            }

            $ratingStar->title = '';

            $ratingStar->slug = '';

            if($ratingStar->object_type == 'post')
            {
                $post = Post::whereKey($ratingStar->object_id)->select('id', 'title', 'slug')->first();

                if(hasItems($post))
                {
                    $ratingStar->title = $post->title;
                    $ratingStar->slug  = $post->slug;
                }
            }

            if($ratingStar->object_type == 'products')
            {
                $post = Product::whereKey($ratingStar->object_id)->select('id', 'title', 'slug')->first();

                if(hasItems($post))
                {
                    $ratingStar->title = $post->title;
                    $ratingStar->slug  = $post->slug;
                }
            }

            $ratingStar->reply = RatingStar::where('parent_id', $ratingStar->id)->where('object_type', 'comment')->count();

            response()->success(trans('ajax.save.success'), [
                'item' => view( 'rating-star::admin/item', ['item' => $ratingStar])
            ]);
        }

        response()->error(trans('ajax.save.error'));
    }

    static function delete(Request $request): void
    {
        $id         = (int)$request->input('id');

        $ratingStar = RatingStar::find($id);

        if (hasItems($ratingStar))
        {
            RatingStar::whereKey($id)->delete();

            response()->success(trans('ajax.delete.success'));
        }

        response()->error(trans('ajax.delete.error'));
    }

    static function randomReview(Request $request): void
    {
        $data = $request->input('data');

        if(!hasItems($data))
        {
            response()->error(trans('Không có sản phẩm nào được chọn'));
        }

        foreach ($data as $productId)
        {
            RatingStarProducts::randoms($productId);
        }

        response()->success(trans('Thêm đánh giá cho sản phẩm thành công'));
    }
}