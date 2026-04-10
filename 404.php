<?php
/*
Template Name: 404
*/
?>

<!-- Charge le head manuellement sans le header -->
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php wp_title(); ?></title>

    <?php wp_head(); // Inclut les éléments essentiels, comme les scripts et styles chargés par WordPress ?>
</head>
<body <?php body_class(); ?>>

<div class="page404">
    <h2>404</h2>
    <p> Vous avez loupé le piment en route ! Cette page n'existe malheureusement pas ... <br>
            Cependant rien n'est perdu, suivez le guide ci-dessous !
    </p>
    <a href="pimentsoleil.fr/dev" class="btn_404">La boutique, c'est par là ...</a>
</div>
<?php

