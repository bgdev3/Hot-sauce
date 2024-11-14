<?php
/**
 * Astra Child Theme functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Astra-child
 * @since 1.0.0
 */

/**
 * Define Constants
 */
define( 'ASTRA_CHILD_THEME_VERSION', '1.0.1' );

/**
 * Enqueue styles
 */
function child_enqueue_scripts() {
    // Charge les styles
	wp_enqueue_style( 'astra-child-theme-css', get_stylesheet_directory_uri() . '/style.css', array('astra-theme-css'), ASTRA_CHILD_THEME_VERSION, 'all' );
    // Charge le police
	wp_enqueue_script('font-awesome-kit', 'https://kit.fontawesome.com/878534cf28.js');
}
add_action( 'wp_enqueue_scripts', 'child_enqueue_scripts' );

// Active l'affichage de l'image à la une
add_theme_support( 'post-thumbnails' );
// Active l'affichage grande largeur(pour les images)
add_theme_support( "align-wide" );

// 
add_filter( 'doing_it_wrong_trigger_error', '__return_false' );


function themeTuto_customize_register($wp_customize) {
    
	//Personnalise l'image de la page d'accueil
	$wp_customize->add_section('image_header', array(
        'title' => 'Image page d\'accueil',
        'priority' => 120,
    ));

    $wp_customize->add_setting('image', array(
        'default' => '',
        'transport' => 'refresh',
        'capability' => 'edit_theme_options',
        'sanitize_callback' => 'absint',
    ));
	
    $wp_customize->add_control( new WP_Customize_Media_Control($wp_customize, 'image', array(
        'setting' => 'image',
        'label' => 'Image d\'accueil',
        'section' => 'image_header',
        'mime_type' => 'image',
    )));
}
add_action('customize_register', 'themeTuto_customize_register');

/**
 * Récupère et affiche dynamiquement l'image d'accueil
 */
function getImage_header_responsive() {

    $image_url = wp_get_attachment_url( get_theme_mod('image') );
    ?>
        <style>
            div.img_accueil{
                background-image: url('<?php echo $image_url; ?>');
            }
        </style>
    <?php
}
add_action('wp_head', 'getImage_header_responsive');

/**
 * Vérifie lors du chargement des scripts que le shortcode contactForm est présent
 * et ne charge le script de l'API Recaptcha uniquement en cas de la présence du shortcode.
 */
add_action('wp_print_scripts', function () {
    global $post;
    if(is_a($post, 'WP_Post') && !has_shortcode( $post->post_content, 'contact-form-7')) {
        wp_dequeue_script('google-recaptcha');
        wp_dequeue_script('wpcf7-recaptcha');
    }
});
