<?php

if(!class_exists('GF_PLL')) :


class GF_PLL {


  private $whitelist;
  private $form;
  
  
  public function __construct() {

    $this->whitelist = array(
      'title', 
      'description', 
      'text', 
      'content', 
      'message', 
      'defaultValue', 
      'errorMessage',
      'placeholder'
    );

  }


  private function is_translatable($key, $value) {

    return 
      $key && 
      in_array($key, $this->whitelist) &&
      is_string($value);

  }


  private function iterate(&$value, $key, $callback = null) {

    if(!$callback && is_callable($key)) {
      $callback = $key;
    }

    if(is_array($value) || is_object($value)) {
      foreach ($value as $new_key => &$new_value) {
        $this->iterate($new_value, $new_key, $callback);
      }
    } else {
      $callback($value, $key);
    }

  }


  public function register_strings() {

    if(!class_exists('GFAPI') || !function_exists('pll_register_string')) return;

    $forms = GFAPI::get_forms();
    foreach ($forms as $form) {
      $this->form = $form;
      $this->iterate($form, function($value, $key) {
        if($this->is_translatable($key, $value)) {
          $name = $key;
          $group = "Form #{$this->form['id']}: {$this->form['title']}";
          pll_register_string($name, $value, $group);
        }
      });
    }

  }


  public function translate_strings($form) {

    if(function_exists('pll__')) {
      $this->iterate($form, function(&$value, $key) {
        if($this->is_translatable($key, $value)) {
          $value = pll__($value);
        }
      });
    }

    return $form;

  }


}

endif;