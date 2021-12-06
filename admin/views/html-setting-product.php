<div class="box">
    <div class="box-content p10">
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
            $Form
                ->add('rating_star_setting[item_align]', 'radio', [
                    'label' => 'Vị trí hiển thị', 'value' => 'left', 'single' => true,
                    'options' => ['left' => 'Canh trái', 'center' => 'Canh giữa', 'right' => 'Canh phải'],
                ], rating_star::config('item_align'))

                ->add('rating_star_setting[item_position]', 'number', [
                    'label' => 'Số thứ tự hiển thị', 'value' => '30',
                ], rating_star::config('item_position'))

                ->html(false);
            ?>
        </div>
    </div>
</div>
<div class="box">
    <div class="box-content">
        <div class="col-md-12">
            <div class="select-img">
                <div class="checkbox">
                    <input style="opacity: 0;" id="rating_star_setting_template_product_form_template1" type="radio" name="rating_star_setting[template]" value="template1" <?php echo (rating_star::config('template.product_form') == 'template1') ? 'checked' : '';?>>
                    <label for="rating_star_setting_template_product_form_template1" class="">
                        <span>Giao diện 1</span>
                        <?php Template::img('https://user-images.githubusercontent.com/86478092/126191091-cd97efd1-bb86-4da2-b8a5-879525e43246.png', '', ['style' => 'max-width:500px']);?>
                    </label>
                </div>
                <div class="checkbox">
                    <input style="opacity: 0;" id="rating_star_setting_template_product_form_template2" type="radio" name="rating_star_setting[template]" value="template2" <?php echo (rating_star::config('template.product_form') == 'template2') ? 'checked' : '';?>>
                    <label for="rating_star_setting_template_product_form_template2" class="">
                        <span>Giao diện 2</span>
                        <?php Template::img('https://user-images.githubusercontent.com/86478092/126294849-30c1d7ef-9661-4e90-92a3-8447248732d4.png', '', ['style' => 'max-width:500px']);?>
                    </label>
                </div>
            </div>
            <?php
            $Form
                ->add('rating_star_setting[reply]', 'radio', [
                    'label' => 'Trả lời review', 'value' => 'all', 'single' => true,
                    'options' => ['all' => 'Cho phép tất cả mọi người trả lời', 'login' => 'Chỉ cho phép thành viên', 'admin' => 'Chỉ cho phép admin trả lời'],
                ], rating_star::config('reply'))

                ->html(false);
            ?>
        </div>
    </div>
</div>