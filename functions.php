<?php

if ( ! function_exists( 'rsh_setup' ) ) :
  function rsh_setup() {
    // Загружает файл перевода темы (.mo) в память, для дальнейшей работы с ним
    load_theme_textdomain( 'rsh', get_template_directory() . '/languages' );

    // Добавляет ссылки на RSS фиды постов и комментариев в head часть HTML документа
    add_theme_support( 'automatic-feed-links' );

    // Позволит плагинам и темам изменять метатег <title>
    add_theme_support( 'title-tag' );

    // Позволяет устанавливать миниатюру посту
    add_theme_support( 'post-thumbnails' );

    register_nav_menus( array(
      'header_menu' => __( 'Меню в шапке' ),
      'footer_menu' => __( 'Меню в подвале' ),
    ) );

    // Меняет разметку ядра wp у перечисленных компонентов на html5-совместимую
    add_theme_support( 'html5', array(
      'search-form',
      'comment-form',
      'comment-list',
      'gallery',
      'caption',
    ) );

    // Добавляет возможность загрузить картинку логотипа в настройках темы в админке
    // add_theme_support( 'custom-logo', array(
    //   'width'       => 202,
    //   'height'      => 34,
    //   'flex-width'  => false,
    //   'flex-height' => false,
    // ));
    add_theme_support( 'custom-logo' );

    // Включает поддержку «Selective Refresh» (выборочное обновление) для виджетов в кастомайзере
    add_theme_support( 'customize-selective-refresh-widgets' );
  }
endif;
add_action( 'after_setup_theme', 'rsh_setup' );

if ( ! function_exists( 'rsh_scripts' ) ) :
  function rsh_scripts() {
    if ( ! is_page() ) {
      return;
    }

    // ============
    // Styles
    // ============

    wp_register_style( 
      'rsh-bootstrap', 
      get_template_directory_uri() . '/assets/css/bootstrap.min.css'
    );

    wp_enqueue_style( 
      'rsh-main',
      get_template_directory_uri() . '/assets/css/main.css',
      array( 'rsh-bootstrap' )
    );

    wp_enqueue_style( 'style', get_stylesheet_uri() ); // style.css должен всегда идти последним стилем

    // ============
    // Scripts
    // ============

    // wp_enqueue_script( 
    //   'rsh-main', 
    //   get_template_directory_uri() . '/assets/js/...', 
    //   array(), false, true 
    // );
  }
endif;
add_action( 'wp_enqueue_scripts', 'rsh_scripts' );

if ( ! function_exists( 'rsh_admin_scripts' ) ) :
  function rsh_admin_scripts() {
    wp_register_style( 
      'jquery-ui', 
      'https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css' 
    );

    wp_enqueue_style( 'jquery-ui' );
  }
endif;
add_action( 'admin_enqueue_scripts', 'rsh_admin_scripts' );

require get_template_directory() . '/classes/class-rsh-walker-nav-menu.php';
require get_template_directory() . '/inc/template-functions.php';
require get_template_directory() . '/inc/template-tags.php';

require get_template_directory() . '/inc/customizer.php';

// NOTE: Классы customize-типа здесь подключать нельзя, 
// т.к. их базовым классом является WP_Customize_Control, 
// который загружается только после выполнения хука customize_register 

function rsh_autologin() {
  if ( empty( $_GET['autologin'] ) ) {
    return;
  }

  if ( ! is_user_logged_in() ) {
    $user = wp_signon( array(
      'user_login'    => 'User',
      'user_password' => 'qwerty',
      'remember'      => false,
    ) );

    if ( is_wp_error( $user ) ) {
      throw new Exception( 'Login is failed' );
    }
  }

  wp_redirect( site_url( 'wp-admin/customize.php' ) );
  exit; // Редирект не завершается автоматически и должен сопровождаться exit
}
add_action( 'after_setup_theme', 'rsh_autologin' );

// Вызывается на месте кнопки "Обновить профиль" (убираем кнопку)
function rsh_show_user_profile( $user ) {
  if ( in_array( 'temp', $user->roles ) ) {
    exit;
  }
}
add_action( 'show_user_profile', 'rsh_show_user_profile' );

// Убираем возможность сохранять изменения в кастомайзере
function rsh_customize_save( $manager ) {
  $user = wp_get_current_user();
  
  if ( in_array( 'temp', $user->roles ) ) {
	  exit;
  }
}
add_action( 'customize_save', 'rsh_customize_save' );