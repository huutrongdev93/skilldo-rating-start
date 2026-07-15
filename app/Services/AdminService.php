<?php
namespace RatingStar\Services;

use RatingStar\Models\RatingStar;
use SkillDo\Cms\Menu\AdminMenu;
use SkillDo\Cms\Support\ThemeOption;
use Illuminate\Support\Facades\DB;
use SkillDo\Support\Auth;

class AdminService
{
    static public function navigation(): void
    {
        if(Auth::hasCap('rating_star'))
        {
            $count = RatingStar::where('is_read',0)->count();

            AdminMenu::add('rating-star', 'Đánh giá', 'plugins/rating-star', [
                'icon' => '<img src="'.asset('rating-star::images/rating-star.png').'">',
                'callback' => [self::class, 'page'],
                'position' => 30,
                'count' => $count
            ]);
        }
    }

    static public function page(): void
    {
        DB::table('rating_star')->where('is_read', 0)->update(['is_read' => 1]);

        echo view('rating-star::admin/index');
    }

    static function themeOptions(): void
    {
        ThemeOption::addGroup('rating-star', [
            'position' => 40,
            'label' => 'Đánh giá sao',
            'icon' => '<i class="fa-solid fa-stars"></i>',
            'form' => function (\SkillDo\Cms\Form\Form $form) {
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
}