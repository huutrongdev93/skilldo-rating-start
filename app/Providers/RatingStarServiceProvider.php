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

        $this->mergeConfig($configOption, 'rating-star::config');

        $this->mergeConfig($configOption, 'rating-star::theme');
    }
}
