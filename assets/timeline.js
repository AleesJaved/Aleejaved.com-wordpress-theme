(function () {
  function initTimelineControls() {
    var filterWrap = document.querySelector('.timeline-filters');
    var list = document.querySelector('.timeline-list');
    var items = Array.prototype.slice.call(document.querySelectorAll('.timeline-item'));
    var sortWrap = document.querySelector('.timeline-sort');
    var sortButtons = sortWrap ? Array.prototype.slice.call(sortWrap.querySelectorAll('.timeline-sort-btn[data-sort]')) : [];

    var currentSortMode = 'date';
    if (sortWrap) {
      var defaultSort = sortWrap.getAttribute('data-default-sort') || '';
      if (defaultSort === 'impressiveness' || defaultSort === 'date') {
        currentSortMode = defaultSort;
      }
    }

    var tagInputs = [];
    var allTags = [];
    if (filterWrap) {
      tagInputs = Array.prototype.slice.call(filterWrap.querySelectorAll('input[type="checkbox"][data-tag]'));
      allTags = tagInputs
        .map(function (i) {
          return i.getAttribute('data-tag') || '';
        })
        .filter(Boolean);
    }

    function getSortMode() {
      return currentSortMode;
    }

    function setSortMode(mode) {
      currentSortMode = mode;
    }

    function parseIntAttr(el, name) {
      var raw = el.getAttribute(name) || '';
      var n = parseInt(raw, 10);
      return isNaN(n) ? 0 : n;
    }

    function applySort() {
      if (!list || !items.length) {
        return;
      }

      var mode = getSortMode();
      var sorted = items.slice().sort(function (a, b) {
        if (mode === 'impressiveness') {
          var oa = parseIntAttr(a, 'data-order');
          var ob = parseIntAttr(b, 'data-order');
          var hasOa = oa >= 0;
          var hasOb = ob >= 0;

          if (hasOa && hasOb && oa !== ob) {
            return oa - ob;
          }
          if (hasOa !== hasOb) {
            return hasOa ? -1 : 1;
          }

          var ia = parseIntAttr(a, 'data-impressiveness');
          var ib = parseIntAttr(b, 'data-impressiveness');
          if (ib !== ia) {
            return ib - ia;
          }
        }

        var da = parseIntAttr(a, 'data-date');
        var db = parseIntAttr(b, 'data-date');
        if (db !== da) {
          return db - da;
        }

        return 0;
      });

      sorted.forEach(function (el) {
        list.appendChild(el);
      });
    }

    function updateSortUI() {
      if (!sortButtons.length) {
        return;
      }
      var mode = getSortMode();
      sortButtons.forEach(function (btn) {
        var isActive = (btn.getAttribute('data-sort') || '') === mode;
        btn.setAttribute('aria-pressed', isActive ? 'true' : 'false');
      });
    }

    function applyFilters() {
      if (!tagInputs.length) {
        items.forEach(function (el) {
          el.style.display = '';
        });
        return;
      }

      var active = tagInputs
        .filter(function (i) {
          return i.checked;
        })
        .map(function (i) {
          return i.getAttribute('data-tag') || '';
        })
        .filter(Boolean);

      if (active.length === allTags.length) {
        items.forEach(function (el) {
          el.style.display = '';
        });
        return;
      }

      if (active.length === 0) {
        items.forEach(function (el) {
          el.style.display = 'none';
        });
        return;
      }

      items.forEach(function (el) {
        var raw = (el.getAttribute('data-tags') || '').trim();
        if (!raw) {
          el.style.display = 'none';
          return;
        }
        var tagList = raw.split(/\s+/).filter(Boolean);
        var match = tagList.some(function (t) {
          return active.indexOf(t) !== -1;
        });
        el.style.display = match ? '' : 'none';
      });
    }

    if (tagInputs.length) {
      tagInputs.forEach(function (i) {
        i.addEventListener('change', applyFilters);
      });
    }

    if (sortButtons.length) {
      sortButtons.forEach(function (btn) {
        btn.addEventListener('click', function (e) {
          e.preventDefault();
          var mode = btn.getAttribute('data-sort') || 'date';
          if (mode !== 'impressiveness') {
            mode = 'date';
          }
          setSortMode(mode);
          updateSortUI();
          applySort();
        });
      });
    }

    updateSortUI();
    applySort();
    applyFilters();
  }

  function setActive(media, index) {
    var items = media.querySelectorAll('.timeline-media-item');
    if (!items.length) {
      return;
    }

    var count = items.length;
    var nextIndex = ((index % count) + count) % count;

    items.forEach(function (el, i) {
      var isActive = i === nextIndex;
      if (isActive) {
        el.classList.add('is-active');
      } else {
        el.classList.remove('is-active');
      }

      if (!isActive && el && el.tagName && el.tagName.toLowerCase() === 'video') {
        try {
          el.pause();
          el.currentTime = 0;
        } catch (e) {
          // ignore
        }
      }
    });

    media.setAttribute('data-index', String(nextIndex));
  }

  function initMedia(media) {
    var items = media.querySelectorAll('.timeline-media-item');
    var prev = media.querySelector('[data-action="prev"]');
    var next = media.querySelector('[data-action="next"]');

    if (items.length <= 1) {
      if (prev) {
        prev.style.display = 'none';
      }
      if (next) {
        next.style.display = 'none';
      }
      return;
    }

    if (prev) {
      prev.style.display = '';
    }
    if (next) {
      next.style.display = '';
    }

    var current = parseInt(media.getAttribute('data-index') || '0', 10);
    if (isNaN(current)) {
      current = 0;
    }

    setActive(media, current);

    if (prev) {
      prev.addEventListener('click', function (e) {
        e.preventDefault();
        var idx = parseInt(media.getAttribute('data-index') || '0', 10);
        setActive(media, idx - 1);
      });
    }

    if (next) {
      next.addEventListener('click', function (e) {
        e.preventDefault();
        var idx = parseInt(media.getAttribute('data-index') || '0', 10);
        setActive(media, idx + 1);
      });
    }
  }

  function init() {
    var all = document.querySelectorAll('.timeline-media');
    all.forEach(function (media) {
      initMedia(media);
    });

    initTimelineControls();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
