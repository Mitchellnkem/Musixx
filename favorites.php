<?php
$musicTracks = $conn->query("SELECT m.id, m.title, m.artist, m.description, m.banner_path, m.audio_path,
        COALESCE(c.name, 'Independent') AS category_name
    FROM music_list m LEFT JOIN category_list c ON c.id = m.category_id
    WHERE m.status = 1 AND m.delete_flag = 0 AND m.audio_path <> ''
    ORDER BY m.title ASC, m.id ASC")->fetch_all(MYSQLI_ASSOC);
?>
<section class="page-intro music-intro">
    <p class="eyebrow eyebrow-dark">YOUR PERSONAL CRATE</p>
    <div class="page-intro-row"><div><h1>Saved <em>for later.</em></h1><p>Your favorites stay in this browser, ready whenever the mood returns.</p></div><span class="intro-count" id="favorites-count"><?= count($musicTracks) ?><small>tracks</small></span></div>
</section>
<?php if ($musicTracks): ?>
<section class="browse-tools" aria-label="Find saved music">
    <label class="search-box"><i class="fas fa-search" aria-hidden="true"></i><input type="search" id="track-search" placeholder="Search your saved tracks" aria-label="Search saved tracks"><kbd>/</kbd></label>
    <div class="browse-tools-right"><span class="results-caption" id="track-results">Saved tracks</span></div>
</section>
<div class="track-grid collection-grid" id="saved-grid" data-saved-only>
    <?php foreach ($musicTracks as $index => $track): include 'inc/trackCard.php'; endforeach; ?>
</div>
<p class="empty-search" id="track-empty" hidden>No saved tracks match that search.</p>
<section class="empty-state" id="saved-empty" hidden><span class="empty-icon"><i class="far fa-heart" aria-hidden="true"></i></span><h2>Your saved crate is empty.</h2><p>Tap the heart on any track to keep it close.</p><a class="button button-primary" href="<?= e(base_url) ?>?page=music_list">Find tracks to save <i class="fas fa-arrow-right" aria-hidden="true"></i></a></section>
<?php else: ?>
<section class="empty-state"><span class="empty-icon"><i class="far fa-heart" aria-hidden="true"></i></span><h2>No tracks to save yet.</h2><p>When the collection has playable tracks, you can keep your favorites here.</p><a class="button button-primary" href="<?= e(base_url) ?>?page=music_list">Browse tracks <i class="fas fa-arrow-right" aria-hidden="true"></i></a></section>
<?php endif; ?>
