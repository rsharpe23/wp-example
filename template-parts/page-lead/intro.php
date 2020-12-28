<?php
global $store;
$_store = new RSh_Data_Store_Derived( $store, 'pagelead_intro_' );

if ( $_store->get( 'enabled' ) ) : 
?>

  <div class="container-fluid intro">
    <h1 class="intro__title"><?php $_store->e( 'title' ); ?></h1>

    <div class="d-none d-sm-block intro__content">
      <?php rsh_content_e( $_store->get( 'content' ) ); ?>
    </div>

    <?php rsh_link( $_store->get( 'link' ), 'btn btn-primary intro__btn' ); ?>
  </div>
  
<?php 
endif;
?>  