<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="<?php bloginfo( 'charset' ); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
  <header class="container-fluid header">
    <?php the_custom_logo(); // rsh_header_logo - не нужент, т.к. он без selective refresh ?>
    <?php rsh_header_menu(); ?> 
  </header>