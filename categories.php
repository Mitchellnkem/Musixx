<?php
$categoryList = $conn->query("SELECT c.id, c.name, c.description, COUNT(m.id) AS track_count
    FROM category_list c
    LEFT JOIN music_list m ON m.category_id = c.id AND m.status = 1 AND m.delete_flag = 0 AND m.audio_path <> ''
    WHERE c.status = 1 AND c.delete_flag = 0
    GROUP BY c.id, c.name, c.description
    ORDER BY c.name ASC")->fetch_all(MYSQLI_ASSOC);
?>
<section class="page-intro">
    <p class="eyebrow eyebrow-dark">PICK A DIRECTION</p>
    <div class="page-intro-row"><div><h1>Find your <em>frequency.</em></h1><p>Choose a genre and follow the sound wherever it takes you.</p></div><span class="intro-count"><?= count($categoryList) ?><small>genres</small></span></div>
</section>

<section class="browse-tools" aria-label="Find a genre">
    <label class="search-box"><i class="fas fa-search" aria-hidden="true"></i><input type="search" id="genre-search" placeholder="Search genres" aria-label="Search genres"><kbd>/</kbd></label>
    <span class="results-caption" id="genre-results"><?= count($categoryList) ?> <?= count($categoryList) === 1 ? 'genre' : 'genres' ?></span>
</section>

<?php if ($categoryList): ?>
<div class="genre-grid genre-grid-light" id="genre-grid">
    <?php foreach ($categoryList as $index => $category): ?>
    <a class="genre-card" href="<?= e(base_url.'?page=music_list&cid='.(int) $category['id']) ?>" data-search-item>
        <span class="genre-card-top"><span class="genre-number"><?= str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) ?></span><span class="genre-arrow"><i class="fas fa-arrow-right" aria-hidden="true"></i></span></span>
        <span class="genre-name"><?= e($category['name']) ?></span>
        <span class="genre-description"><?= e($category['description']) ?></span>
        <span class="genre-track-count"><?= (int) $category['track_count'] ?> <?= (int) $category['track_count'] === 1 ? 'track' : 'tracks' ?></span>
        <span class="genre-explore">Explore this sound <i class="fas fa-arrow-right" aria-hidden="true"></i></span>
    </a>
    <?php endforeach; ?>
</div>
<p class="empty-search" id="genre-empty" hidden>No genres match that search. Try another word.</p>
<?php else: ?>
<div class="empty-state"><span class="empty-icon"><i class="fas fa-headphones-alt" aria-hidden="true"></i></span><h2>Genres are on their way.</h2><p>There aren’t any active genres to explore just yet.</p></div>
<?php endif; ?>
