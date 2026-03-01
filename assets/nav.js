(function () {
  function init() {
    var isSinglePost = false;
    if (document.body && document.body.classList) {
      isSinglePost = document.body.classList.contains('single-post');
    }

    try {
      var restoreUrl = sessionStorage.getItem('aleejaved_restore_url') || '';
      var restoreScroll = sessionStorage.getItem('aleejaved_restore_scroll') || '';
      if (restoreUrl && restoreUrl === window.location.href) {
        var y = parseInt(restoreScroll, 10);
        if (!isNaN(y)) {
          window.requestAnimationFrame(function () {
            window.scrollTo(0, y);
          });
        }
        sessionStorage.removeItem('aleejaved_restore_url');
        sessionStorage.removeItem('aleejaved_restore_scroll');
      }
    } catch (e) {
      // ignore
    }

    if (isSinglePost) {
      var backBtn = document.querySelector('[data-action="post-back"]');
      if (backBtn) {
        backBtn.addEventListener('click', function (e) {
          e.preventDefault();
          try {
            var prevUrl = sessionStorage.getItem('aleejaved_prev_url') || '';
            var prevScroll = sessionStorage.getItem('aleejaved_prev_scroll') || '0';
            if (prevUrl) {
              sessionStorage.setItem('aleejaved_restore_url', prevUrl);
              sessionStorage.setItem('aleejaved_restore_scroll', prevScroll);
              window.location.href = prevUrl;
              return;
            }
          } catch (err) {
            // ignore
          }
          if (window.history && window.history.length > 1) {
            window.history.back();
            return;
          }
          var homeLink = document.querySelector('.site-title a');
          if (homeLink && homeLink.href) {
            window.location.href = homeLink.href;
          }
        });
      }
    } else {
      document.addEventListener(
        'click',
        function (e) {
          var target = e.target;
          if (!target || !target.closest) {
            return;
          }
          var a = target.closest('a[href]');
          if (!a) {
            return;
          }
          if (a.getAttribute('target') === '_blank') {
            return;
          }
          if (e.defaultPrevented) {
            return;
          }
          if (e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) {
            return;
          }
          var href = a.getAttribute('href') || '';
          if (!href || href.indexOf('#') === 0) {
            return;
          }
          var url;
          try {
            url = new URL(a.href, window.location.href);
          } catch (err) {
            return;
          }
          if (url.origin !== window.location.origin) {
            return;
          }
          try {
            sessionStorage.setItem('aleejaved_prev_url', window.location.href);
            sessionStorage.setItem('aleejaved_prev_scroll', String(window.pageYOffset || 0));
          } catch (err2) {
            // ignore
          }
        },
        true
      );
    }

    var btn = document.querySelector('.nav-toggle[aria-controls]');
    if (!btn) {
      return;
    }

    var navId = btn.getAttribute('aria-controls') || '';
    var nav = navId ? document.getElementById(navId) : null;

    if (!btn || !nav) {
      return;
    }

    function isOpen() {
      return document.body.classList.contains('nav-open');
    }

    function setOpen(open) {
      if (open) {
        document.body.classList.add('nav-open');
        btn.setAttribute('aria-expanded', 'true');
      } else {
        document.body.classList.remove('nav-open');
        btn.setAttribute('aria-expanded', 'false');
      }
    }

    btn.addEventListener('click', function (e) {
      e.preventDefault();
      setOpen(!isOpen());
    });

    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') {
        setOpen(false);
      }
    });

    nav.addEventListener('click', function (e) {
      var target = e.target;
      if (!target || !target.closest) {
        return;
      }
      if (target.closest('a')) {
        setOpen(false);
      }
    });

    window.addEventListener('resize', function () {
      if (window.innerWidth > 800) {
        setOpen(false);
      }
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
