<?php
function RatingStarThemeOptions(): void
{
    ThemeOption::addGroup('rating-star', [
        'position' => 40,
        'label' => 'Đánh giá sao',
        'icon' => '<i class="fa-solid fa-stars"></i>',
        'form' => function (\SkillDo\Form\Form $form) {
            $form->color('rating_star_style[color_star]', ['label' => 'Màu biểu tượng sao']);
            $form
                ->radio('rating_star_style[item_align]',
                    ['left' => 'Canh trái', 'center' => 'Canh giữa', 'right' => 'Canh phải'],
                    [ 'label' => 'Vị trí hiển thị', 'value' => 'left', 'single' => true, ])
                ->number('rating_star_style[item_position]', [
                    'label' => 'Số thứ tự hiển thị', 'value' => '30',
                ]);
        }
    ]);
}

add_action('theme_custom_options', 'RatingStarThemeOptions');