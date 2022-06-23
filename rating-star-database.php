<?php
Class Rating_Star_Database {

    static public function create() {
        $model = model();
        if(!$model::schema()->hasTable('rating_star')) {
            $model::schema()->create('rating_star', function ($table) {
                $table->increments('id');
                $table->string('name', 200)->collate('utf8mb4_unicode_ci')->nullable();
                $table->string('email', 200)->collate('utf8mb4_unicode_ci')->nullable();
                $table->string('title', 200)->collate('utf8mb4_unicode_ci')->nullable();
                $table->text('message')->collate('utf8mb4_unicode_ci')->nullable();
                $table->integer('star')->default(0);
                $table->integer('object_id')->default(0);
                $table->string('object_type', 200)->collate('utf8mb4_unicode_ci');
                $table->string('status', 200)->collate('utf8mb4_unicode_ci');
                $table->integer('is_read')->default(0);
                $table->integer('parent_id')->default(0);
                $table->integer('user_id')->default(0);
                $table->integer('order')->default(0);
                $table->dateTime('created');
                $table->dateTime('updated')->nullable();
            });
        }
        $role = Role::get('root');
        $role->add_cap('rating_star');
        $role = Role::get('administrator');
        $role->add_cap('rating_star');
    }

    static public function drop() {
        $model = model();
        $model::schema()->drop('rating_star');
    }
}