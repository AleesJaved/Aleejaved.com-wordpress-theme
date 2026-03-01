(function ($) {
  function init() {
    var list = $('#aleejaved-page-order-list');
    if (!list.length) {
      return;
    }

    console.log('Page order admin initialized');

    // Move up button
    $('.move-up').on('click', function(e) {
      e.preventDefault();
      console.log('Up button clicked');
      var button = $(this);
      var item = button.closest('li');
      var prevItem = item.prev('li');
      
      if (prevItem.length) {
        prevItem.before(item);
        updateButtonStates();
        console.log('Moved item up');
      }
    });

    // Move down button
    $('.move-down').on('click', function(e) {
      e.preventDefault();
      console.log('Down button clicked');
      var button = $(this);
      var item = button.closest('li');
      var nextItem = item.next('li');
      
      if (nextItem.length) {
        nextItem.after(item);
        updateButtonStates();
        console.log('Moved item down');
      }
    });

    function updateButtonStates() {
      var items = list.find('li');
      console.log('Updating button states for ' + items.length + ' items');
      items.each(function(index) {
        var upButton = $(this).find('.move-up');
        var downButton = $(this).find('.move-down');
        
        if (index === 0) {
          upButton.prop('disabled', true);
        } else {
          upButton.prop('disabled', false);
        }
        
        if (index === items.length - 1) {
          downButton.prop('disabled', true);
        } else {
          downButton.prop('disabled', false);
        }
      });
    }

    var btn = $('#aleejaved-page-order-save');
    var status = $('#aleejaved-page-order-status');

    function setStatus(text, isError) {
      if (!status.length) {
        return;
      }
      status.text(text || '');
      status.css('color', isError ? '#b32d2e' : '#1d2327');
    }

    function collectOrder() {
      return list
        .children('[data-id]')
        .map(function () {
          return $(this).attr('data-id');
        })
        .get();
    }

    btn.on('click', function (e) {
      e.preventDefault();

      if (!window.AleejavedPageOrder || !AleejavedPageOrder.ajaxUrl) {
        setStatus('Missing AJAX config.', true);
        return;
      }

      var order = collectOrder();

      btn.prop('disabled', true);
      setStatus('Saving…', false);

      $.ajax({
        url: AleejavedPageOrder.ajaxUrl,
        method: 'POST',
        dataType: 'json',
        data: {
          action: 'save_page_order',
          nonce: AleejavedPageOrder.nonce,
          page_order: order,
        },
      })
        .done(function (res) {
          if (res && res.success) {
            setStatus('Saved.', false);
          } else {
            var msg = (res && res.data && res.data.message) || 'Save failed.';
            setStatus(msg, true);
          }
        })
        .fail(function () {
          setStatus('Save failed.', true);
        })
        .always(function () {
          btn.prop('disabled', false);
        });
    });
  }

  $(init);
})(jQuery);
