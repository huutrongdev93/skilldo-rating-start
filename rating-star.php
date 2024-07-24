<?php
const RATING_STAR_NAME = 'rating-star';

const RATING_STAR_VERSION = '4.6.0';

define('RATING_STAR_PATH',  Path::plugin(RATING_STAR_NAME));

class rating_star {

    private string $name = 'rating_star';

    function __construct() {}

    public function active(): void
    {
        Rating_Star_Database::create();
    }

    public function uninstall(): void
    {
        Rating_Star_Database::drop();
    }
    
    static function timeElapsed($datetime, $full = false): string
    {

        $now = new DateTime;

        $ago = new DateTime($datetime);

        $diff = $now->diff($ago);

        $diff = (object)[
            "y" => $diff->y,
            "m" => $diff->m,
            "d" => $diff->d,
            "h" => $diff->h,
            "i" => $diff->i,
            "s" => $diff->s,
            "f" => $diff->f,
            "w" => floor($diff->d / 7),
            "invert" => $diff->invert,
            "days" => $diff->days,
        ];

        $diff->d -= $diff->w * 7;

        $string = array(
            'y' => 'năm',
            'm' => 'tháng',
            'w' => 'tuần',
            'd' => 'ngày',
            'h' => 'giờ',
            'i' => 'phút',
            's' => 'giây',
        );

        foreach ($string as $k => &$v) {
            if ($diff->$k) {
                $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? '' : '');
            } else {
                unset($string[$k]);
            }
        }

        if (!$full) $string = array_slice($string, 0, 1);

        return $string ? implode(', ', $string) : '';
    }

    static function getKeyName($name): string
    {
        $name = explode(' ', $name);
        $first = mb_substr($name[0], 0, 1);
        $last = (count($name) > 1) ? mb_substr(Arr::last($name), 0, 1) : '';
        return trim($first.$last);
    }

    static function module($key = null) {

        $module = apply_filters('rating_start_register',[
            'products' => [
                'key'   => 'products',
                'class' => 'Rating_Star_Product',
                'name'  => 'Đánh giá sản phẩm',
            ],
            'post'  => [
                'key'   => 'post',
                'class' => 'Rating_Star_Post',
                'name'  => 'Đánh giá bài viết'
            ]
        ]);

        if($key != null) return Arr::get($module, $key);

        return $module;
    }
}

include 'rating-star-database.php';
include 'rating-star-ajax.php';
include 'rating-star-template.php';
include 'includes/rating-star.php';
include 'module/post.php';
include 'module/product.php';
if(Admin::is()) {
    include 'admin/admin.php';
    include 'update.php';
}