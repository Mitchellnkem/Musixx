<?php $art = $art ?? validate_image($track['banner_path'] ?? ''); ?>
<article class="track-card" data-search-item data-track-card data-track-id="<?= (int) $track['id'] ?>">
    <div class="track-art">
        <img src="<?= e($art) ?>" alt="Cover art for <?= e($track['title']) ?>" loading="lazy">
        <button class="art-play" type="button" data-track-play data-audio-url="<?= e(base_url.$track['audio_path']) ?>" data-track-id="<?= (int) $track['id'] ?>" data-track-title="<?= e($track['title']) ?>" data-track-artist="<?= e($track['artist']) ?>" data-track-art="<?= e($art) ?>" aria-label="Play <?= e($track['title']) ?>"><i class="fas fa-play" aria-hidden="true"></i></button>
        <button class="favorite-toggle" type="button" data-favorite-toggle data-track-id="<?= (int) $track['id'] ?>" data-track-title="<?= e($track['title']) ?>" aria-pressed="false" aria-label="Save <?= e($track['title']) ?>"><i class="far fa-heart" aria-hidden="true"></i></button>
        <?php if (isset($index)): ?><span class="track-index"><?= str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) ?></span><?php endif; ?>
    </div>
    <div class="track-card-info"><div><h2><?= e($track['title']) ?></h2><p><?= e($track['artist']) ?></p></div><?php if (!empty($track['category_name'])): ?><span class="genre-pill"><?= e($track['category_name']) ?></span><?php endif; ?></div>
    <div class="track-card-actions">
        <a href="<?= e(base_url.'?page=music_view&id='.(int) $track['id']) ?>" class="text-link">Details <i class="fas fa-arrow-right" aria-hidden="true"></i></a>
        <a href="<?= e(base_url.$track['audio_path']) ?>" download="<?= e($track['title'].'.'.pathinfo($track['audio_path'], PATHINFO_EXTENSION)) ?>" class="icon-link" aria-label="Download <?= e($track['title']) ?>"><i class="fas fa-download" aria-hidden="true"></i></a>
    </div>
</article>
