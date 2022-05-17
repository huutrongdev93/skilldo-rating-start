<?php
Class Rating_Star_Database {

    static public function create() {
        $model = get_model('plugins', 'backend');
        $model->query("CREATE TABLE IF NOT EXISTS `".CLE_PREFIX."rating_star` (
            `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `name` varchar(255) COLLATE utf8_unicode_ci NULL,
            `email` varchar(255) COLLATE utf8_unicode_ci NULL,
            `title` varchar(255) COLLATE utf8_unicode_ci NULL,
            `message` text COLLATE utf8_unicode_ci NULL,
            `star` int(11) NOT NULL DEFAULT '0',
            `object_type` varchar(255) COLLATE utf8_unicode_ci NULL,
            `object_id` int(11) NOT NULL DEFAULT '0',
            `status` varchar(255) COLLATE utf8_unicode_ci NULL,
            `is_read` int(11) NOT NULL DEFAULT '0',
	    `like` int(11) NOT NULL DEFAULT '0',
            `parent_id` int(11) NOT NULL DEFAULT '0',
            `user_id` int(11) NOT NULL DEFAULT '0',
            `order` int(11) NOT NULL DEFAULT '0',
            `created` datetime DEFAULT NULL,
            `updated` datetime DEFAULT NULL
	    ) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
        $role = get_role('root');
        $role->add_cap('rating_star');
        $role = get_role('administrator');
        $role->add_cap('rating_star');
    }

    static public function drop() {
        $model = get_model('plugins', 'backend');
        $model->query("DROP TABLE IF EXISTS `".CLE_PREFIX."rating_star`");
    }
}
