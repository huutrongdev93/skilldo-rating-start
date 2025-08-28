<?php
include 'ajax.php';
include 'setting.php';
include 'theme.php';
include 'products.php';
include 'roles.php';

Class AdminRatingStar {

    static public function navigation(): void
    {
        if(Auth::hasCap('rating_star')) {

            $count = RatingStar::count(Qr::set('is_read',0));

            AdminMenu::add('rating-star', 'Đánh giá', 'plugins/rating-star', [
                'icon' => '<img src="'.RATING_STAR_PATH.'/assets/images/rating-star.png">',
                'callback' => 'AdminRatingStar::page',
                'position' => 'theme',
                'count' => $count
            ]);
        }
    }

    static public function page(): void
    {
        \SkillDo\DB::table('rating_star')->where('is_read', 0)->update(['is_read' => 1]);

		Plugin::view(RATING_STAR_NAME, 'admin/index');
    }
}

add_action('init', 'AdminRatingStar::navigation', 10);