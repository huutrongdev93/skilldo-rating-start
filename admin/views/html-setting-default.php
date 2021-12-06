<div class="box">
    <div class="box-content p10">
        <?php
        $Form
            ->add('rating_star_setting[product_enable]', 'switch', [
                'label' => 'Bật / Tắt đánh giá sản phẩm',
                'note' => 'Bật tùy chọn này khi sử dụng đánh giá sao trong sản phẩm.'], rating_star::config('product_enable'))
            ->add('rating_star_setting[post_enable]', 'switch', [
                'label' => 'Bật / Tắt đánh giá bài viết',
                'note' => 'Bật tùy chọn này khi sử dụng đánh giá sao trong bài viết.'], rating_star::config('post_enable'))

            ->add('rating_star_setting[has_approving]', 'switch', [
            'label' => 'Duyệt đánh giá',
            'note' => 'Cấu hình cho phép chủ cửa hàng duyệt các đánh giá sản phẩm trước khi cho hiển thị.'], rating_star::config('has_approving'))

            ->add('rating_star_setting[color][star]', 'color', [
                'label' => 'Màu biểu tượng sao',], rating_star::config('color.star'))

            ->add('rating_star_setting[illegal_message]', 'textarea', [
                'label' => 'Từ khóa không cho phép',
                'note' => 'Mỗi từ khóa cách nhau bằng dầu ","'
            ], rating_star::config('illegal_message'))

            ->html(false);
        ?>
    </div>
</div>
