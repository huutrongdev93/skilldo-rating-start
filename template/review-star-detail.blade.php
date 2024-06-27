<div class="skd-product-reviews-star skd-product-detail-reviews-star" style="text-align:left;color:var(--star-color);font-size:13px; margin-bottom: 10px;">
    <div class="product-reviews__inner" style="display: inline-block; text-align: left">
        @for( $i = 0; $i < 5; $i++ )
        <span>
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 32 32">
                <path fill="none" fill-rule="evenodd" stroke="var(--star-color)" stroke-width="1.5" d="M16 1.695l-4.204 8.518-9.401 1.366 6.802 6.631-1.605 9.363L16 23.153l8.408 4.42-1.605-9.363 6.802-6.63-9.4-1.367L16 1.695z"></path>
            </svg>
        </span>
        @endfor
        <div style="width: 100%;">
            @for( $i = 0; $i < $total_star; $i++ )
            <span>
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 32 32">
                    <path fill="var(--star-color)" fill-rule="evenodd" stroke="var(--star-color)" stroke-width="1.5" d="M16 1.695l-4.204 8.518-9.401 1.366 6.802 6.631-1.605 9.363L16 23.153l8.408 4.42-1.605-9.363 6.802-6.63-9.4-1.367L16 1.695z"></path>
                </svg>
            </span>
            @endfor
        </div>
        <span class="star-count">({!! $total_number_review !!} {{ trans('template.rating.rate') }})</span>
    </div>
</div>