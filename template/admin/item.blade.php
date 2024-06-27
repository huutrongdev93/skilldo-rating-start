<tr class="js_column tr_{{ $item->id}} js_rating_star_table_item rating-star-item {{ ($item->is_read == 0) ? 'is_read' : ''}}" data-item="{{ htmlentities(json_encode($item))}}">
    <td style="width:600px;" class="js_rating_star_table_review">
		<div class="review-item-info d-flex gap-2">
			<div class="review-item-avatar">
				<div class="avatar"><span>{{ rating_star::getKeyName($item->name)}}</span></div>
			</div>
			<div class="review-item-content">
				<div class="review-item-user d-flex gap-3">
					<span class="name">{{ $item->name}}</span>
					<span class="time">{{ $item->created}}</span>
				</div>
				<p class="review-item-comment mt-1 mb-1">
                    {{ Str::limit($item->message, 200)}}
				</p>
				<div class="review-item-object d-flex align-items-center gap-2">
					<i class="fa-light fa-arrow-turn-down-right"></i>
					<a href="{{ Url::permalink($item->slug)}}" target="_blank">{{ $item->title}}</a>
				</div>
			</div>
		</div>
    </td>
    <td class="text-center" style="width:100px;">
        <div style="color: rgb(255, 190, 0); text-align: center;">
           	@for($i = 0; $i < $item->star; $i++) 
           		<i class="fa fa-star" aria-hidden="true" style="color:#FFBB03; font-weight: bold;font-size: 10px"></i>
           	@endfor
           	@for($i = 0; $i < (5-$item->star); $i++) 
           		<i class="far fa-star" aria-hidden="true" style="color:#ccc;font-size: 10px"></i>
           	@endfor
        </div>
    </td>
    <td class="js_column_status">
	    
	    @if($item->status == 'pending') 
	    	<span class="badge badge-yellow">Đợi duyệt</span>
	    @endif
	    @if($item->status == 'public') 
	    	<span class="badge badge-green">Hiển thị</span>
	    @endif
	    @if($item->status == 'hidden') 
	    	<span class="badge badge-red">Tạm ẩn</span>
	    @endif
    </td>
	<td>
        @php
        $files = Metadata::get('rating_star', $item->id, 'attach', true);
        @endphp
        @if(have_posts($files))
	        <div class="attach-images">
                @foreach ($files as $path => $file)
			        <div class="images"><a href="{{ Url::base($path)}}" data-fancybox="group">{!! Template::img(Url::base($path))!!}</a></div>
                @endforeach
	        </div>
      	@else {{ 'không có'  }}
        @endif
	</td>
	<td>{{ (empty($item->reply)) ? 'không có' : $item->reply. ' trả lời'}}</td>
    <td style="width:290px;" class="text-right js_column_action">
        @if($item->status == 'pending' ||$item->status == 'hidden') 
        	<button class="btn-green btn btn-green-bg js_rating_star_btn_status p-1 ps-2 pe-2" data-id="{{ $item->id}}">Hiển thị</button>
        @endif
	    <button class="btn-red btn btn-red-bg js_rating_star_btn_reply p-1 ps-2 pe-2" data-id="{{ $item->id}}">Trả lời</button>
	    <button class="btn-blue btn btn-blue-bg js_rating_star_btn_edit p-1 ps-2 pe-2" data-id="{{ $item->id}}">Chỉnh sửa</button>
	    <button class="btn-red btn js_rating_star_btn_delete p-1 ps-2 pe-2" data-id="{{ $item->id}}">{!! Admin::icon('delete')!!}</button>
    </td>
</tr>