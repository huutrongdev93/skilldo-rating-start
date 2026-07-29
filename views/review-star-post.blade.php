<div class="skd-product-reviews-star" style="color: var(--star-color);margin-bottom:10px;height: 11px;font-size:13px;">
    @if($total != 0)
        @include('partials.star-icon', ['star' => $total])
    @endif
</div>