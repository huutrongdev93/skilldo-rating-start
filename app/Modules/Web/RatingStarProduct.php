<?php
namespace RatingStar\Modules\Web;

Class RatingStarProduct
{
    static function data($id): array
    {
        $data = \Ecommerce\Models\Product::getMeta($id, 'rating_star', true);

        $totalStar = (isset($data['star'])) ? $data['star'] : 0;

        $numberReview  = (isset($data['count'])) ? $data['count'] : 0;

        $avgStar = (!empty($numberReview)) ? round($totalStar/$numberReview) : 5;

        return compact('totalStar', 'numberReview', 'avgStar');
    }

    static function object($object): void
    {
        [
            'totalStar'     => $totalStar,
            'numberReview'  => $numberReview,
            'avgStar'       => $avgStar
        ] = static::data($object->id);

        self::template($numberReview, $avgStar);
    }

    static function detail($object): void {

        [
            'totalStar'     => $totalStar,
            'numberReview'  => $numberReview,
            'avgStar'       => $avgStar
        ] = static::data($object->id);

        echo view('rating-star::review-star-detail', [
            'total_star' => $avgStar,
            'total_number_review' => $numberReview
        ]);
    }

    static function form($object): void
    {
        [
            'totalStar'     => $totalStar,
            'numberReview'  => $numberReview,
            'avgStar'       => $avgStar
        ] = static::data($object->id);

        $data = [
            'type'   => 'products',
            'objectName' => 'sản phẩm',
            'object' => $object,
            'star'   => $avgStar,
            'count'  => $numberReview,
            'form'   => [
                'name' => '',
                'email' => '',
            ]
        ];

        if($data['count'] != 0) $data['star'] = round($data['star']/$data['count']);

        echo view('rating-star::'.config('rating-star::theme.template'), $data);

        echo view('rating-star::review', $data);
    }

    static function template($total_count, $total_star): void
    {
        echo view('rating-star::review-star', [
            'total_star' => $total_star,
            'total_count' => $total_count
        ]);
    }
}