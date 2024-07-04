<?php

use Illuminate\Database\Schema\Blueprint;

if(!Admin::is()) return;
function Rating_Star_update_core(): void
{
    if(Admin::is() && Auth::check()) {
        $version = Option::get('rating_star_version');
        $version = (empty($version)) ? '3.3.0' : $version;
        if (version_compare(RATING_STAR_VERSION, $version) === 1) {
            $update = new Rating_Star_Update_Version();
            $update->runUpdate($version);
        }
    }
}
add_action('admin_init', 'Rating_Star_update_core');

Class Rating_Star_Update_Version {
    public function runUpdate($DiscountVersion): void
    {
        $listVersion    = ['2.0.0', '3.0.0', '4.0.0', '4.2.0', '4.3.0', '4.4.0', '4.4.2', '4.5.0', '4.6.0'];
        $model          = model();
        foreach ($listVersion as $version) {
            if(version_compare($version, $DiscountVersion) == 1) {
                $function = 'update_version_'.str_replace('.','_',$version);
                if(method_exists($this, $function)) $this->$function($model);
            }
        }
        Option::update('rating_star_version', RATING_STAR_VERSION );
    }
    public function update_version_2_0_0($model): void
    {
        Rating_Star_Update_Database::version_2_0_0($model);
    }
    public function update_version_3_0_0($model): void
    {
        Rating_Star_Update_Database::version_3_0_0($model);
    }
    public function update_version_4_0_0($model): void
    {
        Rating_Star_Update_Files::version_4_0_0($model);
        Rating_Star_Update_Database::version_4_0_0($model);
    }
    public function update_version_4_2_0($model): void
    {
        Rating_Star_Update_Database::version_4_2_0($model);
    }
    public function update_version_4_3_0($model): void
    {
        Rating_Star_Update_Database::version_4_3_0($model);
    }
    public function update_version_4_4_0($model): void
    {
        Rating_Star_Update_Files::version_4_4_0($model);
    }
    public function update_version_4_4_2($model): void
    {
        Rating_Star_Update_Database::version_4_4_2($model);
    }
    public function update_version_4_5_0($model): void
    {
        Rating_Star_Update_Files::version_4_5_0();
    }
    public function update_version_4_6_0($model): void
    {
        Rating_Star_Update_Database::version_4_6_0($model);
    }
}
Class Rating_Star_Update_Database {

    static function version_2_0_0($model): void
    {
        if(!schema()->hasColumn('rating_star', 'is_read')) {

            schema()->table('rating_star', function(Blueprint $table) {
                $table->integer('is_read')->default(1)->after('status');
                $table->integer('parent_id')->default(0)->after('status');
            });

            $model->query("UPDATE `".CLE_PREFIX."rating_star` SET `is_read`= '1' WHERE 1;");
        }
    }

    static function version_3_0_0($model): void
    {
        if(!schema()->hasColumn('rating_star', 'user_id')) {
            schema()->table('rating_star', function(Blueprint $table) {
                $table->integer('user_id')->default(0)->after('status');
            });
        }
    }

    static function version_4_0_0($model): void
    {
        if(!schema()->hasColumn('rating_star', 'like')) {
            schema()->table('rating_star', function(Blueprint $table) {
                $table->integer('like')->default(0)->after('user_id');
            });
        }
        $rating = Option::get('rating_star_setting');
        $rating['template'] = 'template1';
        Option::update('rating_star_setting', $rating);
    }

    static function version_4_2_0($model): void
    {
        $model->table('rating_star')::where('object_type', 'product')->update(['object_type' => 'products']);
    }

    static function version_4_3_0($model): void
    {
        if(!schema()->hasColumn('rating_star', 'type')) {

            schema()->table('rating_star', function($table) {
                $table->string('type', '100')->default('handmade');
            });

            $model->table('rating_star')::where('status', 'auto')->update(['type' => 'auto', 'status' => 'public']);
        }
    }

    static function version_4_4_2($model): void
    {
        if(!schema()->hasColumn('rating_star', 'like'))
        {
            schema()->table('rating_star', function($table) {
                $table->integer('like')->default(0)->after('user_id');
            });
        }
    }

    static function version_4_6_0($model): void
    {

        if(schema()->hasTable('rating_star')) {
            schema()->table('rating_star', function (Blueprint $table) {
                $table->dateTime('created')->default('CURRENT_TIMESTAMP')->change();
            });
        }

        $setting = Option::get('rating_star_setting');

        $options = model('system')::where('option_name', 'theme_option')->first();

        $options = unserialize($options->option_value);

        $setting = [
            'star_color'      => $setting['star']['color'] ?? '#ffbe00',
            'item_align'      => $setting['item_align'] ?? 'left',
            'item_position'   => $setting['item_position'] ?? 45,
        ];

        $options['rating_star_style'] = $setting;

        model('system')::where('option_name', 'theme_option')->update([
            'option_value' => serialize($options)
        ]);

        CacheHandler::delete('system');
    }
}

Class Rating_Star_Update_Files {
    static function version_4_0_0($model): void
    {
        $path = FCPATH.VIEWPATH.'plugins/'.RATING_STAR_NAME.'/';
        $Files = [
            'admin/rating-star-update.php',
            'admin/views/html-empty.php',
            'template/rating-star-product-form.php',
            'template/rating-star-post-review.php',
            'template/rating-star-post-review-item.php',
        ];
        foreach ($Files as $file) {
            if(file_exists($path.$file)) {
                unlink($path.$file);
            }
        }
    }
    static function version_4_4_0($model): void
    {
        $path = FCPATH.VIEWPATH.'plugins/'.RATING_STAR_NAME.'/';
        $Files = [
            'admin/views/html-setting.php',
            'admin/views/html-setting-default.php',
            'admin/views/html-setting-auto.php',
        ];
        foreach ($Files as $file) {
            if(file_exists($path.$file)) {
                unlink($path.$file);
            }
        }
    }
    static function version_4_5_0(): void
    {
        $path = FCPATH.VIEWPATH.'plugins/'.RATING_STAR_NAME.'/';
        $Files = [
            'rating-star.png',
        ];
        foreach ($Files as $file) {
            if(file_exists($path.$file)) {
                unlink($path.$file);
            }
        }
    }
}