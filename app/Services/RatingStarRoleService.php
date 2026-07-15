<?php
namespace RatingStar\Services;

class RatingStarRoleService
{
    static function group($group)
    {
        $group['rating_star'] = [
            'label' => 'Đánh giá sao',
            'capabilities' => array_keys(static::capabilities())
        ];
        return $group;
    }

    static function label($label): array
    {
        return array_merge($label, static::capabilities());
    }

    static function capabilities(): array
    {
        $label['rating_star']  = 'Xem danh sách đánh giá';

        return $label;
    }
}