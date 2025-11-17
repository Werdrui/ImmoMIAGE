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
    <title>ImmoMIAGE Carte des prix immobiliers</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://unpkg.com/maplibre-gl@3.6.1/dist/maplibre-gl.css" rel="stylesheet" />
    <title>Une maison ? Un prix !</title>
    <script src="https://unpkg.com/maplibre-gl@3.6.1/dist/maplibre-gl.js"></script>
</head>

<body>
    <header class="site-header">
        <div class="container">
            <h1 class="logo"><a href="index.php">ImmoMIAGE</a></h1>
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
        <section id="calcul-prix" class="content">
            <h2>Calculer le prix d’un bien</h2>
            <div id="form-calcul">
                <select id="departementSelect">
                    <option value="">Sélectionnez un département</option>
                </select>

                <input type="number" id="surfaceInput" value="10" placeholder="Surface en m²" min="1" style="width: 120px; padding: 0.3rem; border-radius: 5px; border: 1px solid #ccc;">
                <div>
                    
                    <label>Type de logement :</label>
                    <input type="radio" name="type_logement" value="maison" checked> Maison
                    <input type="radio" name="type_logement" value="appartement"> Appartement
                </div>
                <div>
                    <input type="number" id="anneeInput" placeholder="Année de construction" min="1800" max="2025">
                </div>
                

                <input type="number" id="nbPiecesInput" placeholder="Nombre de pièces" min="1">

                <input type="number" id="nbMetresJardinInput" placeholder="Surface du jardin (m²)" min="0">

                <input type="number" id="nbEtagesInput" placeholder="Nombre d'étages" min="0">

                <div>
                    <label><input type="checkbox" id="piscineInput"> Piscine</label>
                    <label><input type="checkbox" id="parkingInput"> Parking</label>
                    <label><input type="checkbox" id="balconTerrasseInput"> Balcon/Terrasse</label>
                </div>

                <select id="diagEnergetiqueInput">
                    <option value="Inconnu">Diagnostic énergétique</option>
                    <option value="A">A</option>
                    <option value="B">B</option>
                    <option value="C">C</option>
                    <option value="D">D</option>
                    <option value="E">E</option>
                    <option value="F">F</option>
                </select>

                <select id="vestusteInput">
                    <option value="Inconnu">Vétusté</option>
                    <option value="A">Très bon état</option>
                    <option value="B">Bon état</option>
                    <option value="C">Passable</option>
                    <option value="D">Travaux obligatoires</option>
                </select>

                <button id="calculer" style="padding: 0.5rem 1rem; border-radius: 5px; border: none; background-color: #0b66ff; color: #fff; cursor: pointer;">Calculer</button>
            </div>

            <div id="info" style="font-weight: bold; margin-bottom: 1rem;"></div>
        </section>

        <section id="map-section" class="content">
            <h2>Carte des prix au m²</h2>
            <p>Survolez les points pour connaître le prix moyen.</p>
            <div id="map"></div>
        </section>
    </main>

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

        fetch('data/communes_gironde_prix.geojson')
            .then(res => res.json())
            .then(data => {
                // Ajouter la source
                map.addSource('communes', {
                    type: 'geojson',
                    data: data
                });

                map.addLayer({
                    id: 'communes-layer',
                    type: 'fill',
                    source: 'communes',
                    paint: {
                        'fill-color': [
                            'interpolate',
                            ['linear'],
                            ['get', 'prix_m2'],
                            500, '#edf8e9',
                            1250, '#bae4b3',
                            2500, '#74c476',
                            3000, '#31a354',
                            4000, '#006d2c'
                        ],
                        'fill-opacity': 0.7,
                        'fill-outline-color': '#21a3d6ff'
                    }
                });

                const select = document.getElementById('departementSelect');
                data.features.forEach(f => {
                    const opt = document.createElement('option');
                    opt.value = f.properties.DCOE_C_COD;
                    opt.textContent = f.properties.DCOE_L_LIB;
                    select.appendChild(opt);
                });

                const popup = new maplibregl.Popup({
                    closeButton: false,
                    closeOnClick: false
                });

                map.on('mousemove', 'communes-layer', e => {
                    if (!e.features.length) return;
                    const f = e.features[0];
                    const props = f.properties;
                    const prix = props.prix_m2 ? `${props.prix_m2} €/m²` : 'N/A';
                    const insee = props.DCOE_C_COD;
                    map.getCanvas().style.cursor = 'pointer';
                    popup
                        .setLngLat(e.lngLat)
                        .setHTML(`<strong>${props.DCOE_L_LIB} (${insee})</strong><br>Prix moyen : ${prix}`)
                        .addTo(map);
                });

                map.on('mouseleave', 'communes-layer', () => {
                    map.getCanvas().style.cursor = '';
                    popup.remove();
                });

                map.on('click', 'communes-layer', e => {
                    if (!e.features.length) return;
                    const props = e.features[0].properties;
                    document.getElementById('departementSelect').value = props.DCOE_C_COD;
                    calculerPrix();
                });

                document.getElementById('calculer').addEventListener('click', calculerPrix);

                function calculerPrix() {
                    const insee = document.getElementById('departementSelect').value;
                    const surface = parseFloat(document.getElementById('surfaceInput').value);
                    const commune = data.features.find(f => f.properties.DCOE_C_COD === insee);

                    if (commune && commune.properties.prix_m2) {
                        const surface = parseFloat(document.getElementById('surfaceInput').value) || 0;
                        const annee = parseInt(document.getElementById('anneeInput').value) || 2000;
                        const typeLogement = document.querySelector('input[name="type_logement"]:checked').value;
                        const nbPieces = parseInt(document.getElementById('nbPiecesInput').value) || 1;
                        const nbMetresJardin = parseInt(document.getElementById('nbMetresJardinInput').value) || 0;
                        const nbEtages = parseInt(document.getElementById('nbEtagesInput').value) || 1;
                        const piscine = document.getElementById('piscineInput').checked;
                        const parking = document.getElementById('parkingInput').checked;
                        const balconTerrasse = document.getElementById('balconTerrasseInput').checked;
                        const diagEnergetique = document.getElementById('diagEnergetiqueInput').value;
                        const vestuste = document.getElementById('vestusteInput').value;

                        let prix_m2 = commune.properties.prix_m2;

                        if(typeLogement === 'appartement') prix_m2 *= 1.2;
                        else prix_m2 *= 1.5;

                        if(nbPieces > 3) prix_m2 *= 1 + (nbPieces - 3) * 0.05;

                        if(nbMetresJardin > 0) prix_m2 += nbMetresJardin * 20;

                        if(nbEtages > 1) prix_m2 *= 1 + (nbEtages - 1) * 0.03;

                        if(piscine) prix_m2 += 5000;
                        if(parking) prix_m2 += 3000;
                        if(balconTerrasse) prix_m2 += 2000;

                        const diagMultiplicateurs = {A:1.2, B:1.1, C:1, D:0.9, E:0.8, F:0.7};
                        if(diagEnergetique in diagMultiplicateurs) prix_m2 *= diagMultiplicateurs[diagEnergetique];

                        const vestusteMultiplicateurs = {A:1.2, B:1.1, C:0.9, D:0.7};
                        if(vestuste in vestusteMultiplicateurs) prix_m2 *= vestusteMultiplicateurs[vestuste];

                        const age = new Date().getFullYear() - annee;
                        if(age < 10) prix_m2 *= 1.1;
                        else if(age < 30) prix_m2 *= 1;
                        else if(age < 50) prix_m2 *= 0.9;
                        else prix_m2 *= 0.8;
                        const total = Math.round(prix_m2 * surface);

                        document.getElementById('info').textContent =
                            `🏠 ${commune.properties.DCOE_L_LIB} : ${prix_m2} €/m² → Total estimé : ${total} €`;
                    } else {
                        document.getElementById('info').textContent = 'Aucune donnée disponible pour cette commune.';
                    }
                }
            })
            .catch(err => console.error('Erreur de chargement du GeoJSON :', err));
    </script>

</body>

</html>