<?php

use SkillDo\Cms\Support\Role;
use SkillDo\Support\Path;

class RatingStar
{
    public function active(): void
    {
        include_once Path::plugin('rating-star/app/Services/ActivatorService.php');

        \RatingStar\Services\ActivatorService::activate();
    }

    public function uninstall(): void
    {
        include_once Path::plugin('rating-star/app/Services/DeactivatorService.php');

        \RatingStar\Services\DeactivatorService::uninstall();
    }
}