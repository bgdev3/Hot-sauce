# Piment du Soleil 🌶️

Site e-commerce de vente de sauces pimentées, développé avec **WordPress** et un thème enfant **Astra** personnalisé.

---

## Stack technique

| Couche | Technologie |
|---|---|
| CMS | WordPress |
| Thème | Astra (thème enfant) |
| E-commerce | WooCommerce |
| Backend | PHP 8 |
| Styles | CSS3 (surcharge thème enfant) |

---

## Fonctionnalités

- Catalogue produits avec fiches détaillées
- Panier et tunnel d'achat WooCommerce
- Personnalisation complète du thème via thème enfant Astra
- Gestion des produits via back-office WordPress

---

## Installation locale

**Prérequis** : WordPress installé, WooCommerce activé, thème Astra activé

```bash
# 1. Cloner le repo dans le dossier des thèmes WordPress
git clone https://github.com/charlieGui/Hot-sauce.git wp-content/themes/astra-child

# 2. Activer le thème enfant dans WordPress
# Apparence > Thèmes > Astra Child > Activer

# 3. Configurer wp-config.php
# Renseigner DB_NAME, DB_USER, DB_PASSWORD, DB_HOST
```

---

## Structure

```
astra-child/
├── functions.php       # Hooks et personnalisations
├── style.css           # Surcharge styles thème parent
├── template-parts/     # Composants de templates
├── header.php          # Template header personnalisé
└── 404.php             # Page d'erreur personnalisée
```

---

## Auteur

**Guillaume** — [bgdev.fr](https://bgdev.fr)  
Développeur PHP freelance · Spécialisation Symfony
