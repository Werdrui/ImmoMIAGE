<?php
function isActive($page)
{
    $current = basename($_SERVER['PHP_SELF']);
    return $current === $page ? 'active' : '';
}
?>
<!doctype html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mon Site Classique</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
</head>

<body>
    <header class="site-header">
        <div class="container">
            <h1 class="logo"><a href="index.php">MonSite</a></h1>
            <nav class="main-nav">
                <ul>
                    <li class="<?php echo isActive('index.php'); ?>"><a href="index.php">Accueil</a></li>
                    <li class="<?php echo isActive('about.php'); ?>"><a href="about.php">À propos</a></li>
                    <li class="<?php echo isActive('contact.php'); ?>"><a href="contact.php">Contact</a></li>
                </ul>
            </nav>
        </div>
    </header>


    <main class="container">
        <section class="hero">
            <h2>Bienvenue sur mon site classique</h2>
            <p>Ceci est une page d'exemple construite avec PHP (pour le template) et un fichier CSS séparé.</p>
            <p><a class="btn" href="#features">Découvrir</a></p>
        </section>


        <section id="features" class="features">
            <article>
                <h3>Design épuré</h3>
                <p>Structure HTML sémantique, facile à lire et à maintenir.</p>
            </article>
            <article>
                <h3>Navigation simple</h3>
                <p>Menu en haut de page avec état actif géré par PHP.</p>
            </article>
            <article>
                <h3>Responsive</h3>
                <p>Mise en page adaptative avec CSS moderne.</p>
            </article>
        </section>


        <section class="content">
            <h2>Contenu principal</h2>
            <p>Remplacez ce texte par votre contenu : articles, produits, portfolio, etc.</p>


            <h3>Exemple de carte</h3>
            <div class="card">
                <h4>Titre de la carte</h4>
                <p>Description courte. Un petit appel à l'action en bas.</p>
                <a class="card-link" href="#">En savoir plus →</a>
            </div>


            <div class="map-section">
                <h3>Carte interactive</h3>
                <p>Voici un exemple de carte interactive (utilisant <strong>Leaflet.js</strong>, une alternative libre à Google Maps) :</p>
                <div id="map"></div>
            </div>
        </section>
    </main>


    <footer class="site-footer">
        <div class="container">
            <p>© <?php echo date('Y'); ?> MonSite — Tous droits réservés.</p>
            <p class="small">Ce modèle utilise PHP pour un petit rendu dynamique.</p>
        </div>
    </footer>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        const map = L.map('map').setView([48.8566, 2.3522], 13); // Paris par défaut


        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap'
        }).addTo(map);


        L.marker([48.8566, 2.3522]).addTo(map)
            .bindPopup('Bienvenue à Paris !')
            .openPopup();
    </script>
</body>

</html>