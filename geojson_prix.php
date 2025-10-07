<?php
header('Content-Type: application/json');

require_once 'includes/database.inc.php';
// Connexion MySQL
$pdo = Database::connect();

// Chemin vers ton GeoJSON de base (sans les prix)
$geojson_file = __DIR__ . '/data/regions.geojson';
$geojson = json_decode(file_get_contents($geojson_file), true);

// Parcours des features pour ajouter le prix depuis la base
foreach ($geojson['features'] as &$feature) {
    $code = $feature['properties']['code'] ?? null;

    if ($code) {
        $stmt = $pdo->prepare("SELECT prix_m2_moyen FROM departements_prix WHERE code_departement = ?");
        $stmt->execute([$code]);
        $prix = $stmt->fetchColumn();

        // Ajout du prix dans les propriétés
        $feature['properties']['prix_m2'] = $prix ? (int)$prix : null;
    } else {
        $feature['properties']['prix_m2'] = null;
    }
}

// Retour JSON
echo json_encode($geojson, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
