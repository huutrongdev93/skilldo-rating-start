<?php
Class RatingStarRoles {
    static function group($group) {
        $group['rating_star'] = [
            'label' => __('Đánh giá sao'),
            'capabilities' => array_keys(RatingStarRoles::capabilities())
        ];
        return $group;
    }
    static function label($label): array
    {
        return array_merge($label, RatingStarRoles::capabilities() );
    }
    static function capabilities(): array
    {
        $label['rating_star']     = 'Xem mã danh sách đánh giá';
        return $label;
    }
}

add_filter('user_role_editor_group', 'RatingStarRoles::group' );
add_filter('user_role_editor_label', 'RatingStarRoles::label' );