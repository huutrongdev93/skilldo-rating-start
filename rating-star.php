<?php
/**
Plugin name     : Rating Star
Plugin class    : rating_star
Plugin uri      : http://sikido.vn
Description     : Với ứng dụng Rating Star, bạn có thể thu được nhiều đánh giá hơn bất kì một cách thức nào khác. Chúng đã được duyệt kỹ lưỡng trước khi được hiển thị trên website của bạn
Author          : Nguyễn Hữu Trọng
Version         : 4.1.0
 */
define( 'RATING_STAR_NAME',     'rating-star' );

define( 'RATING_STAR_PATH',     Path::plugin( RATING_STAR_NAME ));

define( 'RATING_STAR_VERSION', '4.1.0' );

class rating_star {

    private $name = 'rating_star';

    function __construct() {}

    public function active() {
        Rating_Star_Database::create();
    }

    public function uninstall() {
        Rating_Star_Database::drop();
    }

    static public function config($key = '') {

        $setting = [
            'product_enable' => 1,
            'post_enable'    => 1,
            'has_approving'  => 0,
            'color' => [
                'star' => '#ffbe00',
            ],
            'illegal_message' => '',
            'item_align'      => 'left',
            'item_position'   => 45,
            'template'        => 'template2',
            'reply'           => 'all',
            'auto_enable'     => 0,
            'auto_min_number' => 0,
            'auto_max_number' => 10,
            'auto_percent_5'  => 90,
            'auto_percent_4'  => 50,
            'auto_percent_3'  => 0,
        ];

        $option = Option::get('rating_star_setting', $setting);

        if(!have_posts($option)) {
            $option = $setting;
        }
        else {
            $setting = array_merge($setting, $option);
        }

        if(isset($option['has_approving']))  $setting['has_approving'] = $option['has_approving'];

        if(isset($option['illegal_message']))  $setting['illegal_message'] = $option['illegal_message'];

        if(isset($option['color']))  $setting['color'] = array_merge($setting['color'], $option['color']);

        if(is_array($setting['color']['star'])) $setting['color']['star'] = '#ffbe00';

        if(!empty($key)) {
            return Arr::get($setting, $key);
        }

        return $setting;
    }

    static public function get($args = '') {

        $model = get_model('home')->settable('rating_star');

        if(is_numeric($args)) {
            $args = ['where' => array('id' => (int)$args)];
        }

        if(!have_posts($args)) return [];

        $args = array_merge(array('where' => [], 'params' => []), $args );

        $rating_star =  $model->get_data($args);

        return $rating_star;
    }

    static public function gets($args = '') {

        $model = get_model('home')->settable('rating_star');

        if(is_numeric($args)) {
            $args = ['where' => array('id' => (int)$args)];
        }
        if(!have_posts($args)) $args = [];

        $args = array_merge(['where' => [], 'params' => []], $args);

        if(empty($args['object_type'])) {
            $args['where']['object_type <>'] = 'comment';
        } else {
            $args['where']['object_type'] = $args['object_type'];
        }

        $rating_star =  $model->gets_data($args);

        return $rating_star;
    }

    static public function count($args = '') {

        $model = get_model('home')->settable('rating_star');

        if(is_numeric($args)) {
            $args = ['where' => array('id' => (int)$args)];
        }
        if(!have_posts($args)) $args = [];

        $args = array_merge(['where' => [], 'params' => []], $args);

        if(empty($args['object_type'])) {
            $args['where']['object_type <>'] = 'comment';
        } else {
            $args['where']['object_type'] = $args['object_type'];
        }

        $rating_star =  $model->count_data($args);

        return $rating_star;
    }

    static public function insert($rating_star = []) {

        $model = get_model()->settable('rating_star');

        if(!empty($rating_star['id'])) {

            $id 		   = (int) $rating_star['id'];

            $update 	   = true;

            $old_rating_star = rating_star::get($id);

            if (!$old_rating_star) return new SKD_Error( 'invalid_rating_star_id', __( 'ID đanh giá sao không chính xác.' ));

            $rating_star['name'] = (isset($rating_star['name'])) ? $rating_star['name'] : $old_rating_star->name;
            $rating_star['email'] = (isset($rating_star['email'])) ? $rating_star['email'] : $old_rating_star->email;
            $rating_star['title'] = (isset($rating_star['title'])) ? $rating_star['title'] : $old_rating_star->title;
            $rating_star['message'] = (isset($rating_star['message'])) ? $rating_star['name'] : $old_rating_star->message;
            $rating_star['star'] = (isset($rating_star['star'])) ? $rating_star['star'] : $old_rating_star->star;
            $rating_star['object_type'] = (isset($rating_star['object_type'])) ? $rating_star['object_type'] : $old_rating_star->object_type;
            $rating_star['object_id'] = (isset($rating_star['object_id'])) ? $rating_star['object_id'] : $old_rating_star->object_id;
            $rating_star['is_read'] = (isset($rating_star['is_read'])) ? $rating_star['is_read'] : $old_rating_star->is_read;
            $rating_star['parent_id'] = (isset($rating_star['parent_id'])) ? $rating_star['parent_id'] : $old_rating_star->parent_id;
            $rating_star['status'] = (isset($rating_star['status'])) ? $rating_star['status'] : $old_rating_star->status;
            $rating_star['user_id'] = (isset($rating_star['user_id'])) ? $rating_star['user_id'] : $old_rating_star->user_id;
            $rating_star['like'] = (isset($rating_star['like'])) ? $rating_star['like'] : $old_rating_star->like;
            $rating_star['created'] = (isset($rating_star['created'])) ? $rating_star['created'] : $old_rating_star->created;
        }
        else {

            $update = false;
        }

        $name   = (isset($rating_star['name'])) ? Str::clear($rating_star['name']) : '';

        $email  = (isset($rating_star['email'])) ? Str::clear($rating_star['email']) : '';

        $title  = (isset($rating_star['title'])) ? Str::clear($rating_star['title']) : '';

        $message    = (isset($rating_star['message'])) ? Str::clear($rating_star['message']) : '';

        $star       = (isset($rating_star['star'])) ? (int)Str::clear($rating_star['star']) : '';

        $object_type = (isset($rating_star['object_type'])) ? Str::clear($rating_star['object_type']) : 'product';

        $object_id      = (isset($rating_star['object_id'])) ? (int)Str::clear($rating_star['object_id']) : 0;

        $is_read        = (isset($rating_star['is_read'])) ? (int)Str::clear($rating_star['is_read']) : 0;

        $parent_id      = (isset($rating_star['parent_id'])) ? (int)Str::clear($rating_star['parent_id']) : 0;

        $like           = (isset($rating_star['like'])) ? (int)Str::clear($rating_star['like']) : 0;

        $status         = empty( $rating_star['status'] ) ? 'public' : Str::clear($rating_star['status']);

        $created         = empty($rating_star['created']) ? '' : Str::clear($rating_star['created']);

        $user_id = 0;

        if(isset($rating_star['user_id'])) {
            $user_id = (int)Str::clear($rating_star['user_id']);
        }
        else if(Auth::check()) {
            $user_id = Auth::userID();
        }

        $data = compact('name', 'email', 'title', 'message', 'star', 'object_type', 'object_id', 'status', 'is_read', 'parent_id', 'user_id', 'created', 'like');

        if( $update ) {

            $model->settable('rating_star')->update_where( $data, compact('id' ));

            $rating_star_id = $id;
        }
        else{

            $model->settable('rating_star');

            $rating_star_id = $model->add( $data );
        }

        return $rating_star_id;
    }

    static public function delete($id = '') {

        $model = get_model()->settable('rating_star');

        $rating_star = static::get($id);

        if(have_posts($rating_star)) {

            if($rating_star->object_type != 'comment') {

                $count_rating_star = Metadata::get($rating_star->object_type, $rating_star->object_id, 'rating_star', true);

                if (!have_posts($count_rating_star)) {
                    $count_rating_star = array('count' => 0, 'star' => 0);
                } else {
                    $count_rating_star['count'] = $count_rating_star['count'] - 1;
                    $count_rating_star['star'] = $count_rating_star['star'] - $rating_star->star;
                }

                if ($rating_star->status == 'public') {

                    Metadata::update($rating_star->object_type, $rating_star->object_id, 'rating_star', $count_rating_star);
                }
            }

            $model->settable('rating_star');

            return $model->delete_where(['id' => $id]);
        }

        return 0;
    }

    static public function deleteList($productID = []) {

        if(have_posts($productID)) {

            $model      = get_model()->settable('rating_star');

            foreach ($productID as $key => $id) {

                static::delete($id);
            }

            return $productID;
        }

        return false;
    }

    static public function random() {

        include_once RATING_STAR_PATH.'/includes/BiasRandom.php';

        $biasRandom = new BiasRandom();

        $dataNumberStar = [];

        $percent = (int)rating_star::config('auto_percent_5');

        if($percent != 0) $dataNumberStar[5] = $percent;

        $percent = (int)rating_star::config('auto_percent_4');

        if($percent != 0) $dataNumberStar[4] = $percent;

        $percent = (int)rating_star::config('auto_percent_3');

        if($percent != 0) $dataNumberStar[3] = $percent;

        $biasRandom->setData($dataNumberStar);

        $service = 'http://cdn.sikido.vn';

        $dataTemp = file_get_contents($service.'/star-ratings');

        $dataTemp = (array)json_decode($dataTemp);

        $number = rand(rating_star::config('auto_min_number'), rating_star::config('auto_max_number'));

        $dataRandom = [];

        $randomSuccess = ['name' => [], 'message' => []];

        for($i = 0; $i <= $number; $i++) {

            $dataRandom[$i] = ['name' => '', 'message' => '', 'star' => 5, 'object_type' => 'product', 'is_read' => 1];
            //Name
            $keyRandom = array_rand($dataTemp['name'], 1);
            while (!empty($randomSuccess['name']) && in_array($keyRandom, $randomSuccess['name']) !== false) {
                $keyRandom = array_rand( $dataTemp['name'], 1);
            }
            $randomSuccess['name'][$keyRandom] = $keyRandom;
            $dataRandom[$i]['name'] = $dataTemp['name'][$keyRandom];

            //Message
            $keyRandom = array_rand($dataTemp['message'], 1);
            while (!empty($randomSuccess['message']) && in_array($keyRandom, $randomSuccess['message']) !== false) {
                $keyRandom = array_rand( $dataTemp['message'], 1);
            }
            $randomSuccess['message'][$keyRandom] = $keyRandom;
            $dataRandom[$i]['message'] = $dataTemp['message'][$keyRandom];

            //Number Rating
            $dataRandom[$i]['star'] = $biasRandom->random()[0];
        }

        return $dataRandom;
    }

    static public function starLabel($star = 1) {
        $label = [
            1 => __('Rất không hài lòng'),
            2 => __('Không hài lòng'),
            3 => __('Bình thường'),
            4 => __('Hài lòng'),
            5 => __('Cực kì hài lòng')
        ];
        return Arr::get($label, $star);
    }

    static public function timeElapsed($datetime, $full = false) {

        $now = new DateTime;

        $ago = new DateTime($datetime);

        $diff = $now->diff($ago);

        $diff->w = floor($diff->d / 7);

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

    static public function getKeyName($name) {
        $name = explode(' ', $name);
        $first = mb_substr($name[0], 0, 1);
        $last = (count($name) > 1) ? mb_substr(Arr::last($name), 0, 1) : '';
        return trim($first.$last);
    }
}

include 'rating-star-database.php';

include 'rating-star-helper.php';

include 'rating-star-ajax.php';

if(Admin::is()) {
    include 'admin/rating-star-admin.php';
    include 'update.php';
}
else {
    function rating_star_root_assets() {
        ?>
        <style>
            :root {
                --star-color:<?php echo rating_star::config('color.star');?>;
                --star-align:<?php echo rating_star::config('item_align');?>;
            }
        </style>
        <?php
    }
    add_action('cle_header', 'rating_star_root_assets');
    function rating_star_assets() {
        Template::asset()->location('header')->add('rating-star', RATING_STAR_PATH.'/assets/rt-style.css', ['minify' => true]);
        if(Template::isPage('products_detail')) {
            Template::asset()->location('footer')->add('micromodal', RATING_STAR_PATH.'/assets/micromodal.min.js', ['minify' => false]);
        }
    }
    add_action('init','rating_star_assets', 30);
}

if(rating_star::config('product_enable') == 1) new Rating_Star_Product();
if(rating_star::config('post_enable') == 1) new Rating_star_post();