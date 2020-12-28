<?php
require_once get_template_directory() . '/classes/class-rsh-customize-items-control.php';

class RSh_Customize_Portlets_Control extends RSh_Customize_Items_Control {
  public $type = 'rsh_portlets';

  public function enqueue() {
    wp_enqueue_editor();
    wp_enqueue_media();

    wp_register_script( 
      'rsh-template', 
      get_template_directory_uri() . '/js/rsh-template.js', 
      array( 'jquery', ), false, true 
    );

    wp_register_script(
      'rsh-object',
      get_template_directory_uri() . '/js/rsh-object.js', 
      array( 'jquery' ), false, true
    );

    wp_register_script( 
      'rsh-editor', 
      get_template_directory_uri() . '/js/rsh-editor.js', 
      array( 'rsh-object' ), false, true 
    );

    wp_register_script( 
      'rsh-media', 
      get_template_directory_uri() . '/js/rsh-media.js', 
      array( 'rsh-object' ), false, true
    );

    wp_enqueue_script( 
      'rsh-portlet', 
      get_template_directory_uri() . '/js/rsh-portlet.js', 
      array( 'rsh-template', 'rsh-editor', 'rsh-media' ), false, true 
    );

    parent::enqueue();
  }

  public function to_json() {
    parent::to_json();

    // Изначально attachment это url картинки, 
    // а после преобразования - объект с данными картинки.
    foreach ( $this->json['items'] as $item ) {
      if ( ! empty( $item->attachment ) ) {
        if ( $attachment_id = attachment_url_to_postid( $item->attachment ) ) {
          $item->attachment = wp_prepare_attachment_for_js( $attachment_id );
        }
      }
    }

    $this->json['canUpload'] = current_user_can( 'upload_files' );
  }

  protected function content_template() {
    ?>
    <# if ( data.label ) { #>
      <span class="customize-control-title">{{ data.label }}</span>
    <# } #>

    <# if ( data.description ) { #>
      <span class="description customize-control-description">{{ data.description }}</span>
    <# } #>

    <div class="rsh-input-group">
      <input type="text" class="rsh-input" placeholder="<?php esc_attr_e( 'Добавить новую', 'rsh' ); ?>">
      <button type="button" class="button rsh-btn"><span class="ui-icon ui-icon-plus"></span></button>
    </div>

    <div class="rsh-portlets-area"></div>
    <?php
  }
}