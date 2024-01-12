<?php
Class Rating_Star_Post {

    function __construct() {
        add_action('the_content', 'Rating_star_post::form', 99);
    }

    static function object($object) {

        $rating_star_data = Posts::getMeta($object->id, 'rating_star', true);

        $total_star     = (isset($rating_star_data['star'])) ? $rating_star_data['star'] : 0;

        $total_number_review    = (isset($rating_star_data['count'])) ? $rating_star_data['count'] : 0;

        if( $total_number_review != 0 ) $total_star = round($total_star/$total_number_review);

        static::template($total_number_review, $total_star);
    }

    static function form($content) {

        if(Template::isPage('post_detail')) {

            $object = get_object_current();

            $rating_star_data = Posts::getMeta($object->id, 'rating_star', true);

            $data = [
                'type' => 'post',
                'objectName' => 'bài viết',
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

            ob_start();

            Plugin::partial(RATING_STAR_NAME, 'template1', $data);

            Plugin::partial(RATING_STAR_NAME, 'review', $data);

            $content .= ob_get_contents();

            ob_end_clean();
        }

        return $content;
    }

    static function template($total_count, $total_star) {
        ?>
        <div class="skd-product-reviews-star" style="color: var(--star-color);margin-bottom:10px;height: 11px;font-size:13px;">
            <?php if($total_star != 0) { ?>
                <?php for( $i = 0; $i < $total_star; $i++ ) {?>
                    <i class="fa fa-star" aria-hidden="true" style="color:var(--star-color); font-weight: bold;"></i>&nbsp;
                <?php } ?>
                <?php for( $i = 0; $i < (5 - $total_star); $i++ ) {?>
                    <i class="fas fa-star" aria-hidden="true" style="color:#ccc;"></i>&nbsp;
                <?php } ?>
            <?php } ?>
        </div>
        <?php
    }

    static function get($args = null) {
        return Posts::get($args);
    }
    static function getsData($listId) {
        return Posts::gets(Qr::set()->whereIn('id', $listId)->select('id', 'title', 'slug'));
    }
}

if(RatingStar::config('post_enable') == 1) {
    new Rating_Star_Post();
}