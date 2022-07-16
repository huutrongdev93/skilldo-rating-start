<?php
class RatingStarTemplate {
    static function assetsVariable() {
        ?>
        <style>
            :root {
                --star-color:<?php echo RatingStar::config('color.star');?>;
                --star-align:<?php echo RatingStar::config('item_align');?>;
            }
        </style>
        <?php
    }
    static function assets() {
        Template::asset()->location('header')->add('rating-star', RATING_STAR_PATH.'/assets/rt-style.css', ['minify' => true]);
        Template::asset()->location('footer')->add('micromodal', RATING_STAR_PATH.'/assets/micromodal.min.js', ['minify' => false]);
    }

}
add_action('cle_header', 'RatingStarTemplate::assetsVariable');

add_action('init','RatingStarTemplate::assets', 30);