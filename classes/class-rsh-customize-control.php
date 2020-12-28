<?php

class RSh_Customize_Control extends WP_Customize_Control {
  public function enqueue() {
    wp_enqueue_style( 
      'rsh-controls',
      get_template_directory_uri() . '/css/rsh-controls.css' 
    );

    wp_enqueue_script(
      'rsh-controls',
      get_template_directory_uri() . '/js/rsh-controls.js', 
      array( 'jquery' ), false, true
    );
  }

  protected function get_default() {
    return $this->setting->default;
  }
}