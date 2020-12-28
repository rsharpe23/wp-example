<?php

if ( ! class_exists( 'RSh_Walker_Nav_Menu' ) ) :
  class RSh_Walker_Nav_Menu extends Walker_Nav_Menu {
    protected $extra;

    public function __construct( $extra = array() ) {
      $this->extra = $extra;
    }

    // $item в параметрах это тег <a>
    public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
      $_class = $this->extra['_class'] ?? '';

      if ( $_class ) {
        // Работает только если ссылка в меню совпадает с адресом просматриваемой страницы.
        // Посему активный элемент лучше определять через js
        if ( isset( $item->classes ) && in_array( 'current-menu-item', $item->classes ) ) {
          $active_class = $this->extra['active_class'] ?? 'active';
          $_class = trim( "{$_class} {$active_class}" );
        } 

        $_class = " class=\"$_class\"";
      }

      $output .= "<li{$_class}>";

      $item_url = $item->url ?? '#';
      $item_class = $this->extra['item_class'] ?? '';

      if ( $item_class ) {
        $item_class = " class=\"$item_class\"";
      }

      $item_output = $args->before ?? '';
      $item_output .= "<a href=\"{$item_url}\"{$item_class}>";
      $item_output .= ( $args->link_before ?? '' ) . $item->title . ( $args->link_after ?? '' );
      $item_output .= '</a>';
      $item_output .= $args->after ?? '';

      $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
    }
  }
endif;