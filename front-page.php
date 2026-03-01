<?php
get_header();

if (have_posts()) {
  while (have_posts()) {
    the_post();
    $avatar_url = get_the_post_thumbnail_url(get_the_ID(), 'large');
    $subtitle = get_theme_mod('aleejaved_portfolio_hero_subtitle', 'and I am a student and tech enthusiast');

    $greeting = get_theme_mod('aleejaved_portfolio_hero_greeting', 'Hello!');
    $name = get_theme_mod('aleejaved_portfolio_hero_name', 'Alee Javed');
    
    // Split name intelligently for better display
    $name_parts = explode(' ', $name);
    if (count($name_parts) > 2) {
      // For names with 3+ parts, put first name on line 1, rest on line 2
      $first_name = $name_parts[0];
      $last_name = implode(' ', array_slice($name_parts, 1));
    } elseif (count($name_parts) === 2) {
      // For 2-part names, split evenly
      $first_name = $name_parts[0];
      $last_name = $name_parts[1];
    } else {
      // For single names, put on first line
      $first_name = $name;
      $last_name = '';
    }
    $buttons = json_decode(get_theme_mod('aleejaved_portfolio_buttons', json_encode(array(
      array('name' => 'LinkedIn', 'link' => ''),
      array('name' => 'GitHub', 'link' => ''),
    ))), true) ?: array();
    $email = get_theme_mod('aleejaved_portfolio_email', '');
    $location = get_theme_mod('aleejaved_portfolio_location', '📍 Nottingham, UK');
    ?>

    <section class="hero" id="me">
      <div class="container hero-inner">
        <div class="hero-left">
          <p class="hero-kicker"><?php echo esc_html($greeting); ?></p>
          <h1 class="hero-title">
            <span class="hero-line"><span class="hero-im">I'm</span><span class="hero-brand"><?php echo esc_html($first_name); ?></span></span>
            <?php if ($last_name): ?>
            <span class="hero-brand"><?php echo esc_html($last_name); ?></span>
            <?php endif; ?>
          </h1>
          <p class="hero-subtitle"><?php echo esc_html($subtitle); ?></p>
        </div>

        <div class="hero-right">
          <div class="hero-avatar" role="img" aria-label="Portrait"<?php if ($avatar_url) { echo ' style="background-image:url(' . esc_url($avatar_url) . ')"'; } ?>></div>

          <div class="hero-links" aria-label="Links">
            <?php foreach ($buttons as $button): ?>
              <?php if (!empty($button['name']) && !empty($button['link'])): ?>
                <a class="hero-btn" href="<?php echo esc_url($button['link']); ?>" target="_blank" rel="noopener noreferrer">
                  <?php echo esc_html($button['name']); ?>
                </a>
              <?php endif; ?>
            <?php endforeach; ?>

            <?php if ($email) { ?>
              <a class="hero-email" href="mailto:<?php echo antispambot(sanitize_email($email)); ?>"><?php echo esc_html(antispambot(sanitize_email($email))); ?></a>
            <?php } ?>

            <?php if ($location) { ?>
              <span class="hero-location"><?php echo esc_html($location); ?></span>
            <?php } ?>
          </div>
        </div>
      </div>
    </section>

    <div class="section-wave" aria-hidden="true">
      <svg viewBox="0 0 1440 40" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M0,20 C20,33 40,7 60,20 C80,33 100,7 120,20 C140,33 160,7 180,20 C200,33 220,7 240,20 C260,33 280,7 300,20 C320,33 340,7 360,20 C380,33 400,7 420,20 C440,33 460,7 480,20 C500,33 520,7 540,20 C560,33 580,7 600,20 C620,33 640,7 660,20 C680,33 700,7 720,20 C740,33 760,7 780,20 C800,33 820,7 840,20 C860,33 880,7 900,20 C920,33 940,7 960,20 C980,33 1000,7 1020,20 C1040,33 1060,7 1080,20 C1100,33 1120,7 1140,20 C1160,33 1180,7 1200,20 C1220,33 1240,7 1260,20 C1280,33 1300,7 1320,20 C1340,33 1360,7 1380,20 C1400,33 1420,7 1440,20" fill="none" stroke="currentColor" stroke-width="2" />
      </svg>
    </div>

    <section id="about" class="content">
      <div class="container">
        <?php the_content(); ?>
      </div>
    </section>

    <div class="section-wave" aria-hidden="true">
      <svg viewBox="0 0 1440 40" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M0,20 C20,33 40,7 60,20 C80,33 100,7 120,20 C140,33 160,7 180,20 C200,33 220,7 240,20 C260,33 280,7 300,20 C320,33 340,7 360,20 C380,33 400,7 420,20 C440,33 460,7 480,20 C500,33 520,7 540,20 C560,33 580,7 600,20 C620,33 640,7 660,20 C680,33 700,7 720,20 C740,33 760,7 780,20 C800,33 820,7 840,20 C860,33 880,7 900,20 C920,33 940,7 960,20 C980,33 1000,7 1020,20 C1040,33 1060,7 1080,20 C1100,33 1120,7 1140,20 C1160,33 1180,7 1200,20 C1220,33 1240,7 1260,20 C1280,33 1300,7 1320,20 C1340,33 1360,7 1380,20 C1400,33 1420,7 1440,20" fill="none" stroke="currentColor" stroke-width="2" />
      </svg>
    </div>

    <section id="timeline" class="timeline-section">
      <div class="container">
        <div class="timeline">
          <?php
            $timeline_query = new WP_Query(
              array(
                'post_type' => 'post',
                'posts_per_page' => 20,
                'ignore_sticky_posts' => true,
                'category_name' => 'timeline',
                'meta_query' => array(
                  'relation' => 'OR',
                  array(
                    'key' => '_aleejaved_timeline_date',
                    'compare' => 'EXISTS',
                  ),
                  array(
                    'key' => '_aleejaved_timeline_date',
                    'compare' => 'NOT EXISTS',
                  ),
                ),
                'orderby' => array(
                  'meta_value' => 'DESC',
                  'date' => 'DESC',
                ),
                'meta_key' => '_aleejaved_timeline_date',
                'meta_type' => 'DATE',
              )
            );

            if ($timeline_query->have_posts()) {
              $timeline_post_ids = wp_list_pluck($timeline_query->posts, 'ID');
              $timeline_tags = array();
              $default_sort = get_theme_mod('aleejaved_portfolio_timeline_default_sort', 'date');
              if ($default_sort !== 'impressiveness') {
                $default_sort = 'date';
              }
              if (!empty($timeline_post_ids)) {
                $timeline_tags = get_terms(
                  array(
                    'taxonomy' => 'post_tag',
                    'hide_empty' => true,
                    'object_ids' => $timeline_post_ids,
                    'orderby' => 'name',
                    'order' => 'ASC',
                  )
                );
              }

              echo '<div class="timeline-sort" role="group" aria-label="Sort timeline" data-default-sort="' . esc_attr($default_sort) . '">';
              echo '<span class="timeline-sort-label">Sort by:</span>';
              echo '<div class="timeline-sort-group">';
              $sort_options = array(
                'date' => 'Chronological',
                'impressiveness' => 'Impressiveness',
              );
              $sort_order = $default_sort === 'impressiveness' ? array('impressiveness', 'date') : array('date', 'impressiveness');
              foreach ($sort_order as $mode) {
                $pressed = $mode === $default_sort ? 'true' : 'false';
                echo '<button type="button" class="timeline-sort-btn" data-sort="' . esc_attr($mode) . '" aria-pressed="' . esc_attr($pressed) . '">' . esc_html($sort_options[$mode]) . '</button>';
              }
              echo '</div>';
              echo '</div>';

              if (!empty($timeline_tags) && !is_wp_error($timeline_tags)) {
                echo '<div class="timeline-filters" aria-label="Timeline tag filters">';
                echo '<div class="timeline-filters-inner">';
                foreach ($timeline_tags as $term) {
                  echo '<label class="timeline-filter">';
                  echo '<input type="checkbox" checked data-tag="' . esc_attr($term->slug) . '" />';
                  echo '<span>' . esc_html($term->name) . '</span>';
                  echo '</label>';
                }
                echo '</div>';
                echo '</div>';
              }

              echo '<div class="timeline-list">';

              while ($timeline_query->have_posts()) {
                $timeline_query->the_post();

                $image_side = get_post_meta(get_the_ID(), '_aleejaved_timeline_image_side', true);
                if ($image_side !== 'left') {
                  $image_side = 'right';
                }

                $gallery_ids_raw = (string) get_post_meta(get_the_ID(), '_aleejaved_timeline_gallery', true);
                $gallery_ids = array_filter(array_map('absint', array_map('trim', explode(',', $gallery_ids_raw))));

                $timeline_date = (string) get_post_meta(get_the_ID(), '_aleejaved_timeline_date', true);
                $timeline_date_label = '';
                $timeline_sort_ts = 0;
                if ($timeline_date) {
                  $ts = strtotime($timeline_date);
                  if ($ts) {
                    $timeline_date_label = date_i18n('M Y', $ts);
                    $timeline_sort_ts = (int) $ts;
                  }
                }
                if (!$timeline_sort_ts) {
                  $timeline_sort_ts = (int) get_post_time('U', true, get_the_ID());
                }

                $impressiveness = (int) get_post_meta(get_the_ID(), '_aleejaved_timeline_impressiveness', true);
                if ($impressiveness < 0) {
                  $impressiveness = 0;
                }
                if ($impressiveness > 100) {
                  $impressiveness = 100;
                }

                $impressiveness_order = (int) get_post_meta(get_the_ID(), '_aleejaved_timeline_impressiveness_order', true);
                if ($impressiveness_order < 0) {
                  $impressiveness_order = -1;
                }

                $desc_raw = (string) get_post_field('post_excerpt', get_the_ID());
                if (!$desc_raw) {
                  $desc_raw = wp_trim_words(wp_strip_all_tags((string) get_post_field('post_content', get_the_ID())), 24);
                }
                $desc_html = $desc_raw ? wpautop(esc_html($desc_raw)) : '';

                $has_blog_post = trim((string) get_post_field('post_content', get_the_ID())) !== '';

                $tags = get_the_terms(get_the_ID(), 'post_tag');
                $tag_slugs = array();
                $tag_names = array();
                if (is_array($tags)) {
                  foreach ($tags as $t) {
                    $tag_slugs[] = $t->slug;
                    $tag_names[] = $t->name;
                  }
                }
                $tag_data = implode(' ', array_map('sanitize_title', $tag_slugs));
                ?>

                <article class="timeline-item timeline-image-<?php echo esc_attr($image_side); ?><?php echo empty($gallery_ids) ? ' timeline-no-media' : ''; ?>" data-tags="<?php echo esc_attr($tag_data); ?>" data-date="<?php echo esc_attr((string) $timeline_sort_ts); ?>" data-impressiveness="<?php echo esc_attr((string) $impressiveness); ?>" data-order="<?php echo esc_attr((string) $impressiveness_order); ?>">
                  <?php if ($timeline_date_label) { ?>
                    <div class="timeline-date"><?php echo esc_html($timeline_date_label); ?></div>
                  <?php } ?>
                  <div class="timeline-dot" aria-hidden="true"></div>

                  <div class="timeline-card">
                    <?php if (!empty($gallery_ids)) { ?>
                      <div class="timeline-media" data-index="0">
                        <div class="timeline-media-inner">
                          <?php
                            $i = 0;
                            foreach ($gallery_ids as $attachment_id) {
                              $mime = (string) get_post_mime_type($attachment_id);
                              $is_video = strpos($mime, 'video/') === 0;

                              if ($is_video) {
                                $video_url = wp_get_attachment_url($attachment_id);
                                if (!$video_url) {
                                  continue;
                                }
                                echo '<video class="timeline-media-item' . ($i === 0 ? ' is-active' : '') . '" src="' . esc_url($video_url) . '" controls preload="metadata"></video>';
                                $i++;
                                continue;
                              }

                              $img_url = wp_get_attachment_image_url($attachment_id, 'large');
                              if (!$img_url) {
                                continue;
                              }
                              echo '<img class="timeline-media-item' . ($i === 0 ? ' is-active' : '') . '" src="' . esc_url($img_url) . '" alt="" loading="lazy" />';
                              $i++;
                            }
                          ?>
                        </div>

                        <?php if ($i > 1) { ?>
                          <button class="timeline-nav timeline-prev" type="button" data-action="prev" aria-label="Previous image">‹</button>
                          <button class="timeline-nav timeline-next" type="button" data-action="next" aria-label="Next image">›</button>
                        <?php } ?>
                      </div>
                    <?php } ?>

                    <div class="timeline-body">
                      <div class="timeline-title-row">
                        <h3 class="timeline-title"><?php the_title(); ?></h3>

                        <?php if (!empty($tag_names)) { ?>
                          <div class="timeline-tags" aria-label="Tags">
                            <?php foreach ($tag_names as $name) { ?>
                              <span class="timeline-tag"><?php echo esc_html($name); ?></span>
                            <?php } ?>
                          </div>
                        <?php } ?>
                      </div>
                      <?php if ($desc_html) { ?>
                        <div class="timeline-desc"><?php echo wp_kses_post($desc_html); ?></div>
                      <?php } ?>

                      <?php if ($has_blog_post) { ?>
                        <a class="timeline-blog" href="<?php the_permalink(); ?>">Blog post <span aria-hidden="true">→</span></a>
                      <?php } ?>
                    </div>
                  </div>
                </article>

                <?php
              }

              echo '</div>';
              wp_reset_postdata();
            }
          ?>
        </div>
      </div>
    </section>

    <?php
  }
}

get_footer();
