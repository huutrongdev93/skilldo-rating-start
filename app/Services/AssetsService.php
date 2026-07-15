<?php
namespace RatingStar\Services;

use SkillDo\Cms\Support\Admin;
use SkillDo\Cms\Template\Assets\AssetPosition;

class AssetsService
{
    static function web(AssetPosition $header, AssetPosition $footer): void
    {
        $header->add('rating-star', asset('rating-star::css/rt-style.css'), ['minify' => true]);
        $footer->add('micro-modal', asset('rating-star::js/micromodal.min.js'), ['minify' => false]);
    }

    static function webVariable($variables): array
    {
        $variables['--star-color'] =  config('rating-star::theme.color_star');
        $variables['--star-align'] =  config('rating-star::theme.item_align');
        return $variables;
    }

    static function admin(): void
    {
        Admin::asset()->location('header')->add('rating-star', asset('rating-star::css/style.admin.css'));
        Admin::asset()->location('footer')->add('rating-star', asset('rating-star::js/script.admin.js'));
    }
}