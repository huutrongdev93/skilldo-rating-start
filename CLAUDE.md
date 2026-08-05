# CLAUDE.md — Plugin rating-star

File này giúp agent hiểu ngay cấu trúc plugin mà không cần scan lại source. Đọc TRƯỚC khi sửa bất kỳ file nào trong plugin.

## Plugin này là gì

**Rating Star** — đánh giá sao (review) cho **sản phẩm** (sicommerce) và **bài viết**: khách gửi đánh giá kèm ảnh, admin duyệt/trả lời/ẩn, và tính năng **tự sinh đánh giá ảo** (auto/random) khi tạo sản phẩm mới (dữ liệu fake từ API `cms/data-fake/rating-start` hoặc tự nhập lưu ở `assets/auto-data.json`).

- **Namespace PHP: `RatingStar\*`**. Main class `RatingStar` trong `index.php`. Version 5.0.2.
- Provider: `RatingStarServiceProvider` — không đăng ký alias nào, chỉ merge 2 option vào config.
- **Phụ thuộc mềm vào plugin sicommerce** (`Ecommerce\Models\Product`): cột sao trong bảng sản phẩm, random review khi thêm SP, xóa review khi xóa SP. Không khai dependency trong plugin.json — tắt sicommerce thì các hook liên quan sản phẩm sẽ lỗi class not found khi được gọi.

## Database & config

- **1 bảng `rating_star`**: name/email/title/message, `star`, `like`, `object_id` + `object_type` (`products` | `post` | `comment` — comment = reply, dùng `parent_id`), `status` (`public`/`pending`/`hidden`), `type` (`handmade`/`auto`), `is_read` (badge admin), `user_id`.
- **Metadata** (bảng `metadata` của core, qua `SkillDo\Cms\Support\Metadata`): key `rating_star` trên products/post lưu tổng hợp `['count','star']`; key `attach` trên object_type `rating_star` lưu ảnh đính kèm. Mọi thao tác đổi status/xóa đều phải đồng bộ lại metadata tổng hợp này.
- **Options**: `rating_star_setting` → merge vào `rating-star::config.*` (product_enable, post_enable, has_approving, illegal_message, reply, auto_enable, auto_min/max_number, auto_percent_5/4/3, autoDataType); `rating_star_style` → `rating-star::theme.*` (color_star, item_align, item_position — dùng làm priority hook, template).

## Map file

| File | Chức năng |
|---|---|
| `index.php` | Class `RatingStar` — active tạo bảng + cấp quyền, uninstall drop bảng |
| `bootstrap/admin.php` | Wire hook admin: assets, navigation, role, hooks sản phẩm (random/cột sao/xóa), tab setting hệ thống, theme options. Hook `save_products_object_add` → `RatingStarProducts::randoms` CHỈ đăng ký khi `auto_enable == 1` |
| `bootstrap/ajax.php` | Registry ajax: web `reviewLoad/reviewSave/reviewReply/reviewLike`; admin `load/commentLoad/commentAdd/commentEdit/commentDelete/status/save/delete/randomReview` |
| `bootstrap/web.php` | Assets frontend, CSS variable (`--star-color`), chèn form review: `view_post_detail_after`@99 (bài viết), `product_detail_tabs`@30 (form SP), `product_detail_info`@6 (sao detail), `product_object_info`@`theme.item_position` (sao trong product box) |
| `config/config.php` / `config/theme.php` | Defaults chức năng / giao diện |
| `app/Ajax/Web/RatingStarAjax.php` | Ajax web: `reviewLoad` (phân trang + lọc sao), `reviewSave` (validate ảnh, cập nhật metadata, bắn `rating_star_save_success`), `reviewReply` (lưu `object_type='comment'`), `reviewLike` |
| `app/Ajax/Admin/RatingStarAjax.php` | Ajax admin: danh sách/filter, quản lý reply, `status` (đổi trạng thái + đồng bộ metadata sao), `save`, `delete`, `randomReview` (random hàng loạt cho SP được chọn) |
| `app/Models/RatingStar.php` | Model bảng `rating_star`; boot deleted: trừ metadata tổng hợp của object + xóa các reply con |
| `app/Modules/Admin/RatingStarProducts.php` | Tích hợp bảng sản phẩm admin: `randoms($id)` sinh review ảo (BiasRandom), `addColumnTitle` (badge sao), `delete` (dọn review khi xóa SP), `buttonAction` (bulk "Tạo đánh giá") |
| `app/Modules/Admin/RatingStarSetting.php` | Tab "Đánh giá sao" trong System: render 3 khối form qua `admin_system_rating_start_html`, `save()` lưu option + ghi handmade data ra `assets/auto-data.json` |
| `app/Modules/Web/RatingStarPost.php` | Form + số sao cho trang chi tiết bài viết (chỉ khi `Theme::isPage('post_detail')`) |
| `app/Modules/Web/RatingStarProduct.php` | Render sao cho sản phẩm: `object()` (product box), `detail()` (trang chi tiết), `form()` (theo `theme.template`), `template()` (partial số sao) |
| `app/Macros/ServiceCms.php` | Macro `ServiceCms::ratingStartDataFake()` — gọi API lấy dữ liệu fake |
| `app/Services/ActivatorService.php` / `DeactivatorService.php` | Tạo/drop bảng `rating_star`, cấp quyền `rating_star` cho root/administrator |
| `app/Services/AdminService.php` | Menu admin (badge = số review `is_read=0`), trang quản lý (đánh dấu đã đọc), theme options |
| `app/Services/AssetsService.php` | Đăng ký CSS/JS web + admin; `webVariable()` bơm `--star-color`/`--star-align` |
| `app/Services/BiasRandom.php` | Weighted random cho auto-review |
| `app/Services/RatingStarRoleService.php` | Đăng ký nhóm quyền `rating_star` |
| `app/Supports/RatingStarHelper.php` | Helper: `module()` (đăng ký loại object review qua filter `rating_start_register`), `random()` (sinh review ảo), `avgStar()`, `starParts()` (full/half/empty), `timeElapsed()`, `getKeyName()` (avatar chữ cái) |

**views/**: `admin/` (trang quản lý — JS class `RatingStarTableHandle` nằm inline trong `index.blade.php`), `partials/` (star-icon FA + star-svg gradient nửa sao), `review*.blade.php` (modal danh sách + item + tóm tắt sao), `template1|2|3.blade.php` (3 kiểu form — template1 cho bài viết, template2/3 chọn qua config cho SP).

**assets/**: CSS viết bằng LESS (sửa `.less`, không sửa `.css`); `micromodal.min.js` (popup); `auto-data.json` (dữ liệu review mẫu handmade — hiện RỖNG); JS admin phần lớn nằm inline trong view chứ không ở `script.admin.js`.

**language/**: vi + en, 4 file: `messages` (ajax), `review` (khối danh sách), `template` (form), `lang-js` (chuỗi cho JS). Gọi `trans('rating-star::messages.x')`.

## Hooks plugin bắn ra cho bên ngoài

- `apply_filters('rating_star_save_error', [], $rating)` — chặn/thêm lỗi trước khi lưu review.
- `do_action('rating_star_save_success', $id, $rating)` — sau khi tạo review.
- `apply_filters('rating_start_register', [...])` — đăng ký thêm loại object có thể review (⚠️ tên hook typo "start" thay vì "star" — GIỮ NGUYÊN, đổi sẽ gãy tương thích).

## Quy tắc khi sửa

1. Đổi status/xóa review luôn phải **đồng bộ metadata tổng hợp** `rating_star` (count/star) trên object gốc — xem cách `status()`/model boot deleted làm.
2. Reply là bản ghi `rating_star` với `object_type='comment'` + `parent_id` — không phải bảng riêng.
3. JS admin chính nằm **inline trong `views/admin/index.blade.php`** (class `RatingStarTableHandle`) — đừng tìm trong assets/js.
4. Sửa CSS → sửa file `.less`. Chuỗi hiển thị dùng `trans('rating-star::...')`.
5. Tính năng auto-review có 2 nguồn dữ liệu (`autoDataType`): `auto` = API fake, `handmade` = `assets/auto-data.json`.

## Gotcha / nghi vấn bug (đã phát hiện khi scan, CHƯA sửa)

- `app/Ajax/Web/RatingStarAjax.php:372` — `reviewLike()` khi thành công lại gọi `response()->error(trans('ajax.save.success'))` thay vì `->success(...)` → FE luôn nhận status error.
- `app/Ajax/Admin/RatingStarAjax.php:390` — `save()` gán `$ratingStar->phone` nhưng bảng/model **không có cột `phone`** → field điện thoại trong form sửa admin không bao giờ được lưu.
- `views/review-item.blade.php:19` — gọi `RatingStarProduct::template()` với 3 tham số (method chỉ nhận 2) và key config `theme.color.star.detail` không tồn tại — code chết.
- Config `rating-star::config.reply` (all/login/admin) được dùng để giới hạn quyền trả lời nhưng **không có UI để sửa** (dòng lưu bị comment trong `RatingStarSetting::save()`:134) → luôn là `'all'`.
- `views/review-star-post.blade.php` — view mồ côi, không nơi nào gọi.
- `assets/auto-data.json` rỗng: nếu admin chọn `autoDataType=handmade` mà chưa lưu dữ liệu mẫu, `RatingStarHelper::random()` sẽ `array_rand()` trên mảng rỗng → lỗi khi auto-review chạy.
