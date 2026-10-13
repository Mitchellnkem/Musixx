<?php
$featuredTracks = $conn->query("SELECT m.id, m.title, m.artist, m.banner_path, m.audio_path,
        COALESCE(c.name, 'Independent') AS category_name
    FROM music_list m
    LEFT JOIN category_list c ON c.id = m.category_id
    WHERE m.status = 1 AND m.delete_flag = 0 AND m.audio_path <> ''
    ORDER BY m.created_at DESC, m.id DESC")->fetch_all(MYSQLI_ASSOC);
$featuredCategories = $conn->query("SELECT c.id, c.name, c.description, COUNT(m.id) AS track_count
    FROM category_list c
    LEFT JOIN music_list m ON m.category_id = c.id AND m.status = 1 AND m.delete_flag = 0 AND m.audio_path <> ''
    WHERE c.status = 1 AND c.delete_flag = 0
    GROUP BY c.id, c.name, c.description
    ORDER BY c.name ASC")->fetch_all(MYSQLI_ASSOC);
$trackCount = (int) $conn->query("SELECT COUNT(*) AS total FROM music_list WHERE status = 1 AND delete_flag = 0 AND audio_path <> ''")->fetch_assoc()['total'];
$categoryCount = (int) $conn->query("SELECT COUNT(*) AS total FROM category_list WHERE status = 1 AND delete_flag = 0")->fetch_assoc()['total'];
$heroTrack = $featuredTracks[0] ?? null;
$heroImage = $heroTrack ? validate_image($heroTrack['banner_path']) : validate_image($_settings->info('cover'));
?>
<section class="hero-panel">
    <div class="hero-copy">
        <p class="eyebrow"><span class="eyebrow-dot"></span> YOUR LISTENING ROOM</p>
        <h1>Find your<br><em>next favorite.</em></h1>
        <p class="hero-description">A handpicked little corner of the internet for good sounds, new moods, and the tracks you’ll come back to.</p>
        <div class="hero-actions">
            <a class="button button-primary" href="<?= e(base_url) ?>?page=music_list">Explore the collection <i class="fas fa-arrow-right" aria-hidden="true"></i></a>
            <a class="button button-quiet" href="<?= e(base_url) ?>?page=categories">Browse by genre</a>
        </div>
        <div class="hero-proof"><span class="proof-avatar"><i class="fas fa-headphones-alt" aria-hidden="true"></i></span><span><strong><?= $trackCount ?> tracks</strong><small>ready when you are</small></span></div>
    </div>
    <div class="hero-visual">
        <div class="hero-orbit orbit-one"></div><div class="hero-orbit orbit-two"></div>
        <div class="hero-record"><span></span></div>
        <div class="hero-feature">
            <div class="feature-image-wrap"><img src="<?= e($heroImage) ?>" alt="<?= e($heroTrack['title'] ?? 'Featured music artwork') ?>">
                <?php if ($heroTrack): ?>
                <button class="art-play" type="button" data-track-play data-track-id="<?= (int) $heroTrack['id'] ?>" data-audio-url="<?= e(base_url.$heroTrack['audio_path']) ?>" data-track-title="<?= e($heroTrack['title']) ?>" data-track-artist="<?= e($heroTrack['artist']) ?>" data-track-art="<?= e($heroImage) ?>" aria-label="Play <?= e($heroTrack['title']) ?>"><i class="fas fa-play" aria-hidden="true"></i></button>
                <?php endif; ?>
            </div>
            <div class="feature-details"><div><span class="player-kicker">ON REPEAT</span><h2><?= e($heroTrack['title'] ?? 'A fresh start') ?></h2><p><?= e($heroTrack['artist'] ?? 'Discover something new') ?></p></div><span class="feature-sound" aria-hidden="true"><i></i><i></i><i></i><i></i><i></i></span></div>
        </div>
        <span class="visual-caption">SIDE A <span>•</span> GOOD THINGS AHEAD</span>
    </div>
</section>

<section class="content-section collection-section">
    <div class="section-heading">
        <div><p class="eyebrow eyebrow-dark">A GOOD PLACE TO START</p><h2>On the playlist<span class="brand-period">.</span></h2></div>
        <a class="text-link" href="<?= e(base_url) ?>?page=music_list">See all tracks <i class="fas fa-arrow-right" aria-hidden="true"></i></a>
    </div>
    <?php if ($featuredTracks): ?>
    <div class="track-grid">
        <?php foreach ($featuredTracks as $index => $track): include 'inc/trackCard.php'; ?>
        <?php endforeach; ?>
    </div>
    <?php else: ?>
    <div class="empty-state"><span class="empty-icon"><i class="fas fa-compact-disc" aria-hidden="true"></i></span><h3>The first side is waiting.</h3><p>Add active tracks with audio files to start building your listening room.</p></div>
    <?php endif; ?>
</section>

<section class="content-section recent-section" id="recent-section" hidden>
    <div class="section-heading"><div><p class="eyebrow eyebrow-dark">PICK UP WHERE YOU LEFT OFF</p><h2>Recently played<span class="brand-period">.</span></h2></div></div>
    <div class="track-grid" id="recent-grid"></div>
</section>

<section class="genre-band">
    <div class="genre-band-heading"><div><p class="eyebrow">FOLLOW A FEELING</p><h2>Where are we going?</h2></div><a class="text-link text-link-light" href="<?= e(base_url) ?>?page=categories">All genres <i class="fas fa-arrow-right" aria-hidden="true"></i></a></div>
    <?php if ($featuredCategories): ?>
    <div class="genre-grid">
        <?php foreach ($featuredCategories as $category): ?>
        <a class="genre-card" href="<?= e(base_url.'?page=music_list&cid='.(int) $category['id']) ?>"><span class="genre-arrow"><i class="fas fa-arrow-right" aria-hidden="true"></i></span><span class="genre-name"><?= e($category['name']) ?></span><span class="genre-description"><?= e($category['description']) ?></span><span class="genre-track-count"><?= (int) $category['track_count'] ?> <?= (int) $category['track_count'] === 1 ? 'track' : 'tracks' ?></span></a>
        <?php endforeach; ?>
    </div>
    <?php else: ?><p class="muted-light">Genres are being tuned. Check back soon.</p><?php endif; ?>
    <div class="collection-note"><span class="note-line"></span><span><?= $categoryCount ?> genres <b>·</b> <?= $trackCount ?> playable tracks <b>·</b> one good afternoon</span></div>
</section>
