document.addEventListener('DOMContentLoaded', function () {
    const audio = document.getElementById('site-audio');
    const player = document.getElementById('listening-bar');
    const playButton = document.getElementById('player-toggle');
    const seek = document.getElementById('player-seek');
    const volume = document.getElementById('player-volume');
    const currentTime = document.getElementById('player-current');
    const duration = document.getElementById('player-duration');
    const playerTitle = document.getElementById('player-title');
    const playerArtist = document.getElementById('player-artist');
    const playerArt = document.getElementById('player-art');
    const previousButton = document.getElementById('player-previous');
    const nextButton = document.getElementById('player-next');
    const equalizerToggle = document.getElementById('equalizer-toggle');
    const equalizerPanel = document.getElementById('equalizer-panel');
    const equalizerEnabled = document.getElementById('equalizer-enabled');
    const equalizerPreset = document.getElementById('equalizer-preset');
    const equalizerStatus = document.getElementById('equalizer-status');
    const equalizerSliders = Array.from(document.querySelectorAll('[data-eq-band]'));
    const baseUrl = audio.dataset.baseUrl || '/';
    let activeTrackUrl = '';
    let queue = [];
    let queueIndex = -1;
    const favoriteKey = 'musixx.favorites.v1';
    const recentKey = 'musixx.recent.v1';
    const equalizerKey = 'musixx.equalizer.v1';
    const frequencies = [60, 250, 1000, 4000, 12000];
    const presets = {
        flat: [0, 0, 0, 0, 0],
        bass: [7, 5, 1, 0, 2],
        vocal: [-2, 0, 4, 3, 1],
        treble: [-2, -1, 0, 4, 7]
    };
    let audioContext = null;
    let equalizerFilters = [];
    let equalizerGraphReady = false;

    function readStorage(key) {
        try { const value = JSON.parse(localStorage.getItem(key) || '[]'); return Array.isArray(value) ? value : []; }
        catch (error) { return []; }
    }

    function writeStorage(key, value) {
        try { localStorage.setItem(key, JSON.stringify(value)); } catch (error) { /* Storage can be unavailable in private mode. */ }
    }

    function getEqualizerSettings() {
        try {
            const stored = JSON.parse(localStorage.getItem(equalizerKey) || '{}');
            return stored && typeof stored === 'object' && !Array.isArray(stored) ? stored : {};
        } catch (error) { return {}; }
    }

    function syncEqualizer() {
        const enabled = equalizerEnabled.checked;
        equalizerFilters.forEach(function (filter, index) {
            filter.gain.value = enabled ? Number(equalizerSliders[index].value) : 0;
        });
        equalizerSliders.forEach(function (slider) {
            const output = slider.parentElement.querySelector('output');
            if (output) output.textContent = `${Number(slider.value) > 0 ? '+' : ''}${slider.value} dB`;
            slider.disabled = !enabled;
        });
    }

    function saveEqualizerSettings() {
        writeStorage(equalizerKey, {
            enabled: equalizerEnabled.checked,
            gains: equalizerSliders.map(function (slider) { return Number(slider.value); }),
            preset: equalizerPreset.value
        });
    }

    function applyEqualizerPreset(name) {
        const values = presets[name];
        if (!values) return;
        equalizerSliders.forEach(function (slider, index) { slider.value = String(values[index]); });
        equalizerPreset.value = name;
        syncEqualizer();
        saveEqualizerSettings();
    }

    function ensureEqualizerGraph() {
        if (equalizerGraphReady) return true;
        const AudioContextClass = window.AudioContext || window.webkitAudioContext;
        if (!AudioContextClass) {
            if (equalizerStatus) equalizerStatus.textContent = 'Equalizer is not supported by this browser.';
            return false;
        }
        try {
            audioContext = new AudioContextClass();
            const source = audioContext.createMediaElementSource(audio);
            equalizerFilters = frequencies.map(function (frequency, index) {
                const filter = audioContext.createBiquadFilter();
                filter.frequency.value = frequency;
                filter.type = index === 0 ? 'lowshelf' : (index === frequencies.length - 1 ? 'highshelf' : 'peaking');
                if (filter.type === 'peaking') filter.Q.value = 1;
                return filter;
            });
            source.connect(equalizerFilters[0]);
            equalizerFilters.forEach(function (filter, index) {
                filter.connect(equalizerFilters[index + 1] || audioContext.destination);
            });
            equalizerGraphReady = true;
            syncEqualizer();
            return true;
        } catch (error) {
            if (equalizerStatus) equalizerStatus.textContent = 'Could not initialize audio controls in this browser.';
            console.error('Equalizer initialization failed:', error);
            return false;
        }
    }

    const savedEqualizer = getEqualizerSettings();
    if (Array.isArray(savedEqualizer.gains)) {
        equalizerSliders.forEach(function (slider, index) {
            if (Number.isFinite(Number(savedEqualizer.gains[index]))) slider.value = String(Math.max(-12, Math.min(12, Number(savedEqualizer.gains[index]))));
        });
        equalizerPreset.value = presets[savedEqualizer.preset] ? savedEqualizer.preset : 'custom';
    }
    if (typeof savedEqualizer.enabled === 'boolean') equalizerEnabled.checked = savedEqualizer.enabled;

    let favorites = readStorage(favoriteKey).map(String);

    function syncFavorites() {
        document.querySelectorAll('[data-favorite-toggle]').forEach(function (button) {
            const saved = favorites.includes(String(button.dataset.trackId));
            const icon = button.querySelector('i');
            button.classList.toggle('is-favorite', saved);
            button.setAttribute('aria-pressed', String(saved));
            button.setAttribute('aria-label', `${saved ? 'Remove' : 'Save'} ${button.dataset.trackTitle || 'track'} ${saved ? 'from' : 'to'} favorites`);
            if (icon) { icon.classList.toggle('fas', saved); icon.classList.toggle('far', !saved); }
        });
        const count = document.getElementById('saved-count');
        if (count) count.textContent = String(favorites.length);
        const savedGrid = document.getElementById('saved-grid');
        if (savedGrid) {
            let visible = 0;
            savedGrid.querySelectorAll('[data-track-card]').forEach(function (card) {
                const search = document.getElementById('track-search');
                const matchesSearch = !search || card.textContent.toLocaleLowerCase().includes(search.value.trim().toLocaleLowerCase());
                card.hidden = !favorites.includes(String(card.dataset.trackId)) || !matchesSearch;
                if (!card.hidden) visible += 1;
            });
            const empty = document.getElementById('saved-empty');
            if (empty) empty.hidden = visible !== 0;
            const counter = document.getElementById('favorites-count');
            if (counter) counter.innerHTML = `${visible}<small>${visible === 1 ? 'track' : 'tracks'}</small>`;
            const caption = document.getElementById('track-results');
            if (caption) caption.textContent = `${visible} saved ${visible === 1 ? 'track' : 'tracks'}`;
        }
    }

    function rememberRecent(button) {
        if (!button.dataset.trackId) return;
        const track = { id: String(button.dataset.trackId), title: button.dataset.trackTitle || 'Untitled track', artist: button.dataset.trackArtist || 'Unknown artist', art: button.dataset.trackArt || '', url: button.dataset.audioUrl || '' };
        const recent = readStorage(recentKey).filter(function (item) { return item && String(item.id) !== track.id; });
        recent.unshift(track);
        writeStorage(recentKey, recent.slice(0, 6));
        renderRecent();
    }

    function renderRecent() {
        const grid = document.getElementById('recent-grid');
        const section = document.getElementById('recent-section');
        if (!grid || !section) return;
        const recent = readStorage(recentKey).slice(0, 6);
        grid.replaceChildren();
        recent.forEach(function (track, index) {
            if (!track || !track.id || !track.url) return;
            const article = document.createElement('article');
            article.className = 'track-card'; article.dataset.trackCard = ''; article.dataset.trackId = String(track.id);
            const artBox = document.createElement('div'); artBox.className = 'track-art';
            const image = document.createElement('img'); image.src = track.art || ''; image.alt = `Cover art for ${track.title}`; image.loading = 'lazy';
            const play = document.createElement('button'); play.type = 'button'; play.className = 'art-play'; play.dataset.trackPlay = ''; play.dataset.trackId = String(track.id); play.dataset.audioUrl = track.url; play.dataset.trackTitle = track.title; play.dataset.trackArtist = track.artist; play.dataset.trackArt = track.art || ''; play.setAttribute('aria-label', `Play ${track.title}`); play.innerHTML = '<i class="fas fa-play" aria-hidden="true"></i>';
            const heart = document.createElement('button'); heart.type = 'button'; heart.className = 'favorite-toggle'; heart.dataset.favoriteToggle = ''; heart.dataset.trackId = String(track.id); heart.dataset.trackTitle = track.title; heart.setAttribute('aria-pressed', 'false'); heart.innerHTML = '<i class="far fa-heart" aria-hidden="true"></i>';
            artBox.append(image, play, heart);
            const info = document.createElement('div'); info.className = 'track-card-info';
            const copy = document.createElement('div'); const title = document.createElement('h2'); title.textContent = track.title; const artist = document.createElement('p'); artist.textContent = track.artist; copy.append(title, artist); info.append(copy);
            const actions = document.createElement('div'); actions.className = 'track-card-actions';
            const details = document.createElement('a'); details.className = 'text-link'; details.href = `${baseUrl}?page=music_view&id=${encodeURIComponent(track.id)}`; details.textContent = 'Details';
            const download = document.createElement('a'); download.className = 'icon-link'; download.href = track.url; download.download = ''; download.setAttribute('aria-label', `Download ${track.title}`); download.innerHTML = '<i class="fas fa-download" aria-hidden="true"></i>'; actions.append(details, download);
            article.append(artBox, info, actions); grid.appendChild(article);
        });
        section.hidden = grid.childElementCount === 0;
        syncFavorites();
    }

    function buildQueue() {
        const seen = new Set();
        return Array.from(document.querySelectorAll('[data-track-play]')).filter(function (button) {
            const card = button.closest('[data-track-card]');
            if (card && card.hidden) return false;
            if (!button.dataset.audioUrl || seen.has(button.dataset.audioUrl)) return false;
            seen.add(button.dataset.audioUrl); return true;
        });
    }

    function formatTime(seconds) {
        if (!Number.isFinite(seconds) || seconds < 0) return '0:00';
        const minutes = Math.floor(seconds / 60);
        const remainder = String(Math.floor(seconds % 60)).padStart(2, '0');
        return `${minutes}:${remainder}`;
    }

    function updatePlayButtons() {
        document.querySelectorAll('[data-track-play]').forEach(function (button) {
            const icon = button.querySelector('i');
            if (!icon) return;
            const isCurrent = button.dataset.audioUrl === activeTrackUrl;
            icon.classList.toggle('fa-play', !isCurrent || audio.paused);
            icon.classList.toggle('fa-pause', isCurrent && !audio.paused);
            button.setAttribute('aria-label', `${isCurrent && !audio.paused ? 'Pause' : 'Play'} ${button.dataset.trackTitle || 'track'}`);
        });
        const icon = playButton.querySelector('i');
        const playing = !audio.paused;
        icon.classList.toggle('fa-play', !playing);
        icon.classList.toggle('fa-pause', playing);
        playButton.setAttribute('aria-label', playing ? 'Pause' : 'Play');
        if (previousButton) previousButton.disabled = queueIndex <= 0;
        if (nextButton) nextButton.disabled = queueIndex < 0 || queueIndex >= queue.length - 1;
    }

    async function startTrack(button) {
        const trackUrl = button.dataset.audioUrl;
        if (!trackUrl) return;

        if (trackUrl === activeTrackUrl && !audio.paused) {
            audio.pause();
            return;
        }

        if (trackUrl !== activeTrackUrl) {
            queue = buildQueue();
            queueIndex = queue.findIndex(function (item) { return item.dataset.audioUrl === trackUrl; });
            activeTrackUrl = trackUrl;
            audio.src = trackUrl;
            audio.load();
            playerTitle.textContent = button.dataset.trackTitle || 'Untitled track';
            playerArtist.textContent = button.dataset.trackArtist || 'Unknown artist';
            playerArt.src = button.dataset.trackArt || playerArt.src;
            player.hidden = false;
            rememberRecent(button);
            document.body.classList.add('has-player');
            seek.value = '0';
            currentTime.textContent = '0:00';
            duration.textContent = '0:00';
        }

        try {
            ensureEqualizerGraph();
            if (audioContext && audioContext.state === 'suspended') await audioContext.resume();
            await audio.play();
        } catch (error) {
            playerTitle.textContent = 'Playback could not start';
            playerArtist.textContent = 'Try again or check the audio file.';
            console.error('Audio playback failed:', error);
        }
        updatePlayButtons();
    }

    document.addEventListener('click', function (event) {
        const favorite = event.target.closest('[data-favorite-toggle]');
        if (favorite) {
            event.preventDefault();
            const id = String(favorite.dataset.trackId);
            favorites = favorites.includes(id) ? favorites.filter(function (item) { return item !== id; }) : favorites.concat(id);
            writeStorage(favoriteKey, favorites);
            syncFavorites();
            return;
        }
        const trigger = event.target.closest('[data-track-play]');
        if (trigger) startTrack(trigger);
    });

    async function playQueueItem(index) {
        const button = queue[index];
        if (!button) return;
        queueIndex = index;
        await startTrack(button);
        updatePlayButtons();
    }
    if (previousButton) previousButton.addEventListener('click', function () { playQueueItem(queueIndex - 1); });
    if (nextButton) nextButton.addEventListener('click', function () { playQueueItem(queueIndex + 1); });

    equalizerToggle.addEventListener('click', function () {
        const isOpen = equalizerPanel.hidden;
        equalizerPanel.hidden = !isOpen;
        equalizerToggle.setAttribute('aria-expanded', String(isOpen));
    });
    equalizerEnabled.addEventListener('change', function () {
        ensureEqualizerGraph(); syncEqualizer(); saveEqualizerSettings();
    });
    equalizerPreset.addEventListener('change', function () {
        if (equalizerPreset.value === 'custom') return;
        applyEqualizerPreset(equalizerPreset.value);
    });
    equalizerSliders.forEach(function (slider) {
        slider.addEventListener('input', function () {
            equalizerPreset.value = 'custom';
            ensureEqualizerGraph(); syncEqualizer(); saveEqualizerSettings();
        });
    });
    syncEqualizer();

    playButton.addEventListener('click', function () {
        if (!activeTrackUrl) return;
        if (audio.paused) {
            audio.play().catch(function (error) {
                console.error('Audio playback failed:', error);
            });
        } else {
            audio.pause();
        }
    });

    audio.addEventListener('play', updatePlayButtons);
    audio.addEventListener('pause', updatePlayButtons);
    audio.addEventListener('loadedmetadata', function () {
        duration.textContent = formatTime(audio.duration);
    });
    audio.addEventListener('timeupdate', function () {
        currentTime.textContent = formatTime(audio.currentTime);
        if (Number.isFinite(audio.duration) && audio.duration > 0) {
            seek.value = String(Math.round((audio.currentTime / audio.duration) * Number(seek.max)));
        }
    });
    audio.addEventListener('ended', function () {
        if (queueIndex >= 0 && queueIndex < queue.length - 1) playQueueItem(queueIndex + 1);
        else { audio.currentTime = 0; updatePlayButtons(); }
    });
    audio.addEventListener('error', function () {
        if (!activeTrackUrl) return;
        playerArtist.textContent = 'This audio file could not be loaded.';
    });

    seek.addEventListener('input', function () {
        if (Number.isFinite(audio.duration) && audio.duration > 0) {
            audio.currentTime = (Number(seek.value) / Number(seek.max)) * audio.duration;
        }
    });
    audio.volume = Number(volume.value);
    volume.addEventListener('input', function () {
        audio.volume = Number(volume.value);
    });

    document.getElementById('player-close').addEventListener('click', function () {
        audio.pause();
        audio.removeAttribute('src');
        audio.load();
        activeTrackUrl = '';
        queue = []; queueIndex = -1;
        player.hidden = true;
        document.body.classList.remove('has-player');
        updatePlayButtons();
    });

    const navToggle = document.querySelector('.nav-toggle');
    const navLinks = document.getElementById('primary-links');
    if (navToggle && navLinks) {
        navToggle.addEventListener('click', function () {
            const isOpen = navLinks.classList.toggle('is-open');
            navToggle.setAttribute('aria-expanded', String(isOpen));
            navToggle.setAttribute('aria-label', isOpen ? 'Close navigation' : 'Open navigation');
        });
    }

    document.querySelectorAll('.search-box input[type="search"]').forEach(function (input) {
        const grid = input.closest('section')?.nextElementSibling;
        const items = grid?.querySelectorAll('[data-search-item]');
        const empty = grid?.nextElementSibling;
        const counter = document.getElementById(input.id === 'genre-search' ? 'genre-results' : 'track-results');
        if (!items) return;

        input.addEventListener('input', function () {
            const term = input.value.trim().toLocaleLowerCase();
            let visibleCount = 0;
            items.forEach(function (item) {
                const matches = item.textContent.toLocaleLowerCase().includes(term) && (!grid.hasAttribute('data-saved-only') || favorites.includes(String(item.dataset.trackId)));
                item.hidden = !matches;
                if (matches) visibleCount += 1;
            });
            if (empty) empty.hidden = visibleCount !== 0;
            if (counter) counter.textContent = `${visibleCount} ${visibleCount === 1 ? (input.id === 'genre-search' ? 'genre' : 'track') : (input.id === 'genre-search' ? 'genres' : 'tracks')}`;
        });
    });

    syncFavorites();
    renderRecent();

    document.addEventListener('keydown', function (event) {
        if (event.key !== '/' || event.ctrlKey || event.metaKey || event.altKey) return;
        if (event.target.matches('input, textarea, [contenteditable="true"]')) return;
        const search = document.getElementById('track-search') || document.getElementById('genre-search');
        if (search) {
            event.preventDefault();
            search.focus();
        }
    });
});
