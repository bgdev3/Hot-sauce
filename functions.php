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
 * Masque la version de wordpress dans la balise meta
 * @return string
 */
function cs_remove_version() {
    return '';
}
add_filter('the_generator', 'cs_remove_version');


/**
 * Masquer la version de WordPress des scripts et styles
 * @return string 
 */
function remove_wp_version_strings($src) {
    // Si la version de WordPress est présente dans l'URL du script/style, on la retire
    if ( strpos( $src, 'ver=' . get_bloginfo( 'version' ) ) ) {
        $src = remove_query_arg( 'ver', $src );
    }
    return $src;
}
add_filter( 'script_loader_src', 'remove_wp_version_strings' );
add_filter( 'style_loader_src', 'remove_wp_version_strings' );


/**
 * Masque les erreurs de connexion d administration
 * @return string les messssage d'erreur
 */
function wpm_hide_errors() {
	return "L'identifiant ou le mot de passe est incorrect";
}
add_filter('login_errors', 'wpm_hide_errors');


/**
 * AJoute les categories de produits en blocs sur la page d'accueil
 */
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
                                        <div> Livraison sous 4 jours ouvrés </div>
        </section>";
}
add_action('astra_footer_before', 'add_content_footer');


/**
 * Personnalise les styles des emails WooCommerce
 */
function personnaliser_couleur_email_woocommerce( $email_content, $email ) {
    // Vérifier si l'email est un e-mail WooCommerce spécifique
    if ( isset( $email->id ) && in_array( $email->id, array( 'new_order', 'processing_order', 'completed_order' ) ) ) {
        // Ajouter du CSS personnalisé dans le contenu de l'email
        $email_content = '<style>
            div#m_-2663956121969469923wrapper {
                padding: 20px 0 !important;

            }
            body {
                color: #fff; 
            }
            h1, h2, h3 {
                color: #B51C04; 
            }
            p {
                color: #fff; 
            }
            .im {
               color : #fff
               }
            .email-order-details {
                color: #fff; 
            }
        </style>' . $email_content;
    }

    return $email_content;
}
add_filter( 'woocommerce_email_content', 'personnaliser_couleur_email_woocommerce', 10, 2 );


/**
 * Affiche le code court des avis google juste après les produits de la boutique
 */
function afficher_code_court_apres_produits() {
    echo do_shortcode('[trustindex no-registration=google]'); // Remplacez [votre_shortcode] par le code court que vous souhaitez afficher
}
add_action('woocommerce_after_shop_loop', 'afficher_code_court_apres_produits', 20);


/**
 * Modifie le texte du bouton "Ajouter au panier" en fonction de la disponibilité du produit
 */
function change_button_based_on_stock($button_text, $product) {
    // Vérifier si le produit est en stock
    if ( ! $product->is_in_stock() ) {
        // Si le produit n'est pas en stock, modifier le texte du bouton
        return __('Découvrir', 'textdomain');
    }

    // Si le produit est en stock, le bouton "Ajouter au panier" reste inchangé
    return $button_text;
}
add_filter('woocommerce_product_add_to_cart_text', 'change_button_based_on_stock', 10, 2);

// Injection du champ SIRET et Raison sociale dans le formulaire d'inscription WHOLS, compatible avec les constructeurs de page et l'AJAX
add_action( 'wp_footer', 'whols_injecter_champs_custom' );
function whols_injecter_champs_custom() {
?>
<script>
(function() {

    function injectFields() {

        const form = document.querySelector('form[action*="whols_registration_action"]');
        if (!form) return;

        if (form.dataset.customInjected === "true") return;

        form.dataset.customInjected = "true";

        const fieldsContainer = form.querySelector('form[action*="whols_registration_action"]') || form;

        const nameField = form.querySelector('#reg_name_field');

        const siret = document.createElement('p');
        siret.className = 'whols-field';
        siret.innerHTML = `
            <label for="siret_number">Numéro de SIRET <span class="required">*</span></label>
            <input type="text"
                   name="siret_number"
                   id="siret_number"
                   maxlength="14"
                   pattern="\\d{14}"
                   inputmode="numeric"
                   required
                   placeholder="SIRET (14 chiffres)">
        `;

        const raison = document.createElement('p');
        raison.className = 'whols-field';
        raison.innerHTML = `
            <label for="raison_sociale">Raison sociale <span class="required">*</span></label>
            <input type="text"
                   name="raison_sociale"
                   id="raison_sociale"
                   required
                   placeholder="Nom de l'entreprise">
        `;

        fieldsContainer.insertBefore( siret, nameField);
        fieldsContainer.insertBefore( raison, nameField);
    }

        injectFields();

})();
</script>
<?php
}

/**
 * Bloque la soumission du formulaire si le SIRET est invalide
 * Client side
 */
add_action( 'wp_footer', 'whols_bloquer_submit_si_siret_invalide' );
 function whols_bloquer_submit_si_siret_invalide() {
        ?>
    <script>

document.addEventListener('click', function (e) {

    const btn = e.target.closest('button[type="submit"], input[type="submit"]');
     if (!btn) return;

    const form = btn.closest('form');
    if (!form) return;

    // 🔍 adapte le sélecteur au vrai champ SIRET
    const siretInput =
         form.querySelector('input[name="siret_number"], input[name="billing_siret"], #siret_number');

    if (!siretInput) return;

    const siret = siretInput.value.trim();

     // SIRET invalide
    if (!isValidSiret(siret)) {
       e.preventDefault();
        e.stopImmediatePropagation();
        e.stopPropagation();

        alert('❌ SIRET invalide (14 chiffres requis)');
        siretInput.focus();

         return false;
    }
 }, true); // ← capture = IMPORTANT


 function isValidSiret(siret) {
    
     siret = String(siret);

     if (!/^\d{14}$/.test(siret)) return false;
     if (/^0{14}$/.test(siret)) return false;

     let sum = 0;
     let shouldDouble = false;

     for (let i = siret.length - 1; i >= 0; i--) {
         let digit = parseInt(siret[i], 10);

         if (shouldDouble) {
             digit *= 2;
             if (digit > 9) digit -= 9;
         }

         sum += digit;
        shouldDouble = !shouldDouble;
    }

     return sum % 10 === 0;
     
 }

 </script>
 <?php
 }

/**
 * Vérifie si un SIRET est valide (14 chiffres + Luhn)
 * Serveur-Side
 */
function is_valid_siret( $siret ) {

    // Nettoyage (espaces, points, etc.)
    $siret = preg_replace('/\D/', '', $siret);

    // Doit contenir exactement 14 chiffres
    if (strlen($siret) !== 14) {
        return false;
    }

    $sum = 0;

    for ($i = 0; $i < 14; $i++) {
        $digit = (int) $siret[$i];

        if ($i % 2 === 0) {
            $digit *= 2;
            if ($digit > 9) {
                $digit -= 9;
            }
        }

        $sum += $digit;
    }

    return ($sum % 10 === 0);
}


add_action( 'wcwp_register_post', 'validate_siret_wholesale', 10, 3 );
function validate_siret_wholesale( $username, $email, $errors ) {

    if ( empty($_POST['fields']['siret_number']) ) {
        $errors->add( 'siret_error', 'Le SIRET est obligatoire.' );
        return;
    }

    $siret = sanitize_text_field($_POST['fields']['siret_number']);

    if ( ! preg_match('/^[0-9]{14}$/', $siret) ) {
        $errors->add( 'siret_error', 'SIRET invalide (14 chiffres requis).' );
    }

}


// Redirection après connexion WooCommerce
add_filter('woocommerce_login_redirect', 'redirection_apres_connexion', 10, 2);
function redirection_apres_connexion($redirect, $user) {
    return wc_get_page_permalink('shop'); // page Boutique
}

// Redirection après création de compte WooCommerce
add_filter('woocommerce_registration_redirect', 'redirection_apres_inscription');
function redirection_apres_inscription() {
    return wc_get_page_permalink('shop');
}

add_shortcode('astra_user_status', function () {

    // Nombre d’articles dans le panier
    $cart_count = WC()->cart ? WC()->cart->get_cart_contents_count() : 0;

    $cart = '
    <a href="' . wc_get_cart_url() . '" class="cart-icon" title="Panier">
        🛒 <span class="cart-count">' . $cart_count . '</span>
    </a>';

    if (is_user_logged_in()) {

        $user = wp_get_current_user();
        $prenom = esc_html($user->first_name);

        return '
        <span class="header-icons">
            <a href="' . wc_get_page_permalink('myaccount') . '" title="Mon compte">👤 ' . $prenom . '</a>
            ' . $cart . '
            <a class="logout" href="' . wp_logout_url(home_url()) . '" title="Déconnexion">🔓</a>
        </span>';

    } else {

        return '
        <span class="header-icons">
            <a href="' . wc_get_page_permalink('myaccount') . '" title="Se connecter">👤</a>
            ' . $cart . '
        </span>';
    }
});

add_filter('woocommerce_add_to_cart_fragments', function ($fragments) {

    $cart_count = WC()->cart->get_cart_contents_count();

    $fragments['span.cart-count'] = '<span class="cart-count">' . $cart_count . '</span>';

    return $fragments;
});

// 
add_filter( 'woocommerce_available_payment_gateways', 'paiement_grossistes_bacs_cb' );
function paiement_grossistes_bacs_cb( $gateways ) {

    if ( is_admin() ) {
        return $gateways;
    }

    // 🔒 Par défaut : on SUPPRIME le virement bancaire pour tout le monde
    if (! is_user_logged_in() && isset( $gateways['bacs']) ) {
         unset( $gateways['bacs'] );
         return $gateways;
    }
  
    // Si l'utilisateur est connecté
    if ( is_user_logged_in() ) {

        $user = wp_get_current_user();

        // ✅ UNIQUEMENT pour les grossistes
        if ( in_array( 'whols_default_role', (array) $user->roles ) ) {

            // Moyens autorisés pour les grossistes
            $allowed_gateways = array( 'bacs', 'stripe' );

            foreach ( $gateways as $gateway_id => $gateway ) {
                if ( ! in_array( $gateway_id, $allowed_gateways ) ) {
                    unset( $gateways[ $gateway_id ] );
                }
            }
        }
    }

    return $gateways;
}

// 
add_action( 'woocommerce_cart_calculate_fees', 'frais_cb_grossiste', 20, 1 );
function frais_cb_grossiste( $cart ) {

    if ( is_admin() && ! defined( 'DOING_AJAX' ) ) return;
    if ( ! is_user_logged_in() ) return;

    $user = wp_get_current_user();

    // Vérifie rôle grossiste
    if ( ! in_array( 'whols_default_role', (array) $user->roles ) ) {
        return;
    }

    // Sécurise l'accès au moyen de paiement
    $chosen_payment_method = WC()->session->get( 'chosen_payment_method' );

    // Si on est sur la page de commande et que le paiement est posté
    if ( isset( $_POST['payment_method'] ) ) {
        $chosen_payment_method = sanitize_text_field( $_POST['payment_method'] );
    }

    // Applique uniquement si Stripe (CB) est sélectionné
    if ( $chosen_payment_method === 'stripe' ) {
        $cart->add_fee( 'Frais paiement carte bancaire', 1.95, false );
    }
}

// Permet de recalculer les frais à chaque changement de moyen de paiement (notamment pour Stripe Express / Google Pay)
add_action( 'wp_footer', function() {
    if ( is_checkout() ) : ?>
        <script>
            jQuery(function($){
                $('form.checkout').on('change', 'input[name="payment_method"]', function(){
                    $('body').trigger('update_checkout');
                });
            });
        </script>
    <?php endif;
});

// Supprime les options de paiement Google Pay pour les grossistes via CSS dans le panier(car Stripe Express ne gère pas les frais CB)
add_action( 'wp_head', 'hide_google_pay_for_wholesalers_css' );

function hide_google_pay_for_wholesalers_css() {

    if ( ! is_user_logged_in() ) return;

    $user = wp_get_current_user();

    if ( in_array( 'whols_default_role', (array) $user->roles ) ) {
        echo '<style>
                #wc-stripe-express-checkout-element, .ppcp-messages {
                display: none !important;
            }
        </style>';
    }
}


add_filter( 'woocommerce_package_rates', 'custom_free_shipping_label_wholesale', 100 );
function custom_free_shipping_label_wholesale( $rates ) {

    if ( is_user_logged_in() ) {
        $user = wp_get_current_user();

        if ( in_array( 'whols_default_role', $user->roles, true ) ) {

            $rates = array();

            $rates['custom_free_shipping'] = new WC_Shipping_Rate(
                'custom_free_shipping',
                'Franco de port - frais de livraison inclus',
                0,
                array(),
                'custom_free_shipping'
            );
        }
    }

    return $rates;
}

// Ajouter une checkbox dans l'onglet Avancée
add_action('woocommerce_product_options_advanced', function() {
    woocommerce_wp_checkbox([
        'id'          => '_hide_for_wholesaler',
        'label'       => 'Masquer pour les Wholesalers',
        'description' => 'Ce produit sera invisible pour les professionnels.',
    ]);
});

// Enregistrer la valeur de la checkbox
add_action('woocommerce_process_product_meta', function($post_id) {
    $value = isset($_POST['_hide_for_wholesaler']) ? 'yes' : 'no';
    update_post_meta($post_id, '_hide_for_wholesaler', $value);
});

// Rendre le produit invisible pour les utilisateurs avec le rôle "whols_default_role" si la checkbox est cochée
add_filter('woocommerce_product_is_visible', function($visible, $product_id) {

    if (is_user_logged_in()) {
        $user = wp_get_current_user();

        if (in_array('whols_default_role', $user->roles)) {
            $hide = get_post_meta($product_id, '_hide_for_wholesaler', true);

            if ($hide === 'yes') {
                return false;
            }
        }
    }
    return $visible;
}, 10, 2);

// Remplacement du contenu
add_action('wp_footer', function() {

    if ( ! is_user_logged_in() ) return;

    $user = wp_get_current_user();

    if ( in_array( 'whols_default_role', (array) $user->roles ) ) {
    ?>
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        const wrapper = document.querySelector(".categories-blocks-wrapper");
        if(wrapper){
            wrapper.innerHTML = '<div class="pro">ESPACE PROFESSIONNEL</div>';
        }
    });
    </script>
    <?php
    }
});

// Bloque l'accès au dashboard pour les clients
function block_dashboard_for_customers() {
    if ( is_admin() && ! current_user_can( 'administrator' ) && ! wp_doing_ajax() ) {
        wp_redirect( home_url() );
        exit;
    }
}
add_action( 'admin_init', 'block_dashboard_for_customers' );

// Validation du mot de passe côté client pour le formulaire d'inscription 
function custom_password_validation_script() {
    if(is_page('mon-compte')) {
    ?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Sélectionner le formulaire spécifique
        const form = document.querySelector('.xoo-el-action-form.xoo-el-form-register');
       
         let btn  = document.querySelector('.button.xoo-el-action-btn.xoo-el-register-btn');
     
        
        
        if (!form) return; // Si le formulaire n'est pas trouvé, on arrête le script
        
        const passwordInput = form.querySelector('[name="xoo_el_reg_pass"], input[type="text"]'); // Le champ de mot de passe
     

        // Fonction pour créer ou récupérer le conteneur d'erreur
        function getErrorContainer() {
            let errorContainer = form.querySelector('.xoo-el-notice-error');
            
                 if (!errorContainer) {
                // Si le conteneur d'erreur n'existe pas, le créer
                errorContainer = document.createElement('div');
                errorContainer.classList.add('xoo-el-notice-error');
                passwordInput.insertAdjacentElement('afterend', errorContainer); // L'ajouter après le champ mot de passe
            } 
            return errorContainer;
            } 

        // Fonction de validation du mot de passe
        function validatePassword() {
            const password = passwordInput.value; // Récupère la valeur du champ de mot de passe (client ou professionnel)
            let errors = [];

            // Validation des critères
            if (password.length < 12) errors.push("Le mot de passe doit contenir au moins 12 caractères.");
            if (!/[A-Z]/.test(password)) errors.push("Le mot de passe doit contenir au moins une lettre majuscule.");
            if (!/[a-z]/.test(password)) errors.push("Le mot de passe doit contenir au moins une lettre minuscule.");
            if (!/[\W_]/.test(password)) errors.push("Le mot de passe doit contenir au moins un caractère spécial.");

            // Récupérer ou créer le conteneur d'erreur
            const errorContainer = getErrorContainer();

            // Affichage des erreurs
            if (errors.length > 0) {
                btn.disabled = true; 
                errorContainer.textContent = "Erreur mot de passe : " + errors.join(" ");
                errorContainer.style.display = 'block'; // Affiche l'erreur
                return false; // Échec de la validation
            } else {
                btn.disabled = false;
                errorContainer.style.display = 'none'; // Cache l'erreur si valide
                return true; // Validation réussie
            }
        }

        passwordInput.addEventListener('input', validatePassword); // Valide à chaque saisie
           
       
        passwordInput.addEventListener('blur',() =>{
                     validatePassword(); // Valide au focus pour afficher les erreurs dès que l'utilisateur clique sur le champ
                        });     
            

                form.addEventListener('submit', function(e) {
                e.preventDefault();
                if(validatePassword()) {
                    this.submit()
            }
            });
        }); 
    </script>
    <?php
    }
}
add_action('wp_footer', 'custom_password_validation_script');


add_filter( 'wc_password_strength_meter', 'custom_password_strength', 10, 2 );
// Ajouter les balises meta Facebook Open Graph globalement
function ajouter_meta_facebook_global() {
    echo '<meta property="og:image" content="https://pimentsoleil.fr/wp-content/uploads/2025/06/cropped-WhatsApp-Image-2025-06-10-a-08.45.52_a1767f68.png" />' . "\n";
    echo '<meta property="og:image:width" content="1200" />' . "\n";
    echo '<meta property="og:image:height" content="630" />' . "\n";
    echo '<meta property="fb:app_id" content="1439759470889897" />' . "\n";
}
add_action('wp_head', 'ajouter_meta_facebook_global', 9999);

function ajouter_shortcode_avant_LoginButton() {
    if (is_page('mon-compte')) {

        ?>
        <script type="text/javascript">
            document.addEventListener('DOMContentLoaded', function() {
                var shortcodeHTML = '<?php echo do_shortcode("[nextend_social_login provider=\"facebook\"]"); ?>';
                var loginButton = document.querySelector('.button.btn.xoo-el-action-btn.xoo-el-login-btn');
                
                if (loginButton) {
                    loginButton.insertAdjacentHTML('afterend', shortcodeHTML);
                }
            });
        </script>
        <?php
    }
}
add_action('wp_footer', 'ajouter_shortcode_avant_LoginButton');

// Force la déconnexion et redirige vers l'accueil
function force_logout_and_redirect() {

    if ( isset($_GET['action']) && $_GET['action'] === 'logout' ) {

        // Déconnexion réelle
        wp_logout();

        // Redirection vers l'accueil
        wp_safe_redirect( home_url() );
        exit;
    }
}
add_action( 'init', 'force_logout_and_redirect' );


/**
 * Sauvegarder les champs société lors de l'inscription WHOLS
 */
add_action('user_register', 'whols_save_company_fields');

function whols_save_company_fields($user_id) {

    if (!empty($_POST['fields']['raison_sociale'])) {

        $company = sanitize_text_field($_POST['fields']['raison_sociale']);

        update_user_meta($user_id, 'raison_sociale', $company);

        // pour WooCommerce
        update_user_meta($user_id, 'billing_company', $company);
    }

    if (!empty($_POST['fields']['siret_number'])) {

        $siret = sanitize_text_field($_POST['fields']['siret_number']);

        update_user_meta($user_id, 'siret_number', $siret);
    }

}


/**
 * Afficher les champs société dans l'admin utilisateur
 */
add_action('show_user_profile', 'whols_display_company_fields');
add_action('edit_user_profile', 'whols_display_company_fields');

function whols_display_company_fields($user) {

    $raison_sociale = get_user_meta($user->ID, 'raison_sociale', true);
    $siret = get_user_meta($user->ID, 'siret_number', true);

?>

<h2>Informations société</h2>

<table class="form-table">

<tr>
<th><label>Raison sociale</label></th>
<td>
<input type="text"
value="<?php echo esc_attr($raison_sociale); ?>"
class="regular-text"
disabled>
</td>
</tr>

<tr>
<th><label>SIRET</label></th>
<td>
<input type="text"
value="<?php echo esc_attr($siret); ?>"
class="regular-text"
disabled>
</td>
</tr>

</table>

<?php
}


/**
 * Ajouter le SIRET dans la commande WooCommerce
 */
add_action('woocommerce_checkout_create_order', 'whols_add_siret_to_order', 20, 2);

function whols_add_siret_to_order($order, $data) {

    $user_id = get_current_user_id();

    if ($user_id) {

        $siret = get_user_meta($user_id, 'siret_number', true);

        if ($siret) {
            $order->update_meta_data('SIRET', $siret);
        }

    }

}


/**
 * Afficher le SIRET dans l'admin commande WooCommerce
 */
add_action('woocommerce_admin_order_data_after_billing_address', 'whols_display_siret_admin');

function whols_display_siret_admin($order) {

    $siret = $order->get_meta('SIRET');

    if ($siret) {
        echo '<p><strong>SIRET :</strong> ' . esc_html($siret) . '</p>';
    }

}


// Supprime le moyen de paiemnt paypal uniquement sur le panier
add_action('wp_head', 'supprimer_paypal_panier');

function supprimer_paypal_panier() {
    if (is_page('panier')) {
        ?>
            <style>
                #ppc-button-ppcp-gateway, .ppcp-messages {
                    display: none !important;
                }
            </style>
            <?php
    }
}