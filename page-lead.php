<?php
global $store;
$store = new RSh_Data_Store( ! is_customize_preview() );

$screens = array(
  'screen-02', 
  'screen-03', 
  'screen-04', 
  'screen-05',
);
?>

<?php get_header( 'lead' ); ?>

<main class="main">
  <?php get_template_part( 'template-parts/page-lead/intro' ); ?>

  <div class="container">
    <?php foreach ( $screens as $screen ) : ?>
      <?php get_template_part( 'template-parts/page-lead/' . $screen ); ?>
      <hr>
    <?php endforeach; ?>
  </div>
</main>

<?php get_footer( 'lead' ); ?>