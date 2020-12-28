<?php
require_once get_template_directory() . '/classes/class-rsh-customize-control.php';

class RSh_Customize_Editor_Control extends RSh_Customize_Control {
  public $type = 'rsh_editor';

  public function enqueue() {
    wp_enqueue_editor();

    wp_register_script(
      'rsh-object',
      get_template_directory_uri() . '/js/rsh-object.js', 
      array( 'jquery' ), false, true
    );

    wp_enqueue_script( 
      'rsh-editor', 
      get_template_directory_uri() . '/js/rsh-editor.js', 
      array( 'rsh-object' ), false, true 
    );

    parent::enqueue();
  }

  public function to_json() {
    parent::to_json();

    $this->json['value'] = $this->value() ?: 
      $this->get_default();
  }
  
  protected function content_template() {
    ?>
    <# var editorId = _.uniqueId( 'rsh-editor-' ); #>

    <# if ( data.label ) { #>
      <label for="{{ editorId }}" class="customize-control-title">{{ data.label }}</label>
    <# } #>

    <div class="customize-control-notifications-container"></div>

    <# if ( data.description ) { #>
      <span class="description customize-control-description">{{ data.description }}</span>
    <# } #>

    <textarea id="{{ editorId }}" class="rsh-editor">{{ data.value }}</textarea>
    <?php
  }
}