<?php
const ADMIN_PASSWORD_HASH = '$2y$10$7MDz1OjrpGrLEHMZhrrizObU2echDsuKofMsowwEmOYaFeQYgh0lu';

const LANGS      = ['en' => 'English', 'nl' => 'Nederlands', 'id' => 'Bahasa Indonesia'];
const IMAGE_EXT  = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];

define('ROOT_DIR',  dirname(__DIR__, 2));
define('JSON_DIR',  ROOT_DIR . '/json');
define('MEDIA_DIR', ROOT_DIR . '/media');
define('BLOG_DIR',  ROOT_DIR . '/blog');