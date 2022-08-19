<div class="col-md-12">    <div class="ui-title-bar__group">        <h1 class="ui-title-bar__title">Danh sách đánh giá</h1>        <p style="margin-top: 10px; margin-left: 1px; color: #8c8c8c">Đánh giá sản phẩm, bài viết</p>        <div class="ui-title-bar__action">            <a class="btn btn-default <?php echo (Request::Get('type') == '') ? 'active' : '';?>" href="<?php echo Url::admin('plugins?page=rating-star&object_type='.$object_type);?>">Khách hàng đánh giá</a>            <a class="btn btn-default <?php echo (Request::Get('type') == 'auto') ? 'active' : '';?>" href="<?php echo Url::admin('plugins?page=rating-star&object_type='.$object_type.'&type=auto');?>">Hệ thống đánh giá</a>        </div>    </div>    <div class="box">        <div class="box-heading">            <div class="box-heading-left"></div>            <div class="box-heading-right">                <form class="search-box" action="<?php echo Url::admin('plugins?page=rating-star');?>">                    <input type="hidden" name="page" value="rating-star">                    <input type="hidden" name="type" value="<?php echo $type;?>">                    <div class="form-group-search">                        <select name="star" class="form-control  form-control" id="star">                            <option value="0">Tất cả review</option>                            <option value="1" <?php echo (Request::Get('star') == 1) ? 'selected' : '';?>>1 Sao</option>                            <option value="2" <?php echo (Request::Get('star') == 2) ? 'selected' : '';?>>2 Sao</option>                            <option value="3" <?php echo (Request::Get('star') == 3) ? 'selected' : '';?>>3 Sao</option>                            <option value="4" <?php echo (Request::Get('star') == 4) ? 'selected' : '';?>>4 Sao</option>                            <option value="5" <?php echo (Request::Get('star') == 5) ? 'selected' : '';?>>5 Sao</option>                        </select>                    </div>                    <button type="submit" class="btn"><i class="fad fa-search"></i></button>                </form>            </div>        </div>        <!-- .box-content -->        <div class="box-content">            <table class="display table table-striped media-table">                <thead>                    <tr>                        <th class="manage-column">#</th>                        <th class="manage-column">Đối tượng</th>                        <th class="manage-column">Người Review</th>                        <th class="manage-column">Rating</th>                        <th class="manage-column">Nội dung đánh giá</th>                        <th class="manage-column">Ngày</th>                        <th class="manage-column">Trạng thái</th>                        <th class="manage-column">#</th>                    </tr>                </thead>                <tbody>                    <?php foreach ($rating_stars as $key => $item) { ?>                    <tr class="js_column tr_<?php echo $item->id;?> rating-star-item <?php echo ($item->is_read == 0) ? 'is_read' : '';?>">                        <td class="check-column"><input class="icheck select" value="<?php echo $item->id;?>" type="checkbox" name="select[]"></td>                        <td style="width:300px;">                            <p class="object"><a href="<?php echo Url::permalink($item->slug);?>" target="_blank"><?php echo $item->title;?></a></p>                            <?php                            $files = Metadata::get('rating_star', $item->id, 'attach', true);                            if(have_posts($files)) {                                ?>                                <div class="attach-images">                                    <?php foreach ($files as $path => $file) { ?>                                        <div class="images"><a href="<?php echo Url::base($path);?>" data-fancybox="group"><?php Template::img(Url::base($path));?></a></div>                                    <?php } ?>                                </div>                                <?php                            }                            ?>                        </td>                        <td style="width:100px;">                            <p><b><?php echo $item->name;?></b></p>                            <p><?php echo (!empty($item->email)) ? $item->email : '<label class="label label-info">auto</label>';?></p>                        </td>                        <td style="width:130px;">                            <div style="color: rgb(255, 190, 0); text-align: center;">                                <?php for($i = 0; $i < $item->star; $i++) { ?><i class="fa fa-star" aria-hidden="true" style="color:#FFBB03; font-weight: bold;"></i><?php } ?>                                <?php for($i = 0; $i < (5-$item->star); $i++) { ?><i class="far fa-star" aria-hidden="true" style="color:#ccc;"></i><?php } ?>                            </div>                        </td>                        <td style="width:400px;">                            <div class="message"><?php echo Str::limit($item->message, 200);?></div>                        </td>                        <td style="width:150px;"><p><?php echo $item->created;?></p></td>                        <td style="width:100px;">                            <p class="status text-center"><span class="label label-<?php echo ($item->status == 'public') ? 'success' : 'danger';?>"><?php echo ($item->status == 'public') ? 'Hiển thị' : 'Ẩn';?></span> </p>                        </td>                        <td style="width:200px;">                            <button class="btn-blue btn rating-star__btn-message" data-id="<?php echo $item->id;?>"><i class="fad fa-comments"></i></button>                            <button class="btn-white btn rating-star__btn-status" data-id="<?php echo $item->id;?>"><?php echo ($item->status == 'public') ? 'Ẩn' : 'Hiển thị';?> </button>                            <?php echo Admin::btnDelete(['id' => $item->id, 'module' => 'RatingStar']);?>                        </td>                    </tr>                    <?php } ?>                </tbody>            </table>            <div class="paging">                <div class="pull-left" style="padding-top:20px;"></div>                <div class="pull-right"><?php echo $pagination->html();?></div>            </div>        </div>        <!-- /.box-content -->    </div></div><div class="modal fade" id="rating-star__modal-message">    <div class="modal-dialog">        <form action="" id="rating-star__form-message">            <div class="modal-content">                <div class="modal-header">                    <button type="button" class="close" data-bs-dismiss="modal" aria-hidden="true">&times;</button>                    <h4 class="modal-title"></h4>                </div>                <div class="modal-body">                    <div class="form-group">                        <label for="">Tên hiển thị</label>                        <input name="comment_name" value="<?php echo Option::get('general_label');?>" class="form-control" placeholder="Tên hiển thị" required>                    </div>                    <div class="form-group">                        <label for="">Câu trả lời</label>                        <textarea name="comment" cols="40" rows="5" class="form-control tinymce-comment" ></textarea>                    </div>                    <div class="rating-star-comment"></div>                </div>                <div class="modal-footer">                    <button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>                    <button type="submit" class="btn btn-primary">Gửi trả lời</button>                </div>            </div><!-- /.modal-content -->        </form>    </div><!-- /.modal-dialog --></div><!-- /.modal --><style>    table.table .rating-star-item.is_read td {        background-color: #c8c8c8;    }    .rating-star-item .status .label {        display: block;padding:5px;border-radius: 5px;    }    .rating-star-item .object a {        color: #214171; font-weight: bold;    }    .rating-star-item .message {        font-size: 13px;        color: #111;        position: relative;        margin-top: 9px;        padding-left: 15px;        margin-bottom: 5px;        font-weight: 400;    }    .rating-star-item .message:before {        content: "";        position: absolute;        top: 0px;        left: 0px;        bottom: 0px;        width: 4px;        -moz-border-radius: 4px;        -webkit-border-radius: 4px;        border-radius: 4px;        background: rgba(0,0,0,0.1);    }    .rating-star-comment .rating-star-comment-item {        padding: 30px 0;        overflow-y: hidden;    }    .rating-star-comment .rating-star-comment__main {        float: left; width:100%;    }    .rating-star-comment .rating-star-comment__title {        font-size: 14px;color:#000;    }    .rating-star-comment .rating-star-comment__rating {        margin-top: 0; display:inline-block; padding-right:20px;font-size:15px;line-height:10px;    }    .rating-star-comment .rating-star-comment__main .buy-already {        font-size: 13px;        padding: 1px 0 1px 0px;        margin: 6px 0;        color: #22b345;        font-weight: 400;        background-size: contain;    }    .rating-star-comment .rating-star-comment__main .name {        font-size: 14px;        margin: 15px 0 5px 0;        font-weight: bold;        text-transform: capitalize;        text-align: left;    }    .rating-star-item .attach-images {        overflow: hidden;    }    .rating-star-item .attach-images .images {        float: left;        width: calc(100%/5 - 10px);        margin-right: 10px;        border-radius: 5px;        overflow: hidden;    }    .rating-star-item .attach-images img{        width: 100%; height: 100%; object-fit: cover;    }</style><script type="text/javascript">    $(function(){        let id = 0;        $('.rating-star__btn-message').click(function(){            id  = $(this).attr('data-id');            $('#rating-star__modal-message').modal('show');            let data = {                action : 'Rating_Star_Admin_Ajax::commentLoad',                id     : id            };            $.post(ajax, data, function(){}, 'json').done(function(data){                if(data.status === 'success') {                    $('#rating-star__form-message .rating-star-comment').html(data.html);                }            });            return false;        });        $('#rating-star__form-message').submit(function(){            let data = $(this).serializeJSON();            data.id = id;            data.action = 'Rating_Star_Admin_Ajax::commentSave';            $.post(ajax, data, function(){}, 'json').done(function(data){                show_message(data.message, data.status);                if(data.status === 'success') {                    $('#rating-star__form-message').trigger('reset');                }            });            return false;        });        $('.rating-star__btn-status').click(function(){            id  = $(this).attr('data-id');            let ths = $(this);            let item = $(this).closest('.rating-star-item');            let data = {                action : 'Rating_Star_Admin_Ajax::statusSave',                id     : id            };            $.post(ajax, data, function(){}, 'json').done(function(response){                if(response.status == 'success') {                    item.find('.-status').html(response.status);                    ths.text(response.status_label);                }            });            return false;        });        $('.rating-star__btn-delete').bootstrap_confirm_delete({            heading:'Xác nhận xóa',            message:'Bạn muốn xóa trường dữ liệu này ?',            callback:function ( event ) {                let button = event.data.originalObject;                id = button.attr('data-id');                if(id == null || id.length == 0) {                    show_message('Không có dữ liệu nào được xóa ?', 'error');                }                else {                    let data = {                        'action' : 'Rating_Star_Admin_Ajax::delete',                        'id'   : id,                    };                    $.post(ajax, data, function() {}, 'json').done(function(response) {                        show_message(response.message, response.status);                        if(response.status === 'success') {                            button.closest( 'tr' ).remove();                        }                    });                }            },        });        $(document).on('click', '.js_comment__btn_delete', function () {            id  = $(this).attr('data-id');            let item = $(this).closest('.rating-star-comment-item');            let data = {                action : 'Rating_Star_Admin_Ajax::commentDelete',                id     : id            };            $.post(ajax, data, function(){}, 'json').done(function(data){                if(data.status === 'success') {                    item.remove();                }            });            return false;        });    })</script>