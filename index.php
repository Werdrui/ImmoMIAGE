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
                <select id="departement-select">
                    <option value="">Sélectionnez un département</option>
                    <?php
                    $stmt = $pdo->query("SELECT code_departement, nom_departement FROM departements_prix ORDER BY nom_departement");
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        echo "<option value=\"" . htmlspecialchars($row['code_departement']) . "\">" . htmlspecialchars($row['nom_departement']) . "</option>";
                    }
                    ?>
                </select>

                <input type="number" id="surface-input" value="10" placeholder="Surface en m²" min="1" style="width: 120px; padding: 0.3rem; border-radius: 5px; border: 1px solid #ccc;">

                <button id="calcul-btn" style="padding: 0.5rem 1rem; border-radius: 5px; border: none; background-color: #0b66ff; color: #fff; cursor: pointer;">Calculer</button>
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
        let departementsData = [];

        async function initCarte() {
            // --- Charger GeoJSON depuis PHP ---
            const response = await fetch('geojson_prix.php');
            const geojson = await response.json();
            departementsData = geojson.features.map(f => ({
                code: f.properties.code,
                nom: f.properties.nom,
                prix_m2: f.properties.prix_m2
            }))
        };

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
                layers: [{
                    id: 'osm-layer',
                    type: 'raster',
                    source: 'osm'
                }]
            },
            center: [2.3522, 48.8566], // Paris
            zoom: 10
        });

        map.on('load', async () => {
            try {
                // === 1️⃣ Chargement du fichier GeoJSON ===
                const response = await fetch('geojson_prix.php');
                const geojson = await response.json();

                // === 2️⃣ Ajout de la source ===
                map.addSource('regions', {
                    type: 'geojson',
                    data: geojson
                });

                // === 3️⃣ Couleurs selon le prix (ou autre champ) ===
                map.addLayer({
                    id: 'regions-layer',
                    type: 'fill',
                    source: 'regions',
                    paint: {
                        'fill-color':
                              [
                                'interpolate',
                                ['linear'],
                                ['get', 'prix_m2'], // 👉 adapte ici selon ton attribut
                                1000, '#00FF00',
                                3000, '#FFFF00',
                                5000, '#FFA500',
                                8000, '#FF0000'
                              ]
                            ,
                        'fill-opacity': 0.6,
                        'fill-outline-color': '#444'
                    }
                });

                // === 4️⃣ Contour au survol ===
                map.addLayer({
                    id: 'region-hover',
                    type: 'line',
                    source: 'regions',
                    paint: {
                        'line-color': '#000',
                        'line-width': 2
                    },
                    filter: ['==', 'nom', ''] // rien par défaut
                });

                const popup = new maplibregl.Popup({
                    closeButton: false,
                    closeOnClick: false
                });

                // === 5️⃣ Interaction au survol ===
                map.on('mousemove', 'regions-layer', (e) => {
                    const feature = e.features[0];
                    map.getCanvas().style.cursor = 'pointer';

                    map.setFilter('region-hover', ['==', 'nom', feature.properties.nom]);

                    popup
                        .setLngLat(e.lngLat)
                        .setHTML(`<b>${feature.properties.nom}</b><br>${feature.properties.prix_m2 ?? 'N/A'} € / m²`)
                        .addTo(map);
                });

                map.on('mouseleave', 'regions-layer', () => {
                    map.getCanvas().style.cursor = '';
                    popup.remove();
                    map.setFilter('region-hover', ['==', 'nom', '']);
                });
            } catch (err) {
                console.error('Erreur de chargement du GeoJSON :', err);
            }
        });

        // --- ⚡ Quand on clique sur un département ---
        map.on('click', 'regions-layer', (e) => {
            const feature = e.features[0];
            const code = feature.properties.code;

            // Sélectionne le département dans le menu
            const select = document.getElementById('departement-select');
            select.value = code;

            // Recalcule automatiquement le prix si la surface est déjà renseignée
            calculerPrix();
        });

        // --- Fonction de calcul du prix ---
        function calculerPrix() {
            const code = document.getElementById('departement-select').value;
            const surface = parseFloat(document.getElementById('surface-input').value);
            const resultDiv = document.getElementById('resultat');

            if (!code || isNaN(surface) || surface <= 0) {
                resultDiv.textContent = 'Veuillez sélectionner un département et entrer une surface valide.';
                return;
            }

            const dep = departementsData.find(d => d.code === code);
            if (!dep || !dep.prix_m2) {
                resultDiv.textContent = 'Prix non disponible pour ce département.';
                return;
            }

            const prixTotal = dep.prix_m2 * surface;
            resultDiv.textContent = `🏠 Prix estimé : ${prixTotal.toLocaleString()} € pour ${surface} m² à ${dep.nom}`;
        }

        // --- Événement du bouton ---
        document.getElementById('calcul-btn').addEventListener('click', calculerPrix);

        initCarte();
    </script>

</body>

</html>