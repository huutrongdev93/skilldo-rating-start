<div class="skd-product-reviews-star" style="color: var(--star-color);margin-bottom:10px;height: 11px;font-size:13px;">
    @if($total != 0)
        @for( $i = 0; $i < $total; $i++ )
        <i class="fa fa-star" aria-hidden="true" style="color:var(--star-color); font-weight: bold;"></i>&nbsp;
        @endfor
        @for( $i = 0; $i < (5 - $total); $i++ )
        <i class="fas fa-star" aria-hidden="true" style="color:#ccc;"></i>&nbsp;
        @endfor
    @endif
</div>