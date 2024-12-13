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
    // Charge la police GoogoleFont
    wp_enqueue_style( 'wpb-google-fonts', 'https://fonts.googleapis.com/css2?family=Pacifico&display=swap', false ); 

}
add_action( 'wp_enqueue_scripts', 'child_enqueue_scripts' );


// Active l'affichage de l'image à la une
add_theme_support( 'post-thumbnails' );
// Active l'affichage grande largeur(pour les images)
add_theme_support( "align-wide" );


/**
 * Ajoute une personnalisation pour l'image d'accueil
 */
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


/**
 * Gère les fluctuation du prix de livraison selon le panier
 *  
 */
function modifier_frais_livraison_conditionnels($rates) {
    // Récupère le nombre de produits panier
    $cartCount = WC()->cart->get_cart_contents_count();

    // Si le panier est d'au moins 90euros, indique uniquement la livraison gratuite
   if (WC()->cart->get_subtotal() >= 60 ) {
        foreach ($rates as $rate_id => $rate) {
            // Désactive toutes les autres méthodes de livraison 
            if ('free_shipping' !== $rate->method_id) {
                unset($rates[$rate_id]);
            }
        }
    } else {
        // Selon le nombre de produits dans le panier, ajoute 2 ou 4 euros aux frais de livraisons
        if ($cartCount > 3  && $cartCount < 7) {
            foreach ( $rates as $rate_id => $rate ) {
                if ( 'flat_rate' === $rate->method_id ) {
                    $rates[$rate_id]->cost = $rate->cost + 2;
                }
            }
        } elseif ($cartCount > 6 && $cartCount < 10) {
            foreach ( $rates as $rate_id => $rate ) {
                if ( 'flat_rate' === $rate->method_id ) {
                    $rates[$rate_id]->cost = $rate->cost + 4;
                }
            }
        }
    }
    return $rates;
}
add_filter( 'woocommerce_package_rates', 'modifier_frais_livraison_conditionnels', 10, 2 );


/**
 * Masque la version de wordpress dans la balise meta
 * @return string
 */
function cs_remove_version() {
    return '';
}
add_filter('the_generator', 'cs_remove_version');


/**
 * Masquer la version de WordPress des scripts et style
 * @return string 
 */
function fjarrett_remove_wp_version_strings( $src ) {
    global $wp_version;
    parse_str(parse_url($src, PHP_URL_QUERY), $query);
    if ( !empty($query['ver']) && $query['ver'] === $wp_version ) {
    $src = remove_query_arg('ver', $src);
    }
    return $src;
}
add_filter( 'script_loader_src', 'fjarrett_remove_wp_version_strings' );
add_filter( 'style_loader_src', 'fjarrett_remove_wp_version_strings' );


/**
 * Masque les erreurs de connexion d administration
 * @return string les messassage d'erreur
 */
function wpm_hide_errors() {
	return "L'identifiant ou le mot de passe est incorrect";
}
add_filter('login_errors', 'wpm_hide_errors');

function afficher_categories_par_bloc() {
    // Si on est sur la boutique et s'il y a des catégoties de produits et si on se situe uniauement sur la page d'acceuil.
    if (is_shop() || is_product_category() && is_front_page()) {
        // Récupérer toutes les catégories de produits
        $terms = get_terms(array(
            'taxonomy'   => 'product_cat',
            'orderby'    => 'name',
            'hide_empty' => false, // Afficher même celles qui n'ont pas de produits
        ));

        if (!empty($terms) && !is_wp_error($terms)) {
            echo '<div class="categories-blocks-wrapper">'; // Conteneur principal des blocs de catégories

            // Afficher chaque catégorie dans un bloc
            foreach ($terms as $term) {
                echo '<div class="categories-block">'; // Conteneur pour chaque catégorie
                echo '<h3 class="category-title"><a href="' . get_term_link($term) . '">' . $term->name . '</a></h3>';
                echo '<p class="category-description">' . $term->description . '</p>';
                echo '</div>'; // Fin du bloc de catégorie
            }

            echo '</div>'; // Fin du conteneur principal
        }
    }
}
add_action('woocommerce_before_shop_loop', 'afficher_categories_par_bloc', 5);

/**
 * Ajoute du contenu juste avant le footer
 */
function add_content_footer() {
    echo "<section class='flex-logo'>   <div> Garanti sans gluten </div>
                                        <div> Paiement sécurisé</div>
                                        <div> Livraison 1 à 2 jours ouvrés </div>
        </section>";
}
add_action('astra_footer_before', 'add_content_footer');

// function ajouter_note_avec_etoiles_boutique( $rating_html, $product ) {
//     // Vérifie si le produit a une note
//     if ( $product->get_average_rating() > 0 ) {
//         $average_rating = $product->get_average_rating(); // Note moyenne du produit
//         $rating_count = $product->get_rating_count(); // Nombre d'avis

//         // Ajoute la note et le nombre d'avis après les étoiles
//         $rating_html .= '<div class="average-rating">';
//         $rating_html .= '<span class="average-rating-value">(' . $average_rating . ' / 5) - ' . $rating_count . ' avis</span>';
//         $rating_html .= '</div>';
//     }
    
//     return $rating_html;
// }

// add_filter( 'woocommerce_product_get_rating_html', 'ajouter_note_avec_etoiles_boutique', 10, 2 );