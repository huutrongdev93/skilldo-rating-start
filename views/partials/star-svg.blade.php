@php
    /**
     * Lớp sao đã tô màu (đầy + nửa) theo điểm trung bình $star
     * $size   : kích thước svg
     * $fill   : màu tô
     * $stroke : màu viền
     */
    $parts  = \RatingStar\Supports\RatingStarHelper::starParts($star ?? 0);
    $size   = $size ?? 18;
    $fill   = $fill ?? 'var(--star-color)';
    $stroke = $stroke ?? 'var(--star-color)';
    $path   = 'M16 1.695l-4.204 8.518-9.401 1.366 6.802 6.631-1.605 9.363L16 23.153l8.408 4.42-1.605-9.363 6.802-6.63-9.4-1.367L16 1.695z';
    //Mỗi màu tô dùng 1 gradient riêng, trùng id giữa các khối là vô hại vì nội dung giống nhau
    $gradient = 'rating-star-half-'.preg_replace('/[^a-zA-Z0-9]/', '', $fill);
@endphp
@for( $i = 0; $i < $parts['full']; $i++ )
    <span>
        <svg xmlns="http://www.w3.org/2000/svg" width="{{ $size }}" height="{{ $size }}" viewBox="0 0 32 32">
            <path fill="{{ $fill }}" fill-rule="evenodd" stroke="{{ $stroke }}" stroke-width="1.5" d="{{ $path }}"></path>
        </svg>
    </span>
@endfor
@if( $parts['half'] )
    <span>
        <svg xmlns="http://www.w3.org/2000/svg" width="{{ $size }}" height="{{ $size }}" viewBox="0 0 32 32">
            <defs>
                <linearGradient id="{{ $gradient }}">
                    <stop offset="50%" stop-color="{{ $fill }}"></stop>
                    <stop offset="50%" stop-color="{{ $fill }}" stop-opacity="0"></stop>
                </linearGradient>
            </defs>
            <path fill="url(#{{ $gradient }})" fill-rule="evenodd" stroke="{{ $stroke }}" stroke-width="1.5" d="{{ $path }}"></path>
        </svg>
    </span>
@endif
