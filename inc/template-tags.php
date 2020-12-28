<?php
/**
 * Функции которые выводят html темы
 */

if ( ! function_exists( 'rsh_header_menu' ) ) :
  function rsh_header_menu() {
    rsh_nav_menu( array(
      'theme_location'  => 'header_menu',
      'container_class' => 'd-none d-sm-flex nav',
      'menu_class'      => 'menu header__menu',
    ) );
  }
endif;

if ( ! function_exists( 'rsh_footer_menu' ) ) :
  function rsh_footer_menu() {
    rsh_nav_menu( array(
      'theme_location' => 'footer_menu',
      'container'      => false,
      'menu_class'     => 'menu footer__menu',
    ) );
  }
endif;

if ( ! function_exists( 'rsh_nav_menu' ) ) :
  function rsh_nav_menu( $args ) {
    $default_args = array(
      'container'       => 'nav',
      'container_class' => 'nav',
      'walker'          => 'RSh_Walker_Nav_Menu',
    );

    wp_nav_menu( wp_parse_args( $args, $default_args ) );
  }
endif;

if ( ! function_exists( 'rsh_link' ) ) :
  function rsh_link( $value, $_class = '', $has_shortcode = false ) {
    $value = json_decode( $value );

    if ( ! is_array( $value ) ) {
      return;
    }

    // Вызов $store->get( $key, true ) для json строки не работает
    if ( $has_shortcode && ( $text = $value[1] ) ) {
      $value[1] = do_shortcode( $text );
    }

    if ( $_class ) {
      $_class = sprintf( ' class="%s"', esc_html( $_class ) );
    }
    ?>
      <a href="<?php echo esc_url( $value[0] ); ?>"<?php echo $_class; ?>><?php echo wp_kses_post( $value[1] ); ?></a>
    <?php
  }
endif;