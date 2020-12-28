<?php
global $store;
$_store = new RSh_Data_Store_Derived( $store, 'pagelead_screen_02_' );

if ( $_store->get( 'enabled' ) ) :
  $items = json_decode( $_store->get( 'items' ) );
?>

  <div class="row screen screen_02">
    <?php if ( is_array( $items ) ) : ?>
      <?php foreach ( $items as $item ) :
        // $pl = rsh_get_pl( $item->title );
        ?>
        <div class="col-md-6 col-lg-4 item screen__item">
          <?php if ( isset( $item->attachment ) ) : ?>
            <div class="img-wrap">
              <img 
                src="<?php echo esc_url( $item->attachment ); ?>" 
                alt="<?php echo esc_attr( $item->title ); ?>" 
                class="item__img">
            </div>
          <?php endif; ?>

          <h2 class="item__title"><?php esc_html_e( /*$pl->text*/ $item->title ); ?></h2>

          <div class="item__content">
            <?php rsh_content_e( $item->text ?? '' ); ?>
          </div>

          <a 
            href="<?php echo esc_url( /*$pl->url*/ '#' ); ?>" 
            class="btn btn-secondary"><?php esc_html_e( /*$pl->alt*/ 'Смотреть больше »' ); ?></a>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>

<?php
endif;
?>