<?php

use SkillDo\Service\ServiceCms;

ServiceCms::macro('ratingStartDataFake', function() {

    $object = $this->call('get', 'cms/data-fake/rating-start')->getData();

    if(!$object) return false;

    return (object)$object;
});