<?php
class RatingStarTemplate {
    static function assetsVariable(): void
    {
        ?>
        <style>
            :root {
                --star-color:<?php echo RatingStar::config('color_star');?>;
                --star-align:<?php echo RatingStar::config('item_align');?>;
            }
        </style>
        <?php
    }
    static function assets(AssetPosition $header, AssetPosition $footer): void
    {
        $header->add('rating-star', RATING_STAR_PATH.'/assets/rt-style.css', ['minify' => true]);
        $footer->add('micro-modal', RATING_STAR_PATH.'/assets/micromodal.min.js', ['minify' => false]);
    }
    static function adminAssets(): void
    {
        $asset = RATING_STAR_PATH.'/assets/';
        if(Admin::is()) {
            Admin::asset()->location('header')->add('rating-star', $asset.'css/style.admin.css');
            Admin::asset()->location('footer')->add('rating-star', $asset.'/script/script.admin.js');
        }
    }

}
add_action('cle_header', 'RatingStarTemplate::assetsVariable');
add_action('theme_custom_assets','RatingStarTemplate::assets', 30, 2);
add_action('admin_init','RatingStarTemplate::adminAssets', 100);