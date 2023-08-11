<div class="row">
    <div class="col-md-3">
        <div class="ui-title-bar__group" style="padding-bottom:5px;">
            <h3 class="ui-title-bar__title" style="font-size:20px;">Sản phẩm</h3>
            <p style="margin-top: 10px; margin-left: 1px; color: #8c8c8c">Quản lý hiển thị đánh giá ở đối tượng sản phẩm</p>
        </div>
    </div>
    <div class="col-md-9">
        <div class="box">
            <div class="box-content" style="padding:10px;">
                <div class="row">
                    <div class="col-md-4">
                        <div class="product-item">
                            <div class="product-item__image"></div>
                            <div class="product-item__title"></div>
                            <div class="product-item__price"></div>
                            <div class="product-item__start">
                                <i class="fas fa-star" aria-hidden="true" style="color:var(--star-color); font-weight: bold;"></i>&nbsp;
                                <i class="fas fa-star" aria-hidden="true" style="color:var(--star-color); font-weight: bold;"></i>&nbsp;
                                <i class="fas fa-star" aria-hidden="true" style="color:var(--star-color); font-weight: bold;"></i>&nbsp;
                                <i class="fas fa-star" aria-hidden="true" style="color:var(--star-color); font-weight: bold;"></i>&nbsp;
                                <i class="fas fa-star" aria-hidden="true" style="color:var(--star-color); font-weight: bold;"></i>&nbsp;
                            </div>
                        </div>
                        <style>
                            .product-item {
                                border: 1px solid var(--content-bg);
                                padding:10px;
                            }
                            .product-item__image {
                                background-color: var(--content-bg);
                                min-height: 200px;
                            }
                            .product-item__title {
                                background-color: var(--content-bg);
                                height: 20px;
                                margin-top: 10px;
                            }
                            .product-item__price {
                                background-color: var(--content-bg);
                                margin-top: 10px;
                                height:20px; width: 50%;
                            }
                            .product-item__start {
                                margin-top: 10px;
                            }
                        </style>
                    </div>
                    <div class="col-md-8">
                        <?php
                        $Form = new FormBuilder();
                        $Form
                            ->add('rating_star_setting[item_align]', 'radio', [
                                'label' => 'Vị trí hiển thị', 'value' => 'left', 'single' => true,
                                'options' => ['left' => 'Canh trái', 'center' => 'Canh giữa', 'right' => 'Canh phải'],
                            ], $config['item_align'])

                            ->add('rating_star_setting[item_position]', 'number', [
                                'label' => 'Số thứ tự hiển thị', 'value' => '30',
                            ], $config['item_position'])
                            ->html(false);
                        ?>
                    </div>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
</div>
<hr />

<div class="row">
    <div class="col-md-3">
        <div class="ui-title-bar__group" style="padding-bottom:5px;">
            <h3 class="ui-title-bar__title" style="font-size:20px;">Mẫu form đánh giá sản phẩm</h3>
            <p style="margin-top: 10px; margin-left: 1px; color: #8c8c8c">Quản lý giao diện form đánh giá</p>
        </div>
    </div>
    <div class="col-md-9">
        <div class="box">
            <div class="box-content" style="padding:10px;">
                <div class="row">
                    <div class="select-img">
                        <div class="checkbox">
                            <input style="opacity: 0;" id="rating_star_setting_template_product_form_template1" type="radio" name="rating_star_setting[template]" value="template1" <?php echo ($config['template'] == 'template1') ? 'checked' : '';?>>
                            <label for="rating_star_setting_template_product_form_template1" class="">
                                <span>Giao diện 1</span>
                                <?php Template::img('https://user-images.githubusercontent.com/86478092/126191091-cd97efd1-bb86-4da2-b8a5-879525e43246.png', '', ['style' => 'max-width:500px']);?>
                            </label>
                        </div>
                        <div class="checkbox">
                            <input style="opacity: 0;" id="rating_star_setting_template_product_form_template2" type="radio" name="rating_star_setting[template]" value="template2" <?php echo ($config['template'] == 'template2') ? 'checked' : '';?>>
                            <label for="rating_star_setting_template_product_form_template2" class="">
                                <span>Giao diện 2</span>
                                <?php Template::img('https://user-images.githubusercontent.com/86478092/126294849-30c1d7ef-9661-4e90-92a3-8447248732d4.png', '', ['style' => 'max-width:500px']);?>
                            </label>
                        </div>
                        <div class="checkbox">
                            <input style="opacity: 0;" id="rating_star_setting_template_product_form_template3" type="radio" name="rating_star_setting[template]" value="template3" <?php echo ($config['template'] == 'template3') ? 'checked' : '';?>>
                            <label for="rating_star_setting_template_product_form_template3" class="">
                                <span>Giao diện 3</span>
                                <?php Template::img('https://user-images.githubusercontent.com/86478092/126294849-30c1d7ef-9661-4e90-92a3-8447248732d4.png', '', ['style' => 'max-width:500px']);?>
                            </label>
                        </div>
                    </div>
                    <?php
                    $Form
                        ->add('rating_star_setting[reply]', 'radio', [
                            'label' => 'Trả lời review', 'value' => 'all', 'single' => true,
                            'options' => ['all' => 'Cho phép tất cả mọi người trả lời', 'login' => 'Chỉ cho phép thành viên', 'admin' => 'Chỉ cho phép admin trả lời'],
                        ], $config['reply'])
                        ->html(false);
                    ?>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
</div>
<hr />


<style>
    .select-img {
        flex-wrap: nowrap;
    }
    .select-img img {
        width: 100%;
    }
</style>