<footer class="site-footer">
    <div class="footer-main">
        <a class="site-brand site-brand-footer" href="<?= e(base_url) ?>">
            <span class="brand-mark" aria-hidden="true"><i class="fas fa-wave-square"></i></span>
            <span>musixx<span class="brand-period">.</span></span>
        </a>
        <p>A little more room for the music you love.</p>
        <div class="footer-links">
            <a href="<?= e(base_url) ?>?page=categories">Explore genres</a>
            <a href="<?= e(base_url) ?>?page=about">About Musixx</a>
            <a href="<?= e(base_url) ?>admin/login.php">Admin</a>
        </div>
    </div>
    <div class="footer-meta"><span>&copy; <?= date('Y') ?> Musixx</span><span>Made for listening.</span></div>
</footer>

<section class="listening-bar" id="listening-bar" aria-label="Audio player" hidden>
    <div class="player-track">
        <img class="player-art" id="player-art" src="<?= e(validate_image('')) ?>" alt="">
        <div class="player-copy">
            <span class="player-kicker">NOW PLAYING</span>
            <strong id="player-title">Select a track</strong>
            <span id="player-artist">Your next favorite is waiting</span>
        </div>
    </div>
    <div class="player-controls">
        <button type="button" class="player-skip" id="player-previous" aria-label="Play previous track" disabled><i class="fas fa-step-backward" aria-hidden="true"></i></button>
        <button type="button" class="player-toggle" id="player-toggle" aria-label="Play">
            <i class="fas fa-play" aria-hidden="true"></i>
        </button>
        <button type="button" class="player-skip" id="player-next" aria-label="Play next track" disabled><i class="fas fa-step-forward" aria-hidden="true"></i></button>
        <div class="player-timeline">
            <span id="player-current">0:00</span>
            <input id="player-seek" type="range" min="0" max="1000" value="0" aria-label="Seek through track">
            <span id="player-duration">0:00</span>
        </div>
    </div>
    <div class="player-volume">
        <i class="fas fa-volume-up" aria-hidden="true"></i>
        <input id="player-volume" type="range" min="0" max="1" step="0.01" value="0.72" aria-label="Volume">
    </div>
    <button type="button" class="player-close" id="player-close" aria-label="Close player"><i class="fas fa-times" aria-hidden="true"></i></button>
</section>
<audio id="site-audio" preload="metadata" data-base-url="<?= e(base_url) ?>"></audio>

<script src="<?= e(base_url) ?>plugins/jquery/jquery.min.js"></script>
<script src="<?= e(base_url) ?>plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="<?= e(base_url) ?>assets/js/script.js"></script>
