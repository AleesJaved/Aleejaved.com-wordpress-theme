(function ($) {
  function idsFromInput($input) {
    var raw = ($input.val() || '').trim();
    if (!raw) {
      return [];
    }
    return raw
      .split(',')
      .map(function (s) {
        return parseInt(s, 10);
      })
      .filter(function (n) {
        return !isNaN(n) && n > 0;
      });
  }

  function renderThumbs($wrap, attachments) {
    $wrap.empty();

    attachments.forEach(function (att) {
      if (att.type === 'video') {
        var $tag = $('<div />')
          .text('VIDEO')
          .css({
            width: '58px',
            height: '58px',
            display: 'inline-flex',
            alignItems: 'center',
            justifyContent: 'center',
            fontSize: '10px',
            fontWeight: '700',
            borderRadius: '10px',
            border: '1px solid rgba(0,0,0,0.12)',
            background: 'rgba(0,0,0,0.04)',
          });
        $wrap.append($tag);
        return;
      }

      var url = att.url;
      if (att.sizes && att.sizes.thumbnail && att.sizes.thumbnail.url) {
        url = att.sizes.thumbnail.url;
      }

      var $img = $('<img />')
        .attr('src', url)
        .attr('alt', '')
        .css({
          width: '58px',
          height: '58px',
          objectFit: 'cover',
          borderRadius: '10px',
          border: '1px solid rgba(0,0,0,0.12)',
        });

      $wrap.append($img);
    });
  }

  function fetchAttachments(ids, cb) {
    if (!ids.length) {
      cb([]);
      return;
    }

    var remaining = ids.length;
    var results = new Array(ids.length);

    ids.forEach(function (id, idx) {
      var model = wp.media.attachment(id);
      model.fetch({
        success: function () {
          results[idx] = model.toJSON();
          remaining -= 1;
          if (remaining === 0) {
            cb(results.filter(Boolean));
          }
        },
        error: function () {
          remaining -= 1;
          if (remaining === 0) {
            cb(results.filter(Boolean));
          }
        },
      });
    });
  }

  function initTileBox() {
    var $box = $('#aleejaved_timeline_tile');
    if (!$box.length || !wp.media) {
      return;
    }

    var $input = $box.find('input[name="aleejaved_timeline_gallery"]');
    var $thumbs = $box.find('[data-role="thumbs"]');

    fetchAttachments(idsFromInput($input), function (atts) {
      renderThumbs($thumbs, atts);
    });

    $box.on('click', '[data-action="select"]', function (e) {
      e.preventDefault();

      var frame = wp.media({
        title: 'Select carousel images',
        button: { text: 'Use these images' },
        library: { type: ['image', 'video'] },
        multiple: true,
      });

      frame.on('open', function () {
        var selection = frame.state().get('selection');
        idsFromInput($input).forEach(function (id) {
          var att = wp.media.attachment(id);
          att.fetch();
          selection.add(att);
        });
      });

      frame.on('select', function () {
        var selection = frame.state().get('selection');
        var ids = [];
        var atts = [];
        selection.each(function (model) {
          ids.push(model.get('id'));
          atts.push(model.toJSON());
        });

        $input.val(ids.join(','));
        renderThumbs($thumbs, atts);
      });

      frame.open();
    });

    $box.on('click', '[data-action="clear"]', function (e) {
      e.preventDefault();
      $input.val('');
      $thumbs.empty();
    });
  }

  $(initTileBox);
})(jQuery);
