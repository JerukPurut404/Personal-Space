<?php
define('CMS_PUBLIC', true);
require __DIR__ . '/includes/lib.php';

$_SESSION = [];
session_destroy();
redirect('login.php');