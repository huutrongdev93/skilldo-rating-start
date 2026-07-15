<?php
return [
    /*
    |--------------------------------------------------------------------------
    |  Cấu hình
    |--------------------------------------------------------------------------
    | product_enable - bật tắt đánh giá cho sản phẩm
    | post_enable - bật tắt đánh giá cho bài viết
    | has_approving - bật tắt duyệt đánh gia
    | illegal_message - danh sách từ khóa cấm trong bình luận
    */
    'product_enable' => 1,
    'post_enable'    => 1,
    'has_approving'  => 0,
    'illegal_message' => '',
    'reply'           => 'all',
    /*
    |--------------------------------------------------------------------------
    |  Tụ động đánh giá
    |--------------------------------------------------------------------------
    | auto_enable - bật tắt đánh giá tự động
    | auto_min_number - Số đánh giá sao nhỏ nhất được random ra
    | auto_max_number - Số đánh giá sao cao nhất được random ra
    | auto_percent_* - tỷ lệ ra đánh gia sao có số sao tương ứng
    | autoDataType - dữ liệu đánh giá tự động hoặc tự tạo
    */
    'auto_enable'     => 0,
    'auto_min_number' => 0,
    'auto_max_number' => 10,
    'auto_percent_5'  => 90,
    'auto_percent_4'  => 50,
    'auto_percent_3'  => 0,
    'autoDataType'    => 'auto',
];