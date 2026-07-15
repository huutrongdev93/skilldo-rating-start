<?php
namespace RatingStar\Services;

Class DeactivatorService
{
    public static function uninstall(): void
    {
        schema()->drop('rating_star');
    }
}