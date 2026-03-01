<?php
if (!have_posts()) {
  wp_redirect(home_url());
  exit;
}
the_post();

$card_type = get_post_meta(get_the_ID(), '_aj_card_type', true) ?: 'classic';
$card_color = get_post_meta(get_the_ID(), '_aj_card_color', true) ?: '#ff6b6b';
$card_pattern = get_post_meta(get_the_ID(), '_aj_card_pattern', true) ?: 'none';
$bg_color = get_post_meta(get_the_ID(), '_aj_card_bg_color', true) ?: '#1a1a2e';
$bg_pattern = get_post_meta(get_the_ID(), '_aj_card_bg_pattern', true) ?: 'none';

$front_title = get_post_meta(get_the_ID(), '_aj_card_front_title', true) ?: 'Happy Birthday!';
$front_subtitle = get_post_meta(get_the_ID(), '_aj_card_front_subtitle', true) ?: 'Click to open';
$front_image = get_post_meta(get_the_ID(), '_aj_card_front_image', true) ?: '';
$front_emoji = get_post_meta(get_the_ID(), '_aj_card_front_emoji', true) ?: '🎂';

$inside_message = get_post_meta(get_the_ID(), '_aj_card_inside_message', true) ?: '';
$inside_from = get_post_meta(get_the_ID(), '_aj_card_inside_from', true) ?: '';
$inside_image = get_post_meta(get_the_ID(), '_aj_card_inside_image', true) ?: '';
$inside_extra = get_post_meta(get_the_ID(), '_aj_card_inside_extra', true) ?: '';
$inside_bg_color = get_post_meta(get_the_ID(), '_aj_card_inside_bg_color', true) ?: '#fffef9';

$music_url = get_post_meta(get_the_ID(), '_aj_card_music', true) ?: '';
$music_autoplay = get_post_meta(get_the_ID(), '_aj_card_music_autoplay', true) === '1';
$open_effect = get_post_meta(get_the_ID(), '_aj_card_open_effect', true) ?: 'confetti';
$animation_style = get_post_meta(get_the_ID(), '_aj_card_animation', true) ?: 'bounce';
$particle_color = get_post_meta(get_the_ID(), '_aj_card_particle_color', true) ?: '#ffd700';
$envelope = get_post_meta(get_the_ID(), '_aj_card_envelope', true) === '1';
$envelope_text = get_post_meta(get_the_ID(), '_aj_card_envelope_text', true) ?: 'To: You ❤️';
$envelope_color = get_post_meta(get_the_ID(), '_aj_card_envelope_color', true) ?: '#f5e6d3';
$envelope_seal_emoji = get_post_meta(get_the_ID(), '_aj_card_envelope_seal_emoji', true) ?: '❤️';
$envelope_seal_color = get_post_meta(get_the_ID(), '_aj_card_envelope_seal_color', true) ?: $card_color;
$pages_json = get_post_meta(get_the_ID(), '_aj_card_pages', true) ?: '[]';
$photos_json = get_post_meta(get_the_ID(), '_aj_card_photos', true) ?: '[]';
$pages = json_decode($pages_json, true) ?: [];
$photos = json_decode($photos_json, true) ?: [];
$total_pages = 1 + count($pages);

$card_color_rgb = sscanf($card_color, "#%02x%02x%02x");
$card_color_light = sprintf("rgba(%d,%d,%d,0.15)", $card_color_rgb[0], $card_color_rgb[1], $card_color_rgb[2]);
$card_color_dark = sprintf("rgba(%d,%d,%d,0.85)", $card_color_rgb[0], $card_color_rgb[1], $card_color_rgb[2]);
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo('charset'); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="robots" content="noindex, nofollow">
<title><?php echo esc_html($front_title); ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Pacifico&family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
html,body{height:100%;overflow:hidden}
body{
  font-family:'Poppins',sans-serif;
  background:<?php echo esc_attr($bg_color); ?>;
  display:flex;
  align-items:center;
  justify-content:center;
  perspective:1500px;
  overflow:hidden;
}
#bg-effects{position:fixed;inset:0;pointer-events:none;z-index:0}
#card-container{position:relative;z-index:10}
.envelope{
  width:380px;
  height:280px;
  position:relative;
  cursor:pointer;
  transform-style:preserve-3d;
  transition:transform 1s cubic-bezier(0.4,0,0.2,1);
}
.envelope.flipped{transform:rotateY(180deg)}
.envelope.opened{animation:envelopeOpen 1.5s ease-out forwards}
@keyframes envelopeOpen{0%{transform:rotateY(180deg) scale(1)}30%{transform:rotateY(180deg) scale(1.1) translateY(-20px)}60%{transform:rotateY(180deg) scale(0.8) translateY(-80px)}100%{transform:rotateY(180deg) scale(0.6) translateY(-150px);opacity:0;pointer-events:none}}
.envelope-front,.envelope-back{
  position:absolute;
  width:100%;
  height:100%;
  backface-visibility:hidden;
  border-radius:12px;
  box-shadow:0 20px 60px rgba(0,0,0,0.3);
}
.envelope-front{
  background:linear-gradient(135deg,<?php echo esc_attr($envelope_color); ?> 0%,<?php echo esc_attr($envelope_color); ?>dd 100%);
  display:flex;
  flex-direction:column;
  align-items:center;
  justify-content:center;
  padding:30px;
}
.envelope-front-text{
  font-family:'Pacifico',cursive;
  font-size:28px;
  color:#8b7355;
  text-align:center;
  text-shadow:1px 1px 2px rgba(0,0,0,0.1);
}
.envelope-front-hint{
  margin-top:20px;
  font-size:14px;
  color:rgba(139,115,85,0.6);
}
.envelope-back{
  background:linear-gradient(135deg,<?php echo esc_attr($envelope_color); ?> 0%,<?php echo esc_attr($envelope_color); ?>dd 100%);
  transform:rotateY(180deg);
  position:relative;
  overflow:hidden;
}
.envelope-flap{
  position:absolute;
  top:0;left:0;right:0;
  height:140px;
  background:linear-gradient(135deg,<?php echo esc_attr($envelope_color); ?>cc 0%,<?php echo esc_attr($envelope_color); ?>99 100%);
  clip-path:polygon(0 0,50% 100%,100% 0);
  z-index:2;
}
.envelope-seal{
  position:absolute;
  top:90px;left:50%;
  transform:translateX(-50%);
  width:60px;height:60px;
  background:<?php echo esc_attr($envelope_seal_color); ?>;
  border-radius:50%;
  display:flex;align-items:center;justify-content:center;
  font-size:24px;
  box-shadow:0 4px 15px rgba(0,0,0,0.2);
  z-index:3;
}
.envelope-back-hint{
  position:absolute;
  bottom:30px;
  left:50%;
  transform:translateX(-50%);
  font-size:14px;
  color:rgba(139,115,85,0.6);
}
.card{
  width:500px;
  min-height:650px;
  position:relative;
  transform-style:preserve-3d;
  transition:transform 1s cubic-bezier(0.4,0,0.2,1);
  cursor:pointer;
}
.card.with-envelope{
  position:absolute;
  top:50%;left:50%;
  transform:translate(-50%,-50%) scale(0.85);
  opacity:0;
  pointer-events:none;
  transition:all 0.8s cubic-bezier(0.4,0,0.2,1) 0.3s;
}
.card.with-envelope.revealed{
  opacity:1;
  transform:translate(-50%,-50%) scale(1);
  pointer-events:auto;
}
.card.opened{transform:rotateY(-180deg)}
.card.closing{animation:cardClose 1.2s ease-out forwards !important;transform-origin:center center}
.card.opened.anim-flip{animation:cardFlip 1.5s ease-out forwards}
.card.opened.anim-swing{animation:cardSwing 1.8s ease-out forwards}
.card.opened.anim-unfold{animation:cardUnfold 2s ease-out forwards}
.card.opened.anim-zoom{animation:cardZoom 1.5s ease-out forwards}
.card.opened.anim-spiral{animation:cardSpiral 2s ease-out forwards}
.card.opened.anim-shatter{animation:cardShatter 1.5s ease-out forwards}
.card.opened.anim-glitch{animation:cardGlitch 1.2s ease-out forwards}
.card.opened.anim-magic{animation:cardMagic 2s ease-out forwards}
@keyframes cardBounce{0%{transform:rotateY(0)}50%{transform:rotateY(-200deg)}75%{transform:rotateY(-170deg)}100%{transform:rotateY(-180deg)}}
@keyframes cardFlip{0%{transform:rotateY(0) rotateX(0) scale(1)}40%{transform:rotateY(-90deg) rotateX(10deg) scale(1.15)}100%{transform:rotateY(-180deg) rotateX(0) scale(1)}}
@keyframes cardSwing{0%{transform:rotateY(0) rotate(0)}25%{transform:rotateY(-60deg) rotate(-8deg)}50%{transform:rotateY(-120deg) rotate(6deg)}75%{transform:rotateY(-160deg) rotate(-3deg)}100%{transform:rotateY(-180deg) rotate(0)}}
@keyframes cardUnfold{0%{transform:rotateY(0) scale(0.85)}50%{transform:rotateY(-90deg) scale(1)}100%{transform:rotateY(-180deg) scale(1)}}
@keyframes cardZoom{0%{transform:rotateY(0) scale(1)}30%{transform:rotateY(-60deg) scale(1.4)}60%{transform:rotateY(-120deg) scale(1.2)}100%{transform:rotateY(-180deg) scale(1)}}
@keyframes cardSpiral{0%{transform:rotateY(0) rotateZ(0) scale(0.7)}50%{transform:rotateY(-90deg) rotateZ(180deg) scale(1.1)}100%{transform:rotateY(-180deg) rotateZ(360deg) scale(1)}}
@keyframes cardShatter{0%{transform:rotateY(0);filter:blur(0)}20%{transform:rotateY(-40deg);filter:blur(4px)}40%{transform:rotateY(-90deg);filter:blur(0)}60%{transform:rotateY(-130deg);filter:blur(3px)}100%{transform:rotateY(-180deg);filter:blur(0)}}
@keyframes cardGlitch{0%{transform:rotateY(0) translateX(0)}15%{transform:rotateY(-30deg) translateX(15px)}30%{transform:rotateY(-60deg) translateX(-12px)}50%{transform:rotateY(-100deg) translateX(8px)}70%{transform:rotateY(-140deg) translateX(-5px)}100%{transform:rotateY(-180deg) translateX(0)}}
@keyframes cardMagic{0%{transform:rotateY(0) scale(0.5);opacity:0.5;filter:hue-rotate(0deg) brightness(1.5)}50%{transform:rotateY(-90deg) scale(1.2);opacity:1;filter:hue-rotate(180deg) brightness(1.2)}100%{transform:rotateY(-180deg) scale(1);filter:hue-rotate(360deg) brightness(1)}}
@keyframes cardClose{0%{transform:rotateY(-180deg)}40%{transform:rotateY(-100deg) scale(1.05)}70%{transform:rotateY(-20deg)}100%{transform:rotateY(0)}}
.card.with-envelope.closing{animation:cardCloseEnv 1.2s ease-out forwards !important}
@keyframes cardCloseEnv{0%{transform:translate(-50%,-50%) rotateY(-180deg)}40%{transform:translate(-50%,-50%) rotateY(-100deg) scale(1.05)}70%{transform:translate(-50%,-50%) rotateY(-20deg)}100%{transform:translate(-50%,-50%) rotateY(0)}}
.card.opened.with-envelope.revealed.anim-bounce{animation:cardBounceEnv 1.5s ease-out forwards}
.card.opened.with-envelope.revealed.anim-flip{animation:cardFlipEnv 2s ease-out forwards}
.card.opened.with-envelope.revealed.anim-swing{animation:cardSwingEnv 2s ease-out forwards}
.card.opened.with-envelope.revealed.anim-unfold{animation:cardUnfoldEnv 3s ease-out forwards}
.card.opened.with-envelope.revealed.anim-zoom{animation:cardZoomEnv 1.8s ease-out forwards}
.card.opened.with-envelope.revealed.anim-spiral{animation:cardSpiralEnv 2.5s ease-out forwards}
.card.opened.with-envelope.revealed.anim-shatter{animation:cardShatterEnv 2s ease-out forwards}
.card.opened.with-envelope.revealed.anim-glitch{animation:cardGlitchEnv 1.5s ease-out forwards}
.card.opened.with-envelope.revealed.anim-magic{animation:cardMagicEnv 2.5s ease-out forwards}
@keyframes cardBounceEnv{0%{transform:translate(-50%,-50%) rotateY(0)}50%{transform:translate(-50%,-50%) rotateY(-200deg) scale(1.1)}75%{transform:translate(-50%,-50%) rotateY(-170deg)}100%{transform:translate(-50%,-50%) rotateY(-180deg)}}
@keyframes cardFlipEnv{0%{transform:translate(-50%,-50%) rotateY(0) rotateX(0)}40%{transform:translate(-50%,-50%) rotateY(-90deg) rotateX(10deg) scale(1.15)}100%{transform:translate(-50%,-50%) rotateY(-180deg) rotateX(0)}}
@keyframes cardSwingEnv{0%{transform:translate(-50%,-50%) rotateY(0) rotate(0)}25%{transform:translate(-50%,-50%) rotateY(-60deg) rotate(-8deg)}50%{transform:translate(-50%,-50%) rotateY(-120deg) rotate(6deg)}75%{transform:translate(-50%,-50%) rotateY(-160deg) rotate(-3deg)}100%{transform:translate(-50%,-50%) rotateY(-180deg) rotate(0)}}
@keyframes cardUnfoldEnv{0%{transform:translate(-50%,-50%) rotateY(0) scale(0.85)}50%{transform:translate(-50%,-50%) rotateY(-90deg) scale(1)}100%{transform:translate(-50%,-50%) rotateY(-180deg)}}
@keyframes cardZoomEnv{0%{transform:translate(-50%,-50%) rotateY(0) scale(1)}30%{transform:translate(-50%,-50%) rotateY(-60deg) scale(1.4)}60%{transform:translate(-50%,-50%) rotateY(-120deg) scale(1.2)}100%{transform:translate(-50%,-50%) rotateY(-180deg) scale(1)}}
@keyframes cardSpiralEnv{0%{transform:translate(-50%,-50%) rotateY(0) rotateZ(0) scale(0.7)}50%{transform:translate(-50%,-50%) rotateY(-90deg) rotateZ(180deg) scale(1.1)}100%{transform:translate(-50%,-50%) rotateY(-180deg) rotateZ(360deg) scale(1)}}
@keyframes cardShatterEnv{0%{transform:translate(-50%,-50%) rotateY(0);filter:blur(0)}20%{transform:translate(-50%,-50%) rotateY(-40deg);filter:blur(4px)}40%{transform:translate(-50%,-50%) rotateY(-90deg);filter:blur(0)}60%{transform:translate(-50%,-50%) rotateY(-130deg);filter:blur(3px)}100%{transform:translate(-50%,-50%) rotateY(-180deg);filter:blur(0)}}
@keyframes cardGlitchEnv{0%{transform:translate(-50%,-50%) rotateY(0) translateX(0)}15%{transform:translate(-50%,-50%) rotateY(-30deg) translateX(15px)}30%{transform:translate(-50%,-50%) rotateY(-60deg) translateX(-12px)}50%{transform:translate(-50%,-50%) rotateY(-100deg) translateX(8px)}70%{transform:translate(-50%,-50%) rotateY(-140deg) translateX(-5px)}100%{transform:translate(-50%,-50%) rotateY(-180deg) translateX(0)}}
@keyframes cardMagicEnv{0%{transform:translate(-50%,-50%) rotateY(0) scale(0.5);opacity:0.5;filter:hue-rotate(0deg) brightness(1.5)}50%{transform:translate(-50%,-50%) rotateY(-90deg) scale(1.2);opacity:1;filter:hue-rotate(180deg) brightness(1.2)}100%{transform:translate(-50%,-50%) rotateY(-180deg) scale(1);filter:hue-rotate(360deg) brightness(1)}}
.card-face{
  position:absolute;
  width:100%;
  min-height:650px;
  backface-visibility:hidden;
  border-radius:16px;
  box-shadow:0 25px 80px rgba(0,0,0,0.4);
  overflow:hidden;
}
.card-front{
  background:<?php echo esc_attr($card_color); ?>;
  display:flex;
  flex-direction:column;
  align-items:center;
  justify-content:center;
  padding:40px;
  text-align:center;
  z-index:2;
}
<?php if ($card_pattern === 'dots'): ?>
.card-front::before{
  content:'';position:absolute;inset:0;
  background-image:radial-gradient(circle,rgba(255,255,255,0.3) 10%,transparent 10%);
  background-size:30px 30px;
}
<?php elseif ($card_pattern === 'stripes'): ?>
.card-front::before{
  content:'';position:absolute;inset:0;
  background:repeating-linear-gradient(45deg,transparent,transparent 10px,rgba(255,255,255,0.1) 10px,rgba(255,255,255,0.1) 20px);
}
<?php elseif ($card_pattern === 'hearts'): ?>
.card-front::before{
  content:'❤️ ❤️ ❤️ ❤️ ❤️ ❤️ ❤️ ❤️ ❤️ ❤️';
  position:absolute;inset:0;
  font-size:20px;opacity:0.2;
  display:flex;flex-wrap:wrap;align-content:center;justify-content:center;
  word-spacing:20px;line-height:2.5;
}
<?php elseif ($card_pattern === 'stars'): ?>
.card-front::before{
  content:'⭐ ✨ ⭐ ✨ ⭐ ✨ ⭐ ✨ ⭐ ✨';
  position:absolute;inset:0;
  font-size:18px;opacity:0.25;
  display:flex;flex-wrap:wrap;align-content:center;justify-content:center;
  word-spacing:15px;line-height:2.5;
}
<?php endif; ?>
.card-back{
  background:linear-gradient(135deg,#fffef9 0%,#faf8f0 100%);
  transform:rotateY(180deg);
  padding:0;
  display:block;
  height:650px;
}
.card-emoji{font-size:80px;margin-bottom:20px;animation:float 3s ease-in-out infinite}
@keyframes float{0%,100%{transform:translateY(0)}50%{transform:translateY(-10px)}}
.card-title{
  font-family:'Pacifico',cursive;
  font-size:36px;
  color:#fff;
  text-shadow:2px 2px 8px rgba(0,0,0,0.2);
  margin-bottom:10px;
  position:relative;
  z-index:1;
}
.card-subtitle{
  font-size:14px;
  color:rgba(255,255,255,0.85);
  position:relative;z-index:1;
}
.card-front-image{
  width:100%;max-height:200px;object-fit:cover;border-radius:12px;margin-bottom:20px;position:relative;z-index:1;
}
.inside-message{
  font-size:18px;
  line-height:1.8;
  color:#333;
  text-align:center;
  margin-bottom:20px;
  white-space:pre-line;
}
.inside-image{
  width:100%;max-height:180px;object-fit:cover;border-radius:12px;margin-bottom:20px;
}
.inside-from{
  font-family:'Pacifico',cursive;
  font-size:22px;
  color:<?php echo esc_attr($card_color); ?>;
  text-align:right;
  margin-top:auto;
}
.inside-extra{
  font-size:14px;
  color:#666;
  font-style:italic;
  text-align:center;
  margin-top:15px;
}
.click-hint{
  position:fixed;
  bottom:30px;left:50%;transform:translateX(-50%);
  background:rgba(255,255,255,0.15);
  backdrop-filter:blur(10px);
  padding:12px 24px;
  border-radius:30px;
  color:#fff;
  font-size:14px;
  opacity:0.8;
  animation:pulse 2s infinite;
  z-index:100;
}
@keyframes pulse{0%,100%{opacity:0.6}50%{opacity:1}}
.music-control{
  position:fixed;
  top:20px;right:20px;
  background:rgba(255,255,255,0.15);
  backdrop-filter:blur(10px);
  width:50px;height:50px;
  border-radius:50%;
  border:none;
  color:#fff;
  font-size:20px;
  cursor:pointer;
  z-index:100;
  transition:transform 0.2s,background 0.2s;
}
.music-control:hover{background:rgba(255,255,255,0.25);transform:scale(1.1)}
#particles-container{position:fixed;inset:0;pointer-events:none;z-index:50}
.particle{position:absolute;pointer-events:none}
#photos-container{position:fixed;inset:0;pointer-events:none;z-index:45;overflow:hidden}
.scrapbook-photo{position:absolute;pointer-events:auto;opacity:0;transform:scale(0) rotate(-20deg);transition:transform 0.3s ease-out,opacity 0.3s;cursor:pointer}
.scrapbook-photo.sticking{animation:stickerSlap 0.4s cubic-bezier(0.34,1.56,0.64,1) forwards}
.scrapbook-photo.stuck{opacity:1;transform:scale(1) rotate(var(--rotation))}
.scrapbook-photo.dismissing{transition:transform 0.4s ease-out,opacity 0.4s}
.scrapbook-photo img{width:100%;height:100%;object-fit:cover;border-radius:10px;box-shadow:0 10px 35px rgba(0,0,0,0.35);border:5px solid #fff}
@keyframes stickerSlap{0%{opacity:0;transform:scale(2.5) rotate(var(--start-rotation)) translateY(-100px)}40%{opacity:1;transform:scale(0.9) rotate(var(--rotation)) translateY(10px)}70%{transform:scale(1.1) rotate(var(--rotation)) translateY(-5px)}100%{opacity:1;transform:scale(1) rotate(var(--rotation)) translateY(0)}}
@media(max-width:768px){
.scrapbook-photo img{border-width:3px;border-radius:6px}
}
.page-container{position:relative;width:100%;height:100%;overflow:hidden;background:<?php echo esc_attr($inside_bg_color); ?>}
.card-page{position:absolute;inset:0;width:100%;height:100%;padding:40px;padding-bottom:50px;box-sizing:border-box;display:flex;flex-direction:column;justify-content:center;opacity:0;pointer-events:none;transition:opacity 0.3s,transform 0.3s;overflow-y:auto;scrollbar-width:none;-ms-overflow-style:none}
.card-page::-webkit-scrollbar{display:none}
.card-page.active{opacity:1;pointer-events:auto;transform:rotateY(0)}
.card-page:not(.active){opacity:0;transform:rotateY(-90deg);transform-origin:left center}
.card-page.turning-out{animation:pageTurnOut 0.3s ease-in forwards}
.card-page.turning-in{animation:pageTurnIn 0.3s ease-out forwards}
@keyframes pageTurnOut{0%{opacity:1}100%{opacity:0}}
@keyframes pageTurnIn{0%{opacity:0}100%{opacity:1}}
.card-back{cursor:pointer}
.page-hint{position:absolute;bottom:20px;left:50%;transform:translateX(-50%);font-size:13px;color:rgba(0,0,0,0.4);pointer-events:none;z-index:10}
.page-content{font-size:16px;line-height:1.8;color:#333;text-align:center}
.page-image{max-width:100%;max-height:200px;object-fit:cover;border-radius:12px;margin:15px auto;display:block}
<?php if ($card_type === 'elegant'): ?>
.card-front{background:linear-gradient(135deg,#2c3e50 0%,#1a252f 100%)}
.card-title{color:#ffd700;font-family:'Poppins',sans-serif;font-weight:700;letter-spacing:2px}
.card-subtitle{color:rgba(255,215,0,0.7)}
.inside-from{color:#2c3e50}
<?php elseif ($card_type === 'fun'): ?>
.card-front{background:linear-gradient(135deg,#ff6b6b 0%,#feca57 50%,#48dbfb 100%)}
.card{animation:wiggle 0.5s ease-in-out infinite}
@keyframes wiggle{0%,100%{transform:rotate(-1deg)}50%{transform:rotate(1deg)}}
.card.opened{animation:none}
<?php elseif ($card_type === 'minimal'): ?>
.card-front{background:#fff;border:3px solid #eee}
.card-title{color:#333;font-family:'Poppins',sans-serif;font-weight:600}
.card-subtitle{color:#999}
.card-emoji{filter:grayscale(100%)}
<?php elseif ($card_type === 'retro'): ?>
.card-front{background:#f4e4ba;border:8px solid #8b4513;box-shadow:inset 0 0 30px rgba(139,69,19,0.2)}
.card-title{color:#8b4513;font-family:Georgia,serif;text-shadow:none}
.card-subtitle{color:#a0522d}
.inside-message{font-family:Georgia,serif}
<?php elseif ($card_type === 'neon'): ?>
.card-front{background:#0a0a0a}
.card-title{
  color:#fff;
  text-shadow:0 0 10px <?php echo esc_attr($card_color); ?>,0 0 20px <?php echo esc_attr($card_color); ?>,0 0 40px <?php echo esc_attr($card_color); ?>;
  animation:neon-flicker 1.5s infinite alternate;
}
@keyframes neon-flicker{0%,18%,22%,25%,53%,57%,100%{text-shadow:0 0 10px <?php echo esc_attr($card_color); ?>,0 0 20px <?php echo esc_attr($card_color); ?>,0 0 40px <?php echo esc_attr($card_color); ?>}20%,24%,55%{text-shadow:none}}
.card-subtitle{color:<?php echo esc_attr($card_color); ?>}
<?php endif; ?>
@media(max-width:550px){
  .card{width:calc(100vw - 40px);min-height:550px}
  .card-face{min-height:550px}
  .card-back{height:550px}
  .card-title{font-size:28px}
  .card-emoji{font-size:60px}
  .envelope{width:320px;height:240px}
}
</style>
</head>
<body>
<div id="bg-effects"></div>
<div id="particles-container"></div>
<div id="photos-container"></div>

<div id="card-container">
<?php if ($envelope): ?>
<div class="envelope" id="envelope">
  <div class="envelope-front">
    <div class="envelope-front-text"><?php echo esc_html($envelope_text); ?></div>
    <div class="envelope-front-hint">(tap to flip)</div>
  </div>
  <div class="envelope-back">
    <div class="envelope-flap"></div>
    <div class="envelope-seal"><?php echo esc_html($envelope_seal_emoji); ?></div>
    <div class="envelope-back-hint">(tap to open)</div>
  </div>
</div>
<?php endif; ?>

<div class="card <?php echo $envelope ? 'with-envelope' : ''; ?>" id="card">
  <div class="card-face card-front">
    <?php if ($front_image): ?>
    <img src="<?php echo esc_url($front_image); ?>" alt="" class="card-front-image">
    <?php endif; ?>
    <div class="card-emoji"><?php echo esc_html($front_emoji); ?></div>
    <h1 class="card-title"><?php echo esc_html($front_title); ?></h1>
    <?php if ($front_subtitle): ?>
    <p class="card-subtitle"><?php echo esc_html($front_subtitle); ?></p>
    <?php endif; ?>
  </div>
  <div class="card-face card-back">
    <div class="page-container">
      <div class="card-page active" data-page="0" style="background:<?php echo esc_attr($inside_bg_color); ?>;">
        <?php if ($inside_image): ?>
        <img src="<?php echo esc_url($inside_image); ?>" alt="" class="inside-image">
        <?php endif; ?>
        <div class="inside-message"><?php echo wp_kses_post($inside_message); ?></div>
        <?php if ($inside_from): ?>
        <p class="inside-from"><?php echo esc_html($inside_from); ?></p>
        <?php endif; ?>
      </div>
      <?php foreach ($pages as $i => $page): 
        $page_bg = isset($page['bgColor']) ? $page['bgColor'] : '#fffef9';
      ?>
      <div class="card-page" data-page="<?php echo $i + 1; ?>" style="background:<?php echo esc_attr($page_bg); ?>;">
        <?php if (!empty($page['image'])): ?>
        <img src="<?php echo esc_url($page['image']); ?>" alt="" class="page-image">
        <?php endif; ?>
        <div class="page-content"><?php echo wp_kses_post($page['content'] ?? ''); ?></div>
      </div>
      <?php endforeach; ?>
      <div class="page-hint" id="page-hint"></div>
    </div>
  </div>
</div>
</div>

<?php if ($music_url): ?>
<button class="music-control" id="music-btn" title="Toggle Music">🔇</button>
<audio id="bg-music" loop>
  <source src="<?php echo esc_url($music_url); ?>" type="audio/mpeg">
</audio>
<?php endif; ?>

<script>
(function(){
  const card = document.getElementById('card');
  const envelope = document.getElementById('envelope');
  const particlesContainer = document.getElementById('particles-container');
  const bgEffects = document.getElementById('bg-effects');
  const music = document.getElementById('bg-music');
  const musicBtn = document.getElementById('music-btn');
  
  let isOpen = false;
  let envelopeOpened = false;
  const hasEnvelope = <?php echo $envelope ? 'true' : 'false'; ?>;
  const openEffect = '<?php echo esc_js($open_effect); ?>';
  const particleColor = '<?php echo esc_js($particle_color); ?>';
  const bgPattern = '<?php echo esc_js($bg_pattern); ?>';
  const musicAutoplay = <?php echo $music_autoplay ? 'true' : 'false'; ?>;
  const animationStyle = '<?php echo esc_js($animation_style); ?>';
  const photos = <?php echo $photos_json; ?>;
  const totalPages = <?php echo $total_pages; ?>;
  const photosContainer = document.getElementById('photos-container');
  let currentPage = 0;
  let photoInterval = null;
  let audioContext, analyser, dataArray;

  function createParticle(type, x, y) {
    const particle = document.createElement('div');
    particle.className = 'particle';
    
    const colors = [particleColor, '#ff6b6b', '#ffd700', '#48dbfb', '#ff9ff3', '#54a0ff'];
    const color = colors[Math.floor(Math.random() * colors.length)];
    
    if (type === 'confetti') {
      particle.style.cssText = `
        left:${x || Math.random()*100}%;top:${y || -10}px;
        width:${8+Math.random()*8}px;height:${8+Math.random()*8}px;
        background:${color};
        transform:rotate(${Math.random()*360}deg);
      `;
      particle.animate([
        {transform:`translateY(0) rotate(0deg)`,opacity:1},
        {transform:`translateY(${window.innerHeight+100}px) rotate(${720+Math.random()*720}deg)`,opacity:0}
      ],{duration:2000+Math.random()*2000,easing:'cubic-bezier(0.25,0.46,0.45,0.94)'});
    } else if (type === 'hearts') {
      particle.innerHTML = '❤️';
      particle.style.cssText = `
        left:${x || Math.random()*100}%;bottom:-50px;
        font-size:${20+Math.random()*20}px;
      `;
      particle.animate([
        {transform:'translateY(0) scale(0)',opacity:0},
        {transform:'translateY(-100px) scale(1)',opacity:1,offset:0.1},
        {transform:`translateY(${-window.innerHeight-100}px) scale(1)`,opacity:0}
      ],{duration:3000+Math.random()*2000,easing:'ease-out'});
    } else if (type === 'stars') {
      particle.innerHTML = '⭐';
      particle.style.cssText = `
        left:${Math.random()*100}%;top:${Math.random()*100}%;
        font-size:${15+Math.random()*25}px;
      `;
      particle.animate([
        {transform:'scale(0) rotate(0deg)',opacity:0},
        {transform:'scale(1.2) rotate(180deg)',opacity:1,offset:0.5},
        {transform:'scale(0) rotate(360deg)',opacity:0}
      ],{duration:1500+Math.random()*1000});
    } else if (type === 'fireworks') {
      const angle = Math.random() * Math.PI * 2;
      const distance = 100 + Math.random() * 150;
      particle.style.cssText = `
        left:${x || 50}%;top:${y || 50}%;
        width:6px;height:6px;border-radius:50%;
        background:${color};
        box-shadow:0 0 6px ${color};
      `;
      particle.animate([
        {transform:'translate(0,0) scale(1)',opacity:1},
        {transform:`translate(${Math.cos(angle)*distance}px,${Math.sin(angle)*distance}px) scale(0)`,opacity:0}
      ],{duration:800+Math.random()*400,easing:'cubic-bezier(0,0,0.2,1)'});
    } else if (type === 'balloons') {
      const balloonColors = ['🎈','🎈','🎈','🎈'];
      particle.innerHTML = balloonColors[Math.floor(Math.random()*balloonColors.length)];
      particle.style.cssText = `
        left:${Math.random()*100}%;bottom:-60px;
        font-size:${30+Math.random()*20}px;
      `;
      particle.animate([
        {transform:'translateY(0) rotate(0deg)',opacity:1},
        {transform:`translateY(${-window.innerHeight-150}px) rotate(${-20+Math.random()*40}deg)`,opacity:1}
      ],{duration:4000+Math.random()*3000,easing:'ease-out'});
    } else if (type === 'sparkle') {
      particle.innerHTML = '✨';
      particle.style.cssText = `
        left:${x || 50}%;top:${y || 50}%;
        font-size:${20+Math.random()*30}px;
        transform:translate(-50%,-50%);
      `;
      const angle = Math.random() * Math.PI * 2;
      const distance = 50 + Math.random() * 100;
      particle.animate([
        {transform:'translate(-50%,-50%) scale(0)',opacity:0},
        {transform:'translate(-50%,-50%) scale(1.5)',opacity:1,offset:0.2},
        {transform:`translate(calc(-50% + ${Math.cos(angle)*distance}px),calc(-50% + ${Math.sin(angle)*distance}px)) scale(0)`,opacity:0}
      ],{duration:1000+Math.random()*500});
    } else if (type === 'rainbow') {
      const rainbowColors = ['#ff0000','#ff7f00','#ffff00','#00ff00','#0000ff','#4b0082','#9400d3'];
      particle.style.cssText = `
        left:${Math.random()*100}%;top:-20px;
        width:${15+Math.random()*15}px;height:${15+Math.random()*15}px;
        background:${rainbowColors[Math.floor(Math.random()*rainbowColors.length)]};
        border-radius:50%;
      `;
      particle.animate([
        {transform:'translateY(0) rotate(0)',opacity:1},
        {transform:`translateY(${window.innerHeight+50}px) rotate(${360*3}deg)`,opacity:0.5}
      ],{duration:4000+Math.random()*2000,easing:'ease-in'});
    } else if (type === 'petals') {
      particle.innerHTML = ['🌸','🌺','💮','🏵️'][Math.floor(Math.random()*4)];
      particle.style.cssText = `
        left:${Math.random()*100}%;top:-30px;
        font-size:${20+Math.random()*15}px;
      `;
      const sway = 50 + Math.random() * 100;
      particle.animate([
        {transform:'translateY(0) translateX(0) rotate(0)',opacity:1},
        {transform:`translateY(${window.innerHeight*0.3}px) translateX(${sway}px) rotate(180deg)`,opacity:1,offset:0.3},
        {transform:`translateY(${window.innerHeight*0.6}px) translateX(${-sway}px) rotate(360deg)`,opacity:0.8,offset:0.6},
        {transform:`translateY(${window.innerHeight+50}px) translateX(${sway/2}px) rotate(540deg)`,opacity:0}
      ],{duration:5000+Math.random()*3000,easing:'ease-in-out'});
    } else if (type === 'butterflies') {
      particle.innerHTML = '🦋';
      particle.style.cssText = `
        left:${Math.random()*100}%;top:${50+Math.random()*40}%;
        font-size:${25+Math.random()*20}px;
      `;
      const startX = Math.random() * 100;
      particle.animate([
        {transform:'translateX(0) translateY(0) scaleX(1)',opacity:0},
        {transform:'translateX(30px) translateY(-20px) scaleX(-1)',opacity:1,offset:0.2},
        {transform:'translateX(-30px) translateY(-50px) scaleX(1)',offset:0.4},
        {transform:'translateX(50px) translateY(-100px) scaleX(-1)',offset:0.6},
        {transform:'translateX(-20px) translateY(-200px) scaleX(1)',offset:0.8},
        {transform:`translateX(${-100+Math.random()*200}px) translateY(-300px) scaleX(-1)`,opacity:0}
      ],{duration:4000+Math.random()*2000,easing:'ease-in-out'});
    } else if (type === 'cosmic') {
      const cosmicItems = ['⭐','✨','💫','🌟','✦','★'];
      particle.innerHTML = cosmicItems[Math.floor(Math.random()*cosmicItems.length)];
      const angle = Math.random() * Math.PI * 2;
      const distance = 150 + Math.random() * 250;
      particle.style.cssText = `
        left:50%;top:50%;
        font-size:${15+Math.random()*25}px;
        text-shadow:0 0 10px ${color},0 0 20px ${color};
      `;
      particle.animate([
        {transform:'translate(-50%,-50%) scale(0)',opacity:0,filter:'blur(0px)'},
        {transform:'translate(-50%,-50%) scale(2)',opacity:1,filter:'blur(0px)',offset:0.1},
        {transform:`translate(calc(-50% + ${Math.cos(angle)*distance}px),calc(-50% + ${Math.sin(angle)*distance}px)) scale(0.5)`,opacity:0,filter:'blur(2px)'}
      ],{duration:2000+Math.random()*1500,easing:'cubic-bezier(0,0,0.2,1)'});
    } else if (type === 'fire') {
      const fireColors = ['#ff4500','#ff6600','#ff8c00','#ffa500','#ffcc00'];
      particle.style.cssText = `
        left:${30+Math.random()*40}%;bottom:0;
        width:${8+Math.random()*12}px;height:${15+Math.random()*20}px;
        background:${fireColors[Math.floor(Math.random()*fireColors.length)]};
        border-radius:50% 50% 50% 50% / 60% 60% 40% 40%;
        filter:blur(1px);
      `;
      particle.animate([
        {transform:'translateY(0) scale(1)',opacity:1},
        {transform:`translateY(${-100-Math.random()*150}px) scale(0.3)`,opacity:0}
      ],{duration:1000+Math.random()*1000,easing:'ease-out'});
    } else if (type === 'snow') {
      particle.innerHTML = ['❄️','❅','❆','✧'][Math.floor(Math.random()*4)];
      particle.style.cssText = `
        left:${Math.random()*100}%;top:-30px;
        font-size:${15+Math.random()*20}px;
        opacity:${0.6+Math.random()*0.4};
      `;
      const drift = -50 + Math.random() * 100;
      particle.animate([
        {transform:'translateY(0) translateX(0) rotate(0)',opacity:0.8},
        {transform:`translateY(${window.innerHeight+50}px) translateX(${drift}px) rotate(${360+Math.random()*360}deg)`,opacity:0.3}
      ],{duration:5000+Math.random()*4000,easing:'linear'});
    }
    
    particlesContainer.appendChild(particle);
    setTimeout(() => particle.remove(), 5000);
  }

  function triggerEffect() {
    if (openEffect === 'none') return;
    
    const rect = card.getBoundingClientRect();
    const centerX = (rect.left + rect.width/2) / window.innerWidth * 100;
    const centerY = (rect.top + rect.height/2) / window.innerHeight * 100;
    
    if (openEffect === 'confetti') {
      for(let i=0;i<80;i++) setTimeout(()=>createParticle('confetti'),i*20);
    } else if (openEffect === 'hearts') {
      for(let i=0;i<30;i++) setTimeout(()=>createParticle('hearts'),i*100);
    } else if (openEffect === 'stars') {
      for(let i=0;i<40;i++) setTimeout(()=>createParticle('stars'),i*50);
    } else if (openEffect === 'fireworks') {
      for(let burst=0;burst<5;burst++){
        setTimeout(()=>{
          const bx = 20+Math.random()*60;
          const by = 20+Math.random()*60;
          for(let i=0;i<30;i++) createParticle('fireworks',bx,by);
        },burst*400);
      }
    } else if (openEffect === 'balloons') {
      for(let i=0;i<20;i++) setTimeout(()=>createParticle('balloons'),i*150);
    } else if (openEffect === 'sparkle') {
      for(let i=0;i<50;i++) setTimeout(()=>createParticle('sparkle',centerX,centerY),i*30);
    } else if (openEffect === 'rainbow') {
      for(let i=0;i<100;i++) setTimeout(()=>createParticle('rainbow'),i*40);
    } else if (openEffect === 'petals') {
      for(let i=0;i<60;i++) setTimeout(()=>createParticle('petals'),i*80);
    } else if (openEffect === 'butterflies') {
      for(let i=0;i<15;i++) setTimeout(()=>createParticle('butterflies'),i*200);
    } else if (openEffect === 'cosmic') {
      for(let burst=0;burst<8;burst++){
        setTimeout(()=>{
          for(let i=0;i<40;i++) createParticle('cosmic');
        },burst*300);
      }
    } else if (openEffect === 'fire') {
      for(let i=0;i<150;i++) setTimeout(()=>createParticle('fire'),i*20);
    } else if (openEffect === 'snow') {
      for(let i=0;i<80;i++) setTimeout(()=>createParticle('snow'),i*60);
    }
  }

  function initBgEffect() {
    if (bgPattern === 'none') return;
    
    if (bgPattern === 'particles') {
      setInterval(()=>{
        const p = document.createElement('div');
        p.style.cssText = `
          position:absolute;
          left:${Math.random()*100}%;bottom:-20px;
          width:${4+Math.random()*8}px;height:${4+Math.random()*8}px;
          background:${particleColor};opacity:0.3;border-radius:50%;
        `;
        p.animate([
          {transform:'translateY(0)',opacity:0.3},
          {transform:`translateY(${-window.innerHeight-50}px)`,opacity:0}
        ],{duration:8000+Math.random()*4000});
        bgEffects.appendChild(p);
        setTimeout(()=>p.remove(),12000);
      },300);
    } else if (bgPattern === 'stars') {
      for(let i=0;i<50;i++){
        const s = document.createElement('div');
        s.innerHTML = '✦';
        s.style.cssText = `
          position:absolute;
          left:${Math.random()*100}%;top:${Math.random()*100}%;
          color:#fff;opacity:${0.2+Math.random()*0.5};
          font-size:${8+Math.random()*12}px;
          animation:twinkle ${2+Math.random()*3}s infinite;
        `;
        bgEffects.appendChild(s);
      }
      const style = document.createElement('style');
      style.textContent = '@keyframes twinkle{0%,100%{opacity:0.2}50%{opacity:0.8}}';
      document.head.appendChild(style);
    } else if (bgPattern === 'confetti') {
      setInterval(()=>createParticle('confetti'),200);
    } else if (bgPattern === 'bubbles') {
      setInterval(()=>{
        const b = document.createElement('div');
        b.style.cssText = `
          position:absolute;
          left:${Math.random()*100}%;bottom:-30px;
          width:${10+Math.random()*30}px;height:${10+Math.random()*30}px;
          border:2px solid rgba(255,255,255,0.3);border-radius:50%;
        `;
        b.animate([
          {transform:'translateY(0) scale(1)',opacity:0.5},
          {transform:`translateY(${-window.innerHeight-50}px) scale(1.2)`,opacity:0}
        ],{duration:6000+Math.random()*4000,easing:'ease-out'});
        bgEffects.appendChild(b);
        setTimeout(()=>b.remove(),10000);
      },400);
    } else if (bgPattern === 'gradient') {
      bgEffects.style.cssText = `
        background:linear-gradient(45deg,${particleColor},#ff6b6b,#ffd700,#48dbfb,${particleColor});
        background-size:400% 400%;
        animation:gradientMove 15s ease infinite;
        opacity:0.3;
      `;
      const style = document.createElement('style');
      style.textContent = '@keyframes gradientMove{0%{background-position:0% 50%}50%{background-position:100% 50%}100%{background-position:0% 50%}}';
      document.head.appendChild(style);
    }
  }

  function openCard() {
    if (isOpen) return;
    isOpen = true;
    card.classList.add('opened');
    card.classList.add('anim-' + animationStyle);
    triggerEffect();
    startPhotoGallery();
    
    if (music && musicAutoplay) {
      music.play().then(()=>{
        musicBtn.textContent = '🔊';
        initBeatDetection();
      }).catch(()=>{});
    }
  }

  let envelopeFlipped = false;
  
  function flipEnvelope() {
    if (envelopeFlipped) return;
    envelopeFlipped = true;
    envelope.classList.add('flipped');
  }
  
  function openEnvelope() {
    if (!envelopeFlipped || envelopeOpened) return;
    envelopeOpened = true;
    envelope.classList.add('opened');
    setTimeout(()=>{
      card.classList.add('revealed');
    }, 800);
  }

  if (hasEnvelope && envelope) {
    envelope.addEventListener('click', ()=>{
      if (!envelopeFlipped) {
        flipEnvelope();
      } else {
        openEnvelope();
      }
    });
  }
  
  // Detect touch device
  const isTouchDevice = 'ontouchstart' in window || navigator.maxTouchPoints > 0;
  const pageHint = document.getElementById('page-hint');
  
  function hasMoreToScroll() {
    const activePage = document.querySelector('.card-page.active');
    if (!activePage) return false;
    return activePage.scrollHeight > activePage.clientHeight && 
           activePage.scrollTop < activePage.scrollHeight - activePage.clientHeight - 10;
  }
  
  function scrollPageDown() {
    const activePage = document.querySelector('.card-page.active');
    if (!activePage) return;
    activePage.scrollBy({ top: activePage.clientHeight * 0.7, behavior: 'smooth' });
  }
  
  function updatePageHint() {
    if (!pageHint) return;
    if (totalPages > 1) {
      const action = isTouchDevice ? 'tap' : 'click';
      pageHint.textContent = '(' + action + ' to next page)';
    } else {
      pageHint.textContent = '';
    }
  }
  
  function closeCard() {
    // First scroll all pages to top
    document.querySelectorAll('.card-page').forEach(p => {
      p.scrollTop = 0;
    });
    
    card.classList.remove('opened');
    card.classList.add('closing');
    card.classList.add('anim-' + animationStyle);
    if (pageHint) pageHint.textContent = '';
    
    // After animation completes, reset state
    setTimeout(() => {
      isOpen = false;
      card.classList.remove('closing');
      card.classList.remove('anim-' + animationStyle);
      currentPage = 0;
      document.querySelectorAll('.card-page').forEach((p, i) => {
        p.classList.toggle('active', i === 0);
      });
    }, 1500);
  }
  
  card.addEventListener('click', (e)=>{
    if (hasEnvelope && !envelopeOpened) return;
    if (!isOpen) {
      openCard();
      updatePageHint();
    } else {
      // If there's more content to scroll, scroll down first
      if (hasMoreToScroll()) {
        scrollPageDown();
      } else if (totalPages > 1) {
        // On last page, close the card and go back to front
        if (currentPage === totalPages - 1) {
          closeCard();
        } else {
          nextPage();
          // Scroll new page to top
          setTimeout(() => {
            const activePage = document.querySelector('.card-page.active');
            if (activePage) activePage.scrollTop = 0;
          }, 100);
        }
      }
    }
  });

  if (musicBtn && music) {
    musicBtn.addEventListener('click', (e)=>{
      e.stopPropagation();
      if (music.paused) {
        music.play();
        musicBtn.textContent = '🔊';
        initBeatDetection();
      } else {
        music.pause();
        musicBtn.textContent = '🔇';
      }
    });
  }

  // Page navigation with page turn animation
  window.goToPage = function(pageNum) {
    const oldPage = currentPage;
    currentPage = pageNum;
    if (currentPage < 0) currentPage = totalPages - 1;
    if (currentPage >= totalPages) currentPage = 0;
    
    const pages = document.querySelectorAll('.card-page');
    const oldPageEl = pages[oldPage];
    const newPageEl = pages[currentPage];
    
    if (oldPageEl && newPageEl && oldPage !== currentPage) {
      // Animate old page out
      oldPageEl.classList.remove('active');
      oldPageEl.classList.add('turning-out');
      
      // Animate new page in after a slight delay
      setTimeout(() => {
        oldPageEl.classList.remove('turning-out');
        newPageEl.classList.add('turning-in');
        newPageEl.classList.add('active');
        
        setTimeout(() => {
          newPageEl.classList.remove('turning-in');
        }, 600);
      }, 300);
    }
  };
  window.nextPage = function() { goToPage(currentPage + 1); };
  window.prevPage = function() { goToPage(currentPage - 1); };

  // Scrapbook photo gallery - sticks photos on beat permanently
  let photoIndex = 0;
  let stuckPhotos = [];
  let placedPositions = []; // Track placed photo positions for overlap control
  
  function getOverlapPercent(x1, y1, s1, x2, y2, s2) {
    const overlapX = Math.max(0, Math.min(x1 + s1, x2 + s2) - Math.max(x1, x2));
    const overlapY = Math.max(0, Math.min(y1 + s1, y2 + s2) - Math.max(y1, y2));
    const overlapArea = overlapX * overlapY;
    const smallerArea = Math.min(s1 * s1, s2 * s2);
    return overlapArea / smallerArea;
  }
  
  function findGoodPosition(size, cardRect) {
    const isMobile = window.innerWidth <= 768;
    const margin = isMobile ? -size * 0.3 : 15; // Negative margin on mobile allows overlap with card edges
    const maxAttempts = 30;
    
    // On mobile, photos can touch/overlap card edges
    const zones = [];
    
    if (isMobile) {
      // Mobile: photos can overlap card edges, spread across entire screen
      const screenW = window.innerWidth;
      const screenH = window.innerHeight;
      
      // Top area (can overlap into card top)
      zones.push({x: 0, xMax: screenW - size, y: 0, yMax: Math.min(cardRect.top + size * 0.4, screenH * 0.35)});
      // Bottom area (can overlap into card bottom)
      zones.push({x: 0, xMax: screenW - size, y: Math.max(cardRect.bottom - size * 0.4, screenH * 0.65), yMax: screenH - size - 30});
      // Left edge (partial overlap allowed)
      zones.push({x: 0, xMax: Math.min(cardRect.left + size * 0.3, screenW * 0.3), y: cardRect.top * 0.5, yMax: cardRect.bottom});
      // Right edge (partial overlap allowed)
      zones.push({x: Math.max(cardRect.right - size * 0.3, screenW * 0.7 - size), xMax: screenW - size, y: cardRect.top * 0.5, yMax: cardRect.bottom});
    } else {
      // Desktop: all four zones
      const leftWidth = Math.max(80, cardRect.left - margin);
      const rightStart = cardRect.right + margin;
      const rightWidth = Math.max(80, window.innerWidth - rightStart);
      const topHeight = Math.max(80, cardRect.top - margin);
      const bottomStart = cardRect.bottom + margin;
      const bottomHeight = Math.max(80, window.innerHeight - bottomStart - 40);
      
      if (leftWidth > size) zones.push({x: 10, xMax: cardRect.left - size - margin, y: 30, yMax: window.innerHeight - size - 60});
      if (rightWidth > size) zones.push({x: rightStart, xMax: window.innerWidth - size - 10, y: 30, yMax: window.innerHeight - size - 60});
      if (topHeight > size) zones.push({x: 10, xMax: window.innerWidth - size - 10, y: 10, yMax: cardRect.top - size - margin});
      if (bottomHeight > size) zones.push({x: 10, xMax: window.innerWidth - size - 10, y: bottomStart, yMax: window.innerHeight - size - 40});
    }
    
    // Fallback zone if nothing fits
    if (!zones.length) {
      return {x: Math.random() * (window.innerWidth - size), y: Math.random() * (window.innerHeight - size)};
    }
    
    // Pick zone with rotation to distribute evenly
    const zoneIndex = photoIndex % zones.length;
    const zone = zones[zoneIndex] || zones[0];
    
    for (let attempt = 0; attempt < maxAttempts; attempt++) {
      const posX = zone.x + Math.random() * Math.max(10, zone.xMax - zone.x);
      const posY = zone.y + Math.random() * Math.max(10, zone.yMax - zone.y);
      
      // Check overlap with existing photos - allow up to 50%
      let maxOverlap = 0;
      for (const placed of placedPositions) {
        const overlap = getOverlapPercent(posX, posY, size, placed.x, placed.y, placed.size);
        maxOverlap = Math.max(maxOverlap, overlap);
      }
      
      // Accept position if overlap is under 50%, or if we're running out of attempts
      if (maxOverlap < 0.5 || attempt > maxAttempts - 5) {
        return {x: Math.max(5, posX), y: Math.max(5, posY)};
      }
    }
    
    // Fallback - just place it somewhere
    return {x: Math.max(5, zone.x + Math.random() * (zone.xMax - zone.x)), y: Math.max(5, zone.y + Math.random() * (zone.yMax - zone.y))};
  }
  
  function stickPhoto() {
    if (!photos.length || photoIndex >= photos.length) return;
    
    const photoData = photos[photoIndex];
    const photoUrl = typeof photoData === 'string' ? photoData : photoData.url;
    const caption = typeof photoData === 'object' ? photoData.caption : '';
    photoIndex++;
    
    const container = document.createElement('div');
    container.className = 'scrapbook-photo';
    
    // Larger size range - responsive based on screen size
    const isMobile = window.innerWidth <= 768;
    const baseSize = isMobile ? 100 : 180;
    const sizeVariance = isMobile ? 50 : 100;
    const size = baseSize + Math.random() * sizeVariance;
    const cardRect = card.getBoundingClientRect();
    const rotation = -12 + Math.random() * 24;
    const startRotation = rotation + (-25 + Math.random() * 50);
    
    // Find position with even distribution and controlled overlap
    const pos = findGoodPosition(size, cardRect);
    
    // Track this position
    placedPositions.push({x: pos.x, y: pos.y, size: size});
    
    container.style.cssText = `
      width:${size}px;height:${size}px;
      left:${pos.x}px;top:${pos.y}px;
      --rotation:${rotation}deg;
      --start-rotation:${startRotation}deg;
      z-index:${50 + photoIndex};
    `;
    
    const img = document.createElement('img');
    img.src = photoUrl;
    container.appendChild(img);
    
    photosContainer.appendChild(container);
    stuckPhotos.push(container);
    
    // Trigger sticking animation
    requestAnimationFrame(() => {
      container.classList.add('sticking');
      setTimeout(() => {
        container.classList.remove('sticking');
        container.classList.add('stuck');
        
        // Add tap/swipe to dismiss
        addDismissHandler(container);
      }, 400);
    });
  }
  
  // Handle tap/swipe to move photos (they stay on screen but move off card)
  function addDismissHandler(el) {
    let startX = 0, startY = 0, startTime = 0;
    let currentLeft = parseFloat(el.style.left);
    let currentTop = parseFloat(el.style.top);
    let isDragging = false;
    
    const onStart = (e) => {
      e.preventDefault();
      isDragging = true;
      const touch = e.touches ? e.touches[0] : e;
      startX = touch.clientX;
      startY = touch.clientY;
      startTime = Date.now();
      currentLeft = parseFloat(el.style.left);
      currentTop = parseFloat(el.style.top);
    };
    
    const onEnd = (e) => {
      if (!isDragging) return;
      isDragging = false;
      
      const touch = e.changedTouches ? e.changedTouches[0] : e;
      const endX = touch.clientX;
      const endY = touch.clientY;
      const dx = endX - startX;
      const dy = endY - startY;
      const dt = Math.max(Date.now() - startTime, 1);
      
      // Calculate velocity (pixels per ms)
      const vx = dx / dt;
      const vy = dy / dt;
      const speed = Math.sqrt(vx * vx + vy * vy);
      
      // Minimum movement to count as intentional
      if (Math.abs(dx) < 10 && Math.abs(dy) < 10 && dt > 300) {
        return; // Long press without movement, ignore
      }
      
      const size = parseFloat(el.style.width);
      const cardRect = card.getBoundingClientRect();
      const photoRect = el.getBoundingClientRect();
      
      // Check if photo overlaps with card
      const overlapsCard = !(photoRect.right < cardRect.left || 
                            photoRect.left > cardRect.right || 
                            photoRect.bottom < cardRect.top || 
                            photoRect.top > cardRect.bottom);
      
      // Calculate move distance based on velocity
      let moveMultiplier = Math.max(80, speed * 250);
      
      // If overlapping card, increase movement to ensure it moves off
      if (overlapsCard) {
        moveMultiplier = Math.max(moveMultiplier, 200);
      }
      
      let moveX, moveY;
      
      if (Math.abs(dx) > 10 || Math.abs(dy) > 10) {
        // Use swipe direction and velocity
        const angle = Math.atan2(dy, dx);
        moveX = Math.cos(angle) * moveMultiplier;
        moveY = Math.sin(angle) * moveMultiplier;
      } else {
        // Quick tap - move away from card center if overlapping, else screen center
        const photoCenterX = photoRect.left + photoRect.width / 2;
        const photoCenterY = photoRect.top + photoRect.height / 2;
        
        let targetX, targetY;
        if (overlapsCard) {
          targetX = cardRect.left + cardRect.width / 2;
          targetY = cardRect.top + cardRect.height / 2;
        } else {
          targetX = window.innerWidth / 2;
          targetY = window.innerHeight / 2;
        }
        
        const awayX = photoCenterX - targetX;
        const awayY = photoCenterY - targetY;
        const dist = Math.sqrt(awayX * awayX + awayY * awayY) || 1;
        moveX = (awayX / dist) * moveMultiplier;
        moveY = (awayY / dist) * moveMultiplier;
      }
      
      // Calculate new position
      let newLeft = currentLeft + moveX;
      let newTop = currentTop + moveY;
      
      // Clamp to screen bounds
      newLeft = Math.max(0, Math.min(newLeft, window.innerWidth - size));
      newTop = Math.max(0, Math.min(newTop, window.innerHeight - size - 30));
      
      // Apply new position with animation
      el.style.transition = 'left 0.3s ease-out, top 0.3s ease-out';
      el.style.left = newLeft + 'px';
      el.style.top = newTop + 'px';
      
      // Reset transition after animation
      setTimeout(() => {
        el.style.transition = '';
      }, 300);
    };
    
    el.addEventListener('touchstart', onStart, {passive: false});
    el.addEventListener('touchend', onEnd);
    el.addEventListener('mousedown', onStart);
    document.addEventListener('mouseup', onEnd);
  }

  function startPhotoGallery() {
    // Photos are now triggered by beat detection, not interval
  }

  // Beat detection for music-synced effects
  function initBeatDetection() {
    if (!music || audioContext) return;
    try {
      audioContext = new (window.AudioContext || window.webkitAudioContext)();
      analyser = audioContext.createAnalyser();
      const source = audioContext.createMediaElementSource(music);
      source.connect(analyser);
      analyser.connect(audioContext.destination);
      analyser.fftSize = 256;
      dataArray = new Uint8Array(analyser.frequencyBinCount);
      detectBeat();
    } catch(e) {}
  }

  let lastBeatTime = 0;
  let beatCount = 0;
  function detectBeat() {
    if (!analyser) return;
    requestAnimationFrame(detectBeat);
    if (music.paused) return;
    
    analyser.getByteFrequencyData(dataArray);
    const bass = dataArray.slice(0, 10).reduce((a,b) => a+b, 0) / 10 / 255;
    const now = Date.now();
    
    // Detect beat - trigger photo stick on every 2nd strong beat
    if (bass > 0.6 && now - lastBeatTime > 400) {
      lastBeatTime = now;
      beatCount++;
      
      // Stick a photo every 2 beats (roughly every bar)
      if (beatCount % 2 === 0 && photoIndex < photos.length) {
        stickPhoto();
      }
    }
  }

  initBgEffect();
})();
</script>
</body>
</html>
