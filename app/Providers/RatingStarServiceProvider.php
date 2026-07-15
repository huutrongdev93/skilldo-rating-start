<?php

namespace RatingStar\Providers;

use SkillDo\Cms\Support\Option;
use SkillDo\ServiceProvider;

class RatingStarServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {
        $configOption = Option::get('rating_star_setting');

        $themeOption = Option::get('rating_star_style');

        $this->mergeConfig($configOption, 'rating-star::config');

        if(empty($themeOption['item_position']))
        {
            $themeOption['item_position'] = 45;
        }

        $this->mergeConfig($themeOption, 'rating-star::theme');
    }
}
