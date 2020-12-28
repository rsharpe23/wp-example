<?php

final class RSh_Sanitize_Helper {
  public static function sanitize_number( $number ) {
    return absint( $number );
  }

  public static function sanitize_text( $text ) {
    return sanitize_text_field( $text );
  }

  public static function sanitize_html( $content ) {
    return wp_kses_post( $content );
  }

  public static function sanitize_checkbox( $checkbox ) {
    return ! empty( $checkbox );
    // return empty( $checkbox ) ? false : true;
  }

  public static function sanitize_radio( $input, $setting ) {
    $input = self::sanitize_text( $input );
    $choices = $setting->manager->get_control( $setting->id )->choices;
    return array_key_exists( $input, $choices ) ? $input : $setting->default;
  }

  public static function sanitize_select( $input, $setting ){
    $input = sanitize_key( $input );
    $control = $setting->manager->get_control( $setting->id );
    return array_key_exists( $input, $control->choices ) ? $input : $setting->default;                
  }

  public static function sanitize_color( $color ) {
    return sanitize_hex_color( $color );
  }

  public static function sanitize_image( $input ) {
    $output = '';
 
    $file_type = wp_check_filetype( $input );
    $mime_type = $file_type['type'];
 
    if ( strpos( $mime_type, 'image' ) !== false ) {
      $output = $input;
    }
 
    return $output;
  }
}

class RSh_Customize_Control_Factory {
  public $wp_customize;

  public function __construct( $wp_customize ) {
    $this->wp_customize = $wp_customize;

    require_once get_template_directory() . '/classes/class-rsh-customize-editor-control.php';
    $this->wp_customize->register_control_type( 'RSh_Customize_Editor_Control' );

    require_once get_template_directory() . '/classes/class-rsh-customize-link-control.php';
    $this->wp_customize->register_control_type( 'RSh_Customize_Link_Control' );

    require_once get_template_directory() . '/classes/class-rsh-customize-portlets-control.php';
    $this->wp_customize->register_control_type( 'RSh_Customize_Portlets_Control' );
  }

  public function create_text( $id, $args, $extra ) {
    $args['type'] = 'text';

    $this->wp_customize->add_setting( $id, array(
      'default' => $extra['default'] ?? '',
      'sanitize_callback' => 'RSh_Sanitize_Helper::sanitize_text',
    ) );

    if ( isset( $extra['selector'] ) ) {
      $this->init_selective_refresh( $id, $extra['selector'] );
    }

    return $this->wp_customize->add_control( $id, $args );
  }

  public function create_number( $id, $args, $extra ) {
    $args['type'] = 'number';

    $this->wp_customize->add_setting( $id, array(
      'default' => $extra['default'] ?? 1,
      'sanitize_callback' => 'RSh_Sanitize_Helper::sanitize_number',
    ) );

    return $this->wp_customize->add_control( $id, $args );
  }

  public function create_checkbox( $id, $args, $extra ) {
    $args['type'] = 'checkbox';

    $this->wp_customize->add_setting( $id, array(
      'default' => $extra['default'] ?? false,
      'sanitize_callback' => 'RSh_Sanitize_Helper::sanitize_checkbox',
    ) );

    return $this->wp_customize->add_control( $id, $args );
  }

  public function create_radio( $id, $args, $extra ) {
    $args['type'] = 'radio';

    // В данном случае установка значения по умолчанию (кроме 1)
    // приведет к ошибке при первой загрузке страницы, когда в базе еще нет значения, 
    // а в preview показывает дефолтное
    $this->wp_customize->add_setting( $id, array(
      'default' => $extra['default'] ?? 1,
      'sanitize_callback' => 'RSh_Sanitize_Helper::sanitize_radio',
    ) );

    return $this->wp_customize->add_control( $id, $args );
  }

  public function create_select( $id, $args, $extra ) {
    $args['type'] = 'select';

    $this->wp_customize->add_setting( $id, array(
      'default' => $extra['default'] ?? '',
      'sanitize_callback' => 'RSh_Sanitize_Helper::sanitize_select',
    ) );

    return $this->wp_customize->add_control( $id, $args );
  }

  public function create_color( $id, $args, $extra ) {
    $this->wp_customize->add_setting( $id, array(
      'default' => $extra['default'] ?? '#fff',
      'sanitize_callback' => 'RSh_Sanitize_Helper::sanitize_color',
    ) );

    // if ( $selector = $extra['selector'] ) {
    //   $this->init_selective_refresh( $id, $selector );
    // }

    return $this->wp_customize->add_control(
      new WP_Customize_Color_Control( $this->wp_customize, $id, $args )
    );
  }

  public function create_image( $id, $args, $extra ) {
    $this->wp_customize->add_setting( $id, array(
      'default'   => $extra['default'] ?? '',
      'sanitize_callback' => 'RSh_Sanitize_Helper::sanitize_image',
    ) );

    return $this->wp_customize->add_control( 
      new WP_Customize_Image_Control( $this->wp_customize, $id, $args ) 
    );
  }

  // Custom

  public function create_editor( $id, $args, $extra ) {
    $this->wp_customize->add_setting( $id, array(
      'default'   => $extra['default'] ?? '',
      'sanitize_callback' => 'RSh_Sanitize_Helper::sanitize_html',
      'transport' => 'postMessage',
    ) );

    if ( isset( $extra['selector'] ) ) {
      $this->init_selective_refresh( $id, $extra['selector'], function ( $id ) {
        if ( $value = get_theme_mod( $id ) ) {
          return rsh_esc_content( $value );
        }

        return false;
      } );
    }

    // if ( $selector = $extra['selector'] ) {
    //   $this->init_selective_refresh( $id, $selector, function ( $id ) {
    //     if ( $value = get_theme_mod( $id ) ) {
    //       return rsh_esc_content( $value );
    //     }

    //     return false;
    //   } );
    // }

    return $this->wp_customize->add_control( 
      new RSh_Customize_Editor_Control( $this->wp_customize, $id, $args ) 
    );
  }

  public function create_link( $id, $args, $extra ) {
    $this->wp_customize->add_setting( $id, array(
      'default' => $extra['default'] ?? '["http://", ""]',
      'sanitize_callback' => 'RSh_Sanitize_Helper::sanitize_html',
    ) );

    if ( isset( $extra['selector'] ) ) {
      $this->init_selective_refresh( $id, $extra['selector'], function ( $id ) {
        if ( $value = get_theme_mod( $id ) ) {
          $value = json_decode( $value );

          if ( is_array( $value ) ) {
            return $value[1];
          }
        }
 
        return false;
      } );
    }

    return $this->wp_customize->add_control( 
      new RSh_Customize_Link_Control( $this->wp_customize, $id, $args ) 
    );
  }

  public function create_portlets( $id, $args, $extra ) {
    $this->wp_customize->add_setting( $id, array(
      'default' => $extra['default'] ?? '[]',
      'sanitize_callback' => 'RSh_Sanitize_Helper::sanitize_html',
    ) );

    // if ( $selector = $extra['selector'] ) {
    //   $this->init_selective_refresh( $id, $selector );
    // }

    $this->wp_customize->add_control( 
      new RSh_Customize_Portlets_Control( $this->wp_customize, $id, $args ) 
    );
  }

  // [НЕТОЧНО]: Чтобы работал режим transform->postMessage необходимо container_inclusive назначить false
  protected final function init_selective_refresh( $id, $selector, $render_func = false ) {
    if ( isset( $this->wp_customize->selective_refresh ) ) {
      $this->wp_customize->get_setting( $id )->transport = 'postMessage';
      $this->wp_customize->selective_refresh->add_partial( $id, array(
        'selector' => $selector,
        'container_inclusive' => false,
        'render_callback' => function () use ( $id, $render_func ) {
          if ( $render_func ) {
            return $render_func( $id );
          }

          return get_theme_mod( $id );
        },
      ) );
    }
  }
}

final class RSh_PageLead_Layout {
  public static function get_choices( $min, $max ) {
    if ($max < $min) {
      throw new Exception( 'Min layout num can`t be less than Max' );
    }
    
    $choices = array();
    for ( $i = $min; $i <= $max; $i++ ) {
      $choices["{$i}"] = __( "{$i} колоночный макет" );
    }

    return $choices;
  }
}

class RSh_Customizer {
  public $control_factory;

  public function __construct( $wp_customize ) {
    $this->control_factory = new RSh_Customize_Control_Factory( $wp_customize );
  }

  public function __call( $method, $args ) {
    switch ( $method ) {
      case 'add_panel':
        $args[0]->$method( $args[1], $args[2] );
        break;

      case 'add_section':
        $on_ready_method = "on_{$args[1]}_ready";
        $section = $args[0]->$method( $args[1], $args[2] );
        $this->$on_ready_method( $section );
        break;
    }
  }

  public function add_control( $type, $section, $slug, $args ) {
    $method = "create_{$type}";

    $sect_id = $section->id;
    $id = $slug ? $sect_id . "_{$slug}" : '';
    $args['section'] = $sect_id;

    $extra = array_filter( $args, function ( $value, $key ) {
      return $key == 'selector' || $key == 'default';
    }, ARRAY_FILTER_USE_BOTH );

    if ( $extra ) {
      $args = array_diff_assoc( $args, $extra );
    }

    return $this->control_factory->$method( $id, $args, $extra );
  }
}

final class RSh_PageLead_Customizer extends RSh_Customizer  {
  public function __construct( $wp_customize ) {
    parent::__construct( $wp_customize );

    $this->add_panel( $wp_customize, 'pagelead', array(
      'title'    => __( 'Главная страница' ),
      'priority' => 1,
    ) );

    $this->add_section( $wp_customize, 'pagelead_intro', array(
      'title' => __( 'Главный экран' ),
      'panel' => 'pagelead',
    ) );

    $this->add_section( $wp_customize, 'pagelead_screen_02', array(
      'title' => __( 'Экран #02' ),
      'panel' => 'pagelead',
    ) );

    $this->add_section( $wp_customize, 'pagelead_screen_03', array(
      'title' => __( 'Экран #03' ),
      'panel' => 'pagelead',
    ) );

    $this->add_section( $wp_customize, 'pagelead_screen_04', array(
      'title' => __( 'Экран #04' ),
      'panel' => 'pagelead',
    ) );

    $this->add_section( $wp_customize, 'pagelead_screen_05', array(
      'title' => __( 'Экран #05' ),
      'panel' => 'pagelead',
    ) );
  }

  public function on_pagelead_intro_ready( $section ) {
    $enabled_control = $this->add_control( 'checkbox', $section, 'enabled', array( 
      'label' => __( 'Включить секцию' ),
    ) );

    // Будет работать только в том случае если Customizer показывает html страницы, 
    // т.к. с ним подругжаются дефолтные скрипты WordPress, 
    // которые и обновляют setting->value() через ajax
    $active_callback = array( 
      $enabled_control->setting, 
      'value' 
    );

    $this->add_control( 'text', $section, 'title', array(
      'label'    => __( 'Заголовок' ),
      'selector' => '.intro__title',
      'active_callback' => $active_callback,
    ) );

    $this->add_control( 'editor', $section, 'content', array(
      'label'    => __( 'Содержимое' ),
      'selector' => '.intro__content',
      'active_callback' => $active_callback,
    ) );

    $this->add_control( 'link', $section, 'link', array(
      'label'    => __( 'Ссылка' ),
      'selector' => '.intro__btn',
      'active_callback' => $active_callback,
    ) );
  }

  public function on_pagelead_screen_02_ready( $section ) {
    $enabled_control = $this->add_control( 'checkbox', $section, 'enabled', array( 
      'label' => __( 'Включить секцию' ),
    ) );

    // Будет работать только в том случае если Customizer показывает html страницы, 
    // т.к. с ним подругжаются дефолтные скрипты WordPress, 
    // которые и обновляют setting->value() через ajax
    $active_callback = array( 
      $enabled_control->setting, 
      'value' 
    );

    $this->add_control( 'portlets', $section, 'items', array(
      'label' => __( 'Карточки' ),
      'active_callback' => $active_callback,
    ) );
  }

  public function on_pagelead_screen_03_ready( $section ) {
    $enabled_control = $this->add_control( 'checkbox', $section, 'enabled', array( 
      'label' => __( 'Включить секцию' ),
    ) );

    // Будет работать только в том случае если Customizer показывает html страницы, 
    // т.к. с ним подругжаются дефолтные скрипты WordPress, 
    // которые и обновляют setting->value() через ajax
    $active_callback = array( 
      $enabled_control->setting, 
      'value' 
    );

    $this->add_control( 'text', $section, 'title', array(
      'label'    => __( 'Заголовок' ),
      'selector' => '.screen_03 .screen__title',
      'active_callback' => $active_callback,
    ) );

    $this->add_control( 'editor', $section, 'content', array(
      'label'    => __( 'Содержимое' ),
      'selector' => '.screen_03 .screen__content',
      'active_callback' => $active_callback,
    ) );

    $this->add_control( 'image', $section, 'thumbnail', array(
      'label' => __( 'Изображение' ),
      'active_callback' => $active_callback,
    ) );
  }

  public function on_pagelead_screen_04_ready( $section ) {
    $enabled_control = $this->add_control( 'checkbox', $section, 'enabled', array( 
      'label' => __( 'Включить секцию' ),
    ) );

    // Будет работать только в том случае если Customizer показывает html страницы, 
    // т.к. с ним подругжаются дефолтные скрипты WordPress, 
    // которые и обновляют setting->value() через ajax
    $active_callback = array( 
      $enabled_control->setting, 
      'value' 
    );

    $this->add_control( 'text', $section, 'title', array(
      'label'    => __( 'Заголовок' ),
      'selector' => '.screen_04 .screen__title',
      'active_callback' => $active_callback,
    ) );

    $this->add_control( 'editor', $section, 'content', array(
      'label'    => __( 'Содержимое' ),
      'selector' => '.screen_04 .screen__content',
      'active_callback' => $active_callback,
    ) );

    $this->add_control( 'image', $section, 'thumbnail', array(
      'label' => __( 'Изображение' ),
      'active_callback' => $active_callback,
    ) );
  }

  public function on_pagelead_screen_05_ready( $section ) {
    $enabled_control = $this->add_control( 'checkbox', $section, 'enabled', array( 
      'label' => __( 'Включить секцию' ),
    ) );

    // Будет работать только в том случае если Customizer показывает html страницы, 
    // т.к. с ним подругжаются дефолтные скрипты WordPress, 
    // которые и обновляют setting->value() через ajax
    $active_callback = array( 
      $enabled_control->setting, 
      'value' 
    );

    $this->add_control( 'text', $section, 'title', array(
      'label'    => __( 'Заголовок' ),
      'selector' => '.screen_05 .screen__title',
      'active_callback' => $active_callback,
    ) );

    $this->add_control( 'editor', $section, 'content', array(
      'label'    => __( 'Содержимое' ),
      'selector' => '.screen_05 .screen__content',
      'active_callback' => $active_callback,
    ) );

    $this->add_control( 'image', $section, 'thumbnail', array(
      'label' => __( 'Изображение' ),
      'active_callback' => $active_callback,
    ) );
  }
}

interface IData_Store {
  public function e( $key, $esc_fn );
  public function get( $key );
}

// NOTE: Текст из БД не нужно переводить на другой язык
class RSh_Data_Store implements IData_Store {
  public $data;

  public function __construct( $non_lazy ) {
    if ( $non_lazy ) {
      $this->data = get_theme_mods();
    }
  }

  public function e( $key, $esc_fn = 'wp_kses_post' ) {
    echo $esc_fn( $this->get( $key ) );
  }

  public function get( $key ) {
    $value = is_array( $this->data ) ? 
      $this->data[ $key ] ?? false : 
      get_theme_mod( $key );

    return apply_filters( 'rsh_data', $value, $key );
  }
}

class RSh_Data_Store_Derived implements IData_Store {
  public $store;
  public $key_prefix;

  public function __construct( $store, $key_prefix ) {
    $this->store = $store;
    $this->key_prefix = $key_prefix;
  }

  public function e( $key, $esc_fn = 'wp_kses_post' ) {
    $this->store->e( $this->get_prefixed_key( $key ), $esc_fn );
  }

  public function get( $key ) {
    return $this->store->get( $this->get_prefixed_key( $key ) );
  }

  protected final function get_prefixed_key( $key ) {
    if ( $this->key_prefix ) {
      $key = $this->key_prefix . $key;
    }

    return $key;
  }
}

function rsh_customize_register( $wp_customize ) {
  temp_customize_register( $wp_customize );

  new RSh_PageLead_Customizer( $wp_customize );

  // Меняем с manage_options чтобы был доступ для роли User
  $wp_customize->get_setting( 'blogname' )->capability = 'edit_theme_options';
  $wp_customize->get_setting( 'blogdescription' )->capability = 'edit_theme_options';
  $wp_customize->get_setting( 'site_icon' )->capability = 'edit_theme_options';
}
add_action( 'customize_register', 'rsh_customize_register' );

function rsh_customize_preview_js() {
  temp_customize_preview_js();

  wp_register_script(
    'wpautop',
    get_template_directory_uri() . '/js/wpautop.js',
    array(), false, true
  );

  wp_enqueue_script( 
    'rsh-customize-preview',
    get_template_directory_uri() . '/js/customize-preview.js',
    array( 'jquery', 'customize-preview', 'wpautop' ), false, true
  );

  wp_localize_script( 
    'rsh-customize-preview', 
    'data', array( 
      'jsonText' => file_get_contents( get_stylesheet_directory_uri() . '/customize-preview.json' ) 
    ) 
  );
}
add_action( 'customize_preview_init', 'rsh_customize_preview_js' );

// ----------------

function temp_customize_register( $wp_customize ) {
  $wp_customize->add_panel( 'temp_panel', array(
    'title' => 'Temp Panel',
    'priority' => 1000,
  ) );

  $wp_customize->add_section( 'temp_section', array(
    'title' => 'Temp Section',
    'panel' => 'temp_panel',
  ) );

  // ----

  $setting = $wp_customize->add_setting( 'temp_field1', array(
    'default' => false,
    'sanitize_callback' => function ( $checkbox ) {
      return ! empty( $checkbox );
    },
  ) );

  $wp_customize->add_control( 'temp_field1', array(
    'type' => 'checkbox',
    'section' => 'temp_section',
    'label' => 'Temp Field1',
  ) );

  // ----

  $wp_customize->add_setting( 'temp_field2', array(
    'default' => '',
    'sanitize_callback' => 'sanitize_text_field',
  ) );

  $wp_customize->add_control( 'temp_field2', array(
    'type' => 'text',
    'section' => 'temp_section',
    'label' => 'Temp Field2',
    'active_callback' => array( $setting, 'value' ),
  ) );
}

function temp_customize_preview_js() {

}