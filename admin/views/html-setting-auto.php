<div class="box">
    <div class="box-content">
        <?php
        $Form
            ->add('rating_star_setting[auto_enable]', 'switch', ['label' => 'Bật / tắt tự động tạo đánh giá', 'note' => 'Tự động tạo đánh giá khi tạo mới sản phẩm' ], rating_star::config('auto_enable'))
            ->add('rating_star_setting[auto_min_number]', 'number', ['label' => 'Số đánh giá nhỏ nhất tạo ra', 'min' => 0,
                'after' => '<div class="col-md-6"><div class="form-group group">', 'before'=> '</div></div>'
            ], rating_star::config('auto_min_number'))

            ->add('rating_star_setting[auto_max_number]', 'number', ['label' => 'Số đánh giá lớn nhất tạo ra', 'min' => 3, 'max' => 10,
                'after' => '<div class="col-md-6"><div class="form-group group">', 'before'=> '</div></div>'
            ], rating_star::config('auto_max_number'))

            ->add('rating_star_setting[auto_percent_5]', 'number', ['label' => 'Tỉ lệ ra 5 sao', 'min' => 0, 'max' => 100,
                'after' => '<div class="col-md-4"><div class="form-group group">', 'before'=> '</div></div>'
            ], rating_star::config('auto_percent_5'))

            ->add('rating_star_setting[auto_percent_4]', 'number', ['label' => 'Tỉ lệ ra 4 sao', 'min' => 0, 'max' => 100,
                'after' => '<div class="col-md-4"><div class="form-group group">', 'before'=> '</div></div>'
            ], rating_star::config('auto_percent_4'))

            ->add('rating_star_setting[auto_percent_3]', 'number', ['label' => 'Tỉ lệ ra 3 sao', 'min' => 0, 'max' => 100,
                'after' => '<div class="col-md-4"><div class="form-group group">', 'before'=> '</div></div>'
            ], rating_star::config('auto_percent_3'))

            ->html(false);
        ?>
    </div>
</div>