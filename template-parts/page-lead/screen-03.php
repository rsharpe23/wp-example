<?php
global $store;
$_store = new RSh_Data_Store_Derived( $store, 'pagelead_screen_03_' );

if ( $_store->get( 'enabled' ) ) :
?>

  <div class="row screen screen_03">
    <div class="col-md-7">
      <h2 class="screen__title"><?php $_store->e( 'title' ); ?></h2>

      <div class="screen__content">
        <?php rsh_content_e( $_store->get( 'content' ) ); ?>
      </div>
    </div>

    <div class="col-md-5">
      <?php if ( $img = $_store->get( 'thumbnail' ) ) : ?>
        <div class="img-wrap">
          <img 
            src="<?php echo esc_url( $img ) ?>" 
            alt="<?php esc_attr( $_store->get( 'title' ) ); ?>" 
            class="screen_img">
        </div>
      <?php endif; ?>
    </div>
  </div>

<?php
endif;
?>