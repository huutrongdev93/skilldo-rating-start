<?php

use SkillDo\DB;

class RatingStar extends \SkillDo\Model\Model {
    
    protected string $table = 'rating_star';

    protected array $columns = [
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

    const USER_CREATED_AT = 'user_id';

    protected static function boot(): void
    {
        parent::boot();

        static::deleted(function (RatingStar $wheel, $listRemoveId, $objects) {

            foreach ($objects as $object) {

                if($object->object_type != 'comment') {

                    $count_rating_star = Metadata::get($object->object_type, $object->object_id, $wheel->getTable(), true);

                    if (!have_posts($count_rating_star))
                    {
                        $count_rating_star = array('count' => 0, 'star' => 0);
                    }
                    else
                    {
                        $count_rating_star['count'] = $count_rating_star['count'] - 1;
                        $count_rating_star['star'] = $count_rating_star['star'] - $object->star;
                    }

                    if ($object->status == 'public') {
                        Metadata::update($object->object_type, $object->object_id, $wheel->getTable(), $count_rating_star);
                    }

                    DB::table($wheel->getTable())->where('object_type', 'comment')->where('parent_id', $object->id)->delete();

                }
            }
        });
    }

    static function deleteById($id): array|int
    {
        return static::whereKey($id)->remove();
    }

    static function config($key = '') {

        $setting = [
            'product_enable' => 1,
            'post_enable'    => 1,
            'has_approving'  => 0,
            'color_star'     => '#ffbe00',
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

        if(empty($option['autoDataType'])) $option['autoDataType'] = 'auto';

        $optionStyle = Option::get('rating_star_style');

        $setting['color_star'] = $optionStyle['color_star'] ?? $setting['color_star'];

        $setting['item_align'] = $optionStyle['item_align'] ?? $setting['item_align'];

        $setting['item_position'] = $optionStyle['item_position'] ?? $setting['item_position'];

        if(!empty($key)) {
            return Arr::get($setting, $key);
        }

        return $setting;
    }

    static function random(): array
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

            $dataTemp = (array)SKDService::cms()->ratingStartDataFake();
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

    static function starLabel($star = 1) {
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