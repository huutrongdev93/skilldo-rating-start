<div class="skd-product-detail-reviews-star" style="text-align:left;color:#fd9a42; margin:5px 0; font-size:10px;">
    <a href="{!! Url::admin('plugins/rating-star?object='.$item->id.'&type=product') !!}">
        @for( $i = 0; $i < $averageStar; $i++ )
        <i class="fas fa-star" style="color:#fd9a42; font-weight: bold;"></i>&nbsp
        @endfor
        @for( $i = 0; $i < (5 - $averageStar); $i++ )
        <i class="fas fa-star" style="color:#ccc;"></i>&nbsp;
        @endfor
        ( {!! $numberReview !!} đánh giá )
    </a>
</div>