<?php
namespace RatingStar\Supports;

use DateTime;
use RatingStar\Services\BiasRandom;
use SkillDo\Cms\Support\SKDService;
use Illuminate\Support\Arr;

class RatingStarHelper
{
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

    static function module($key = null)
    {
        $module = apply_filters('rating_start_register',[
            'products' => [
                'key'   => 'products',
                'class' => \Ecommerce\Models\Product::class,
                'name'  => 'Đánh giá sản phẩm',
            ],
            'post'  => [
                'key'   => 'post',
                'class' => \SkillDo\Cms\Models\Post::class,
                'name'  => 'Đánh giá bài viết'
            ]
        ]);

        if($key != null) return Arr::get($module, $key);

        return $module;
    }

    static function random(): array
    {
        $biasRandom = new BiasRandom();

        $dataNumberStar = [];

        $percent = (int)config('rating-star::config.auto_percent_5');

        if($percent != 0) $dataNumberStar[5] = $percent;

        $percent = (int)config('rating-star::config.auto_percent_4');

        if($percent != 0) $dataNumberStar[4] = $percent;

        $percent = (int)config('rating-star::config.auto_percent_3');

        if($percent != 0) $dataNumberStar[3] = $percent;

        $biasRandom->setData($dataNumberStar);

        if(config('rating-star::config.autoDataType') == 'auto') {

            $dataTemp = (array)SKDService::cms()->ratingStartDataFake();
        }
        else
        {
            $dataTemp = [
                'name' => [],
                'message' => []
            ];

            $dataAuto = file_get_contents(asset('rating-star::auto-data.json'));

            $dataAuto = json_decode($dataAuto);

            if(hasItems($dataAuto))
            {
                foreach ($dataAuto as $item)
                {
                    $dataTemp['name'][] = $item->name;
                    $dataTemp['message'][] = $item->message;
                }
            }
        }

        $number = rand(config('rating-star::config.auto_min_number'), config('rating-star::config.auto_max_number'));

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
            1 => 'Rất không hài lòng',
            2 => 'Không hài lòng',
            3 => 'Bình thường',
            4 => 'Hài lòng',
            5 => 'Cực kì hài lòng'
        ];
        return Arr::get($label, $star);
    }
}