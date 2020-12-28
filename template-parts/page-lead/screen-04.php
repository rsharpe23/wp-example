<?php
global $store;
$_store = new RSh_Data_Store_Derived( $store, 'pagelead_screen_04_' );

if ( $_store->get( 'enabled' ) ) :
?>

  <div class="row screen screen_04">
    <div class="col-md-7 order-md-1">
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

<!-- <div class="row screen screen_04">
  <div class="col-md-7 order-md-1">
    <h2 class="screen__title">Заголовок экрана #03</h2>

    <div class="screen__content">
      <p>Lorem ipsum, dolor sit amet consectetur adipisicing elit. At, impedit placeat? Nihil, illo harum.
        Blanditiis suscipit error exercitationem nihil recusandae aliquam dolorum non! Inventore quia commodi
        adipisci accusamus assumenda soluta.</p>
    </div>
  </div>

  <div class="col-md-5">
    <div class="img-wrap">
      <img src="http://wp-example.loc/wp-content/uploads/2020/03/500x500.png" alt="Квадрат" class="screen_img">
    </div>
  </div>
</div> -->