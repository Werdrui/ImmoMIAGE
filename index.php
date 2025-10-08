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
    <title>Mon Site Classique — Carte des prix immobiliers</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://unpkg.com/maplibre-gl@3.6.1/dist/maplibre-gl.css" rel="stylesheet" />
    <script src="https://unpkg.com/maplibre-gl@3.6.1/dist/maplibre-gl.js"></script>
</head>

<body>
    <?php
    require_once 'includes/database.inc.php';
    $pdo = Database::connect();
    ?>
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
            <p>Ceci est une page d'exemple avec une carte interactive gratuite (MapLibre + OpenStreetMap).</p>
            <p><a class="btn" href="#map-section">Voir la carte</a></p>
        </section>

        <section id="calcul-prix" class="content">
            <h2>Calculer le prix d’un bien</h2>
            <div id="form-calcul" style="margin-bottom: 1rem; display: flex; gap: 0.5rem; flex-wrap: wrap; align-items: center;">
                <select id="departementSelect">
                    <option value="">Sélectionnez un département</option>
                </select>

                <input type="number" id="surface-input" value="10" placeholder="Surface en m²" min="1" style="width: 120px; padding: 0.3rem; border-radius: 5px; border: 1px solid #ccc;">

                <button id="calculer" style="padding: 0.5rem 1rem; border-radius: 5px; border: none; background-color: #0b66ff; color: #fff; cursor: pointer;">Calculer</button>
            </div>

            <div id="resultat" style="font-weight: bold; margin-bottom: 1rem;"></div>
        </section>

        <section id="map-section" class="content">
            <h2>Carte des prix au m²</h2>
            <p>Survolez les points pour connaître le prix moyen.</p>
            <div id="map"></div>
        </section>
    </main>


    <footer class="site-footer">
        <div class="container">
            <p>© <?php echo date('Y'); ?> MonSite — Tous droits réservés.</p>
            <p class="small">Carte rendue possible grâce à MapLibre & OpenStreetMap.</p>
        </div>
    </footer>


    <script>
        const map = new maplibregl.Map({
            container: 'map',
            style: {
      version: 8,
      sources: {
        osm: {
          type: 'raster',
          tiles: ['https://tile.openstreetmap.org/{z}/{x}/{y}.png'],
          tileSize: 256,
          attribution: '© OpenStreetMap contributors'
        }
      },
      layers: [{ id: 'osm-layer', type: 'raster', source: 'osm' }]
    },
            center: [-0.58, 44.84], // Bordeaux
            zoom: 8.5
        });

        // Chargement du GeoJSON fusionné (Gironde uniquement)
        fetch('data/gironde_prix.geojson')
            .then(res => res.json())
            .then(data => {
                // Ajouter la source
                map.addSource('communes', {
                    type: 'geojson',
                    data: data
                });

                // Palette de couleur selon le prix moyen
                map.addLayer({
                    id: 'communes-layer',
                    type: 'fill',
                    source: 'communes',
                    paint: {
                        'fill-color': [
                            'interpolate',
                            ['linear'],
                            ['get', 'prix_m2'],
                            1000, '#edf8e9',
                            2500, '#bae4b3',
                            4000, '#74c476',
                            6000, '#31a354',
                            8000, '#006d2c'
                        ],
                        'fill-opacity': 0.7,
                        'fill-outline-color': '#21a3d6ff'
                    }
                });

                // Ajout des communes dans le menu déroulant
                const select = document.getElementById('departementSelect');
                data.features.forEach(f => {
                    const opt = document.createElement('option');
                    opt.value = f.properties.insee;
                    opt.textContent = f.properties.nom_com;
                    select.appendChild(opt);
                });

                // Popup au survol
                const popup = new maplibregl.Popup({
                    closeButton: false,
                    closeOnClick: false
                });

                map.on('mousemove', 'communes-layer', e => {
                    if (!e.features.length) return;
                    const f = e.features[0];
                    const props = f.properties;
                    const prix = props.prix_m2 ? `${props.prix_m2} €/m²` : 'N/A';
                    map.getCanvas().style.cursor = 'pointer';
                    popup
                        .setLngLat(e.lngLat)
                        .setHTML(`<strong>${props.nom_com}</strong><br>Prix moyen : ${prix}`)
                        .addTo(map);
                });

                map.on('mouseleave', 'communes-layer', () => {
                    map.getCanvas().style.cursor = '';
                    popup.remove();
                });

                // Sélection d’une commune par clic
                map.on('click', 'communes-layer', e => {
                    if (!e.features.length) return;
                    const props = e.features[0].properties;
                    document.getElementById('departementSelect').value = props.insee;
                    calculerPrix();
                });

                // Calculer le prix total quand on clique sur le bouton
                document.getElementById('calculer').addEventListener('click', calculerPrix);

                function calculerPrix() {
                    const insee = document.getElementById('departementSelect').value;
                    const surface = parseFloat(document.getElementById('surfaceInput').value);
                    const commune = data.features.find(f => f.properties.insee === insee);

                    if (commune && commune.properties.prix_m2) {
                        const prix_m2 = commune.properties.prix_m2;
                        const total = Math.round(prix_m2 * surface).toLocaleString('fr-FR');
                        document.getElementById('info').textContent =
                            `🏠 ${commune.properties.nom_com} : ${prix_m2} €/m² → Total estimé : ${total} €`;
                    } else {
                        document.getElementById('info').textContent = 'Aucune donnée disponible pour cette commune.';
                    }
                }
            })
            .catch(err => console.error('Erreur de chargement du GeoJSON :', err));
    </script>

</body>

</html>