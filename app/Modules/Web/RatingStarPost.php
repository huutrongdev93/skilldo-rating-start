<?php
namespace RatingStar\Modules\Web;

use SkillDo\Cms\Models\Post;
use SkillDo\Cms\Support\Cms;
use SkillDo\Cms\Support\Theme;

Class RatingStarPost
{
    static function data($id): array
    {
        $data = Post::getMeta($id, 'rating_star', true);

        $totalStar = (isset($data['star'])) ? $data['star'] : 0;

        $numberReview  = (isset($data['count'])) ? $data['count'] : 0;

        $avgStar = (!empty($numberReview)) ? round($totalStar/$numberReview) : 0;

        return compact('totalStar', 'numberReview', 'avgStar');
    }

    static function form($content)
    {
        if(Theme::isPage('post_detail'))
        {
            $object = Cms::getData('object');

            if(hasItems($object))
            {
                [
                    'totalStar'     => $totalStar,
                    'numberReview'  => $numberReview,
                    'avgStar'       => $avgStar
                ] = static::data($object->id);

                $data = [
                    'type'       => 'post',
                    'objectName' => 'bài viết',
                    'object'     => $object,
                    'star'       => $totalStar,
                    'avgStar'    => $avgStar,
                    'count'      => $numberReview,
                    'form' => [
                        'name' => '',
                        'email' => '',
                    ]
                ];

                $content .= view('rating-star::template1', $data);

                $content .= view('rating-star::review', $data);
            }


        }

        return $content;
    }
}