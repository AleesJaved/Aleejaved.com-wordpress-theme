(function ($) {
  function init() {
    var list = $('#aleejaved-timeline-order-list');
    if (!list.length) {
      return;
    }

    list.sortable({
      axis: 'y',
      containment: 'parent',
      tolerance: 'pointer',
    });

    var btn = $('#aleejaved-timeline-order-save');
    var status = $('#aleejaved-timeline-order-status');

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

      if (!window.AleejavedTimelineOrder || !AleejavedTimelineOrder.ajaxUrl) {
        setStatus('Missing AJAX config.', true);
        return;
      }

      var order = collectOrder();

      btn.prop('disabled', true);
      setStatus('Saving…', false);

      $.ajax({
        url: AleejavedTimelineOrder.ajaxUrl,
        method: 'POST',
        dataType: 'json',
        data: {
          action: 'aleejaved_save_timeline_order',
          nonce: AleejavedTimelineOrder.nonce,
          order: order,
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
