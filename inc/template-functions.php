<?php
/**
 * Функции для работы с данными темы
 */

function rsh_content_e( $text, $has_shortcode = false ) {
  $text = apply_filters( 'the_content', wp_kses_post( $text ) );

  if ( $has_shortcode ) {
    $text = do_shortcode( $text );
  }

  echo str_replace( ']]>', ']]&gt;', $text );
}

// title - [pl url="#" alt="Доп.текст"]Титул[/pl]
function rsh_get_pl( $title ) {
  $tl_json = do_shortcode( $title );
  return json_decode( $tl_json );
}

// pl - portlet link
function rsh_pl_shortcode( $atts, $content ) {
  $value = array( 'text' => $content );

  if ( ! empty( $atts ) ) {
    $value = array_merge( $atts, $value );
  }

  return wp_json_encode( $value, JSON_UNESCAPED_UNICODE );
}
add_shortcode( 'pl', 'rsh_pl_shortcode' );

// $content это либо attachment_id, либо ссылка на картинку
function rsh_img_shortcode( $atts, $content ) {
  if ( $attachment_id = intval( $content ) ) {
    return wp_get_attachment_image( $attachment_id, 'full' );
  }

  return sprintf( '<img src="%s">', esc_url( $content ) );
}
add_shortcode( 'img', 'rsh_img_shortcode' );

function rsh_nav_menu_args( $args ) {
  $valker = $args['walker'];

  if ( $valker ) {
    $extra = ( $valker !== 'RSh_Walker_Nav_Menu' ) ? array() : array( 
      '_class'       => 'menu__item', 
      'active_class' => 'menu__item_active', 
      'item_class'   => 'menu__link' 
    );

    $args['walker'] = new $valker( $extra );
  }

	return $args;
}
// Приоритет обязательно должен быть больше 1000
add_filter( 'wp_nav_menu_args', 'rsh_nav_menu_args', 1001 );