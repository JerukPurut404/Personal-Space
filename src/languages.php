<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$languages = ['nl' => 'Dutch', 'en' => 'English', 'id' => 'Indonesian'];

// All UI strings live in json/translations.json (edited in /admin/translations.php)
$translations = json_decode((string)file_get_contents(__DIR__ . '/../json/translations.json'), true) ?: [];

function t($key) {
    static $translations = null;
    if ($translations === null) {
        $path = __DIR__ . '/../json/translations.json';
        $translations = file_exists($path)
            ? json_decode(file_get_contents($path), true)
            : [];
    }

    $lang = $_SESSION['lang'] ?? 'en';
    return $translations[$lang][$key]
        ?? $translations['en'][$key]
        ?? $key;
}