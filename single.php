<?php
get_header();
?>

<main class="content">
  <div class="container">
    <?php
      if (have_posts()) {
        while (have_posts()) {
          the_post();

          $timeline_date = (string) get_post_meta(get_the_ID(), '_aleejaved_timeline_date', true);
          $timeline_date_label = '';
          if ($timeline_date) {
            $ts = strtotime($timeline_date);
            if ($ts) {
              $timeline_date_label = date_i18n('j F Y', $ts);
            }
          }

          $tags = get_the_terms(get_the_ID(), 'post_tag');

          echo '<button type="button" class="post-back" data-action="post-back">← Back</button>';
          echo '<h1>' . esc_html(get_the_title()) . '</h1>';
          echo '<div class="post-meta">';
          if ($timeline_date_label) {
            echo '<span class="post-date">' . esc_html($timeline_date_label) . '</span>';
          }
          if (is_array($tags) && !empty($tags)) {
            echo '<span class="post-tags">';
            foreach ($tags as $t) {
              echo '<span class="post-tag">' . esc_html($t->name) . '</span>';
            }
            echo '</span>';
          }
          echo '</div>';

          echo '<div class="entry-content">';
          the_content();
          echo '</div>';
        }
      }
    ?>
  </div>
</main>

<?php
get_footer();
