<?php

function aleejaved_portfolio_theme_setup() {
  add_theme_support('title-tag');
  add_theme_support('post-thumbnails');

  register_nav_menus(
    array(
      'primary' => 'Primary Menu',
    )
  );

  add_post_type_support('page', 'excerpt');
}
add_action('after_setup_theme', 'aleejaved_portfolio_theme_setup');

function aleejaved_portfolio_enqueue_assets() {
  $style_path = get_stylesheet_directory() . '/style.css';
  $style_ver = file_exists($style_path) ? (string) filemtime($style_path) : '1.0.0';
  wp_enqueue_style('aleejaved-portfolio-style', get_stylesheet_uri(), array(), $style_ver);

  $nav_js_path = get_stylesheet_directory() . '/assets/nav.js';
  $nav_js_ver = file_exists($nav_js_path) ? (string) filemtime($nav_js_path) : '1.0.0';
  wp_enqueue_script(
    'aleejaved-portfolio-nav',
    get_stylesheet_directory_uri() . '/assets/nav.js',
    array(),
    $nav_js_ver,
    true
  );

  if (is_front_page()) {
    $timeline_js_path = get_stylesheet_directory() . '/assets/timeline.js';
    $timeline_js_ver = file_exists($timeline_js_path) ? (string) filemtime($timeline_js_path) : '1.0.0';
    wp_enqueue_script(
      'aleejaved-portfolio-timeline',
      get_stylesheet_directory_uri() . '/assets/timeline.js',
      array(),
      $timeline_js_ver,
      true
    );
  }

  // Popup JavaScript
  $popup_js_path = get_stylesheet_directory() . '/assets/popup.js';
  $popup_js_ver = file_exists($popup_js_path) ? (string) filemtime($popup_js_path) : '1.0.0';
  wp_enqueue_script(
    'aleejaved-portfolio-popup',
    get_stylesheet_directory_uri() . '/assets/popup.js',
    array(),
    $popup_js_ver,
    true
  );

  // Navigation highlight JavaScript
  $nav_highlight_js_path = get_stylesheet_directory() . '/assets/nav-highlight.js';
  $nav_highlight_js_ver = file_exists($nav_highlight_js_path) ? (string) filemtime($nav_highlight_js_path) : '1.0.0';
  wp_enqueue_script(
    'aleejaved-portfolio-nav-highlight',
    get_stylesheet_directory_uri() . '/assets/nav-highlight.js',
    array(),
    $nav_highlight_js_ver,
    true
  );
}
add_action('wp_enqueue_scripts', 'aleejaved_portfolio_enqueue_assets');

// Page Order Admin
function aleejaved_portfolio_page_order_admin() {
  add_submenu_page(
    'edit.php?post_type=page',
    'Page Order',
    'Page Order',
    'edit_pages',
    'aleejaved-page-order',
    'aleejaved_portfolio_page_order_page'
  );
}
add_action('admin_menu', 'aleejaved_portfolio_page_order_admin');

function aleejaved_portfolio_page_order_page() {
  echo '<div class="wrap">';
  echo '<h1>Page Order</h1>';
  echo '<p>Drag and drop pages to reorder them.</p>';
  echo '<div id="aleejaved-page-order" style="max-width:720px;">';
  echo '<ul id="aleejaved-page-order-list" style="margin:12px 0 16px;list-style:none;padding:0;">';

  $pages = get_pages(array('sort_column' => 'menu_order,post_title'));
  
  if (!empty($pages)) {
    foreach ($pages as $page) {
      echo '<li class="aleejaved-order-item" data-id="' . esc_attr((string) $page->ID) . '" style="display:flex;align-items:center;gap:10px;padding:10px 12px;border:1px solid #c3c4c7;border-radius:8px;background:#fff;margin-bottom:8px;cursor:move;user-select:none;-webkit-user-select:none;-moz-user-select:none;-ms-user-select:none;" draggable="true">';
      echo '<span style="display:inline-flex;width:18px;height:18px;align-items:center;justify-content:center;color:#646970;user-select:none;">⋮⋮</span>';
      echo '<span style="font-weight:600;user-select:none;">' . esc_html($page->post_title) . '</span>';
      echo '</li>';
    }
  }

  if (empty($pages)) {
    echo '<li style="padding:10px 12px;color:#646970;">No pages found.</li>';
  }

  echo '</ul>';
  echo '<p><button type="button" id="aleejaved-page-order-save" class="button button-primary">Save Order</button></p>';
  echo '<p id="aleejaved-page-order-status"></p>';
  echo '</div>';
  ?>
  <style>
  .dragging {
    opacity: 0.5 !important;
  }
  .drag-over {
    border-top: 3px solid #0073aa !important;
  }
  </style>
  <script>
  jQuery(document).ready(function($) {
    console.log('Page order drag script loaded');
    
    var draggedElement = null;
    
    // Prevent text selection during drag
    $(document).on('selectstart', function(e) {
      if (draggedElement) {
        e.preventDefault();
      }
    });
    
    $('#aleejaved-page-order-list').on('dragstart', 'li', function(e) {
      console.log('Drag started');
      draggedElement = $(this);
      $(this).addClass('dragging');
      e.originalEvent.dataTransfer.effectAllowed = 'move';
      e.originalEvent.dataTransfer.setData('text/html', this.innerHTML);
    });
    
    $('#aleejaved-page-order-list').on('dragend', 'li', function(e) {
      console.log('Drag ended');
      $(this).removeClass('dragging');
      $('#aleejaved-page-order-list li').removeClass('drag-over');
      draggedElement = null;
    });
    
    $('#aleejaved-page-order-list').on('dragover', function(e) {
      e.preventDefault();
      e.originalEvent.dataTransfer.dropEffect = 'move';
      
      if (!draggedElement) return;
      
      var mouseY = e.originalEvent.pageY;
      var items = $('#aleejaved-page-order-list li');
      
      // Remove all drag-over classes
      items.removeClass('drag-over');
      
      // Find where to drop
      items.each(function() {
        if ($(this).is(draggedElement)) return true;
        
        var itemTop = $(this).offset().top;
        var itemHeight = $(this).height();
        var itemMiddle = itemTop + itemHeight / 2;
        
        if (mouseY < itemMiddle) {
          $(this).addClass('drag-over');
          return false;
        }
      });
    });
    
    $('#aleejaved-page-order-list').on('drop', function(e) {
      e.preventDefault();
      console.log('Drop occurred');
      
      if (!draggedElement) return;
      
      var mouseY = e.originalEvent.pageY;
      var items = $('#aleejaved-page-order-list li');
      var dropped = false;
      
      items.each(function() {
        if ($(this).is(draggedElement)) return true;
        
        var itemTop = $(this).offset().top;
        var itemHeight = $(this).height();
        var itemMiddle = itemTop + itemHeight / 2;
        
        if (mouseY < itemMiddle) {
          $(this).before(draggedElement);
          dropped = true;
          return false;
        }
      });
      
      if (!dropped) {
        $(this).append(draggedElement);
      }
      
      $('#aleejaved-page-order-list li').removeClass('drag-over');
      console.log('Drag operation completed');
    });
    
    // Save button functionality
    $('#aleejaved-page-order-save').on('click', function(e) {
      e.preventDefault();
      console.log('Save button clicked');
      
      var btn = $(this);
      var status = $('#aleejaved-page-order-status');
      
      // Collect order
      var order = [];
      $('#aleejaved-page-order-list li').each(function(index) {
        order.push($(this).data('id'));
      });
      
      console.log('Order to save:', order);
      
      btn.prop('disabled', true).text('Saving...');
      status.text('Saving...').css('color', '#1d2327');
      
      $.ajax({
        url: ajaxurl,
        method: 'POST',
        data: {
          action: 'save_page_order',
          page_order: order,
          nonce: '<?php echo wp_create_nonce('save_page_order_nonce'); ?>'
        },
        success: function(response) {
          console.log('Save response:', response);
          btn.prop('disabled', false).text('Save Order');
          status.text('Saved!').css('color', '#00a32a');
          
          // Clear message after 3 seconds
          setTimeout(function() {
            status.text('');
          }, 3000);
        },
        error: function(xhr, status, error) {
          console.log('Save error:', error);
          btn.prop('disabled', false).text('Save Order');
          status.text('Save failed. Please try again.').css('color', '#b32d2e');
        }
      });
    });
  });
  </script>
  <?php
}

function aleejaved_portfolio_save_page_order() {
  check_ajax_referer('save_page_order_nonce', 'nonce');
  
  if (!current_user_can('edit_pages')) {
    wp_send_json_error('You do not have permission to edit pages.');
  }
  
  $page_order = $_POST['page_order'];
  
  if (!is_array($page_order)) {
    wp_send_json_error('Invalid order data.');
  }
  
  foreach ($page_order as $index => $page_id) {
    $page_id = intval($page_id);
    if ($page_id > 0) {
      wp_update_post(array(
        'ID' => $page_id,
        'menu_order' => $index
      ));
    }
  }
  
  wp_send_json_success('Order saved successfully');
}
add_action('wp_ajax_save_page_order', 'aleejaved_portfolio_save_page_order');

// Add to existing admin enqueue function
function aleejaved_portfolio_admin_enqueue($hook) {
  if ($hook === 'posts_page_aleejaved-timeline-order') {
    wp_enqueue_script('jquery-ui-sortable');

    $order_js_path = get_stylesheet_directory() . '/assets/timeline-order-admin.js';
    $order_js_ver = file_exists($order_js_path) ? (string) filemtime($order_js_path) : '1.0.0';
    wp_enqueue_script(
      'aleejaved-portfolio-timeline-order-admin',
      get_stylesheet_directory_uri() . '/assets/timeline-order-admin.js',
      array('jquery', 'jquery-ui-sortable'),
      $order_js_ver,
      true
    );
    wp_localize_script(
      'aleejaved-portfolio-timeline-order-admin',
      'AleejavedTimelineOrder',
      array(
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('aleejaved_timeline_order_save'),
      )
    );
    return;
  }

  if ($hook === 'admin_page_aleejaved-page-order') {
    $order_js_path = get_stylesheet_directory() . '/assets/page-order-admin.js';
    $order_js_ver = file_exists($order_js_path) ? (string) filemtime($order_js_path) : '1.0.0';
    wp_enqueue_script(
      'aleejaved-portfolio-page-order-admin',
      get_stylesheet_directory_uri() . '/assets/page-order-admin.js',
      array('jquery'),
      $order_js_ver,
      true
    );
    wp_localize_script(
      'aleejaved-portfolio-page-order-admin',
      'AleejavedPageOrder',
      array(
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('save_page_order_nonce'),
      )
    );
    return;
  }

  if ($hook !== 'post.php' && $hook !== 'post-new.php') {
    return;
  }

  $screen = function_exists('get_current_screen') ? get_current_screen() : null;
  if (!$screen || $screen->post_type !== 'post') {
    return;
  }

  wp_enqueue_media();

  $admin_js_path = get_stylesheet_directory() . '/assets/timeline-admin.js';
  $admin_js_ver = file_exists($admin_js_path) ? (string) filemtime($admin_js_path) : '1.0.0';
  wp_enqueue_script(
    'aleejaved-portfolio-timeline-admin',
    get_stylesheet_directory_uri() . '/assets/timeline-admin.js',
    array('jquery'),
    $admin_js_ver,
    true
  );
}

function aleejaved_portfolio_sanitize_timeline_sort_mode($value) {
  if (!is_string($value)) {
    return 'date';
  }
  return $value === 'impressiveness' ? 'impressiveness' : 'date';
}
add_action('admin_enqueue_scripts', 'aleejaved_portfolio_admin_enqueue');

function aleejaved_portfolio_register_timeline_order_page() {
  add_submenu_page(
    'edit.php',
    'Timeline Order',
    'Timeline Order',
    'edit_posts',
    'aleejaved-timeline-order',
    'aleejaved_portfolio_render_timeline_order_page'
  );
}
add_action('admin_menu', 'aleejaved_portfolio_register_timeline_order_page');

function aleejaved_portfolio_render_timeline_order_page() {
  if (!current_user_can('edit_posts')) {
    return;
  }

  $query = new WP_Query(
    array(
      'post_type' => 'post',
      'posts_per_page' => -1,
      'ignore_sticky_posts' => true,
      'category_name' => 'timeline',
      'orderby' => array(
        'date' => 'DESC',
      ),
    )
  );

  echo '<div class="wrap">';
  echo '<h1>Timeline Order</h1>';
  echo '<p>Drag and drop to set your timeline from most to least impressive.</p>';
  echo '<div id="aleejaved-timeline-order" style="max-width:720px;">';
  echo '<ul id="aleejaved-timeline-order-list" style="margin:12px 0 16px;">';

  $posts = is_array($query->posts) ? $query->posts : array();
  usort(
    $posts,
    function ($a, $b) {
      $ra = get_post_meta($a->ID, '_aleejaved_timeline_impressiveness_order', true);
      $rb = get_post_meta($b->ID, '_aleejaved_timeline_impressiveness_order', true);
      $hasA = $ra !== '';
      $hasB = $rb !== '';
      $oa = (int) $ra;
      $ob = (int) $rb;

      if ($hasA && $hasB && $oa !== $ob) {
        return $oa - $ob;
      }
      if ($hasA !== $hasB) {
        return $hasA ? -1 : 1;
      }

      $da = strtotime((string) $a->post_date);
      $db = strtotime((string) $b->post_date);
      if ($da === $db) {
        return 0;
      }
      return $da < $db ? 1 : -1;
    }
  );

  if (!empty($posts)) {
    foreach ($posts as $p) {
      echo '<li class="aleejaved-order-item" data-id="' . esc_attr((string) $p->ID) . '" style="display:flex;align-items:center;gap:10px;padding:10px 12px;border:1px solid #c3c4c7;border-radius:8px;background:#fff;margin-bottom:8px;cursor:move;">';
      echo '<span style="display:inline-flex;width:18px;height:18px;align-items:center;justify-content:center;color:#646970;">⋮⋮</span>';
      echo '<span style="font-weight:600;">' . esc_html(get_the_title($p->ID)) . '</span>';
      echo '</li>';
    }
  }

  if (empty($posts)) {
    echo '<li style="padding:10px 12px;color:#646970;">No timeline posts found.</li>';
  }

  echo '</ul>';
  echo '<button type="button" class="button button-primary" id="aleejaved-timeline-order-save">Save order</button>';
  echo '<span id="aleejaved-timeline-order-status" style="margin-left:10px;"></span>';
  echo '</div>';
  echo '</div>';
}

function aleejaved_portfolio_save_timeline_order_ajax() {
  if (!current_user_can('edit_posts')) {
    wp_send_json_error(array('message' => 'Unauthorized'), 403);
  }
  check_ajax_referer('aleejaved_timeline_order_save', 'nonce');

  $order = isset($_POST['order']) && is_array($_POST['order']) ? $_POST['order'] : array();
  $ids = array();
  foreach ($order as $id) {
    $ids[] = absint($id);
  }
  $ids = array_values(array_filter($ids));

  foreach ($ids as $index => $post_id) {
    update_post_meta($post_id, '_aleejaved_timeline_impressiveness_order', (string) $index);
  }

  wp_send_json_success(array('count' => count($ids)));
}
add_action('wp_ajax_aleejaved_save_timeline_order', 'aleejaved_portfolio_save_timeline_order_ajax');

// Sanitize hero buttons
function aleejaved_sanitize_hero_buttons($value) {
  if (!is_string($value)) {
    return json_encode(array(
      array('name' => 'LinkedIn', 'link' => ''),
      array('name' => 'GitHub', 'link' => ''),
      array('name' => 'Email', 'link' => ''),
    ));
  }
  
  $buttons = json_decode($value, true);
  if (!is_array($buttons)) {
    return json_encode(array(
      array('name' => 'LinkedIn', 'link' => ''),
      array('name' => 'GitHub', 'link' => ''),
      array('name' => 'Email', 'link' => ''),
    ));
  }
  
  $sanitized = array();
  foreach ($buttons as $button) {
    if (isset($button['name']) && isset($button['link'])) {
      $sanitized[] = array(
        'name' => sanitize_text_field($button['name']),
        'link' => esc_url_raw($button['link']),
      );
    }
  }
  
  return json_encode($sanitized);
}

// Custom control for hero buttons
if (class_exists('WP_Customize_Control')) {
  class Aleejaved_Hero_Buttons_Control extends WP_Customize_Control {
    public $type = 'hero_buttons';
    
    public function render_content() {
      $buttons = json_decode($this->value(), true) ?: array(
        array('name' => 'LinkedIn', 'link' => ''),
        array('name' => 'GitHub', 'link' => ''),
        array('name' => 'Email', 'link' => ''),
      );
      ?>
      <span class="customize-control-title"><?php echo esc_html($this->label); ?></span>
      <div class="hero-buttons-container">
        <div id="hero-buttons-list">
          <?php foreach ($buttons as $index => $button): ?>
          <div class="hero-button-item" data-index="<?php echo $index; ?>">
            <input type="text" placeholder="Button name" value="<?php echo esc_attr($button['name']); ?>" class="hero-button-name">
            <input type="url" placeholder="Button link" value="<?php echo esc_attr($button['link']); ?>" class="hero-button-link">
            <button type="button" class="button hero-button-remove">Remove</button>
          </div>
          <?php endforeach; ?>
        </div>
        <button type="button" class="button hero-button-add">Add Button</button>
        <input type="hidden" id="hero-buttons-value" <?php $this->link(); ?>>
      </div>
      <style>
      .hero-buttons-container { margin-top: 10px; }
      .hero-button-item { display: flex; gap: 8px; margin-bottom: 8px; align-items: center; }
      .hero-button-name { flex: 1; }
      .hero-button-link { flex: 2; }
      .hero-button-remove { flex-shrink: 0; }
      .hero-button-add { margin-top: 8px; }
      </style>
      <script>
      jQuery(document).ready(function($) {
        function updateHeroButtonsValue() {
          var buttons = [];
          $('.hero-button-item').each(function() {
            var name = $(this).find('.hero-button-name').val();
            var link = $(this).find('.hero-button-link').val();
            if (name) {
              buttons.push({name: name, link: link});
            }
          });
          $('#hero-buttons-value').val(JSON.stringify(buttons)).trigger('change');
        }
        
        $(document).on('click', '.hero-button-remove', function() {
          $(this).closest('.hero-button-item').remove();
          updateHeroButtonsValue();
        });
        
        $(document).on('click', '.hero-button-add', function() {
          var index = $('.hero-button-item').length;
          var html = '<div class="hero-button-item" data-index="' + index + '">' +
            '<input type="text" placeholder="Button name" class="hero-button-name">' +
            '<input type="url" placeholder="Button link" class="hero-button-link">' +
            '<button type="button" class="button hero-button-remove">Remove</button>' +
            '</div>';
          $('#hero-buttons-list').append(html);
        });
        
        $(document).on('input', '.hero-button-name, .hero-button-link', updateHeroButtonsValue);
      });
      </script>
      <?php
    }
  }
}

// Sanitize portfolio buttons
function aleejaved_sanitize_portfolio_buttons($value) {
  if (!is_string($value)) {
    return json_encode(array(
      array('name' => 'LinkedIn', 'link' => ''),
      array('name' => 'GitHub', 'link' => ''),
    ));
  }
  
  $buttons = json_decode($value, true);
  if (!is_array($buttons)) {
    return json_encode(array(
      array('name' => 'LinkedIn', 'link' => ''),
      array('name' => 'GitHub', 'link' => ''),
    ));
  }
  
  $sanitized = array();
  foreach ($buttons as $button) {
    if (isset($button['name']) && isset($button['link'])) {
      $sanitized[] = array(
        'name' => sanitize_text_field($button['name']),
        'link' => esc_url_raw($button['link']),
      );
    }
  }
  
  return json_encode($sanitized);
}

// Custom control for portfolio buttons
if (class_exists('WP_Customize_Control')) {
  class Aleejaved_Portfolio_Buttons_Control extends WP_Customize_Control {
    public $type = 'portfolio_buttons';
    
    public function render_content() {
      $buttons = json_decode($this->value(), true) ?: array(
        array('name' => 'LinkedIn', 'link' => ''),
        array('name' => 'GitHub', 'link' => ''),
      );
      ?>
      <span class="customize-control-title"><?php echo esc_html($this->label); ?></span>
      <div class="portfolio-buttons-container">
        <div id="portfolio-buttons-list">
          <?php foreach ($buttons as $index => $button): ?>
          <div class="portfolio-button-item" data-index="<?php echo $index; ?>">
            <input type="text" placeholder="Button name" value="<?php echo esc_attr($button['name']); ?>" class="portfolio-button-name">
            <input type="url" placeholder="Button link" value="<?php echo esc_attr($button['link']); ?>" class="portfolio-button-link">
            <button type="button" class="button portfolio-button-remove">Remove</button>
          </div>
          <?php endforeach; ?>
        </div>
        <button type="button" class="button portfolio-button-add">Add Button</button>
        <input type="hidden" id="portfolio-buttons-value" <?php $this->link(); ?>>
      </div>
      <style>
      .portfolio-buttons-container { margin-top: 10px; }
      .portfolio-button-item { display: flex; gap: 8px; margin-bottom: 8px; align-items: center; }
      .portfolio-button-name { flex: 1; }
      .portfolio-button-link { flex: 2; }
      .portfolio-button-remove { flex-shrink: 0; }
      .portfolio-button-add { margin-top: 8px; }
      </style>
      <script>
      jQuery(document).ready(function($) {
        function updatePortfolioButtonsValue() {
          var buttons = [];
          $('.portfolio-button-item').each(function() {
            var name = $(this).find('.portfolio-button-name').val();
            var link = $(this).find('.portfolio-button-link').val();
            if (name) {
              buttons.push({name: name, link: link});
            }
          });
          $('#portfolio-buttons-value').val(JSON.stringify(buttons)).trigger('change');
        }
        
        $(document).on('click', '.portfolio-button-remove', function() {
          $(this).closest('.portfolio-button-item').remove();
          updatePortfolioButtonsValue();
        });
        
        $(document).on('click', '.portfolio-button-add', function() {
          var index = $('.portfolio-button-item').length;
          var html = '<div class="portfolio-button-item" data-index="' + index + '">' +
            '<input type="text" placeholder="Button name" class="portfolio-button-name">' +
            '<input type="url" placeholder="Button link" class="portfolio-button-link">' +
            '<button type="button" class="button portfolio-button-remove">Remove</button>' +
            '</div>';
          $('#portfolio-buttons-list').append(html);
        });
        
        $(document).on('input', '.portfolio-button-name, .portfolio-button-link', updatePortfolioButtonsValue);
      });
      </script>
      <?php
    }
  }
}

function aleejaved_portfolio_customize_register($wp_customize) {
  $wp_customize->add_section(
    'aleejaved_portfolio_timeline',
    array(
      'title' => 'Timeline',
      'priority' => 28,
    )
  );

  $wp_customize->add_section(
    'aleejaved_portfolio_links',
    array(
      'title' => 'Portfolio Links',
      'priority' => 30,
    )
  );

  $wp_customize->add_section(
    'aleejaved_portfolio_hero',
    array(
      'title' => 'Hero',
      'priority' => 29,
    )
  );

  $wp_customize->add_section(
    'aleejaved_portfolio_footer',
    array(
      'title' => 'Footer',
      'priority' => 31,
    )
  );

  $wp_customize->add_setting(
    'aleejaved_portfolio_footer_text',
    array(
      'type' => 'theme_mod',
      'sanitize_callback' => 'sanitize_textarea_field',
      'default' => 'Copyright © %year% Alee Javed. All rights reserved.',
    )
  );
  $wp_customize->add_control(
    'aleejaved_portfolio_footer_text',
    array(
      'label' => 'Footer text',
      'description' => 'Use %year% to insert the current year.',
      'section' => 'aleejaved_portfolio_footer',
      'type' => 'textarea',
    )
  );

  $wp_customize->add_setting(
    'aleejaved_portfolio_hero_subtitle',
    array(
      'type' => 'theme_mod',
      'sanitize_callback' => 'sanitize_text_field',
      'default' => 'and I am a student and tech enthusiast',
    )
  );
  $wp_customize->add_control(
    'aleejaved_portfolio_hero_subtitle',
    array(
      'label' => 'Portfolio hero subtitle',
      'section' => 'aleejaved_portfolio_hero',
      'type' => 'text',
    )
  );

  $wp_customize->add_setting(
    'aleejaved_portfolio_hero_greeting',
    array(
      'type' => 'theme_mod',
      'sanitize_callback' => 'sanitize_text_field',
      'default' => 'Hello!',
    )
  );
  $wp_customize->add_control(
    'aleejaved_portfolio_hero_greeting',
    array(
      'label' => 'Hero greeting text',
      'section' => 'aleejaved_portfolio_hero',
      'type' => 'text',
    )
  );

  $wp_customize->add_setting(
    'aleejaved_portfolio_hero_name',
    array(
      'type' => 'theme_mod',
      'sanitize_callback' => 'sanitize_text_field',
      'default' => 'Alee Javed',
    )
  );
  $wp_customize->add_control(
    'aleejaved_portfolio_hero_name',
    array(
      'label' => 'Hero name text',
      'section' => 'aleejaved_portfolio_hero',
      'type' => 'text',
    )
  );

  $wp_customize->add_setting(
    'aleejaved_portfolio_header_name',
    array(
      'type' => 'theme_mod',
      'sanitize_callback' => 'sanitize_text_field',
      'default' => 'Alee Javed',
    )
  );
  $wp_customize->add_control(
    'aleejaved_portfolio_header_name',
    array(
      'label' => 'Header site name',
      'section' => 'aleejaved_portfolio_hero',
      'type' => 'text',
    )
  );

  // Colors section
  $wp_customize->add_section(
    'aleejaved_portfolio_colors',
    array(
      'title' => 'Colors',
      'priority' => 27,
    )
  );

  $wp_customize->add_setting(
    'aleejaved_portfolio_accent_color',
    array(
      'type' => 'theme_mod',
      'sanitize_callback' => 'sanitize_hex_color',
      'default' => '#ff6b35',
    )
  );
  $wp_customize->add_control(
    new WP_Customize_Color_Control(
      $wp_customize,
      'aleejaved_portfolio_accent_color',
      array(
        'label' => 'Accent color',
        'section' => 'aleejaved_portfolio_colors',
        'description' => 'Used for links, buttons, and highlights',
      )
    )
  );

  $wp_customize->add_setting(
    'aleejaved_portfolio_background_color',
    array(
      'type' => 'theme_mod',
      'sanitize_callback' => 'sanitize_hex_color',
      'default' => '#ffffff',
    )
  );
  $wp_customize->add_control(
    new WP_Customize_Color_Control(
      $wp_customize,
      'aleejaved_portfolio_background_color',
      array(
        'label' => 'Background color',
        'section' => 'aleejaved_portfolio_colors',
        'description' => 'Main background color of the site',
      )
    )
  );

  $wp_customize->add_setting(
    'aleejaved_portfolio_text_color',
    array(
      'type' => 'theme_mod',
      'sanitize_callback' => 'sanitize_hex_color',
      'default' => '#1a1a1a',
    )
  );
  $wp_customize->add_control(
    new WP_Customize_Color_Control(
      $wp_customize,
      'aleejaved_portfolio_text_color',
      array(
        'label' => 'Text color',
        'section' => 'aleejaved_portfolio_colors',
        'description' => 'Main text color for paragraphs and headings',
      )
    )
  );

  $wp_customize->add_setting(
    'aleejaved_portfolio_muted_color',
    array(
      'type' => 'theme_mod',
      'sanitize_callback' => 'sanitize_hex_color',
      'default' => '#666666',
    )
  );
  $wp_customize->add_control(
    new WP_Customize_Color_Control(
      $wp_customize,
      'aleejaved_portfolio_muted_color',
      array(
        'label' => 'Muted text color',
        'section' => 'aleejaved_portfolio_colors',
        'description' => 'Secondary text color for subtitles and metadata',
      )
    )
  );

  $wp_customize->add_setting(
    'aleejaved_portfolio_border_color',
    array(
      'type' => 'theme_mod',
      'sanitize_callback' => 'sanitize_hex_color',
      'default' => '#e0e0e0',
    )
  );
  $wp_customize->add_control(
    new WP_Customize_Color_Control(
      $wp_customize,
      'aleejaved_portfolio_border_color',
      array(
        'label' => 'Border color',
        'section' => 'aleejaved_portfolio_colors',
        'description' => 'Color for borders and dividers',
      )
    )
  );

  
  $wp_customize->add_setting(
    'aleejaved_portfolio_timeline_default_sort',
    array(
      'type' => 'theme_mod',
      'sanitize_callback' => 'aleejaved_portfolio_sanitize_timeline_sort_mode',
      'default' => 'date',
    )
  );
  $wp_customize->add_control(
    'aleejaved_portfolio_timeline_default_sort',
    array(
      'label' => 'Default timeline sort',
      'section' => 'aleejaved_portfolio_timeline',
      'type' => 'radio',
      'choices' => array(
        'date' => 'Chronological',
        'impressiveness' => 'Impressiveness',
      ),
    )
  );

  
  $wp_customize->add_setting(
    'aleejaved_portfolio_email',
    array(
      'type' => 'theme_mod',
      'sanitize_callback' => 'sanitize_email',
      'default' => '',
    )
  );
  $wp_customize->add_control(
    'aleejaved_portfolio_email',
    array(
      'label' => 'Email',
      'section' => 'aleejaved_portfolio_links',
      'type' => 'text',
    )
  );

  $wp_customize->add_setting(
    'aleejaved_portfolio_location',
    array(
      'type' => 'theme_mod',
      'sanitize_callback' => 'sanitize_text_field',
      'default' => '📍 Nottingham, UK',
    )
  );
  $wp_customize->add_control(
    'aleejaved_portfolio_location',
    array(
      'label' => 'Location',
      'section' => 'aleejaved_portfolio_links',
      'type' => 'text',
    )
  );

  // Customizable portfolio buttons
  $wp_customize->add_setting(
    'aleejaved_portfolio_buttons',
    array(
      'type' => 'theme_mod',
      'sanitize_callback' => 'aleejaved_sanitize_portfolio_buttons',
      'default' => json_encode(array(
        array('name' => 'LinkedIn', 'link' => ''),
        array('name' => 'GitHub', 'link' => ''),
      )),
    )
  );
  $wp_customize->add_control(
    new Aleejaved_Portfolio_Buttons_Control(
      $wp_customize,
      'aleejaved_portfolio_buttons',
      array(
        'label' => 'Portfolio buttons',
        'section' => 'aleejaved_portfolio_links',
        'type' => 'portfolio_buttons',
      )
    )
  );
}
add_action('customize_register', 'aleejaved_portfolio_customize_register');

// Output custom CSS for color customizations
function aleejaved_portfolio_custom_css() {
  $accent_color = get_theme_mod('aleejaved_portfolio_accent_color', '#ff6b35');
  $background_color = get_theme_mod('aleejaved_portfolio_background_color', '#ffffff');
  $text_color = get_theme_mod('aleejaved_portfolio_text_color', '#1a1a1a');
  $muted_color = get_theme_mod('aleejaved_portfolio_muted_color', '#666666');
  $border_color = get_theme_mod('aleejaved_portfolio_border_color', '#e0e0e0');
  
  $custom_css = "
    :root {
      --accent: {$accent_color};
      --bg: {$background_color};
      --text: {$text_color};
      --muted: {$muted_color};
      --border: {$border_color};
    }
  ";
  
  wp_add_inline_style('aleejaved-portfolio-style', $custom_css);
}
add_action('wp_enqueue_scripts', 'aleejaved_portfolio_custom_css');

function aleejaved_portfolio_register_block_patterns() {
  if (!function_exists('register_block_pattern')) {
    return;
  }

  if (function_exists('register_block_pattern_category')) {
    register_block_pattern_category(
      'aleejaved-portfolio',
      array(
        'label' => 'Aleejaved Portfolio',
      )
    );
  }

  register_block_pattern(
    'aleejaved-portfolio/two-column-section',
    array(
      'title' => 'Left / Right Section',
      'categories' => array('aleejaved-portfolio'),
      'content' => "<!-- wp:columns -->\n<div class=\"wp-block-columns\"><!-- wp:column -->\n<div class=\"wp-block-column\"><!-- wp:heading {\"level\":3} -->\n<h3>Left side</h3>\n<!-- /wp:heading -->\n\n<!-- wp:paragraph -->\n<p>Add your text, lists, or images here.</p>\n<!-- /wp:paragraph --></div>\n<!-- /wp:column -->\n\n<!-- wp:column -->\n<div class=\"wp-block-column\"><!-- wp:heading {\"level\":3} -->\n<h3>Right side</h3>\n<!-- /wp:heading -->\n\n<!-- wp:paragraph -->\n<p>Add more content here. You can change column widths, alignment, and spacing in the editor.</p>\n<!-- /wp:paragraph --></div>\n<!-- /wp:column --></div>\n<!-- /wp:columns -->",
    )
  );
}
add_action('init', 'aleejaved_portfolio_register_block_patterns');

function aleejaved_portfolio_register_timeline_metabox() {
  add_meta_box(
    'aleejaved_timeline_tile',
    'Timeline Tile',
    'aleejaved_portfolio_render_timeline_metabox',
    'post',
    'side',
    'default'
  );
}
add_action('add_meta_boxes', 'aleejaved_portfolio_register_timeline_metabox');

function aleejaved_portfolio_render_timeline_metabox($post) {
  $image_side = get_post_meta($post->ID, '_aleejaved_timeline_image_side', true);
  if ($image_side !== 'left') {
    $image_side = 'right';
  }
  $gallery = (string) get_post_meta($post->ID, '_aleejaved_timeline_gallery', true);
  $date = (string) get_post_meta($post->ID, '_aleejaved_timeline_date', true);

  wp_nonce_field('aleejaved_timeline_tile_save', 'aleejaved_timeline_tile_nonce');
  ?>

  <div id="aleejaved_timeline_tile">
    <p><strong>Date</strong></p>
    <p>
      <input type="date" name="aleejaved_timeline_date" value="<?php echo esc_attr($date); ?>" style="max-width:100%;" />
    </p>

    <p><strong>Image side</strong></p>
    <p>
      <label>
        <input type="radio" name="aleejaved_timeline_image_side" value="left" <?php checked($image_side, 'left'); ?> />
        Left
      </label>
      <br />
      <label>
        <input type="radio" name="aleejaved_timeline_image_side" value="right" <?php checked($image_side, 'right'); ?> />
        Right
      </label>
    </p>

    <p><strong>Carousel images</strong></p>
    <input type="hidden" name="aleejaved_timeline_gallery" value="<?php echo esc_attr($gallery); ?>" />

    <p>
      <button type="button" class="button" data-action="select">Select media</button>
      <button type="button" class="button" data-action="clear">Clear</button>
    </p>

    <div data-role="thumbs" style="display:flex;flex-wrap:wrap;gap:8px;"></div>

    <p style="margin-top:10px;">
      <small>
        Use the post <strong>Title</strong> and <strong>Excerpt</strong> for the tile content.
        Add a full blog post in the main editor to automatically show the “Blog post →” button.
      </small>
    </p>
  </div>

  <?php
}

function aleejaved_portfolio_save_timeline_metabox($post_id) {
  if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
    return;
  }
  if (!isset($_POST['aleejaved_timeline_tile_nonce']) || !wp_verify_nonce($_POST['aleejaved_timeline_tile_nonce'], 'aleejaved_timeline_tile_save')) {
    return;
  }
  if (!current_user_can('edit_post', $post_id)) {
    return;
  }

  if (isset($_POST['aleejaved_timeline_date'])) {
    $raw_date = (string) $_POST['aleejaved_timeline_date'];
    $date = preg_match('/^\d{4}-\d{2}-\d{2}$/', $raw_date) ? $raw_date : '';
    if ($date) {
      update_post_meta($post_id, '_aleejaved_timeline_date', $date);
    } else {
      delete_post_meta($post_id, '_aleejaved_timeline_date');
    }
  }

  delete_post_meta($post_id, '_aleejaved_timeline_impressiveness');

  if (isset($_POST['aleejaved_timeline_image_side'])) {
    $side = $_POST['aleejaved_timeline_image_side'] === 'left' ? 'left' : 'right';
    update_post_meta($post_id, '_aleejaved_timeline_image_side', $side);
  }

  if (isset($_POST['aleejaved_timeline_gallery'])) {
    $raw = (string) $_POST['aleejaved_timeline_gallery'];
    $parts = array_filter(array_map('absint', array_map('trim', explode(',', $raw))));
    update_post_meta($post_id, '_aleejaved_timeline_gallery', implode(',', $parts));
  }
}
add_action('save_post_post', 'aleejaved_portfolio_save_timeline_metabox');

function aleejaved_portfolio_create_page_if_missing($title, $slug, $excerpt) {
  $existing = get_page_by_path($slug);
  if ($existing && !is_wp_error($existing)) {
    return (int) $existing->ID;
  }

  $page_id = wp_insert_post(
    array(
      'post_type' => 'page',
      'post_status' => 'publish',
      'post_title' => $title,
      'post_name' => $slug,
      'post_excerpt' => $excerpt,
      'post_content' => '',
    )
  );

  if (is_wp_error($page_id)) {
    return 0;
  }

  return (int) $page_id;
}

function aleejaved_portfolio_bootstrap_site() {
  $portfolio_id = aleejaved_portfolio_create_page_if_missing('Portfolio', 'portfolio', "A plain white portfolio with a clean layout and simple interactions.");
  $contact_id = aleejaved_portfolio_create_page_if_missing('Contact', 'contact', 'Reach out for collaborations, opportunities, or questions.');

  if ($portfolio_id) {
    $portfolio_post = get_post($portfolio_id);
    if ($portfolio_post && trim((string) $portfolio_post->post_content) === '') {
      $starter_content = "<!-- wp:heading -->\n<h2>About</h2>\n<!-- /wp:heading -->\n\n<!-- wp:paragraph -->\n<p>Write a short introduction about yourself here. Mention what you build, what you enjoy, and what you're currently learning.</p>\n<!-- /wp:paragraph -->\n\n<!-- wp:columns -->\n<div class=\"wp-block-columns\"><!-- wp:column -->\n<div class=\"wp-block-column\"><!-- wp:heading {\"level\":3} -->\n<h3>What I do</h3>\n<!-- /wp:heading -->\n\n<!-- wp:list -->\n<ul><!-- wp:list-item -->\n<li>Web development</li>\n<!-- /wp:list-item --><!-- wp:list-item -->\n<li>UI/UX focused builds</li>\n<!-- /wp:list-item --><!-- wp:list-item -->\n<li>Learning new tech</li>\n<!-- /wp:list-item --></ul>\n<!-- /wp:list -->\n</div>\n<!-- /wp:column -->\n\n<!-- wp:column -->\n<div class=\"wp-block-column\"><!-- wp:heading {\"level\":3} -->\n<h3>Highlights</h3>\n<!-- /wp:heading -->\n\n<!-- wp:paragraph -->\n<p>Add a few standout projects, achievements, or interests. You can change the layout, sizes, and structure using the block editor.</p>\n<!-- /wp:paragraph -->\n</div>\n<!-- /wp:column --></div>\n<!-- /wp:columns -->";

      wp_update_post(
        array(
          'ID' => $portfolio_id,
          'post_content' => $starter_content,
        )
      );
    }
  }

  if ($portfolio_id) {
    update_option('show_on_front', 'page');
    update_option('page_on_front', $portfolio_id);
  }

  $menu_name = 'Primary';
  $menu = wp_get_nav_menu_object($menu_name);

  if (!$menu) {
    $menu_id = wp_create_nav_menu($menu_name);

    if (!is_wp_error($menu_id)) {
      if ($portfolio_id) {
        wp_update_nav_menu_item(
          $menu_id,
          0,
          array(
            'menu-item-title' => 'Portfolio',
            'menu-item-object' => 'page',
            'menu-item-object-id' => $portfolio_id,
            'menu-item-type' => 'post_type',
            'menu-item-status' => 'publish',
          )
        );
      }

      if ($contact_id) {
        wp_update_nav_menu_item(
          $menu_id,
          0,
          array(
            'menu-item-title' => 'Contact',
            'menu-item-object' => 'page',
            'menu-item-object-id' => $contact_id,
            'menu-item-type' => 'post_type',
            'menu-item-status' => 'publish',
          )
        );
      }

      $locations = get_theme_mod('nav_menu_locations');
      if (!is_array($locations)) {
        $locations = array();
      }
      $locations['primary'] = $menu_id;
      set_theme_mod('nav_menu_locations', $locations);
    }
  }
}
add_action('after_switch_theme', 'aleejaved_portfolio_bootstrap_site');

// Register Popup Post Type
function aj_register_popup_post_type() {
  register_post_type('aj_popup', array(
    'labels' => array(
      'name' => 'Popups',
      'singular_name' => 'Popup',
      'add_new' => 'Add New Popup',
      'add_new_item' => 'Add New Popup',
      'edit_item' => 'Edit Popup',
      'new_item' => 'New Popup',
      'view_item' => 'View Popup',
      'search_items' => 'Search Popups',
      'not_found' => 'No popups found',
      'not_found_in_trash' => 'No popups found in trash',
      'menu_name' => 'Popups',
    ),
    'public' => false,
    'show_ui' => true,
    'show_in_menu' => true,
    'show_in_rest' => true,
    'menu_icon' => 'dashicons-external',
    'menu_position' => 25,
    'supports' => array('title', 'editor'),
    'has_archive' => false,
    'rewrite' => false,
  ));
}
add_action('init', 'aj_register_popup_post_type');

function aj_popup_meta_boxes() {
  add_meta_box(
    'aj_popup_trigger_info',
    '🔗 Popup Trigger Link',
    'aj_popup_trigger_callback',
    'aj_popup',
    'side',
    'high'
  );
}
add_action('add_meta_boxes', 'aj_popup_meta_boxes');

function aj_popup_trigger_callback($post) {
  $popup_id = $post->ID;
  $trigger = '#aj-popup-' . $popup_id;
  ?>
  <p style="margin-bottom:8px;">Copy this link and add it to any text or button:</p>
  <input type="text" value="<?php echo esc_attr($trigger); ?>" readonly 
    onclick="this.select(); navigator.clipboard.writeText(this.value);"
    style="width:100%; padding:8px; font-family:monospace; font-size:14px; background:#f6f7f7; border:1px solid #ddd; border-radius:4px; cursor:pointer;">
  <p style="margin-top:8px; color:#666; font-size:12px;">Click the field to copy. Paste as a link URL.</p>
  <?php
}

function aj_output_popups() {
  $popups = get_posts(array(
    'post_type' => 'aj_popup',
    'posts_per_page' => -1,
    'post_status' => 'publish',
  ));
  
  foreach ($popups as $popup) {
    $title = get_the_title($popup);
    $content = apply_filters('the_content', $popup->post_content);
    echo aj_popup($popup->ID, $title, $content);
  }
}
add_action('wp_footer', 'aj_output_popups');

function aj_popup($id, $title, $content) {
  $html = '<div id="aj-popup-' . esc_attr($id) . '" class="aj-popup-overlay">';
  $html .= '<div class="aj-popup-container">';
  $html .= '<button class="aj-popup-close" aria-label="Close"></button>';
  if ($title) {
    $html .= '<h3 class="aj-popup-title">' . esc_html($title) . '</h3>';
  }
  $html .= '<div class="aj-popup-content">' . $content . '</div>';
  $html .= '</div></div>';
  return $html;
}

// Register Birthday Card Post Type
function aj_register_card_post_type() {
  register_post_type('aj_card', array(
    'labels' => array(
      'name' => 'Birthday Cards',
      'singular_name' => 'Birthday Card',
      'add_new' => 'Create New Card',
      'add_new_item' => 'Create New Birthday Card',
      'edit_item' => 'Edit Birthday Card',
      'new_item' => 'New Birthday Card',
      'view_item' => 'View Card',
      'search_items' => 'Search Cards',
      'not_found' => 'No cards found',
      'not_found_in_trash' => 'No cards found in trash',
      'menu_name' => 'Birthday Cards',
    ),
    'public' => true,
    'publicly_queryable' => true,
    'show_ui' => true,
    'show_in_menu' => true,
    'show_in_rest' => false,
    'menu_icon' => 'dashicons-heart',
    'menu_position' => 26,
    'supports' => array('title'),
    'has_archive' => false,
    'exclude_from_search' => true,
    'rewrite' => array('slug' => 'card', 'with_front' => false),
  ));
}
add_action('init', 'aj_register_card_post_type');

function aj_card_flush_rewrite() {
  aj_register_card_post_type();
  flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'aj_card_flush_rewrite');
add_action('after_switch_theme', 'aj_card_flush_rewrite');

function aj_card_meta_boxes() {
  add_meta_box('aj_card_design', 'Card Design', 'aj_card_design_callback', 'aj_card', 'normal', 'high');
  add_meta_box('aj_card_front', 'Front of Card', 'aj_card_front_callback', 'aj_card', 'normal', 'high');
  add_meta_box('aj_card_inside', 'Inside of Card', 'aj_card_inside_callback', 'aj_card', 'normal', 'high');
  add_meta_box('aj_card_effects', 'Effects & Music', 'aj_card_effects_callback', 'aj_card', 'normal', 'high');
  add_meta_box('aj_card_link', '🔗 Share Link', 'aj_card_link_callback', 'aj_card', 'side', 'high');
}
add_action('add_meta_boxes', 'aj_card_meta_boxes');

function aj_card_link_callback($post) {
  $url_prefix = get_post_meta($post->ID, '_aj_card_url_prefix', true) ?: 'card';
  $slug = $post->post_name ?: sanitize_title($post->post_title);
  ?>
  <p><strong>URL Prefix:</strong></p>
  <input type="text" name="aj_card_url_prefix" value="<?php echo esc_attr($url_prefix); ?>" style="width:100%;padding:8px;font-size:12px;margin-bottom:5px;" placeholder="card">
  <p style="color:#666;font-size:11px;margin-bottom:15px;">e.g., "birthday" → /birthday/slug</p>
  
  <p><strong>Card Slug:</strong></p>
  <input type="text" name="aj_card_slug" value="<?php echo esc_attr($slug); ?>" style="width:100%;padding:8px;font-size:12px;margin-bottom:5px;">
  <p style="color:#666;font-size:11px;margin-bottom:15px;">e.g., "alee" → /<?php echo esc_html($url_prefix); ?>/alee</p>
  <?php
  if ($post->post_status === 'publish') {
    $url = home_url('/' . $url_prefix . '/' . $slug);
    echo '<p><strong>Share Link:</strong></p>';
    echo '<input type="text" value="' . esc_url($url) . '" readonly onclick="this.select();navigator.clipboard.writeText(this.value);" style="width:100%;padding:8px;font-size:12px;cursor:pointer;background:#f5f5f5;">';
    echo '<p style="color:#666;font-size:11px;margin-top:8px;">Click to copy. Only people with this link can see the card.</p>';
  } else {
    echo '<p style="color:#666;">Publish the card to get the share link.</p>';
  }
}

function aj_card_design_callback($post) {
  wp_nonce_field('aj_card_save', 'aj_card_nonce');
  $card_type = get_post_meta($post->ID, '_aj_card_type', true) ?: 'classic';
  $card_color = get_post_meta($post->ID, '_aj_card_color', true) ?: '#ff6b6b';
  $card_pattern = get_post_meta($post->ID, '_aj_card_pattern', true) ?: 'none';
  $bg_color = get_post_meta($post->ID, '_aj_card_bg_color', true) ?: '#1a1a2e';
  $bg_pattern = get_post_meta($post->ID, '_aj_card_bg_pattern', true) ?: 'none';
  ?>
  <style>
    .aj-card-row { display: flex; gap: 20px; margin-bottom: 15px; flex-wrap: wrap; }
    .aj-card-field { flex: 1; min-width: 200px; }
    .aj-card-field label { display: block; font-weight: 600; margin-bottom: 5px; }
    .aj-card-field input, .aj-card-field select, .aj-card-field textarea { width: 100%; padding: 8px; }
    .aj-card-field input[type="color"] { width: 60px; height: 40px; padding: 0; cursor: pointer; }
    .aj-card-types { display: flex; gap: 10px; flex-wrap: wrap; }
    .aj-card-type { padding: 15px 20px; border: 2px solid #ddd; border-radius: 8px; cursor: pointer; text-align: center; transition: all 0.2s; }
    .aj-card-type:hover { border-color: #2271b1; }
    .aj-card-type.selected { border-color: #2271b1; background: #f0f6fc; }
    .aj-card-type span { display: block; font-size: 24px; margin-bottom: 5px; }
  </style>
  <div class="aj-card-row">
    <div class="aj-card-field" style="flex:2;">
      <label>Card Style</label>
      <div class="aj-card-types">
        <div class="aj-card-type <?php echo $card_type === 'classic' ? 'selected' : ''; ?>" data-type="classic"><span>🎂</span>Classic</div>
        <div class="aj-card-type <?php echo $card_type === 'elegant' ? 'selected' : ''; ?>" data-type="elegant"><span>✨</span>Elegant</div>
        <div class="aj-card-type <?php echo $card_type === 'fun' ? 'selected' : ''; ?>" data-type="fun"><span>🎉</span>Fun</div>
        <div class="aj-card-type <?php echo $card_type === 'minimal' ? 'selected' : ''; ?>" data-type="minimal"><span>🤍</span>Minimal</div>
        <div class="aj-card-type <?php echo $card_type === 'retro' ? 'selected' : ''; ?>" data-type="retro"><span>🕹️</span>Retro</div>
        <div class="aj-card-type <?php echo $card_type === 'neon' ? 'selected' : ''; ?>" data-type="neon"><span>💜</span>Neon</div>
      </div>
      <input type="hidden" name="aj_card_type" id="aj_card_type" value="<?php echo esc_attr($card_type); ?>">
    </div>
  </div>
  <div class="aj-card-row">
    <div class="aj-card-field">
      <label>Card Color</label>
      <input type="color" name="aj_card_color" value="<?php echo esc_attr($card_color); ?>">
    </div>
    <div class="aj-card-field">
      <label>Card Pattern</label>
      <select name="aj_card_pattern">
        <option value="none" <?php selected($card_pattern, 'none'); ?>>None</option>
        <option value="dots" <?php selected($card_pattern, 'dots'); ?>>Polka Dots</option>
        <option value="stripes" <?php selected($card_pattern, 'stripes'); ?>>Stripes</option>
        <option value="confetti" <?php selected($card_pattern, 'confetti'); ?>>Confetti</option>
        <option value="hearts" <?php selected($card_pattern, 'hearts'); ?>>Hearts</option>
        <option value="stars" <?php selected($card_pattern, 'stars'); ?>>Stars</option>
        <option value="balloons" <?php selected($card_pattern, 'balloons'); ?>>Balloons</option>
      </select>
    </div>
    <div class="aj-card-field">
      <label>Background Color</label>
      <input type="color" name="aj_card_bg_color" value="<?php echo esc_attr($bg_color); ?>">
    </div>
    <div class="aj-card-field">
      <label>Background Effect</label>
      <select name="aj_card_bg_pattern">
        <option value="none" <?php selected($bg_pattern, 'none'); ?>>None</option>
        <option value="particles" <?php selected($bg_pattern, 'particles'); ?>>Floating Particles</option>
        <option value="stars" <?php selected($bg_pattern, 'stars'); ?>>Twinkling Stars</option>
        <option value="confetti" <?php selected($bg_pattern, 'confetti'); ?>>Falling Confetti</option>
        <option value="bubbles" <?php selected($bg_pattern, 'bubbles'); ?>>Bubbles</option>
        <option value="gradient" <?php selected($bg_pattern, 'gradient'); ?>>Animated Gradient</option>
      </select>
    </div>
  </div>
  <script>
  jQuery(function($) {
    $('.aj-card-type').click(function() {
      $('.aj-card-type').removeClass('selected');
      $(this).addClass('selected');
      $('#aj_card_type').val($(this).data('type'));
    });
  });
  </script>
  <?php
}

function aj_card_front_callback($post) {
  $front_title = get_post_meta($post->ID, '_aj_card_front_title', true) ?: 'Happy Birthday!';
  $front_subtitle = get_post_meta($post->ID, '_aj_card_front_subtitle', true) ?: '';
  $front_image = get_post_meta($post->ID, '_aj_card_front_image', true) ?: '';
  $front_emoji = get_post_meta($post->ID, '_aj_card_front_emoji', true) ?: '🎂';
  ?>
  <div class="aj-card-row">
    <div class="aj-card-field">
      <label>Main Title</label>
      <input type="text" name="aj_card_front_title" value="<?php echo esc_attr($front_title); ?>" placeholder="Happy Birthday!">
    </div>
    <div class="aj-card-field">
      <label>Large Emoji/Icon</label>
      <input type="text" name="aj_card_front_emoji" value="<?php echo esc_attr($front_emoji); ?>" placeholder="🎂" style="font-size:24px;">
    </div>
  </div>
  <div class="aj-card-row">
    <div class="aj-card-field">
      <label>Subtitle (optional)</label>
      <input type="text" name="aj_card_front_subtitle" value="<?php echo esc_attr($front_subtitle); ?>" placeholder="Click to open...">
    </div>
    <div class="aj-card-field">
      <label>Front Image (optional)</label>
      <input type="text" name="aj_card_front_image" id="aj_front_image" value="<?php echo esc_attr($front_image); ?>" placeholder="Image URL or click to upload">
      <button type="button" class="button aj-upload-btn" data-target="aj_front_image" style="margin-top:5px;">Upload Image</button>
    </div>
  </div>
  <?php
}

function aj_card_inside_callback($post) {
  $inside_message = get_post_meta($post->ID, '_aj_card_inside_message', true) ?: '';
  $inside_from = get_post_meta($post->ID, '_aj_card_inside_from', true) ?: '';
  $inside_image = get_post_meta($post->ID, '_aj_card_inside_image', true) ?: '';
  $inside_extra = get_post_meta($post->ID, '_aj_card_inside_extra', true) ?: '';
  $inside_bg_color = get_post_meta($post->ID, '_aj_card_inside_bg_color', true) ?: '#fffef9';
  $pages = get_post_meta($post->ID, '_aj_card_pages', true) ?: '';
  $photos = get_post_meta($post->ID, '_aj_card_photos', true) ?: '';
  ?>
  <style>
    .aj-pages-container{margin:15px 0;}
    .aj-page-item{background:#f9f9f9;padding:15px;border-radius:8px;margin-bottom:15px;position:relative;}
    .aj-page-item h4{margin:0 0 10px;color:#1d2327;}
    .aj-page-remove{position:absolute;top:10px;right:10px;color:#b32d2e;cursor:pointer;font-size:20px;}
    .aj-photos-grid{display:flex;flex-wrap:wrap;gap:10px;margin:15px 0;}
    .aj-photo-item{width:100px;height:100px;position:relative;border-radius:8px;overflow:hidden;}
    .aj-photo-item img{width:100%;height:100%;object-fit:cover;}
    .aj-photo-remove{position:absolute;top:2px;right:2px;background:rgba(0,0,0,0.7);color:#fff;width:20px;height:20px;border-radius:50%;display:flex;align-items:center;justify-content:center;cursor:pointer;font-size:14px;}
  </style>
  <p style="color:#666;margin-bottom:15px;"><strong>Page 1 - Main Message</strong></p>
  <div class="aj-card-row" style="margin-bottom:15px;">
    <div class="aj-card-field" style="max-width:200px;">
      <label>Page Background Color</label>
      <input type="color" name="aj_card_inside_bg_color" value="<?php echo esc_attr($inside_bg_color); ?>" style="width:60px;height:35px;border:none;cursor:pointer;">
      <span style="margin-left:10px;color:#666;font-size:12px;">Default: cream</span>
    </div>
  </div>
  <div class="aj-card-row">
    <div class="aj-card-field" style="flex:2;">
      <label>Birthday Message</label>
      <?php
      wp_editor($inside_message, 'aj_card_inside_message', array(
        'textarea_name' => 'aj_card_inside_message',
        'textarea_rows' => 8,
        'media_buttons' => true,
        'teeny' => false,
        'quicktags' => true,
        'tinymce' => array(
          'toolbar1' => 'bold,italic,underline,|,bullist,numlist,|,alignleft,aligncenter,alignright,|,link,|,forecolor,backcolor,|,fontsizeselect',
          'toolbar2' => '',
          'fontsize_formats' => '14px 16px 18px 20px 24px 28px 32px 36px 48px 64px',
        ),
      ));
      ?>
    </div>
  </div>
  <div class="aj-card-row">
    <div class="aj-card-field">
      <label>Inside Image (optional)</label>
      <input type="text" name="aj_card_inside_image" id="aj_inside_image" value="<?php echo esc_attr($inside_image); ?>" placeholder="Image URL">
      <button type="button" class="button aj-upload-btn" data-target="aj_inside_image" style="margin-top:5px;">Upload Image</button>
    </div>
    <div class="aj-card-field">
      <label>From (signature)</label>
      <input type="text" name="aj_card_inside_from" value="<?php echo esc_attr($inside_from); ?>" placeholder="With love, Your Name">
    </div>
  </div>
  
  <hr style="margin:25px 0;border:none;border-top:1px solid #ddd;">
  <p style="color:#666;margin-bottom:10px;"><strong>Additional Pages</strong> (swipe/click to navigate)</p>
  <div id="aj-pages-container" class="aj-pages-container"></div>
  <button type="button" class="button" id="aj-add-page">+ Add Another Page</button>
  <textarea name="aj_card_pages" id="aj_card_pages" style="display:none;"><?php echo esc_textarea($pages); ?></textarea>
  
  <hr style="margin:25px 0;border:none;border-top:1px solid #ddd;">
  <p style="color:#666;margin-bottom:10px;"><strong>Photo Gallery</strong> - Photos will animate around the card (random or synced to music)</p>
  <div id="aj-photos-grid" class="aj-photos-grid"></div>
  <button type="button" class="button" id="aj-add-photo">+ Add Photos</button>
  <textarea name="aj_card_photos" id="aj_card_photos" style="display:none;"><?php echo esc_textarea($photos); ?></textarea>
  
  <script>
  jQuery(function($) {
    // Pages management
    var pages = [];
    try { pages = JSON.parse($('#aj_card_pages').val() || '[]'); } catch(e) { pages = []; }
    
    function renderPages() {
      var container = $('#aj-pages-container');
      container.empty();
      pages.forEach(function(page, idx) {
        var editorId = 'page_editor_' + idx;
        var html = '<div class="aj-page-item" data-idx="'+idx+'" style="border:1px solid #ddd;padding:15px;margin-bottom:15px;background:#fafafa;border-radius:8px;">' +
          '<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;">' +
          '<h4 style="margin:0;">Page '+(idx+2)+'</h4>' +
          '<span class="aj-page-remove" style="cursor:pointer;font-size:20px;color:#999;">×</span></div>' +
          '<div style="margin-bottom:10px;"><label style="font-weight:600;">Page Background Color</label><br>' +
          '<input type="color" class="page-bg-color" value="'+(page.bgColor||'#fffef9')+'" style="width:60px;height:35px;border:none;cursor:pointer;">' +
          '<span style="margin-left:10px;color:#666;font-size:12px;">Default: cream</span></div>' +
          '<div style="margin-bottom:10px;"><label style="font-weight:600;">Page Content</label>' +
          '<textarea id="'+editorId+'" class="page-content-editor" rows="6" style="width:100%;">'+(page.content||'')+'</textarea></div>' +
          '<div style="margin-top:10px;"><label style="font-weight:600;">Page Image (optional)</label><br>' +
          '<input type="text" class="page-image" value="'+(page.image||'')+'" style="width:70%;padding:6px;" placeholder="Image URL">' +
          '<button type="button" class="button page-upload" style="margin-left:5px;">Upload</button></div>' +
          '</div>';
        container.append(html);
        
        // Initialize TinyMCE for this page
        if (typeof tinymce !== 'undefined') {
          tinymce.init({
            selector: '#' + editorId,
            height: 200,
            menubar: false,
            plugins: 'lists link',
            toolbar: 'bold italic underline | bullist numlist | alignleft aligncenter alignright | forecolor | fontsizeselect',
            fontsize_formats: '14px 16px 18px 20px 24px 28px 32px',
            setup: function(editor) {
              editor.on('change keyup', function() {
                var idx = $(editor.getContainer()).closest('.aj-page-item').data('idx');
                pages[idx].content = editor.getContent();
                $('#aj_card_pages').val(JSON.stringify(pages));
              });
            }
          });
        }
      });
      $('#aj_card_pages').val(JSON.stringify(pages));
    }
    
    $('#aj-add-page').click(function() {
      pages.push({content:'', image:'', bgColor:'#fffef9'});
      renderPages();
    });
    
    $('#aj-pages-container').on('input', '.page-bg-color', function() {
      var idx = $(this).closest('.aj-page-item').data('idx');
      pages[idx].bgColor = $(this).val();
      $('#aj_card_pages').val(JSON.stringify(pages));
    });
    
    $('#aj-pages-container').on('input', '.page-content', function() {
      var idx = $(this).closest('.aj-page-item').data('idx');
      pages[idx].content = $(this).val();
      $('#aj_card_pages').val(JSON.stringify(pages));
    });
    
    $('#aj-pages-container').on('input', '.page-image', function() {
      var idx = $(this).closest('.aj-page-item').data('idx');
      pages[idx].image = $(this).val();
      $('#aj_card_pages').val(JSON.stringify(pages));
    });
    
    $('#aj-pages-container').on('click', '.page-upload', function() {
      var item = $(this).closest('.aj-page-item');
      var idx = item.data('idx');
      var frame = wp.media({title:'Select Image',button:{text:'Use This'},library:{type:'image'},multiple:false});
      frame.on('select', function() {
        var url = frame.state().get('selection').first().toJSON().url;
        pages[idx].image = url;
        renderPages();
      });
      frame.open();
    });
    
    $('#aj-pages-container').on('click', '.aj-page-remove', function() {
      var idx = $(this).closest('.aj-page-item').data('idx');
      pages.splice(idx, 1);
      renderPages();
    });
    
    // Photos management (with captions for scrapbook effect)
    var photos = [];
    try { 
      var parsed = JSON.parse($('#aj_card_photos').val() || '[]');
      // Convert old format (array of URLs) to new format (array of objects)
      photos = parsed.map(function(p) {
        if (typeof p === 'string') return {url: p, caption: ''};
        return p;
      });
    } catch(e) { photos = []; }
    
    function renderPhotos() {
      var grid = $('#aj-photos-grid');
      grid.empty();
      photos.forEach(function(photo, idx) {
        grid.append('<div class="aj-photo-item" data-idx="'+idx+'" style="position:relative;">' +
          '<img src="'+(photo.url || photo)+'" alt="" style="width:100px;height:100px;object-fit:cover;border-radius:8px;">' +
          '<input type="text" class="photo-caption" value="'+(photo.caption || '')+'" placeholder="Caption..." style="width:100%;margin-top:5px;font-size:11px;padding:4px;">' +
          '<span class="aj-photo-remove" style="position:absolute;top:-8px;right:-8px;background:#e74c3c;color:#fff;border-radius:50%;width:20px;height:20px;display:flex;align-items:center;justify-content:center;cursor:pointer;font-size:14px;">×</span></div>');
      });
      $('#aj_card_photos').val(JSON.stringify(photos));
    }
    
    $('#aj-add-photo').click(function() {
      var frame = wp.media({title:'Select Photos',button:{text:'Add Photos'},library:{type:'image'},multiple:true});
      frame.on('select', function() {
        frame.state().get('selection').each(function(attachment) {
          photos.push({url: attachment.toJSON().url, caption: ''});
        });
        renderPhotos();
      });
      frame.open();
    });
    
    $('#aj-photos-grid').on('input', '.photo-caption', function() {
      var idx = $(this).closest('.aj-photo-item').data('idx');
      photos[idx].caption = $(this).val();
      $('#aj_card_photos').val(JSON.stringify(photos));
    });
    
    $('#aj-photos-grid').on('click', '.aj-photo-remove', function() {
      var idx = $(this).closest('.aj-photo-item').data('idx');
      photos.splice(idx, 1);
      renderPhotos();
    });
    
    renderPages();
    renderPhotos();
  });
  </script>
  <?php
}

function aj_card_effects_callback($post) {
  $music_url = get_post_meta($post->ID, '_aj_card_music', true) ?: '';
  $music_autoplay = get_post_meta($post->ID, '_aj_card_music_autoplay', true) ?: '0';
  $open_effect = get_post_meta($post->ID, '_aj_card_open_effect', true) ?: 'confetti';
  $animation_style = get_post_meta($post->ID, '_aj_card_animation', true) ?: 'bounce';
  $particle_color = get_post_meta($post->ID, '_aj_card_particle_color', true) ?: '#ffd700';
  $envelope = get_post_meta($post->ID, '_aj_card_envelope', true) ?: '0';
  $envelope_text = get_post_meta($post->ID, '_aj_card_envelope_text', true) ?: 'To: You ❤️';
  $envelope_color = get_post_meta($post->ID, '_aj_card_envelope_color', true) ?: '#f5e6d3';
  $envelope_seal_emoji = get_post_meta($post->ID, '_aj_card_envelope_seal_emoji', true) ?: '❤️';
  $envelope_seal_color = get_post_meta($post->ID, '_aj_card_envelope_seal_color', true) ?: '';
  ?>
  <div class="aj-card-row">
    <div class="aj-card-field">
      <label>Card Opening Animation</label>
      <select name="aj_card_animation">
        <option value="bounce" <?php selected($animation_style, 'bounce'); ?>>🎯 Classic Bounce</option>
        <option value="flip" <?php selected($animation_style, 'flip'); ?>>🔄 3D Flip (Dramatic)</option>
        <option value="swing" <?php selected($animation_style, 'swing'); ?>>🚪 Swing Open</option>
        <option value="unfold" <?php selected($animation_style, 'unfold'); ?>>📜 Unfold (Slow & Elegant)</option>
        <option value="zoom" <?php selected($animation_style, 'zoom'); ?>>🔍 Zoom Burst</option>
        <option value="spiral" <?php selected($animation_style, 'spiral'); ?>>🌀 Spiral Reveal</option>
        <option value="shatter" <?php selected($animation_style, 'shatter'); ?>>💥 Shatter & Reform</option>
        <option value="glitch" <?php selected($animation_style, 'glitch'); ?>>⚡ Glitch Effect</option>
        <option value="magic" <?php selected($animation_style, 'magic'); ?>>✨ Magic Portal</option>
      </select>
    </div>
    <div class="aj-card-field">
      <label>Opening Particle Effect</label>
      <select name="aj_card_open_effect">
        <option value="none" <?php selected($open_effect, 'none'); ?>>None</option>
        <option value="confetti" <?php selected($open_effect, 'confetti'); ?>>🎊 Confetti Explosion</option>
        <option value="fireworks" <?php selected($open_effect, 'fireworks'); ?>>🎆 Epic Fireworks</option>
        <option value="hearts" <?php selected($open_effect, 'hearts'); ?>>💕 Floating Hearts</option>
        <option value="stars" <?php selected($open_effect, 'stars'); ?>>⭐ Shooting Stars</option>
        <option value="balloons" <?php selected($open_effect, 'balloons'); ?>>🎈 Rising Balloons</option>
        <option value="sparkle" <?php selected($open_effect, 'sparkle'); ?>>✨ Sparkle Burst</option>
        <option value="rainbow" <?php selected($open_effect, 'rainbow'); ?>>🌈 Rainbow Wave</option>
        <option value="petals" <?php selected($open_effect, 'petals'); ?>>🌸 Cherry Blossoms</option>
        <option value="butterflies" <?php selected($open_effect, 'butterflies'); ?>>🦋 Butterflies</option>
        <option value="cosmic" <?php selected($open_effect, 'cosmic'); ?>>🌌 Cosmic Explosion</option>
        <option value="fire" <?php selected($open_effect, 'fire'); ?>>🔥 Fire & Embers</option>
        <option value="snow" <?php selected($open_effect, 'snow'); ?>>❄️ Magical Snow</option>
      </select>
    </div>
    <div class="aj-card-field">
      <label>Effect Color</label>
      <input type="color" name="aj_card_particle_color" value="<?php echo esc_attr($particle_color); ?>">
    </div>
  </div>
  <div class="aj-card-row">
    <div class="aj-card-field" style="flex:2;">
      <label>Background Music (MP3 URL)</label>
      <input type="text" name="aj_card_music" id="aj_card_music" value="<?php echo esc_attr($music_url); ?>" placeholder="Upload or paste MP3 URL">
      <button type="button" class="button aj-upload-btn" data-target="aj_card_music" data-type="audio" style="margin-top:5px;">Upload Music</button>
    </div>
    <div class="aj-card-field">
      <label>Music Options</label>
      <label style="font-weight:normal;display:flex;align-items:center;gap:8px;margin-top:10px;">
        <input type="checkbox" name="aj_card_music_autoplay" value="1" <?php checked($music_autoplay, '1'); ?>>
        Autoplay when card opens
      </label>
    </div>
  </div>
  <div class="aj-card-row">
    <div class="aj-card-field">
      <label style="font-weight:normal;display:flex;align-items:center;gap:8px;">
        <input type="checkbox" name="aj_card_envelope" value="1" <?php checked($envelope, '1'); ?> id="aj_envelope_toggle">
        Start in envelope (extra animation)
      </label>
    </div>
  </div>
  <div class="aj-card-row aj-envelope-options" style="<?php echo $envelope !== '1' ? 'display:none;' : ''; ?>">
    <div class="aj-card-field">
      <label>Envelope Front Text</label>
      <input type="text" name="aj_card_envelope_text" value="<?php echo esc_attr($envelope_text); ?>" placeholder="To: You ❤️">
    </div>
    <div class="aj-card-field">
      <label>Envelope Color</label>
      <input type="color" name="aj_card_envelope_color" value="<?php echo esc_attr($envelope_color); ?>">
    </div>
    <div class="aj-card-field">
      <label>Seal Emoji</label>
      <input type="text" name="aj_card_envelope_seal_emoji" value="<?php echo esc_attr($envelope_seal_emoji); ?>" style="width:60px;text-align:center;font-size:20px;" maxlength="2">
    </div>
    <div class="aj-card-field">
      <label>Seal Color (optional)</label>
      <input type="color" name="aj_card_envelope_seal_color" value="<?php echo esc_attr($envelope_seal_color ?: $card_color); ?>">
    </div>
  </div>
  <script>
  jQuery('#aj_envelope_toggle').change(function() {
    jQuery('.aj-envelope-options').toggle(this.checked);
  });
  </script>
  <script>
  jQuery(function($) {
    if (typeof wp !== 'undefined' && wp.media) {
      $('.aj-upload-btn').click(function(e) {
        e.preventDefault();
        var targetId = $(this).data('target');
        var mediaType = $(this).data('type') || 'image';
        var frame = wp.media({
          title: 'Select or Upload',
          button: { text: 'Use This' },
          library: { type: mediaType },
          multiple: false
        });
        frame.on('select', function() {
          var attachment = frame.state().get('selection').first().toJSON();
          $('#' + targetId).val(attachment.url);
        });
        frame.open();
      });
    }
  });
  </script>
  <?php
}

function aj_card_save($post_id) {
  if (!isset($_POST['aj_card_nonce']) || !wp_verify_nonce($_POST['aj_card_nonce'], 'aj_card_save')) return;
  if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
  if (!current_user_can('edit_post', $post_id)) return;
  
  $fields = array(
    'aj_card_type', 'aj_card_color', 'aj_card_pattern', 'aj_card_bg_color', 'aj_card_bg_pattern',
    'aj_card_front_title', 'aj_card_front_subtitle', 'aj_card_front_image', 'aj_card_front_emoji',
    'aj_card_inside_from', 'aj_card_inside_image', 'aj_card_inside_extra', 'aj_card_inside_bg_color',
    'aj_card_music', 'aj_card_open_effect', 'aj_card_animation', 'aj_card_particle_color'
  );
  
  foreach ($fields as $field) {
    if (isset($_POST[$field])) {
      update_post_meta($post_id, '_' . $field, sanitize_text_field($_POST[$field]));
    }
  }
  
  if (isset($_POST['aj_card_inside_message'])) {
    update_post_meta($post_id, '_aj_card_inside_message', wp_kses_post($_POST['aj_card_inside_message']));
  }
  if (isset($_POST['aj_card_pages'])) {
    update_post_meta($post_id, '_aj_card_pages', wp_unslash($_POST['aj_card_pages']));
  }
  if (isset($_POST['aj_card_photos'])) {
    update_post_meta($post_id, '_aj_card_photos', wp_unslash($_POST['aj_card_photos']));
  }
  
  update_post_meta($post_id, '_aj_card_music_autoplay', isset($_POST['aj_card_music_autoplay']) ? '1' : '0');
  update_post_meta($post_id, '_aj_card_envelope', isset($_POST['aj_card_envelope']) ? '1' : '0');
  if (isset($_POST['aj_card_envelope_text'])) {
    update_post_meta($post_id, '_aj_card_envelope_text', sanitize_text_field($_POST['aj_card_envelope_text']));
  }
  if (isset($_POST['aj_card_envelope_color'])) {
    update_post_meta($post_id, '_aj_card_envelope_color', sanitize_hex_color($_POST['aj_card_envelope_color']));
  }
  if (isset($_POST['aj_card_envelope_seal_emoji'])) {
    update_post_meta($post_id, '_aj_card_envelope_seal_emoji', sanitize_text_field($_POST['aj_card_envelope_seal_emoji']));
  }
  if (isset($_POST['aj_card_envelope_seal_color'])) {
    update_post_meta($post_id, '_aj_card_envelope_seal_color', sanitize_hex_color($_POST['aj_card_envelope_seal_color']));
  }
  
  // Update URL prefix if provided
  if (isset($_POST['aj_card_url_prefix'])) {
    $new_prefix = sanitize_title($_POST['aj_card_url_prefix']) ?: 'card';
    $old_prefix = get_post_meta($post_id, '_aj_card_url_prefix', true);
    update_post_meta($post_id, '_aj_card_url_prefix', $new_prefix);
    if ($new_prefix !== $old_prefix) {
      flush_rewrite_rules();
    }
  }
  
  // Update slug if provided
  if (isset($_POST['aj_card_slug']) && !empty($_POST['aj_card_slug'])) {
    $new_slug = sanitize_title($_POST['aj_card_slug']);
    if ($new_slug !== get_post_field('post_name', $post_id)) {
      remove_action('save_post_aj_card', 'aj_card_save');
      wp_update_post(array('ID' => $post_id, 'post_name' => $new_slug));
      add_action('save_post_aj_card', 'aj_card_save');
    }
  }
}
add_action('save_post_aj_card', 'aj_card_save');

function aj_card_admin_scripts($hook) {
  global $post;
  if ($hook !== 'post.php' && $hook !== 'post-new.php') return;
  if (!$post || $post->post_type !== 'aj_card') return;
  wp_enqueue_media();
}
add_action('admin_enqueue_scripts', 'aj_card_admin_scripts');

function aj_card_template($template) {
  if (is_singular('aj_card')) {
    $custom = get_stylesheet_directory() . '/single-aj_card.php';
    if (file_exists($custom)) return $custom;
  }
  return $template;
}
add_filter('template_include', 'aj_card_template');
