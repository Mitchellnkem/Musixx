<?php $activePage = isset($_GET['page']) && is_string($_GET['page']) ? $_GET['page'] : 'home'; ?>
<header class="site-header">
    <nav class="site-nav" aria-label="Main navigation">
        <a class="site-brand" href="<?= e(base_url) ?>" aria-label="Musixx home">
            <span class="brand-mark" aria-hidden="true"><i class="fas fa-wave-square"></i></span>
            <span>musixx<span class="brand-period">.</span></span>
        </a>

        <button class="nav-toggle" type="button" aria-expanded="false" aria-controls="primary-links" aria-label="Open navigation">
            <span></span><span></span>
        </button>

        <div class="nav-links" id="primary-links">
            <a class="<?= $activePage === 'home' ? 'is-active' : '' ?>" href="<?= e(base_url) ?>">Discover</a>
            <a class="<?= in_array($activePage, ['categories', 'music_list'], true) ? 'is-active' : '' ?>" href="<?= e(base_url) ?>?page=categories">Genres</a>
            <a class="<?= $activePage === 'about' ? 'is-active' : '' ?>" href="<?= e(base_url) ?>?page=about">About</a>
            <a class="<?= $activePage === 'favorites' ? 'is-active' : '' ?>" href="<?= e(base_url) ?>?page=favorites">Saved <span class="saved-count" id="saved-count">0</span></a>
        </div>

        <a class="nav-listen" href="<?= e(base_url) ?>?page=music_list">
            <span>Browse music</span><i class="fas fa-arrow-right" aria-hidden="true"></i>
        </a>
    </nav>
</header>
