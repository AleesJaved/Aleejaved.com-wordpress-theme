<?php
get_header();

if (have_posts()) {
  while (have_posts()) {
    the_post();
    ?>

    <main class="content">
      <div class="container">
        <div class="entry-content">
          <?php the_content(); ?>
        </div>
      </div>
    </main>

    <?php
  }
}

get_footer();
