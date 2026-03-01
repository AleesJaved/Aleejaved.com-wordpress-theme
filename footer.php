  </div>

  <footer class="site-footer">
    <div class="footer-wave" aria-hidden="true">
      <svg viewBox="0 0 1440 40" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M0,20 C20,33 40,7 60,20 C80,33 100,7 120,20 C140,33 160,7 180,20 C200,33 220,7 240,20 C260,33 280,7 300,20 C320,33 340,7 360,20 C380,33 400,7 420,20 C440,33 460,7 480,20 C500,33 520,7 540,20 C560,33 580,7 600,20 C620,33 640,7 660,20 C680,33 700,7 720,20 C740,33 760,7 780,20 C800,33 820,7 840,20 C860,33 880,7 900,20 C920,33 940,7 960,20 C980,33 1000,7 1020,20 C1040,33 1060,7 1080,20 C1100,33 1120,7 1140,20 C1160,33 1180,7 1200,20 C1220,33 1240,7 1260,20 C1280,33 1300,7 1320,20 C1340,33 1360,7 1380,20 C1400,33 1420,7 1440,20" fill="none" stroke="currentColor" stroke-width="2" />
      </svg>
    </div>

    <div class="container">
      <div class="footer-inner">
        <div class="footer-text">
          <?php
            $year = date('Y');
            $footer_text = (string) get_theme_mod('aleejaved_portfolio_footer_text', 'Copyright © %year% Alee Javed. All rights reserved.');
            if (!$footer_text) {
              $footer_text = 'Copyright © %year% Alee Javed. All rights reserved.';
            }
            echo esc_html(str_replace('%year%', $year, $footer_text));
          ?>
        </div>
      </div>
    </div>
  </footer>

  <?php wp_footer(); ?>
</body>
</html>
