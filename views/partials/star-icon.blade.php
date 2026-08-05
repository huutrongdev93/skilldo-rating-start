@php
    /**
     * Dãy 5 sao Font Awesome theo điểm trung bình $star (hỗ trợ nửa sao)
     * $prefix     : bộ icon fontawesome (fal, fas ...)
     * $color      : màu sao đã đạt
     * $emptyColor : màu sao chưa đạt
     */
    $parts      = \RatingStar\Supports\RatingStarHelper::starParts($star ?? 0);
    $prefix     = $prefix ?? 'fal';
    $color      = $color ?? 'var(--star-color)';
    $emptyColor = $emptyColor ?? '#ccc';
@endphp
@for( $i = 0; $i < $parts['full']; $i++ )
    <i class="{{ $prefix }} fa-star" aria-hidden="true" style="color:{{ $color }}; font-weight: bold;"></i>&nbsp;
@endfor
@if( $parts['half'] )
    <i class="{{ $prefix }} fa-star-half-alt" aria-hidden="true" style="color:{{ $color }}; font-weight: bold;"></i>&nbsp;
@endif
@for( $i = 0; $i < $parts['empty']; $i++ )
    <i class="fas fa-star" aria-hidden="true" style="color:{{ $emptyColor }};"></i>&nbsp;
@endfor
