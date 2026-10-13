<?php
require_once __DIR__ . '/config.php';
$music = null;
if (isset($_GET['id']) && is_scalar($_GET['id']) && filter_var($_GET['id'], FILTER_VALIDATE_INT) !== false && (int) $_GET['id'] > 0) {
    $musicId = (int) $_GET['id'];
    $result = $conn->query("SELECT m.id, m.title, m.artist, m.description, m.banner_path, m.audio_path,
            m.status, COALESCE(c.name, 'Independent') AS category_name
        FROM music_list m LEFT JOIN category_list c ON c.id = m.category_id
        WHERE m.id = {$musicId} AND m.delete_flag = 0 LIMIT 1");
    $music = $result->fetch_assoc() ?: null;
}
?>
<?php if ($music): $art = validate_image($music['banner_path']); ?>
<nav class="breadcrumbs" aria-label="Breadcrumb"><a href="<?= e(base_url) ?>">Discover</a><i class="fas fa-chevron-right" aria-hidden="true"></i><a href="<?= e(base_url) ?>?page=music_list">Collection</a><i class="fas fa-chevron-right" aria-hidden="true"></i><span><?= e($music['title']) ?></span></nav>
<article class="track-detail">
    <div class="detail-art"><img src="<?= e($art) ?>" alt="Cover art for <?= e($music['title']) ?>"></div>
    <div class="detail-copy"><p class="eyebrow eyebrow-dark"><?= e($music['category_name']) ?> · TRACK DETAILS</p><h1><?= e($music['title']) ?></h1><p class="detail-artist">by <?= e($music['artist']) ?></p>
        <div class="detail-actions">
            <?php if ($music['audio_path'] !== ''): ?><button class="button button-primary" type="button" data-track-play data-track-id="<?= (int) $music['id'] ?>" data-audio-url="<?= e(base_url.$music['audio_path']) ?>" data-track-title="<?= e($music['title']) ?>" data-track-artist="<?= e($music['artist']) ?>" data-track-art="<?= e($art) ?>"><i class="fas fa-play" aria-hidden="true"></i> Play track</button><button class="button button-outline favorite-toggle" type="button" data-favorite-toggle data-track-id="<?= (int) $music['id'] ?>" data-track-title="<?= e($music['title']) ?>" aria-pressed="false"><i class="far fa-heart" aria-hidden="true"></i> Save track</button><a class="button button-outline" href="<?= e(base_url.$music['audio_path']) ?>" download="<?= e($music['title'].'.'.pathinfo($music['audio_path'], PATHINFO_EXTENSION)) ?>"><i class="fas fa-download" aria-hidden="true"></i> Download</a><?php endif; ?>
        </div>
        <?php if (trim($music['description']) !== ''): ?><div class="detail-description"><?= nl2br(e(html_entity_decode($music['description'], ENT_QUOTES | ENT_HTML5, 'UTF-8')), false) ?></div><?php endif; ?>
        <a class="text-link detail-back" href="<?= e(base_url) ?>?page=music_list"><i class="fas fa-arrow-left" aria-hidden="true"></i> Back to the collection</a>
    </div>
</article>
<?php else: http_response_code(404); ?>
<section class="empty-state detail-empty"><span class="empty-icon"><i class="fas fa-compact-disc" aria-hidden="true"></i></span><h1>Track not found.</h1><p>This track may have been removed. There’s more to discover in the collection.</p><a class="button button-primary" href="<?= e(base_url) ?>?page=music_list">Browse tracks <i class="fas fa-arrow-right" aria-hidden="true"></i></a></section>
<?php endif; ?>
