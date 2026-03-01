<!doctype html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo('charset'); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
  <?php wp_body_open(); ?>

  <?php
    $page_count = (int) wp_count_posts('page')->publish;
    $show_nav = $page_count > 1;
  ?>

  <header class="site-header">
    <div class="container header-inner<?php echo $show_nav ? '' : ' header-inner-no-nav'; ?>">
      <h1 class="site-title">
        <a href="<?php echo esc_url(home_url('/')); ?>">
          <span class="brand"><?php echo esc_html(get_theme_mod('aleejaved_portfolio_header_name', 'Alee Javed')); ?></span>
          <span class="dot">●</span>
          <span class="page-label">
            <?php
              if (is_front_page()) {
                echo 'Portfolio';
              } elseif (is_page()) {
                the_title();
              } else {
                if (is_singular()) {
                  single_post_title();
                } else {
                  wp_title('');
                }
              }
            ?>
          </span>
        </a>
      </h1>

      <?php if ($show_nav) { ?>
        <button class="nav-toggle" type="button" aria-controls="primary-nav" aria-expanded="false">
          <span class="nav-toggle-icon" aria-hidden="true"></span>
          <span class="sr-only">Menu</span>
        </button>

        <nav class="main-nav" id="primary-nav" aria-label="Primary">
          <?php
            wp_page_menu(
              array(
                'menu_class' => 'menu',
                'show_home' => false,
                'depth' => 1,
                'sort_column' => 'menu_order,post_title',
              )
            );
          ?>
        </nav>
      <?php } ?>
    </div>

    <div class="header-wave" aria-hidden="true">
      <svg viewBox="0 0 1440 40" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M0,20 C20,33 40,7 60,20 C80,33 100,7 120,20 C140,33 160,7 180,20 C200,33 220,7 240,20 C260,33 280,7 300,20 C320,33 340,7 360,20 C380,33 400,7 420,20 C440,33 460,7 480,20 C500,33 520,7 540,20 C560,33 580,7 600,20 C620,33 640,7 660,20 C680,33 700,7 720,20 C740,33 760,7 780,20 C800,33 820,7 840,20 C860,33 880,7 900,20 C920,33 940,7 960,20 C980,33 1000,7 1020,20 C1040,33 1060,7 1080,20 C1100,33 1120,7 1140,20 C1160,33 1180,7 1200,20 C1220,33 1240,7 1260,20 C1280,33 1300,7 1320,20 C1340,33 1360,7 1380,20 C1400,33 1420,7 1440,20" fill="none" stroke="currentColor" stroke-width="2" />
      </svg>
    </div>
  </header>

  <div class="site-content">
