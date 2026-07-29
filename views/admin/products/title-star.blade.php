<div class="skd-product-detail-reviews-star" style="text-align:left;color:#fd9a42; margin:5px 0; font-size:10px;">
    <a href="{!! Url::admin('plugins/rating-star?object='.$item->id.'&type=product') !!}">
        @include('partials.star-icon', ['star' => $averageStar, 'prefix' => 'fas', 'color' => '#fd9a42'])
        ( {!! $numberReview !!} đánh giá )
    </a>
</div>