<?php
$siteName = $_settings->info('name') ?: 'Musixx';
$pageName = isset($_GET['page']) && is_string($_GET['page']) ? $_GET['page'] : 'home';
$pageTitles = [
    'home' => 'Discover',
    'categories' => 'Genres',
    'music_list' => 'Listen',
    'about' => 'About',
    'music_view' => 'Track details',
    'favorites' => 'Saved tracks',
];
$documentTitle = ($pageTitles[$pageName] ?? 'Not found') . ' · ' . $siteName;
?>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#111713">
    <meta name="description" content="Discover and listen to the Musixx collection.">
    <title><?= e($documentTitle) ?></title>
    <link rel="icon" href="<?= e(validate_image($_settings->info('logo'))) ?>">
    <link rel="stylesheet" href="<?= e(base_url) ?>plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="<?= e(base_url) ?>dist/css/adminlte.min.css">
    <link rel="stylesheet" href="<?= e(base_url) ?>assets/css/style.css">
</head>
