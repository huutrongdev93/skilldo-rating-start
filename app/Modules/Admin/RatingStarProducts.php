<?php
namespace RatingStar\Modules\Admin;

use Ecommerce\Models\Product;
use RatingStar\Models\RatingStar;
use RatingStar\Supports\RatingStarHelper;
use SkillDo\Cms\Support\Metadata;
use Illuminate\Support\Facades\DB;
use SkillDo\Support\Auth;

Class RatingStarProducts
{
    static function randoms($id): void
    {
        $ratings = RatingStarHelper::random();

        if(hasItems($ratings))
        {
            foreach ($ratings as $rating)
            {
                $rating['object_id'] = $id;

                $rating['status'] = 'public';

                $rating['type'] = 'auto';

                $error = RatingStar::create($rating);

                if(!is_skd_error($error))
                {
                    DB::table('rating_star')
                        ->where('id',$error)
                        ->update(['created' => date('Y-m-d H:i:s', time() - rand(0, 30)*24*rand(50, 60)*rand(0, 60))]);

                    $rating_star_product = Product::getMeta($id, 'rating_star', true);

                    if(!hasItems($rating_star_product))
                    {
                        $rating_star_product = ['count' => 0, 'star'  => 0];
                    }

                    $rating_star_product['count'] += 1;

                    $rating_star_product['star']  += $rating['star'];

                    Product::updateMeta($id, 'rating_star', $rating_star_product);
                }
            }
        }
    }

    static function addColumnTitle($item): void
    {
        $ratingStarData = Product::getMeta($item->id, 'rating_star', true);

        $totalStar = (isset($ratingStarData['star'])) ? $ratingStarData['star'] : 0;

        $numberReview = (isset($ratingStarData['count'])) ? $ratingStarData['count'] : 0;

        $averageStar = RatingStarHelper::avgStar($totalStar, $numberReview, 0);

        echo view('rating-star::admin/products/title-star', [
            'item' => $item,
            'averageStar' => $averageStar,
            'numberReview' => $numberReview
        ]);
    }

    static function delete($productID): void
    {
        if(is_numeric($productID)) $productID = [$productID];

        $ratingStar = RatingStar::where('object_type', 'products')->whereIn('object_id', $productID)->get();

        if(hasItems($ratingStar))
        {
            $listID = [];

            foreach ($ratingStar as $item)
            {
                $listID[] = $item->id;
                Metadata::delete($item->object_type, $item->object_id, 'rating_star');
            }

            RatingStar::where('object_type', 'comment')->whereIn('parent_id', $listID)->delete();

            RatingStar::whereIn('id', $listID)->delete();
        }
    }

    static function buttonAction($actionList)
    {
        if (Auth::hasCap('product_edit'))
        {
            $actionList['productAddReviews'] = [
                'icon' => '<i class="fa-duotone fa-stars"></i>',
                'label' => 'Tạo đánh giá',
                'class' => 'js_btn_product_add_review',
            ];
        }
        return $actionList;
    }
}