<?php
use Illuminate\Database\Capsule\Manager as DB;

Class Rating_Star_Database {

    static public function create(): void
    {
        if(!schema()->hasTable('rating_star')) {
            schema()->create('rating_star', function (\Illuminate\Database\Schema\Blueprint $table) {
                $table->increments('id');
                $table->string('name', 200)->collate('utf8mb4_unicode_ci')->nullable();
                $table->string('email', 200)->collate('utf8mb4_unicode_ci')->nullable();
                $table->string('title', 200)->collate('utf8mb4_unicode_ci')->nullable();
                $table->text('message')->collate('utf8mb4_unicode_ci')->nullable();
                $table->integer('star')->default(0);
                $table->integer('like')->default(0);
                $table->integer('object_id')->default(0);
                $table->string('object_type', 200)->collate('utf8mb4_unicode_ci');
                $table->string('status', 200)->collate('utf8mb4_unicode_ci');
                $table->string('type', 100)->collate('utf8mb4_unicode_ci')->default('handmade');
                $table->integer('is_read')->default(0);
                $table->integer('parent_id')->default(0);
                $table->integer('user_id')->default(0);
                $table->integer('order')->default(0);
                $table->dateTime('created')->default(DB::raw('CURRENT_TIMESTAMP'));
                $table->dateTime('updated')->nullable();
            });
        }

        Role::get('root')->add('rating_star');

        Role::get('administrator')->add('rating_star');
    }

    static public function drop(): void
    {
        schema()->drop('rating_star');
    }
}