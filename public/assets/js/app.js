/*
 * Portal Info Peserta — JavaScript vanilla, tanpa pustaka.
 * PRD: 2.2.3 (tab tanpa reload, tetap berfungsi tanpa JS), 2.2.5/2.2.7 (konfirmasi hapus).
 *
 *   - Tab bottom navigation (Jadwal / Pengumuman / Info): tampilkan satu panel, simpan tab terakhir di sessionStorage (try/catch)
 *   - Tab hari: ganti hari aktif, scroll pill aktif ke tengah
 *   - Accordion info penting: pakai <details> agar jalan tanpa JS (tidak perlu kode di sini)
 *   - Tombol dengan atribut data-confirm: window.confirm sebelum submit
 *   - Class "js" di <html> (juga dipasang inline di layout agar tidak berkedip)
 *
 * TODO (TASKS Hari 3):
 *   - Menu hamburger admin
 */
(function () {
  'use strict';

  var root = document.documentElement;
  if (!/\bjs\b/.test(root.className)) {
    root.className += ' js';
  }

  var TAB_KEY = 'pip_tab';

  function store(key, value) {
    try {
      if (value === undefined) {
        return window.sessionStorage.getItem(key);
      }
      window.sessionStorage.setItem(key, value);
    } catch (e) {
      // sessionStorage bisa tidak tersedia (mode privat): abaikan.
    }
    return null;
  }

  function each(list, fn) {
    Array.prototype.forEach.call(list, fn);
  }

  // ---------------------------------------------------------------------
  // Tab bottom navigation
  // ---------------------------------------------------------------------
  var panels = document.querySelectorAll('[data-panel]');
  var tabLinks = document.querySelectorAll('[data-tab]');

  function showTab(name, scroll) {
    var found = false;
    each(panels, function (panel) {
      var active = panel.getAttribute('data-panel') === name;
      panel.classList.toggle('is-active', active);
      if (active) { found = true; }
    });
    if (!found) { return false; }

    each(tabLinks, function (link) {
      var active = link.getAttribute('data-tab') === name;
      link.classList.toggle('is-active', active);
      if (active) {
        link.setAttribute('aria-current', 'page');
      } else {
        link.removeAttribute('aria-current');
      }
    });

    store(TAB_KEY, name);
    if (scroll) { window.scrollTo(0, 0); }

    // Pill hari baru punya ukuran setelah panel Jadwal terlihat.
    var activeDay = name === 'jadwal' ? document.querySelector('[data-day].is-active') : null;
    if (activeDay) { centerPill(activeDay, false); }
    return true;
  }

  each(tabLinks, function (link) {
    link.addEventListener('click', function (event) {
      event.preventDefault();
      showTab(link.getAttribute('data-tab'), true);
    });
  });

  // ---------------------------------------------------------------------
  // Tab hari
  // ---------------------------------------------------------------------
  var dayTabs = document.querySelectorAll('[data-day]');
  var dayPanels = document.querySelectorAll('[data-day-panel]');

  function centerPill(pill, smooth) {
    var strip = pill.parentNode;
    if (!strip) { return; }
    var left = pill.offsetLeft - (strip.clientWidth - pill.offsetWidth) / 2;
    if (strip.scrollTo) {
      strip.scrollTo({ left: Math.max(0, left), behavior: smooth ? 'smooth' : 'auto' });
    } else {
      strip.scrollLeft = Math.max(0, left);
    }
  }

  function showDay(id, smooth) {
    each(dayPanels, function (panel) {
      panel.classList.toggle('is-active', panel.getAttribute('data-day-panel') === id);
    });
    each(dayTabs, function (tab) {
      var active = tab.getAttribute('data-day') === id;
      tab.classList.toggle('is-active', active);
      if (active) {
        tab.setAttribute('aria-current', 'true');
        centerPill(tab, smooth);
      } else {
        tab.removeAttribute('aria-current');
      }
    });
  }

  each(dayTabs, function (tab) {
    tab.addEventListener('click', function (event) {
      event.preventDefault();
      showDay(tab.getAttribute('data-day'), true);
    });
  });

  // ---------------------------------------------------------------------
  // Keadaan awal: hash di URL (#pengumuman, #hari-2) lalu tab terakhir di sesi ini
  // ---------------------------------------------------------------------
  if (panels.length) {
    var hash = window.location.hash.replace('#', '');
    var target = hash ? document.getElementById(hash) : null;

    if (target && target.hasAttribute('data-day-panel')) {
      showTab('jadwal', false);
      showDay(target.getAttribute('data-day-panel'), false);
    } else if (!(target && target.hasAttribute('data-panel') && showTab(hash, false))) {
      var last = store(TAB_KEY);
      if (last) { showTab(last, false); }
    }

    // Browser melompat ke anchor saat memuat; dengan tab aktif, header sapaan tetap terlihat.
    if (target) {
      window.setTimeout(function () { window.scrollTo(0, 0); }, 0);
    }

    var activeDay = document.querySelector('[data-day].is-active');
    if (activeDay) { centerPill(activeDay, false); }
  }

  // ---------------------------------------------------------------------
  // Konfirmasi sebelum aksi berbahaya: <button data-confirm="Hapus hari ini?">
  // ---------------------------------------------------------------------
  document.addEventListener('submit', function (event) {
    var form = event.target;
    var submitter = event.submitter || form.querySelector('[data-confirm]');
    var message = (submitter && submitter.getAttribute('data-confirm')) || form.getAttribute('data-confirm');
    if (message && !window.confirm(message)) {
      event.preventDefault();
    }
  });
})();
