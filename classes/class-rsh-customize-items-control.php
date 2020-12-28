<?php
require_once get_template_directory() . '/classes/class-rsh-customize-control.php';

class RSh_Customize_Items_Control extends RSh_Customize_Control {
  public function to_json() {
    parent::to_json();
    $this->json['items'] = $this->get_items();
  }

  protected function get_items() {
    $items = $this->value();
    
    if ( empty( $items ) || $items == '[]' ) {
      $items = $this->get_default();
    }

    return json_decode( $items ) ?: array();
  }
}