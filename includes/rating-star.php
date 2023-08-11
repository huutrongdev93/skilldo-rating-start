<?php
class RatingStar extends Model {
    
    static string $table = 'rating_star';
    
    static public function handleParams($args = null) {
        
        if(is_array($args)) {
            if(empty($args['object_type'])) {
                $args['where']['object_type <>'] = 'comment';
            } else {
                $args['where']['object_type'] = $args['object_type'];
            }

            $query = Qr::convert($args);
        }
        
        if(is_numeric($args)) $query = Qr::set('id', $args);
        
        if($args instanceof Qr) $query = clone $args;
        
        return (isset($query)) ? $query : null;
    }

    static public function insert($insertData = []) {

        $model = model('rating_star');

        $columnsTable = [
            'name'          => ['string'],
            'email'         => ['string'],
            'title'         => ['string'],
            'message'       => ['string'],
            'star'          => ['int', 0],
            'object_type'   => ['string', 'products'],
            'object_id'     => ['int', 0],
            'is_read'       => ['int', 0],
            'parent_id'     => ['int', 0],
            'status'        => ['string', 'public'],
            'type'          => ['string', 'handmade'],
            'user_id'       => ['int', 0],
            'like'          => ['int', 0],
        ];

        $columnsTable = apply_filters('columns_db_'.self::$table, $columnsTable);

        if(!empty($insertData['id'])) {

            $id 		   = (int) $insertData['id'];

            $update 	   = true;

            $oldObject = RatingStar::get($id);

            if (!$oldObject) return new SKD_Error( 'invalid_rating_star_id', __( 'ID đanh giá sao không chính xác.' ));
        }
        else {
            $update = false;
        }

        $insertData = createdDataInsert($columnsTable, $insertData, (isset($oldObject)) ? $oldObject : null);

        foreach ($columnsTable as $columnsKey => $columnsValue ) {
            ${$columnsKey}  = $insertData[$columnsKey];
        }

        if(!empty($insertData['user_id'])) {
            $user_id = (int)Str::clear($insertData['user_id']);
        }
        else if(Auth::check()) {
            $user_id = Auth::userID();
        }

        $data = compact(array_keys($columnsTable));

        $data = apply_filters('pre_insert_'.static::$table.'_data', $data, $insertData, $update ? $oldObject : null);

        $model->settable('rating_star');

        if($update) {
            $data['updated'] = gmdate('Y-m-d H:i:s', time() + 7*3600);
            $model->update( $data, Qr::set($id));
        }
        else {
            $data['created'] = gmdate('Y-m-d H:i:s', time() + 7*3600);
            $id = $model->add($data);
        }

        return $id;
    }

    static public function delete($id = ''): array|int
    {

        $model = model('rating_star');

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

                model('rating_star')->delete(Qr::set('object_type', 'comment')->where('parent_id', $id));
            }

            $model->settable('rating_star')->delete(Qr::set($id));

            return [$id];
        }

        return 0;
    }

    static public function deleteList($productID = []) {
        if(have_posts($productID)) {
            foreach ($productID as $id) {
                static::delete($id);
            }
            return $productID;
        }
        return false;
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
            'autoDataType'    => 'auto',
        ];

        $option = Option::get('rating_star_setting', $setting);

        if(!have_posts($option)) {
            $option = $setting;
        }
        else {
            $setting = array_merge($setting, $option);
        }

        if(empty($option['template'])) $option['template'] = 'template2';

        if(isset($option['has_approving']))  $setting['has_approving'] = $option['has_approving'];

        if(isset($option['illegal_message']))  $setting['illegal_message'] = $option['illegal_message'];

        if(isset($option['color']))  $setting['color'] = array_merge($setting['color'], $option['color']);

        if(empty($option['autoDataType'])) $option['autoDataType'] = 'auto';

        if(is_array($setting['color']['star'])) $setting['color']['star'] = '#ffbe00';

        if(!empty($key)) {
            return Arr::get($setting, $key);
        }

        return $setting;
    }

    static public function random(): array
    {

        include_once RATING_STAR_PATH.'/includes/BiasRandom.php';

        $config = RatingStar::config();

        $biasRandom = new BiasRandom();

        $dataNumberStar = [];

        $percent = (int)$config['auto_percent_5'];

        if($percent != 0) $dataNumberStar[5] = $percent;

        $percent = (int)$config['auto_percent_4'];

        if($percent != 0) $dataNumberStar[4] = $percent;

        $percent = (int)$config['auto_percent_3'];

        if($percent != 0) $dataNumberStar[3] = $percent;

        $biasRandom->setData($dataNumberStar);

        if($config['autoDataType'] == 'auto') {

            $service = 'https://cdn.sikido.vn';

            $dataTemp = file_get_contents($service.'/star-ratings');

            $dataTemp = (array)json_decode($dataTemp);
        }
        else {
            $dataTemp = [
                'name' => [], 'message' => []
            ];

            $dataAuto = file_get_contents(RATING_STAR_PATH.'/assets/auto-data.json');

            $dataAuto = json_decode($dataAuto);

            if(have_posts($dataAuto)) {
                foreach ($dataAuto as $item) {
                    $dataTemp['name'][] = $item->name;
                    $dataTemp['message'][] = $item->message;
                }
            }
        }

        $number = rand(RatingStar::config('auto_min_number'), RatingStar::config('auto_max_number'));

        $dataRandom = [];

        $randomSuccess = ['name' => [], 'message' => []];

        for($i = 0; $i <= $number; $i++) {

            $dataRandom[$i] = [
                'message'       => '',
                'star'          => 5,
                'object_type'   => 'products',
                'is_read'       => 1
            ];
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
}