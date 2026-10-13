<?php
$selectedCategory = null;
$categoryWasRequested = array_key_exists('cid', $_GET);
if ($categoryWasRequested && is_scalar($_GET['cid']) && filter_var($_GET['cid'], FILTER_VALIDATE_INT) !== false) {
    $categoryId = (int) $_GET['cid'];
    $categoryResult = $conn->query("SELECT id, name, description FROM category_list
        WHERE id = {$categoryId} AND status = 1 AND delete_flag = 0 LIMIT 1");
    $selectedCategory = $categoryResult->fetch_assoc() ?: null;
}

$musicSql = "SELECT m.id, m.title, m.artist, m.description, m.banner_path, m.audio_path,
        COALESCE(c.name, 'Independent') AS category_name
    FROM music_list m
    LEFT JOIN category_list c ON c.id = m.category_id
    WHERE m.status = 1 AND m.delete_flag = 0 AND m.audio_path <> ''";
if ($selectedCategory) {
    $musicSql .= ' AND m.category_id = ' . (int) $selectedCategory['id'];
}
$musicSql .= ' ORDER BY m.title ASC, m.id ASC';
$musicTracks = $conn->query($musicSql)->fetch_all(MYSQLI_ASSOC);
$listTitle = $selectedCategory ? $selectedCategory['name'] : ($categoryWasRequested ? 'Genre not found' : 'The collection');
?>
<section class="page-intro music-intro">
    <p class="eyebrow eyebrow-dark">THE MUSIXX COLLECTION</p>
    <div class="page-intro-row"><div><h1><?= e($listTitle) ?><?php if (!$categoryWasRequested): ?> <em>sounds good.</em><?php endif; ?></h1><p><?= $selectedCategory && $selectedCategory['description'] !== '' ? e($selectedCategory['description']) : 'A small collection of sounds for every kind of moment.' ?></p></div><span class="intro-count"><?= count($musicTracks) ?><small>tracks</small></span></div>
</section>

<?php if (!$categoryWasRequested || $selectedCategory): ?>
<section class="browse-tools" aria-label="Find music">
    <label class="search-box"><i class="fas fa-search" aria-hidden="true"></i><input type="search" id="track-search" placeholder="Search tracks, artists, genres" aria-label="Search tracks"><kbd>/</kbd></label>
    <div class="browse-tools-right"><span class="results-caption" id="track-results"><?= count($musicTracks) ?> <?= count($musicTracks) === 1 ? 'track' : 'tracks' ?></span><a class="text-link" href="<?= e(base_url) ?>?page=categories">All genres <i class="fas fa-arrow-right" aria-hidden="true"></i></a></div>
</section>

<?php if ($musicTracks): ?>
<div class="track-grid collection-grid" id="track-grid">
    <?php foreach ($musicTracks as $index => $track): include 'inc/trackCard.php'; ?>
    <?php endforeach; ?>
</div>
<p class="empty-search" id="track-empty" hidden>No tracks match that search. Try a different title or artist.</p>
<?php else: ?>
<div class="empty-state"><span class="empty-icon"><i class="fas fa-compact-disc" aria-hidden="true"></i></span><h2>No playable tracks yet.</h2><p>Tracks appear here after an audio file is added and the track is active.</p></div>
<?php endif; ?>
<?php else: ?>
<div class="empty-state"><span class="empty-icon"><i class="fas fa-map-signs" aria-hidden="true"></i></span><h2>That genre isn’t here.</h2><p>It may have been removed or turned off. Browse the active genres to find another sound.</p><a class="button button-primary" href="<?= e(base_url) ?>?page=categories">Browse genres <i class="fas fa-arrow-right" aria-hidden="true"></i></a></div>
<?php endif; ?>
