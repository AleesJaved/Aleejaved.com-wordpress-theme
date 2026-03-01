(function() {
  var sections = ['me', 'about', 'timeline'];
  var navLinks = {};
  
  function initNavHighlight() {
    sections.forEach(function(sectionId) {
      var link = document.querySelector('.main-nav a[href="#' + sectionId + '"]');
      if (link) {
        navLinks[sectionId] = link;
      }
    });
    
    updateActiveNav();
    window.addEventListener('scroll', updateActiveNav);
  }
  
  function updateActiveNav() {
    var scrollPosition = window.scrollY + 100;
    var activeSection = null;
    
    sections.forEach(function(sectionId) {
      var section = document.getElementById(sectionId);
      if (section) {
        var sectionTop = section.offsetTop;
        var sectionBottom = sectionTop + section.offsetHeight;
        
        if (scrollPosition >= sectionTop && scrollPosition < sectionBottom) {
          activeSection = sectionId;
        }
      }
    });
    
    // Remove active class from all links
    Object.values(navLinks).forEach(function(link) {
      link.style.borderBottomColor = 'transparent';
    });
    
    // Add active class to current section link
    if (activeSection && navLinks[activeSection]) {
      navLinks[activeSection].style.borderBottomColor = 'var(--accent)';
    }
  }
  
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initNavHighlight);
  } else {
    initNavHighlight();
  }
})();
