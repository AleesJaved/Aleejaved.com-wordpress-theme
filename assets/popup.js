(function () {
  var activePopup = null;

  function openPopup(popupId) {
    var popup = document.getElementById(popupId);
    if (!popup) return;
    
    popup.classList.add('aj-popup-active');
    document.body.classList.add('aj-popup-open');
    activePopup = popup;
    
    var container = popup.querySelector('.aj-popup-container');
    if (container) {
      container.scrollTop = 0;
    }
  }

  function closePopup(popup) {
    if (!popup) popup = activePopup;
    if (!popup) return;
    
    popup.classList.remove('aj-popup-active');
    document.body.classList.remove('aj-popup-open');
    activePopup = null;
  }

  function closeAllPopups() {
    var popups = document.querySelectorAll('.aj-popup-overlay.aj-popup-active');
    popups.forEach(function(popup) {
      closePopup(popup);
    });
  }

  document.addEventListener('click', function(e) {
    var trigger = e.target.closest('[data-popup]');
    if (trigger) {
      e.preventDefault();
      var popupId = trigger.getAttribute('data-popup');
      openPopup(popupId);
      return;
    }

    var hashLink = e.target.closest('a[href^="#aj-popup-"]');
    if (hashLink) {
      e.preventDefault();
      var popupId = hashLink.getAttribute('href').substring(1);
      openPopup(popupId);
      return;
    }

    var closeBtn = e.target.closest('.aj-popup-close');
    if (closeBtn) {
      e.preventDefault();
      var popup = closeBtn.closest('.aj-popup-overlay');
      closePopup(popup);
      return;
    }

    var overlay = e.target.closest('.aj-popup-overlay');
    if (overlay && overlay.classList.contains('aj-popup-active')) {
      var container = e.target.closest('.aj-popup-container');
      if (!container) {
        closePopup(overlay);
        return;
      }
    }
  });

  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && activePopup) {
      closePopup(activePopup);
    }
  });

  function checkHashOnLoad() {
    var hash = window.location.hash;
    if (hash && hash.indexOf('#aj-popup-') === 0) {
      var popupId = hash.substring(1);
      openPopup(popupId);
    }
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', checkHashOnLoad);
  } else {
    checkHashOnLoad();
  }

  window.AJPopup = {
    open: openPopup,
    close: closePopup,
    closeAll: closeAllPopups
  };
})();
