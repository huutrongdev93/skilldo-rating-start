<?php
Ajax::client('RatingStar\Ajax\Web\RatingStarAjax::reviewLoad');
Ajax::client('RatingStar\Ajax\Web\RatingStarAjax::reviewSave');
Ajax::client('RatingStar\Ajax\Web\RatingStarAjax::reviewReply');
Ajax::client('RatingStar\Ajax\Web\RatingStarAjax::reviewLike');

Ajax::admin('RatingStar\Ajax\Admin\RatingStarAjax::load');
Ajax::admin('RatingStar\Ajax\Admin\RatingStarAjax::commentLoad');
Ajax::admin('RatingStar\Ajax\Admin\RatingStarAjax::commentAdd');
Ajax::admin('RatingStar\Ajax\Admin\RatingStarAjax::commentEdit');
Ajax::admin('RatingStar\Ajax\Admin\RatingStarAjax::commentDelete');
Ajax::admin('RatingStar\Ajax\Admin\RatingStarAjax::status');
Ajax::admin('RatingStar\Ajax\Admin\RatingStarAjax::save');
Ajax::admin('RatingStar\Ajax\Admin\RatingStarAjax::delete');
Ajax::admin('RatingStar\Ajax\Admin\RatingStarAjax::randomReview');