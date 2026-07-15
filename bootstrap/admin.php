<?php
use RatingStar\Modules\Admin\RatingStarProducts;
use RatingStar\Modules\Admin\RatingStarSetting;
use RatingStar\Services\AdminService;
use RatingStar\Services\AssetsService;
use RatingStar\Services\RatingStarRoleService;

/*
|--------------------------------------------------------------------------
|  Admin Assets
|--------------------------------------------------------------------------
| admin - đăng ký các file css, js
*/
add_action('admin_assets', [AssetsService::class, 'admin']);
add_action('admin_navigation', [AdminService::class, 'navigation']);

/*
|--------------------------------------------------------------------------
|  Role
|--------------------------------------------------------------------------
| Đăng ký phân quyền vào hệt hống phân quyền của cms
*/
add_filter('user_role_editor_group', [RatingStarRoleService::class, 'group']);
add_filter('user_role_editor_label', [RatingStarRoleService::class, 'label']);

/*
|--------------------------------------------------------------------------
|  Product
|--------------------------------------------------------------------------
| randoms: random đánh giá khi thêm mới sản phẩm
| addColumnTitle: hiển thị số sao sản phẩm trên table
| delete: xóa đánh giá khi xóa sản phẩm
*/
if(config('rating-star::config.auto_enable') == 1)
{
    add_action('save_products_object_add', [RatingStarProducts::class, 'randoms'], 10, 2);
}
add_action('admin_product_table_column_title', [RatingStarProducts::class, 'addColumnTitle']);
add_action('delete_product_success', [RatingStarProducts::class, 'delete']);
add_action('delete_products_list_success', [RatingStarProducts::class, 'delete']);
add_filter('table_products_bulk_action_buttons', [RatingStarProducts::class, 'buttonAction']);

/*
|--------------------------------------------------------------------------
|  System
|--------------------------------------------------------------------------
| Đăng ký phân quyền cấu hình
*/
add_filter('admin_system_tabs' , [RatingStarSetting::class, 'register'], 20);
add_action('admin_system_rating_start_html', [RatingStarSetting::class, 'renderDefault'], 10);
add_action('admin_system_rating_start_html', [RatingStarSetting::class, 'renderAuto'], 30);
add_action('admin_system_rating_start_html', [RatingStarSetting::class, 'renderAutoData'], 40);
add_action('admin_system_rating_star_save', [RatingStarSetting::class, 'save'],10);

add_action('theme_custom_options', [AdminService::class, 'themeOptions']);