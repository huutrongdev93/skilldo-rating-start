<?php
Class Rating_Star_Product {

    function __construct() {
        add_action('product_detail_tabs', 'Rating_Star_Product::form', 30);
        add_action('product_detail_info', 'Rating_Star_Product::detail', 6);
        add_action('product_object_info', 'Rating_Star_Product::object', RatingStar::config('item_position') );
    }

    static function object($object): void {

        $rating_star_data = Product::getMeta($object->id, 'rating_star', true);

        $total_star     = (isset($rating_star_data['star'])) ? $rating_star_data['star'] : 0;

        $total_number_review    = (isset($rating_star_data['count'])) ? $rating_star_data['count'] : 0;

        if($total_number_review != 0) {
            $total_star = round($total_star/$total_number_review);
        }
        else {
            $total_star = 5;
        }

        self::template($total_number_review, $total_star);
    }

    static function detail($object): void {

        $rating_star_data = Product::getMeta($object->id, 'rating_star', true);

        $total_star     = (isset($rating_star_data['star'])) ? $rating_star_data['star'] : 0;

        $total_number_review    = (isset($rating_star_data['count'])) ? $rating_star_data['count'] : 0;

        if( $total_number_review != 0 ) $total_star = round($total_star/$total_number_review);

        Plugin::view(RATING_STAR_NAME, 'review-star-detail', [
            'total_star' => $total_star,
            'total_number_review' => $total_number_review
        ]);
    }

    static function form($object): void {

        $rating_star_data = Product::getMeta($object->id, 'rating_star', true);

        $data = [
            'type'   => 'products',
            'objectName' => 'sản phẩm',
            'object' => $object,
            'config' => RatingStar::config(),
            'star'   => (isset($rating_star_data['star'])) ? $rating_star_data['star'] : 0,
            'count'  => (isset($rating_star_data['count'])) ? $rating_star_data['count'] : 0,
            'form'   => [
                'name' => '',
                'email' => '',
            ]
        ];

        if($data['count'] != 0) $data['star'] = round($data['star']/$data['count']);

        Plugin::view(RATING_STAR_NAME, RatingStar::config('template'), $data);

        Plugin::view(RATING_STAR_NAME, 'review', $data);
    }

    static function template($total_count, $total_star): void {
        Plugin::view(RATING_STAR_NAME, 'review-star', [
			'total_star' => $total_star,
			'total_count' => $total_count
        ]);
    }
    static function get($args = null) {
        return Product::get($args);
    }
    static function getsData($listId) {
        return Product::gets(Qr::set()->whereIn('id', $listId)->select('id', 'title', 'slug'));
    }
}
if(RatingStar::config('product_enable') == 1) {
    new Rating_Star_Product();
}